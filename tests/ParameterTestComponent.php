<?php

namespace AndiSiahaan\LivewireModal\Tests;

use AndiSiahaan\LivewireModal\ModalComponent;

class ParameterTestComponent extends ModalComponent
{
    public $message;

    public function mount($message = null)
    {
        $this->message = $message;
    }

    public function render()
    {
        return '<div>' . $this->message . '</div>';
    }
}
