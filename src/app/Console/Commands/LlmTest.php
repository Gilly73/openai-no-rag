<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class LlmTest extends Command
{
    // Run: php artisan llm:test "Say hello to Seeta politely"
    protected $signature = 'llm:test {prompt? : What you want the model to do}';
    protected $description = 'Send a single test prompt to OpenAI and print the reply';

    public function handle(): int
    {
        $prompt  = $this->argument('prompt') ?? 'Say hello to Seeta politely. Keep it under 20 words.';
        $apiKey  = env('OPENAI_API_KEY');
        $model   = env('OPENAI_MODEL', 'gpt-4o-mini');
        $timeout = (int) env('OPENAI_TIMEOUT', 20);

        if (!$apiKey) {
            $this->error('OPENAI_API_KEY missing in .env');
            return self::FAILURE;
        }

        $payload = [
            'model' => $model,
            'messages' => [
                ['role' => 'system', 'content' => 'You are a helpful, concise assistant.'],
                ['role' => 'user',   'content' => $prompt],
            ],
            'temperature' => 0.2,
            'max_tokens'  => 200,
        ];

        try {
            $res = Http::timeout($timeout)
                ->withToken($apiKey)
                ->acceptJson()
                ->asJson()
                ->post('https://api.openai.com/v1/chat/completions', $payload);

            if ($res->failed()) {
                $this->error('OpenAI API error: '.$res->status().' '.$res->body());
                return self::FAILURE;
            }

            $data = $res->json();
            $text = $data['choices'][0]['message']['content'] ?? null;

            if (!$text) {
                $this->warn('No content returned. Full payload:');
                $this->line(json_encode($data, JSON_PRETTY_PRINT));
                return self::FAILURE;
            }

            $this->info('✅ OpenAI replied:');
            $this->line($text);
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Request failed: '.$e->getMessage());
            return self::FAILURE;
        }
    }
}
