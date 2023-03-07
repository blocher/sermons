<?php

namespace App\Http\Livewire;

use App\Models\Sermon;
use Livewire\Component;

class ShowSermon extends Component
{

    public Sermon $sermon;

    public function render()
    {
        $this->appendOtherSermonsToReadings();
        return view('livewire.show-sermon');
    }

    public function appendOtherSermonsToReadings()
    {
        $this->sermon->readings->each(function ($reading) {
            $reading->other_sermons = $reading->sermons->filter(function ($sermon) {
                return $sermon->id !== $this->sermon->id;
            });
        });
    }
}
