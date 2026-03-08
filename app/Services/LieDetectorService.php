<?php

namespace App\Services;
use Illuminate\Support\Facades\Http;

class LieDetectorService
{
    public function analyze(array $statements): array
    {
        $apiKey = config('services.anthropic.key');
        if (!$apiKey) {
            throw new \Exception('Anthropic API key not configured');
        }

        $numbered = collect($statements)
            ->map(fn($s, $i) => ($i+1).'.'.$s['content'])
            ->implode("\n");

        $response = Http::withHeaders([
            'x-api-key'         => $apiKey,
            'anthropic-version' => '2023-06-01',
        ])->post('https://api.anthropic.com/v1/messages', [
            'model'      => 'claude-3-5-haiku-20241022',
            'max_tokens' => 400,
            'system'     => $this->systemPrompt(),
            'messages'   => [['role'=>'user', 'content'=>$numbered]],
        ]);

        if (!$response->successful()) {
            throw new \Exception('Anthropic API request failed: ' . $response->status() . ' - ' . $response->body());
        }

        $text = $response->json('content.0.text');
        if (!$text) {
            throw new \Exception('No response text from Anthropic API');
        }

        $data = json_decode($text, true);
        if ($data === null) {
            throw new \Exception('Invalid JSON response from Anthropic API: ' . $text);
        }

        // Validate structure
        if (!isset($data['lie_index'], $data['scores'], $data['reasoning']) || !is_array($data['scores']) || count($data['scores']) !== 3) {
            throw new \Exception('Invalid response structure from Anthropic API');
        }

        return $data;
    }

    private function systemPrompt(): string
    {
    return <<<PROMPT
You are a professional deception analyst.

Three statements are given.
Exactly one is a lie.

Analyze them carefully using:
- realism
- level of detail
- plausibility
- common human behavior

Return JSON:

{
    "lie_index": number,
    "scores": [number, number, number],
    "reasoning": "short explanation"
}
PROMPT;
}
}
