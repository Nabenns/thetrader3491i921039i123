<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;

class LandingPage extends Component
{
    #[Layout('layouts.marketing')]
    public function render()
    {
        $packages = \App\Models\Package::where('is_active', true)->get();
        return view('livewire.landing-page', compact('packages'));
    }
}
