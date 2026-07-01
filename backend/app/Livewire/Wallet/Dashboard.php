<?php

namespace App\Livewire\Wallet;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.wallet.dashboard', [
            'wallet' => Auth::user()->wallet,
        ]);
    }
}
