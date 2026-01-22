<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Kirim pesan WA melalui Microservice Node.js
     */
    public function sendMessage(string $number, string $message): bool
    {
        $url = env('WA_SERVICE_URL');
        $key = env('WA_SERVICE_KEY');

        // Validasi konfigurasi .env
        if (!$url || !$key) {
            Log::error("WhatsApp Service: Konfigurasi URL atau Key kosong di .env");
            return false;
        }

        try {
            $response = Http::withHeaders([
                'x-api-key' => $key,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->timeout(10)->post($url . '/send-message', [
                'number' => $this->formatNumber($number),
                'message' => $message,
            ]);

            if ($response->successful()) {
                Log::info("WA Terkirim: Ke $number");
                return true;
            }

            Log::error("WA Gagal: Respon server " . $response->status());
            return false;
        } catch (\Exception $e) {
            Log::error("WA Service Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Memastikan format nomor adalah 628xxx
     */
    private function formatNumber(string $number): string
    {
        $number = preg_replace('/[^0-9]/', '', $number);
        if (str_starts_with($number, '0')) {
            $number = '62' . substr($number, 1);
        }
        return $number;
    }
}
