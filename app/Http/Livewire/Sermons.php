<?php

namespace App\Http\Livewire;

use App\Models\Sermon;
use Livewire\Component;

class Sermons extends Component
{
    public $sermons = [];

    public function render()
    {
        $this->sermons = Sermon::orderBy('delivered_on', 'desc')->get();
        return view('livewire.sermons');
    }
}
