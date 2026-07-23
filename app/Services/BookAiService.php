<?php

namespace App\Services;

use App\Models\Book;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class BookAiService
{
    public function __construct(protected AnthropicClient $anthropic)
    {
    }

    /**
     * Pide a Anthropic (o la simulación) que sugiera metadatos para un libro.
     * Devuelve un array con keys: genre, synopsis, tags, published_at (opcional)
     */
    public function inferMetadata(Book $book): array
    {
        $prompt = $this->buildPrompt($book);

        $raw = $this->anthropic->complete($prompt, 400);

        // Si la respuesta parece JSON, decodificarla
        if (Str::startsWith(trim($raw), '{') || Str::startsWith(trim($raw), '[')) {
            $decoded = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
        }

        // Si no es JSON, intentar parsear texto libre en formato simple
        // Buscamos líneas clave: Genre:, Synopsis:, Tags:
        $lines = preg_split('/\r?\n/', trim(strip_tags($raw)));
        $out = [];
        foreach ($lines as $line) {
            if (stripos($line, 'genre:') === 0) {
                $out['genre'] = trim(substr($line, strlen('genre:')));
            }

            if (stripos($line, 'synopsis:') === 0) {
                $out['synopsis'] = trim(substr($line, strlen('synopsis:')));
            }

            if (stripos($line, 'tags:') === 0) {
                $tags = trim(substr($line, strlen('tags:')));
                $out['tags'] = array_map('trim', explode(',', $tags));
            }
        }

        // Si sigue vacío, devolver fallback básico
        if (empty($out)) {
            Log::warning('BookAiService: respuesta no parseable de Anthropic', ['raw' => $raw]);
            return [
                'message' => 'No se pudo generar metadatos a partir de la respuesta de IA.',
                'raw' => $raw,
            ];
        }

        return $out;
    }

    protected function buildPrompt(Book $book): string
    {
        // Pedimos a la IA que devuelva JSON con campos concretos para facilitar parseo.
        return "Eres un asistente que sugiere metadatos bibliográficos en formato JSON.\n" .
               "Recibe la información parcial de un libro y completa: genre, synopsis, tags (array), published_at (año opcional).\n" .
               "Devuelve un único objeto JSON válido.\n\n" .
               "Input:\n" .
               json_encode([
                   'title' => $book->title,
                   'author' => $book->author,
                   'isbn' => $book->isbn,
               ]) . "\n\nJSON:\n";
    }
}
