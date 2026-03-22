<?php

namespace App\Http\Controllers;

use App\Services\GroqService;
use App\Services\StoreDataService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AiChatController extends Controller
{
    public function index(): View
    {
        return view('ai-chat.index');
    }

    public function ask(Request $request): \Illuminate\Http\JsonResponse
    {
        // SEC: Block demo users from AI chat to prevent business data leakage
        if (isDemoUser()) {
            return response()->json([
                'success' => true,
                'reply'   => '🔒 Fitur **Tanya Toko AI** tidak tersedia di akun demo. Fitur ini mengakses data bisnis real yang bersifat rahasia.',
            ]);
        }

        $request->validate([
            'message' => 'required|string|max:1000',
            'history' => 'nullable|array',
            'history.*.role' => 'required_with:history|string|in:user,model',
            'history.*.text' => 'required_with:history|string',
        ]);

        $userMessage = $request->input('message');
        $history     = $request->input('history', []);

        $storeData = new StoreDataService();
        $groq      = new GroqService();

        $context  = $storeData->getContext();
        $response = $groq->chat($userMessage, $context, $history);

        return response()->json([
            'success' => true,
            'reply'   => $response,
        ]);
    }
}
