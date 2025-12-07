<?php

namespace AndiSiahaan\LivewireModal\Tests;

use AndiSiahaan\LivewireModal\Modal;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;

class ModalTest extends TestCase
{
    #[Test]
    public function it_can_render_the_modal_component()
    {
        Livewire::test(Modal::class)
            ->assertStatus(200);
    }

    #[Test]
    public function it_can_open_a_modal()
    {
        Livewire::component('test-component', TestComponent::class);

        Livewire::test(Modal::class)
            ->call('openModal', 'test-component')
            ->assertSet('activeComponent', function ($value) {
                return !is_null($value);
            })
            ->assertSet('components', function ($value) {
                return count($value) === 1;
            });
    }

    #[Test]
    public function it_can_close_a_modal()
    {
        Livewire::component('test-component', TestComponent::class);

        $modal = Livewire::test(Modal::class);

        $modal->call('openModal', 'test-component')
            ->assertSet('activeComponent', function ($value) {
                return !is_null($value);
            });

        $components = $modal->get('components');
        $id = array_key_first($components);

        $modal->call('destroyComponent', $id)
            ->assertSet('components', function ($value) {
                return count($value) === 0;
            });
    }

    #[Test]
    public function it_can_open_nested_modals()
    {
        Livewire::component('test-component', TestComponent::class);
        Livewire::component('nested-test-component', NestedTestComponent::class);

        $modal = Livewire::test(Modal::class);

        $modal->call('openModal', 'test-component')
            ->assertSet('activeComponent', function ($value) {
                return !is_null($value);
            });

        $modal->call('openModal', 'nested-test-component')
            ->assertSet('components', function ($value) {
                return count($value) === 2;
            })
            ->assertSet('activeComponent', function ($value) use ($modal) {
                return $modal->get('components')[$value]['name'] === 'nested-test-component';
            });
    }

    #[Test]
    public function it_can_pass_parameters_to_modal()
    {
        Livewire::component('parameter-test-component', ParameterTestComponent::class);

        Livewire::test(Modal::class)
            ->call('openModal', 'parameter-test-component', ['message' => 'Hello World'])
            ->assertSet('components', function ($value) {
                $component = array_values($value)[0];
                return $component['arguments']['message'] === 'Hello World';
            });
    }

    #[Test]
    public function it_can_dispatch_events_on_close()
    {
        Livewire::component('test-component', TestComponent::class);

        $modal = Livewire::test(Modal::class);

        $modal->call('openModal', 'test-component');

        $events = [['name' => 'test-event', 'params' => ['foo' => 'bar']]];

        $modal->call('closeModal', false, 0, false, $events)
            ->assertDispatched('test-event');
    }

    #[Test]
    public function it_can_configure_modal_properties()
    {
        Livewire::component('custom-property-test-component', CustomPropertyTestComponent::class);

        Livewire::test(Modal::class)
            ->call('openModal', 'custom-property-test-component')
            ->assertSet('components', function ($value) {
                $component = array_values($value)[0];
                return $component['modalAttributes']['maxWidth'] === '7xl'
                    && $component['modalAttributes']['closeOnClickAway'] === false;
            });
    }
}
