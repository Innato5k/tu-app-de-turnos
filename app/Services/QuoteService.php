<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Quote;
use Illuminate\Support\Facades\Log;

class QuoteService
{
    /**
     * Intenta obtener una frase de la API externa.
     * Si falla, devuelve una frase aleatoria de nuestra DB.
     */
    public function getDailyQuote()
    {
        try {
            $responseQuotes = Http::timeout(3)->get('https://www.positive-api.online/phrases/esp');
            $responseAuthors = Http::timeout(3)->get('https://www.positive-api.online/authors');

            if ($responseQuotes->successful() && $responseAuthors->successful()) {
                $quotes = $responseQuotes->json();
                $authorsData = $responseAuthors->json();

                $authorsMap = collect($authorsData)->pluck('name', 'id');
                $randomQuote = $quotes[array_rand($quotes)];

                $authorName = 'Anónimo';
                if (!is_null($randomQuote['author_id'])) {
                    $authorName = $authorsMap->get($randomQuote['author_id'], 'Anónimo');
                }

                return Quote::firstOrCreate(
                    ['text' => $randomQuote['text']],
                    ['author' => $authorName]
                );
            }
        } catch (\Exception $e) {
            Log::warning("Error al sincronizar frases de Positive-API: " . $e->getMessage());
        }

        return Quote::inRandomOrder()->first();
    }
}