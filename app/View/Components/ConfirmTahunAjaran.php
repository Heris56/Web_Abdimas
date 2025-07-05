<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ConfirmTahunAjaran extends Component
{
    public $id;
    public $title;
    public $size; // sm-lg-xl
    /**
     * Create a new component instance.
     */
    public function __construct($id, $title, $size = '')
    {
        $this->id = $id;
        $this->title = $title;
        $this->size = $size;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.confirm-tahun-ajaran');
    }
}
