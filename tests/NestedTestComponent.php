<?php

namespace AndiSiahaan\LivewireModal\Tests;

use AndiSiahaan\LivewireModal\ModalComponent;

class NestedTestComponent extends ModalComponent
{
    public function render()
    {
        return '<div>Nested Component</div>';
    }
}
