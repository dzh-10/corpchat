<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\MessageController;
use App\Http\Controllers\AuthController;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/', function () {
    return view('chat');
})->middleware(['auth'])->name('chat.index');

Route::middleware(['auth'])->group(function () {
    // Get list of conversations
    Route::get('/api/conversations', function () {
        return response()->json(
            Conversation::with(['messages' => function ($q) {
                $q->latest()->limit(1);
            }])->orderBy('updated_at', 'desc')->get()
        );
    });

    // Create a new conversation
    Route::post('/api/conversations', function (\Illuminate\Http\Request $request) {
        $validated = $request->validate([
            'type' => 'required|in:internal_chat,external_email',
            'subject' => 'nullable|string|max:255',
            'external_contact_email' => 'required_if:type,external_email|nullable|email',
            'external_contact_name' => 'required_if:type,external_email|nullable|string|max:255',
        ]);

        $conversation = Conversation::create($validated);

        return response()->json($conversation, 201);
    });

    // Get messages for a specific conversation
    Route::get('/api/conversations/{conversation}/messages', function (Conversation $conversation) {
        return response()->json($conversation->messages()->orderBy('created_at', 'asc')->get());
    });

    // Send a message
    Route::post('/api/conversations/{conversation}/messages', [MessageController::class, 'store']);

    // Search employees for internal chat composer autocomplete
    Route::get('/api/employees/search', function (\Illuminate\Http\Request $request) {
        $q = $request->get('q', '');
        return \App\Models\User::where('id', '!=', auth()->id())
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%");
            })
            ->limit(8)
            ->select('id', 'name', 'email')
            ->get();
    });

    // Create a new conversation with a message from compose panel
    Route::post('/conversations', function (\Illuminate\Http\Request $request) {
        $validated = $request->validate([
            'type' => 'required|in:email,internal',
            'subject' => 'nullable|string|max:255',
            'client_email' => 'required_if:type,email|nullable|email',
            'client_name' => 'required_if:type,email|nullable|string|max:255',
            'recipient_id' => 'required_if:type,internal|nullable|exists:users,id',
            'body' => 'required|string',
        ]);

        $currentUser = auth()->user();

        return \Illuminate\Support\Facades\DB::transaction(function () use ($validated, $currentUser) {
            $type = $validated['type'] === 'email' ? 'external_email' : 'internal_chat';

            if ($type === 'external_email') {
                $conversation = Conversation::where('type', 'external_email')
                    ->where('external_contact_email', $validated['client_email'])
                    ->first();

                if (!$conversation) {
                    $conversation = Conversation::create([
                        'type' => 'external_email',
                        'external_contact_email' => $validated['client_email'],
                        'external_contact_name' => $validated['client_name'] ?? $validated['client_email'],
                        'subject' => $validated['subject'] ?: 'No Subject',
                    ]);
                }
            } else {
                $recipient = \App\Models\User::findOrFail($validated['recipient_id']);
                $subject = $validated['subject'] ?: 'Chat with ' . $recipient->name;

                $conversation = Conversation::create([
                    'type' => 'internal_chat',
                    'subject' => $subject,
                ]);
            }

            $message = new \App\Models\Message([
                'conversation_id' => $conversation->id,
                'sender_id' => $currentUser->id,
                'sender_email' => $currentUser->email,
                'sender_name' => $currentUser->name,
                'body' => $validated['body'],
                'type' => $type === 'external_email' ? 'outbound_email' : 'internal',
                'status' => $type === 'external_email' ? 'sending' : 'delivered',
            ]);

            $message->save();

            // Touch the conversation
            $conversation->touch();

            broadcast(new \App\Events\MessageSent($message))->toOthers();

            if ($type === 'external_email') {
                \App\Jobs\SendOutboundEmailJob::dispatch($message);
            }

            return response()->json([
                'success' => true,
                'conversation_id' => $conversation->id,
            ], 201);
        });
    });
});

