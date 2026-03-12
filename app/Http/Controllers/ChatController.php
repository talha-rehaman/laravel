<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use OpenAI\Laravel\Facades\OpenAI;

class ChatController extends Controller
{
    public function chatpage()
    {
        return view('chat');
    }
    public function chat(Request $request)
    {
        $response = OpenAI::chat()->create([
            'model' => 'gpt-4.1-mini',
            'messages' => [
                ['role' => 'user', 'content' => $request->message],
            ],
        ]);

        return response()->json([
            'reply' => $response->choices[0]->message->content
        ]);
    }
}
