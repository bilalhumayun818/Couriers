<?php

namespace App\Providers;

use Native\Desktop\Facades\Window;
// use Native\Laravel\Facades\Window;
use Native\Desktop\Contracts\ProvidesPhpIni;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    /**
     * Executed once the native application has been booted.
     * Use this method to open windows, register global shortcuts, etc.
     */
    public function boot(): void
    {
        Window::open()
        ->title('CX COURIER')
        ->width(1280)
        ->height(800)
        ->minWidth(1024)        // Prevents the user from making the window too small
        ->minHeight(600)
        ->rememberState();
    }

    /**
     * Return an array of php.ini directives to be set.
     */
    public function phpIni(): array
    {
        return [
        ];
    }
}
