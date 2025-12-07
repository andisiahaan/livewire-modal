<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Include JS
    |--------------------------------------------------------------------------
    |
    | Livewire Modal will inject the required Javascript in your blade template.
    | If you want to bundle the required Javascript you can set this to false
    | and add the modal.js to your bundler.
    |
    */
    'include_js' => true,

    /*
    |--------------------------------------------------------------------------
    | Include CSS
    |--------------------------------------------------------------------------
    |
    | The modal uses TailwindCSS, if you don't use TailwindCSS you will need
    | to set this parameter to true. This includes minimal CSS for the modal.
    |
    */
    'include_css' => false,

    /*
    |--------------------------------------------------------------------------
    | Modal Component Defaults
    |--------------------------------------------------------------------------
    |
    | Configure the default properties for a modal component.
    |
    | Supported modal_max_width:
    | 'sm', 'md', 'lg', 'xl', '2xl', '3xl', '4xl', '5xl', '6xl', '7xl'
    |
    */
    'component_defaults' => [
        'modal_max_width' => '2xl',
        'close_modal_on_click_away' => true,
        'close_modal_on_escape' => true,
        'close_modal_on_escape_is_forceful' => true,
        'dispatch_close_event' => false,
        'destroy_on_close' => false,
    ],
];
