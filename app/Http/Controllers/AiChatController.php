<?php

namespace App\Http\Controllers;

use App\Services\GeminiService;
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
        $request->validate([
            'message' => 'required|string|max:1000',
            'history' => 'nullable|array',
            'history.*.role' => 'required_with:history|string|in:user,model',
            'history.*.text' => 'required_with:history|string',
        ]);

        $userMessage = $request->input('message');
        $history     = $request->input('history', []);

        $storeData = new StoreDataService();
        $gemini    = new GeminiService();

        $context  = $storeData->getContext();
        $response = $gemini->chat($userMessage, $context, $history);

        return response()->json([
            'success' => true,
            'reply'   => $response,
        ]);
    }
}
