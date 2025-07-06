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
    use WithPagination;

    public string $name = '';
    public string $device_id = '';
    public string $type = '';
    public string $status = 'offline';
    public ?array $metadata = [];

    public ?int $editingDeviceId = null;

    public string $search = '';
    public string $sortBy = 'name';
    public string $sortDirection = 'asc';

    protected $queryString = [
        'search' => ['except' => ''],
        'sortBy' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
    ];

    /**
     * Sort the data by the given column.
     */
    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    /**
     * Get the devices with pagination and sorting.
     */
    #[Computed]
    public function devices(): array|LengthAwarePaginator
    {
        return Device::query()
            ->where('user_id', auth()->id())
            ->when($this->search, function ($query, $search) {
                return $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('device_id', 'like', "%{$search}%")
                        ->orWhere('type', 'like', "%{$search}%");
                });
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(10);
    }

    /**
     * Render the component.
     */
    public function render(): View|Application|Factory|\Illuminate\View\View
    {
        return view('livewire.iot-device.device-management');
    }

    /**
     * Reset the form fields.
     */
    public function resetForm(): void
    {
        $this->reset(['name', 'type', 'status', 'metadata', 'editingDeviceId']);
        $this->resetValidation();

        // Generate a new device ID for the creation form
        $this->device_id = $this->generateDeviceId();
    }

    /**
     * Generate a unique device ID.
     */
    private function generateDeviceId(): string
    {
        return 'DEV_' . strtoupper(substr(md5(uniqid((string)mt_rand(), true)), 0, 10));
    }

    public function mount(): void
    {
        // Generate a device ID when the component is mounted
        $this->device_id = $this->generateDeviceId();
    }

    public function createDevice(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:255'],
        ]);

        Device::create([
            'user_id' => auth()->id(),
            'name' => $validated['name'],
            'device_id' => $this->device_id,
            'type' => $validated['type'],
            'status' => 'offline',
            'metadata' => $this->metadata,
        ]);

        $this->resetForm();
        $this->dispatch('device-created');
        $this->modal('create-device')->close();
    }

    /**
     * Load device data for editing when the edit modal is opened.
     */
    #[On('modal-opened')]
    public function handleModalOpened($data): void
    {
        // Extract the modal name from the event data
        $modalName = is_array($data) && isset($data['name']) ? $data['name'] : $data;

        // Check if the modal name starts with 'edit-device-'
        if (str_starts_with($modalName, 'edit-device-')) {
            $deviceId = (int) str_replace('edit-device-', '', $modalName);
            $this->loadDeviceData($deviceId);
        }
    }

    /**
     * Load device data for editing.
     */
    private function loadDeviceData(int $deviceId): void
    {
        // Don't reset the form here, as it will clear the fields we want to populate
        // Just reset validation and editingDeviceId
        $this->resetValidation();
        $this->editingDeviceId = null;

        $device = Device::where('user_id', auth()->id())->findOrFail($deviceId);
        $this->editingDeviceId = $device->id;
        $this->name = $device->name;
        $this->device_id = $device->device_id;
        $this->type = $device->type ?? '';
        $this->status = $device->status;
        $this->metadata = $device->metadata ?? [];
    }

    /**
     * Update an existing device.
     */
    public function updateDevice(int $deviceId): void
    {
        $device = Device::where('user_id', auth()->id())->findOrFail($deviceId);

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:255'],
        ]);

        $device->name = $validated['name'];
        $device->type = $validated['type'];
        $device->metadata = $this->metadata;

        $device->save();

        // Reset the form after updating
        $this->resetForm();
        $this->dispatch('device-updated');
        $this->modal('edit-device-' . $deviceId)->close();
    }

    /**
     * Delete a device.
     */
    public function deleteDevice(int $deviceId): void
    {
        $device = Device::where('user_id', auth()->id())->findOrFail($deviceId);
        $device->delete();

        $this->dispatch('device-deleted');
        $this->modal('delete-device-' . $deviceId)->close();
    }
}
