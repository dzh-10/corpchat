<?php

namespace Tests\Feature;

use App\Jobs\SendOutboundEmailJob;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Tests\TestCase;

class ComposeTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_search_other_employees_excluding_self(): void
    {
        $user1 = User::factory()->create(['name' => 'Alice Smith', 'email' => 'alice@company.com']);
        $user2 = User::factory()->create(['name' => 'Bob Jones', 'email' => 'bob@company.com']);
        $me = User::factory()->create(['name' => 'Charlie Brown', 'email' => 'charlie@company.com']);

        $response = $this->actingAs($me)->getJson('/api/employees/search?q=Ali');

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment([
                'id' => $user1->id,
                'name' => 'Alice Smith',
                'email' => 'alice@company.com',
            ])
            ->assertJsonMissing([
                'id' => $me->id,
            ]);
    }

    public function test_can_compose_external_email_conversation_and_send_message(): void
    {
        Queue::fake();

        $me = User::factory()->create();

        $response = $this->actingAs($me)->postJson('/api/conversations', [
            'type' => 'email',
            'client_email' => 'customer@external.com',
            'client_name' => 'John Customer',
            'subject' => 'Project Inquiry',
            'body' => 'Let us schedule a call tomorrow.',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
            ]);

        $conversation = Conversation::where('type', 'external_email')
            ->where('external_contact_email', 'customer@external.com')
            ->first();

        $this->assertNotNull($conversation);
        $this->assertEquals('John Customer', $conversation->external_contact_name);
        $this->assertEquals('Project Inquiry', $conversation->subject);

        $message = Message::where('conversation_id', $conversation->id)->first();
        $this->assertNotNull($message);
        $this->assertEquals($me->id, $message->sender_id);
        $this->assertEquals('Let us schedule a call tomorrow.', $message->body);
        $this->assertEquals('outbound_email', $message->type);
        $this->assertEquals('sending', $message->status);

        Queue::assertPushed(SendOutboundEmailJob::class, function ($job) use ($message) {
            return $job->messageModel->id === $message->id;
        });
    }

    public function test_can_compose_internal_chat_conversation(): void
    {
        $me = User::factory()->create();
        $colleague = User::factory()->create(['name' => 'Dave Colleague']);

        $response = $this->actingAs($me)->postJson('/api/conversations', [
            'type' => 'internal',
            'recipient_id' => $colleague->id,
            'subject' => 'Quick Sync',
            'body' => 'Do you have 5 minutes?',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
            ]);

        $conversation = Conversation::where('type', 'internal_chat')
            ->where('subject', 'Quick Sync')
            ->first();

        $this->assertNotNull($conversation);

        $message = Message::where('conversation_id', $conversation->id)->first();
        $this->assertNotNull($message);
        $this->assertEquals($me->id, $message->sender_id);
        $this->assertEquals('Do you have 5 minutes?', $message->body);
        $this->assertEquals('internal', $message->type);
        $this->assertEquals('delivered', $message->status);
    }

    public function test_can_get_paginated_messages(): void
    {
        $me = User::factory()->create();
        $conversation = Conversation::create([
            'uuid' => (string) Str::uuid(),
            'type' => 'internal_chat',
            'subject' => 'Group Discussion',
        ]);

        for ($i = 0; $i < 25; $i++) {
            Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $me->id,
                'sender_email' => $me->email,
                'sender_name' => $me->name,
                'body' => "Message number {$i}",
                'type' => 'internal',
                'status' => 'delivered',
            ]);
        }

        $response = $this->actingAs($me)->getJson("/api/conversations/{$conversation->id}/messages");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'conversation_id',
                        'sender_id',
                        'sender_email',
                        'sender_name',
                        'body',
                        'type',
                        'status',
                        'reactions',
                        'created_at',
                    ],
                ],
                'links',
                'meta',
            ]);

        $this->assertCount(20, $response->json('data'));
    }

    public function test_can_toggle_reaction_on_message(): void
    {
        $me = User::factory()->create();
        $conversation = Conversation::create([
            'uuid' => (string) Str::uuid(),
            'type' => 'internal_chat',
            'subject' => 'Group Discussion',
        ]);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $me->id,
            'sender_email' => $me->email,
            'sender_name' => $me->name,
            'body' => 'Hello team!',
            'type' => 'internal',
            'status' => 'delivered',
        ]);

        $response = $this->actingAs($me)->postJson("/api/conversations/{$conversation->id}/messages/{$message->id}/reactions", [
            'reaction' => '👍',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'action' => 'added',
            ]);

        $this->assertDatabaseHas('message_reactions', [
            'message_id' => $message->id,
            'user_id' => $me->id,
            'reaction' => '👍',
        ]);

        $response = $this->actingAs($me)->postJson("/api/conversations/{$conversation->id}/messages/{$message->id}/reactions", [
            'reaction' => '👍',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'action' => 'removed',
            ]);

        $this->assertDatabaseMissing('message_reactions', [
            'message_id' => $message->id,
            'user_id' => $me->id,
            'reaction' => '👍',
        ]);
    }
}
