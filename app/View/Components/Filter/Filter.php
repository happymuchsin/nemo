<?php

namespace App\View\Components\Filter;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Filter extends Component
{
    public $tipe;
    public $label;
    public $id;
    public $name;
    public $alloption;
    public $colom;
    /**
     * Create a new component instance.
     */
    public function __construct($tipe, $label, $id, $name = null, $alloption = true, $colom = 'col-sm-2')
    {
        $this->tipe = $tipe;
        $this->label = $label;
        $this->id = $id;
        $this->name = $name ? $name : $id;
        $this->alloption = $alloption;
        $this->colom = $colom;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.filter.filter');
    }
}
