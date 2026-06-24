<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sender_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('sender_email');
            $table->string('sender_name');
            $table->text('body');
            $table->enum('type', ['internal', 'outbound_email', 'inbound_email'])->default('internal');
            $table->enum('status', ['sending', 'sent', 'delivered', 'failed'])->default('delivered');
            $table->string('message_id_header')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
