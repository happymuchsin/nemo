<?php

namespace App\View\Components\Modal;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Body extends Component
{
    public $tipe;
    public $label;
    public $id;
    public $name;
    public $upper;
    public $min;
    public $max;
    public $accept;
    public $readonly;
    public $disable;
    public $multiple;
    public $defaultOption;
    public $row;
    /**
     * Create a new component instance.
     */
    public function __construct($tipe, $label, $id, $name = null, $upper = true, $min = 1, $max = 999999999999, $accept = "*", $readonly = '', $disable = '', $multiple = '', $defaultOption = true, $row = 2)
    {
        $this->tipe = $tipe;
        $this->label = $label;
        $this->id = $id;
        $this->name = $name ? $name : $id;
        $this->upper = $upper;
        $this->min = $min;
        $this->max = $max;
        $this->accept = $accept;
        $this->readonly = $readonly;
        $this->disable = $disable;
        $this->multiple = $multiple;
        $this->defaultOption = $defaultOption;
        $this->row = $row;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.modal.body');
    }
}
