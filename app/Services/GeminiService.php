<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('services.groq.api_key', '');
        $this->model  = config('services.groq.model', 'llama-3.3-70b-versatile');
    }

    /**
     * Send a message to the AI with store context and return the response.
     */
    public function chat(string $userMessage, array $storeContext, array $conversationHistory = []): string
    {
        if (empty($this->apiKey)) {
            return '⚠️ API Key Groq belum dikonfigurasi. Silakan tambahkan `GROQ_API_KEY` di file `.env`.';
        }

        $systemPrompt = $this->buildSystemPrompt($storeContext);

        // Build messages array
        $messages = [];
        $messages[] = [
            'role' => 'system',
            'content' => $systemPrompt
        ];

        // Add conversation history (limit to last 10 messages)
        $history = array_slice($conversationHistory, -10);
        foreach ($history as $msg) {
            // Groq (OpenAI format) uses 'assistant' instead of 'model'
            $role = ($msg['role'] === 'model') ? 'assistant' : 'user';
            $messages[] = [
                'role' => $role,
                'content' => $msg['text']
            ];
        }

        // Add current user message
        $messages[] = [
            'role' => 'user',
            'content' => $userMessage
        ];

        $payload = [
            'model' => $this->model,
            'messages' => $messages,
            'temperature' => 0.7,
            'max_tokens' => 1024,
        ];

        return $this->sendRequest($payload);
    }

    /**
     * Send request to Groq API.
     */
    protected function sendRequest(array $payload): string
    {
        $url = "https://api.groq.com/openai/v1/chat/completions";

        try {
            $response = Http::timeout(20)
                ->withToken($this->apiKey)
                ->post($url, $payload);

            if ($response->failed()) {
                Log::error('Groq API Error', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);

                if ($response->status() === 429) {
                    return '⏳ Limit Kouta API Groq habis sementara. Silakan coba lagi beberapa saat.';
                }

                if ($response->status() === 401) {
                    return '❌ API Key Groq tidak valid. Silakan periksa file `.env`.';
                }

                return '❌ Gagal menghubungi AI. (HTTP ' . $response->status() . ')';
            }

            $data = $response->json();

            return $data['choices'][0]['message']['content']
                ?? '🤔 AI tidak memberikan respons. Coba ulangi pertanyaan Anda.';

        } catch (\Exception $e) {
            Log::error('Groq Service Exception', [
                'message' => $e->getMessage()
            ]);

            return '❌ Terjadi kesalahan saat menghubungi AI: Koneksi Timeout / Gagal.';
        }
    }

    /**
     * Build a system prompt with store context data.
     */
    protected function buildSystemPrompt(array $storeContext): string
    {
        $ctx = json_encode($storeContext, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return <<<PROMPT
Kamu adalah **Asisten Toko Pintar** untuk aplikasi POS bernama "SMEGABIZ".
Kamu adalah AI yang cerdas, luwes, dan bisa diajak ngobrol tentang apa saja.

## Aturan:
1. Jika user bertanya tentang data penjualan, stok, atau performa toko, gunakan data JSON di bawah ini sebagai sumber kebenaran (Source of Truth).
2. Jika user bertanya hal-hal umum di luar toko (misal: resep masakan, coding, cuaca, ngobrol santai), JAWAB SAJA dengan normal layaknya AI pintar pada umumnya. Jangan kaku.
3. Gunakan bahasa Indonesia yang santai, sopan, dan mudah dipahami.
4. Format jawaban menggunakan Markdown (bold, list, tabel) agar mudah dibaca jika perlu.
5. Berikan saran bisnis yang berguna jika relevan dengan pertanyaan toko.
6. Untuk angka uang (pada konteks toko), format dalam Rupiah (Rp).

## Data Toko Saat Ini:
```json
{$ctx}
```
PROMPT;
    }
}

