<?php declare(strict_types=1);

namespace App\Livewire\IotDevice;

use App\Models\Device;
use Flux;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

final class DeviceManagement extends Component
{
    use WithPagination;

    // Validation rules
    protected $rules = [
        'name' => 'required|string|max:255',
        'device_id' => 'required|string|max:255',
        'type' => 'nullable|string|max:255',
        'status' => 'required|string|in:online,offline,maintenance',
        'metadata' => 'nullable|json',
    ];

    // Sorting properties
    public $sortBy = 'created_at';

    public $sortDirection = 'desc';

    // Form properties
    public $device_id = '';

    public $name = '';

    public $type = '';

    public $status = 'offline';

    public $metadata = '';

    public $editingDeviceId = null;

    public $isCreating = false;

    public function sort($column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    #[Computed]
    public function devices(): LengthAwarePaginator
    {
        return Device::query()
            ->where('user_id', auth()->id())
            ->when($this->sortBy, fn ($query) => $query->orderBy($this->sortBy, $this->sortDirection))
            ->paginate(10);
    }

    public function createDevice()
    {
        $this->resetForm();
        $this->isCreating = true;
        $this->device_id = $this->generateUniqueDeviceId();
    }

    public function editDevice($deviceId): void
    {
        $device = Device::find($deviceId);
        if (! $device) {
            return;
        }

        $this->editingDeviceId = $device->id;
        $this->name = $device->name;
        $this->device_id = $device->device_id;
        $this->type = $device->type;
        $this->status = $device->status;
        $this->metadata = is_array($device->metadata) ? json_encode($device->metadata) : $device->metadata;
    }

    public function saveDevice(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'device_id' => $this->device_id,
            'type' => $this->type,
            'status' => $this->status,
            'metadata' => $this->metadata ? json_decode($this->metadata, true) : null,
            'user_id' => auth()->id(),
        ];

        if ($this->editingDeviceId) {
            // Update existing device
            $device = Device::find($this->editingDeviceId);
            if ($device) {
                $device->update($data);
                // Close the edit modal
                Flux::modal('edit-device-'.$this->editingDeviceId)->close();
            }
        } else {
            // Create new device
            Device::create($data);
            // Close the create modal
            Flux::modal('create-device')->close();
        }

        $this->resetForm();
        $this->dispatch('device-saved');
    }

    public function deleteDevice($deviceId): void
    {
        $device = Device::find($deviceId);
        if ($device) {
            $device->delete();
            // Close the delete modal
            Flux::modal('delete-device-'.$deviceId)->close();
            $this->dispatch('device-deleted');
        }
    }

    public function render()
    {
        return view('livewire.iot-device.device-management');
    }

    private function generateUniqueDeviceId(): string
    {
        // Generate a unique device ID with format: DEV-{random-alphanumeric-string}
        $prefix = 'DEV-';
        $randomString = mb_strtoupper(mb_substr(md5(uniqid((string) mt_rand(), true)), 0, 8));
        $deviceId = $prefix.$randomString;

        // Check if the generated ID already exists, if so, generate a new one
        while (Device::where('device_id', $deviceId)->exists()) {
            $randomString = mb_strtoupper(mb_substr(md5(uniqid((string) mt_rand(), true)), 0, 8));
            $deviceId = $prefix.$randomString;
        }

        return $deviceId;
    }

    private function resetForm(): void
    {
        $this->editingDeviceId = null;
        $this->isCreating = false;
        $this->name = '';
        $this->device_id = '';
        $this->type = '';
        $this->status = 'offline';
        $this->metadata = '';
    }
}
