<?php declare(strict_types=1);

namespace App\Livewire\IotDevice;

use App\Models\Device;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

final class DeviceManagement extends Component
{
    public function render(): View|Application|Factory|\Illuminate\View\View
    {
        return view('livewire.iot-device.device-management');
    }
}
