<?php

namespace AndiSiahaan\LivewireModal\Tests;

use AndiSiahaan\LivewireModal\ModalComponent;

class CustomPropertyTestComponent extends ModalComponent
{
    public static function modalMaxWidth(): string
    {
        return '7xl';
    }

    public static function closeModalOnClickAway(): bool
    {
        return false;
    }

    public function render()
    {
        return '<div>Custom Property Component</div>';
    }
}
