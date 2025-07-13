<?php declare(strict_types=1);

use App\Livewire\Admin\UserManagement;
use App\Livewire\IotDevice\DeviceManagement;
use App\Livewire\IoTThing\ThingManagement;
use App\Livewire\IoTVariable\VariableManagement;
use App\Livewire\IoTTrigger\TriggerManagement;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');

    // Admin routes
    Route::get('admin/users', UserManagement::class)->name('admin.users');
    Route::get('admin/devices', DeviceManagement::class)->name('admin.devices');
    Route::get('admin/things', ThingManagement::class)->name('admin.things');
    Route::get('admin/variables', VariableManagement::class)->name('admin.variables');
    Route::get('admin/triggers', TriggerManagement::class)->name('admin.triggers');
});

require __DIR__.'/auth.php';
