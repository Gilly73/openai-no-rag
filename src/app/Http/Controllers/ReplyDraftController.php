<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ReplyDraftController extends Controller
{
    public function show()
    {
        return view('reply-drafter', [
            'reply' => null,
        ]);
    }

    public function draft(Request $request)
    {
        $validated = $request->validate([
            'customer_email' => ['required', 'string', 'min:10'],
            'tone'           => ['required', 'in:friendly,formal,neutral'],
            'max_words'      => ['nullable', 'integer', 'min:10', 'max:300'],
        ]);

        $cfg     = config('services.openai');
        $apiKey  = $cfg['key'];
        $model   = $cfg['model'];
        $timeout = (int) $cfg['timeout'];
        $maxWords = $validated['max_words'] ?? 120;

        if (!$apiKey) {
            return back()
                ->withErrors(['api' => 'OPENAI_API_KEY missing in .env'])
                ->withInput();
        }

        // Build a safe, predictable prompt
        $system = 'You are a concise, polite customer support assistant. '
            . 'Write clear, professional emails. If company-specific policy is required, do not invent facts—ask for clarification.';

        $user = "Write a {$validated['tone']} reply email. Keep it under {$maxWords} words. "
            . "Acknowledge the customer, be helpful, avoid making policy promises, and propose the next step. "
            . "Sign off with: Best regards,\nSupport Team\n\n"
            . "Customer message:\n\"\"\"\n{$validated['customer_email']}\n\"\"\"";

        $payload = [
            'model' => $model,
            'messages' => [
                ['role' => 'system', 'content' => $system],
                ['role' => 'user',   'content' => $user],
            ],
            'temperature' => 0.2,
            'max_tokens'  => 300, // server-side cap on output length
        ];

        try {
            $res = Http::timeout($timeout)
                ->withToken($apiKey)
                ->acceptJson()
                ->asJson()
                ->post('https://api.openai.com/v1/chat/completions', $payload);

            if ($res->failed()) {
                $status = $res->status();
                $body   = $res->json() ?: $res->body();
                return back()
                    ->withErrors(['api' => "OpenAI API error ({$status}): " . (is_string($body) ? $body : json_encode($body))])
                    ->withInput();
            }

            $data  = $res->json();
            $reply = $data['choices'][0]['message']['content'] ?? null;

            return view('reply-drafter', [
                'reply' => $reply,
                'input' => $validated,
            ]);
        } catch (\Throwable $e) {
            return back()
                ->withErrors(['api' => 'Request failed: ' . $e->getMessage()])
                ->withInput();
        }
    }
}
