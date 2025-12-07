<?php

namespace AndiSiahaan\LivewireModal;

class DemoModal extends ModalComponent
{
    public function render()
    {
        return view('livewire-modal::demo-modal');
    }

    public static function modalMaxWidth(): string
    {
        return '2xl';
    }
}
