<?php

namespace App\View\Components\Layout;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SmallBox extends Component
{
    public $innerStyle;
    public $footerStyle;
    public $id;
    public $inner;
    public $icon;
    public $footer;
    /**
     * Create a new component instance.
     */
    public function __construct($innerStyle, $footerStyle, $id, $inner, $icon, $footer)
    {
        $this->innerStyle = $innerStyle;
        $this->footerStyle = $footerStyle;
        $this->id = $id;
        $this->inner = $inner;
        $this->icon = $icon;
        $this->footer = $footer;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.layout.small-box');
    }
}
