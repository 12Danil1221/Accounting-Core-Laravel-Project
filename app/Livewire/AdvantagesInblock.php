<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Advantages;

class AdvantagesInblock extends Component
{
    public function render()
    {
        $advantages = Advantages::all();
        return view('livewire.advantages-inblock', compact('advantages'));
    }
}