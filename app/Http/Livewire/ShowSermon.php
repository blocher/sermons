<?php

namespace App\Http\Livewire;

use App\Models\Sermon;
use Livewire\Component;

class ShowSermon extends Component
{

    public Sermon $sermon;

    public function render()
    {
        return view('livewire.show-sermon');
    }
}
