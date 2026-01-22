<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;

class WhatsappSettings extends Component
{
    public $status = 'offline';
    public $user = null;
    public $qrCode = null;

    public function mount()
    {
        $this->checkStatus();
    }

    public function checkStatus()
    {
        try {
            $response = Http::withHeaders([
                'x-api-key' => env('WA_SERVICE_KEY')
            ])->get(env('WA_SERVICE_URL') . '/status');

            if ($response->successful()) {
                $data = $response->json();
                $this->status = $data['status'];
                $this->qrCode = $data['qr']; // String QR mentah dari Baileys
                $this->user = $data['user'];
            }
        } catch (\Exception $e) {
            $this->status = 'offline';
        }
    }

    public function logout()
    {
        try {
            Http::withHeaders(['x-api-key' => env('WA_SERVICE_KEY')])
                ->post(env('WA_SERVICE_URL') . '/logout');

            $this->checkStatus();
            session()->flash('success', 'Berhasil logout dari WhatsApp.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghubungi server.');
        }
    }

    public function render()
    {
        return view('livewire.whatsapp-settings')->layout('layouts.klinik');
    }
}
