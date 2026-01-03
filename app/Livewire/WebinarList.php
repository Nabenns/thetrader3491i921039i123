<?php

namespace App\Livewire;

use Livewire\Component;

class WebinarList extends Component
{
    public function render()
    {
        return view('livewire.webinar-list', [
            'webinars' => \App\Models\Webinar::latest()->get(),
        ]);
    }
}
