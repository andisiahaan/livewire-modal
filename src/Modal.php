<?php

namespace AndiSiahaan\LivewireModal;

use Exception;
use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Reflector;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use ReflectionClass;
use ReflectionProperty;

class Modal extends Component
{
    public ?string $activeComponent = null;

    public array $components = [];

    public function resetState(): void
    {
        $this->components = [];
        $this->activeComponent = null;
    }

    public function openModal($component, $arguments = [], $modalAttributes = []): void
    {
        $requiredInterface = \AndiSiahaan\LivewireModal\Contracts\ModalComponent::class;

        try {
            $instance = \Livewire\Livewire::new($component);
        } catch (Exception $e) {
            throw new Exception("Component [{$component}] not found. Error: " . $e->getMessage());
        }

        $reflect = new ReflectionClass($instance);

        if ($reflect->implementsInterface($requiredInterface) === false) {
            throw new Exception("[" . get_class($instance) . "] does not implement [{$requiredInterface}] interface.");
        }

        $componentClass = get_class($instance);

        $id = md5($component . serialize($arguments));

        $arguments = collect($arguments)
            ->merge($this->resolveComponentProps($arguments, $instance))
            ->all();

        $this->components[$id] = [
            'name' => $component,
            'arguments' => $arguments,
            'modalAttributes' => array_merge([
                'closeOnClickAway' => $componentClass::closeModalOnClickAway(),
                'closeOnEscape' => $componentClass::closeModalOnEscape(),
                'closeOnEscapeIsForceful' => $componentClass::closeModalOnEscapeIsForceful(),
                'dispatchCloseEvent' => $componentClass::dispatchCloseEvent(),
                'destroyOnClose' => $componentClass::destroyOnClose(),
                'maxWidth' => $componentClass::modalMaxWidth(),
                'maxWidthClass' => $componentClass::modalMaxWidthClass(),
            ], $modalAttributes),
        ];

        $this->activeComponent = $id;

        $this->dispatch('activeModalComponentChanged', id: $id);
    }

    #[On('closeModal')]
    public function closeModal($force = false, $skipPreviousModals = 0, $destroySkipped = false, array $events = []): void
    {
        if (!empty($events)) {
            foreach ($events as $event) {
                if (isset($event['name'])) {
                    $this->dispatch($event['name'], ...($event['params'] ?? []));
                }
            }
        }

        if ($this->activeComponent) {
            $this->destroyComponent($this->activeComponent);

            $keys = array_keys($this->components);

            if (count($keys) > 0) {
                $this->activeComponent = end($keys);
            } else {
                $this->activeComponent = null;
            }

            $this->dispatch('activeModalComponentChanged', id: $this->activeComponent);
        }
    }

    public function resolveComponentProps(array $attributes, Component $component): Collection
    {
        return $this->getPublicPropertyTypes($component)
            ->intersectByKeys($attributes)
            ->map(function ($className, $propName) use ($attributes) {
                return $this->resolveParameter($attributes, $propName, $className);
            });
    }

    protected function resolveParameter($attributes, $parameterName, $parameterClassName)
    {
        $parameterValue = $attributes[$parameterName];

        if ($parameterValue instanceof UrlRoutable) {
            return $parameterValue;
        }

        if (enum_exists($parameterClassName)) {
            $enum = $parameterClassName::tryFrom($parameterValue);

            if ($enum !== null) {
                return $enum;
            }
        }

        $instance = app()->make($parameterClassName);

        if (!$model = $instance->resolveRouteBinding($parameterValue)) {
            throw (new ModelNotFoundException())->setModel(get_class($instance), [$parameterValue]);
        }

        return $model;
    }

    public function getPublicPropertyTypes($component): Collection
    {
        return collect($component->all())
            ->map(function ($value, $name) use ($component) {
                return Reflector::getParameterClassName(new ReflectionProperty($component, $name));
            })
            ->filter();
    }

    public function destroyComponent($id): void
    {
        unset($this->components[$id]);
    }

    public function getListeners(): array
    {
        return [
            'openModal',
            'destroyComponent',
        ];
    }

    public function render(): View
    {
        $jsPath = null;
        $cssPath = null;

        if (config('livewire-modal.include_js', true)) {
            $jsPath = __DIR__ . '/../resources/js/modal.js';
        }

        if (config('livewire-modal.include_css', false)) {
            $cssPath = __DIR__ . '/../resources/css/modal.css';
        }

        return view('livewire-modal::modal', [
            'jsPath' => $jsPath,
            'cssPath' => $cssPath,
        ]);
    }
}
