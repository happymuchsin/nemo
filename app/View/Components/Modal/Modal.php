<?php

namespace App\View\Components\Modal;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Modal extends Component
{
    public $name;
    public $ukuran;
    /**
     * Create a new component instance.
     */
    public function __construct($name, $ukuran = '')
    {
        $this->name = $name;
        $this->ukuran = $ukuran;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.modal.modal');
    }
}
