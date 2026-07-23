<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AnthropicClient
{
    public function complete(string $prompt, int $maxTokens = 500): string
    {
        $apiKey = config('services.anthropic.key');

        if (! $apiKey || $apiKey === 'simulado') {
            Log::warning('AnthropicClient: No hay API key configurada, usando simulación');

            return $this->simulateResponse($prompt);
        }

        try {
            $response = Http::withHeaders([
                'x-api-key' => $apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])->post('https://api.anthropic.com/v1/messages', [
                'model' => 'claude-3-haiku-20240307', // Modelo más barato y rápido
                'max_tokens' => $maxTokens,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            // Debug: Ver qué devuelve la API
            Log::info('Anthropic Response', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            if ($response->successful()) {
                $json = $response->json();

                // Anthropic can return different shapes depending on endpoint/version.
                // Try common locations for text output.
                if (isset($json['completion']) && is_string($json['completion'])) {
                    return $json['completion'];
                }

                if (isset($json['outputs']) && is_array($json['outputs'])) {
                    foreach ($json['outputs'] as $out) {
                        if (isset($out['content'])) {
                            // content can be array of parts or a string
                            if (is_string($out['content'])) {
                                return $out['content'];
                            }

                            if (is_array($out['content'])) {
                                foreach ($out['content'] as $part) {
                                    if (isset($part['text'])) {
                                        return $part['text'];
                                    }
                                }
                            }
                        }
                    }
                }

                // Fallback: try to find any text in body
                $flat = json_encode($json);

                return $flat ?? '';
            }

            Log::error('AnthropicClient: Error en API', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            // Try to decode error message and request id if present
            $body = $response->json();
            $message = $body['error']['message'] ?? $response->body();
            $requestId = $body['request_id'] ?? ($body['requestId'] ?? null);

            return json_encode([
                'error' => 'Error en la API de Anthropic',
                'status' => $response->status(),
                'message' => $message,
                'request_id' => $requestId,
            ]);

        } catch (\Exception $e) {
            Log::error('AnthropicClient: Excepción', ['message' => $e->getMessage()]);

            return json_encode(['error' => 'Error de conexión: '.$e->getMessage()]);
        }
    }

    public function isSimulated(): bool
    {
        $apiKey = config('services.anthropic.key');

        return ! $apiKey || $apiKey === 'simulado';
    }

    private function simulateResponse(string $prompt): string
    {
        if (str_contains($prompt, 'catalogación bibliotecaria')) {
            return json_encode([
                'genre' => fake()->randomElement(['Ciencia ficción', 'Fantasía', 'Realismo mágico']),
                'published_at' => fake()->numberBetween(1950, 2023),
                'synopsis' => fake()->paragraph(2),
                'tags' => fake()->randomElements(['clásico', 'best-seller', 'novedad'], 3),
            ]);
        }

        return json_encode([
            'message' => 'Hola, soy una IA simulada. Para usar la API real, configura ANTHROPIC_API_KEY.',
            'status' => 'simulated',
        ]);
    }
}
