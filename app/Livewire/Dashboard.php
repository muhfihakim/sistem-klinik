<?php

namespace App\Livewire;

use Livewire\Component;

class Dashboard extends Component
{
    #[Layout('layouts.klinik')]
    public function render()
    {
        return view('livewire.dashboard');
    }
}
