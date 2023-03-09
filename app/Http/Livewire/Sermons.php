<?php

namespace App\Http\Livewire;

use App\Models\Sermon;
use Livewire\Component;

class Sermons extends Component
{
    public $sermons = [];
    public $count = null;
    public $search_term = "";
    public $label = "All Sermons";
    public $loading = false;

    public function render()
    {
        return view('livewire.sermons');
    }

    public function mount()
    {
        $this->search("");
    }

    public function search()
    {
        $this->loading = true;
        if (empty($this->search_term)) {
            $this->sermons = Sermon::orderBy('delivered_on', 'desc')->with(['readings', 'feast', 'location'])->get();
            $this->label = "All Sermons";
        } else {
            $ids = Sermon::search($this->search_term)->take(50)->keys()->toArray();
            $this->sermons = Sermon::with(['readings', 'feast', 'location'])->whereIn('id', $ids)->get();
            $this->label = "Results for \"$this->search_term\"";
        }
        $this->count = count($this->sermons);
        $this->loading = false;
    }

    public function goToSermon($sermon = null)
    {
        return redirect()->route('sermon', $sermon);
    }
}
