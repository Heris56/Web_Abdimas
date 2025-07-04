<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class InputNilaiModal extends Component
{
    public $dataNilai;
    public $mapelList;
    public $tahunPelajaranList;
    public $kegiatanList;

    public function __construct($dataNilai, $mapelList, $tahunPelajaranList, $kegiatanList)
    {
        $this->dataNilai = $dataNilai;
        $this->mapelList = $mapelList;
        $this->tahunPelajaranList = $tahunPelajaranList;
        $this->kegiatanList = $kegiatanList;
    }

    public function render()
    {
        return view('components.input-nilai-modal');
    }
}
