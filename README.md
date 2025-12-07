# Livewire Modal

A Livewire 4 modal component that supports multiple child modals while maintaining state.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/andisiahaan/livewire-modal.svg?style=flat-square)](https://packagist.org/packages/andisiahaan/livewire-modal)
[![Total Downloads](https://img.shields.io/packagist/dt/andisiahaan/livewire-modal.svg?style=flat-square)](https://packagist.org/packages/andisiahaan/livewire-modal)

## Installation

```bash
composer require andisiahaan/livewire-modal
```

## Setup

Add the Livewire directive to your layout file (typically before `</body>`):

```html
<html>
<body>
    <!-- Your content -->
    
    @livewire('livewire-modal')
</body>
</html>
```

### TailwindCSS

Add the following to your `tailwind.config.js` to include the modal styles:

```javascript
export default {
    content: [
        './vendor/andisiahaan/livewire-modal/resources/views/*.blade.php',
        // ... your other paths
    ],
    safelist: [
        {
            pattern: /max-w-(sm|md|lg|xl|2xl|3xl|4xl|5xl|6xl|7xl)/,
            variants: ['sm', 'md', 'lg', 'xl', '2xl']
        }
    ],
}
```

## Creating a Modal

Create a Livewire component that extends `ModalComponent`:

```php
<?php

namespace App\Livewire;

use AndiSiahaan\LivewireModal\ModalComponent;

class EditUser extends ModalComponent
{
    public $user;

    public function mount($userId)
    {
        $this->user = User::findOrFail($userId);
    }

    public function save()
    {
        // Save logic...
        
        $this->closeModal();
    }

    public function render()
    {
        return view('livewire.edit-user');
    }
}
```

## Opening a Modal

You can open a modal by dispatching the `openModal` event:

```html
<!-- Outside of any Livewire component -->
<button onclick="Livewire.dispatch('openModal', { component: 'edit-user', arguments: { userId: 1 }})">
    Edit User
</button>

<!-- Inside a Livewire component -->
<button wire:click="$dispatch('openModal', { component: 'edit-user', arguments: { userId: {{ $user->id }} }})">
    Edit User
</button>
```

## Passing Parameters

Parameters are automatically injected into your modal component:

```php
class EditUser extends ModalComponent
{
    public User $user; // Automatically resolved from route binding

    public function render()
    {
        return view('livewire.edit-user');
    }
}
```

```html
<button wire:click="$dispatch('openModal', { component: 'edit-user', arguments: { user: {{ $user->id }} }})">
    Edit User
</button>
```

## Closing a Modal

### From the view:

```html
<button wire:click="$dispatch('closeModal')">Cancel</button>
```

### From the component:

```php
public function save()
{
    // Save logic...
    
    $this->closeModal();
}
```

### With events:

```php
public function save()
{
    $this->user->save();
    
    $this->closeModalWithEvents([
        UserList::class => 'refreshList',
    ]);
}
```

## Nested Modals

You can open a modal from within another modal:

```html
<!-- Inside EditUser modal -->
<button wire:click="$dispatch('openModal', { component: 'delete-user', arguments: { user: {{ $user->id }} }})">
    Delete User
</button>
```

When closing the child modal, it will return to the parent modal.

### Force Close All Modals

```php
$this->forceClose()->closeModal();
```

### Skip Previous Modals

```php
$this->skipPreviousModal()->closeModal();
// or skip multiple
$this->skipPreviousModals(2)->closeModal();
```

## Modal Properties

Override these static methods in your modal component:

```php
class EditUser extends ModalComponent
{
    // Modal width: 'sm', 'md', 'lg', 'xl', '2xl', '3xl', '4xl', '5xl', '6xl', '7xl'
    public static function modalMaxWidth(): string
    {
        return 'lg';
    }

    // Close when clicking outside
    public static function closeModalOnClickAway(): bool
    {
        return false;
    }

    // Close when pressing Escape
    public static function closeModalOnEscape(): bool
    {
        return true;
    }

    // Destroy component state on close
    public static function destroyOnClose(): bool
    {
        return true;
    }
}
```

## Configuration

Publish the config file:

```bash
php artisan vendor:publish --tag=livewire-modal-config
```

Publish the views:

```bash
php artisan vendor:publish --tag=livewire-modal-views
```

## License

MIT License. See [LICENSE](LICENSE) for more information.
