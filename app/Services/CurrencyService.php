<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CurrencyService
{
    protected $apiKey = 'fca_live_1s6bktzu6WoZvQFu6sRVRFimM1Q1IGuGrBaa1036';
    protected $baseUrl = 'https://api.freecurrencyapi.com/v1/latest';

    public function getRate($from = 'USD', $to = 'IDR')
    {
        $cacheKey = "currency_rate_{$from}_{$to}";

        return Cache::remember($cacheKey, 3600, function () use ($from, $to) {
            try {
                $response = Http::withoutVerifying()->get($this->baseUrl, [
                    'apikey' => $this->apiKey,
                    'base_currency' => $from,
                    'currencies' => $to,
                ]);

                if ($response->successful()) {
                    return $response->json()['data'][$to] ?? 16500;
                }
            } catch (\Exception $e) {
                // Log error if needed
            }

            // Fallback rate if API fails
            return 16500;
        });
    }

    public function convert($amount, $from = 'USD', $to = 'IDR')
    {
        $rate = $this->getRate($from, $to);
        return $amount * $rate;
    }
}
