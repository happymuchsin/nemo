<?php

namespace App\View\Components\Layout;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Button extends Component
{
    public $class;
    public $id;
    public $onclick;
    public $icon;
    public $name;
    /**
     * Create a new component instance.
     */
    public function __construct($class, $id, $onclick, $icon, $name)
    {
        $this->class = $class;
        $this->id = $id;
        $this->onclick = $onclick;
        $this->icon = $icon;
        $this->name = $name;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.layout.button');
    }
}
