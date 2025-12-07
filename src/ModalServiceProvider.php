<?php

namespace AndiSiahaan\LivewireModal;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class ModalServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/livewire-modal.php',
            'livewire-modal'
        );
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'livewire-modal');

        Livewire::component('livewire-modal', Modal::class);

        if ($this->app->runningInConsole()) {
            // Publish config
            $this->publishes([
                __DIR__ . '/../config/livewire-modal.php' => config_path('livewire-modal.php'),
            ], 'livewire-modal-config');

            // Publish views
            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/livewire-modal'),
            ], 'livewire-modal-views');
        }
    }
}
