<?php

namespace App\View\Components;

use Illuminate\View\Component;

class GuestLeft extends Component
{
    /**
     * isGuest.
     *
     * @var string
     */
    public $isGuest;



    /**
     * Create the component instance.
     *
     * @param  string  $isGuest
     * @return void
     */
    public function __construct($isGuest)
    {
        $this->isGuest = $isGuest;

    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|\Closure|string
     */

    public function render()
    {
        return view('components.guest-left');
    }
}
