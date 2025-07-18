<?php declare(strict_types=1);

namespace App\Livewire\IoTThing;

use App\Models\Device;
use App\Models\Sketch;
use App\Models\Thing;
use Flux;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

final class ThingManagement extends Component
{
    use WithPagination;

    protected $rules = [
        'name' => 'required|string|max:255',
        'thing_id' => 'required|string|max:255',
        'description' => 'nullable|string',
        'properties' => 'nullable|json',
        'timezone' => 'required|string',
        'tags' => 'nullable|json',
        'network_config' => 'nullable|json',
        'sketch_id' => 'nullable|exists:sketches,id',
        'status' => 'required|in:online,offline,error',
    ];

    // Sorting properties
    public $sortBy = 'created_at';

    public $sortDirection = 'desc';

    // Form properties
    public $thing_id = '';

    public $name = '';

    public $description = '';

    public $properties = '';

    public $timezone = 'UTC';

    public $tags = '';

    public $network_config = '';

    public $sketch_id = null;

    public $status = 'offline';

    public $editingThingId = null;

    public $isCreating = false;

    public $sketches = [];

    public $devices = [];

    public $selectedDevices = [];

    public $deviceConfigs = [];

    public function mount(): void
    {
        $this->loadSketches();
        $this->loadDevices();
    }

    public function sort($column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    private function loadSketches(): void
    {
        $this->sketches = Sketch::where('user_id', auth()->id())
            ->orderBy('name')
            ->get()
            ->map(function ($sketch) {
                return [
                    'id' => $sketch->id,
                    'name' => $sketch->name,
                ];
            })
            ->toArray();
    }

    private function loadDevices(): void
    {
        $this->devices = Device::where('user_id', auth()->id())
            ->orderBy('name')
            ->get()
            ->map(function ($device) {
                return [
                    'id' => $device->id,
                    'name' => $device->name,
                    'device_id' => $device->device_id,
                    'type' => $device->type,
                    'status' => $device->status,
                ];
            })
            ->toArray();
    }

    #[Computed]
    public function things(): LengthAwarePaginator
    {
        return Thing::query()
            ->with('devices')
            ->where('user_id', auth()->id())
            ->when($this->sortBy, fn ($query) => $query->orderBy($this->sortBy, $this->sortDirection))
            ->paginate(10);
    }

    public function createThing(): void
    {
        $this->resetForm();
        $this->isCreating = true;
        $this->thing_id = $this->generateUniqueThingId();
    }

    public function editThing($thingId): void
    {
        $thing = Thing::with('devices')->find($thingId);
        if (! $thing) {
            return;
        }

        $this->editingThingId = $thing->id;
        $this->name = $thing->name;
        $this->thing_id = $thing->thing_id;
        $this->description = $thing->description;
        $this->properties = is_array($thing->properties) ? json_encode($thing->properties) : $thing->properties;
        $this->timezone = $thing->timezone;
        $this->tags = is_array($thing->tags) ? json_encode($thing->tags) : $thing->tags;
        $this->network_config = is_array($thing->network_config) ? json_encode($thing->network_config) : $thing->network_config;
        $this->sketch_id = $thing->sketch_id;
        $this->status = $thing->status;

        // Load associated devices
        $this->selectedDevices = $thing->devices->pluck('id')->toArray();

        // Load device configurations
        $this->deviceConfigs = [];
        foreach ($thing->devices as $device) {
            $config = $device->pivot->config;
            $this->deviceConfigs[$device->id] = is_array($config) ? json_encode($config) : $config;
        }
    }

    public function saveThing(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'thing_id' => $this->thing_id,
            'description' => $this->description,
            'properties' => $this->properties ? json_decode($this->properties, true) : null,
            'timezone' => $this->timezone,
            'tags' => $this->tags ? json_decode($this->tags, true) : null,
            'network_config' => $this->network_config ? json_decode($this->network_config, true) : null,
            'sketch_id' => $this->sketch_id,
            'status' => $this->status,
            'user_id' => auth()->id(),
        ];

        if ($this->editingThingId) {
            // Update existing thing
            $thing = Thing::find($this->editingThingId);
            if ($thing) {
                $thing->update($data);

                // Update device associations
                $deviceData = [];
                foreach ($this->selectedDevices as $deviceId) {
                    $config = isset($this->deviceConfigs[$deviceId]) ? $this->deviceConfigs[$deviceId] : null;
                    $deviceData[$deviceId] = [
                        'config' => $config ? json_encode(json_decode($config, true)) : null,
                    ];
                }

                $thing->devices()->sync($deviceData);

                // Close the edit modal
                Flux::modal('edit-thing-'.$this->editingThingId)->close();
            }
        } else {
            // Create new thing
            $thing = Thing::create($data);

            // Create device associations
            $deviceData = [];
            foreach ($this->selectedDevices as $deviceId) {
                $config = isset($this->deviceConfigs[$deviceId]) ? $this->deviceConfigs[$deviceId] : null;
                $deviceData[$deviceId] = [
                    'config' => $config ? json_encode(json_decode($config, true)) : null,
                ];
            }

            $thing->devices()->sync($deviceData);

            // Close the create modal
            Flux::modal('create-thing')->close();
        }

        $this->resetForm();
        $this->dispatch('thing-saved');
    }

    public function deleteThing($thingId): void
    {
        $thing = Thing::find($thingId);
        if ($thing) {
            $thing->delete();
            // Close the delete modal
            Flux::modal('delete-thing-'.$thingId)->close();
            $this->dispatch('thing-deleted');
        }
    }

    public function render()
    {
        return view('livewire.iot-thing.thing-management');
    }

    private function generateUniqueThingId(): string
    {
        // Generate a unique thing ID with format: THG-{random-alphanumeric-string}
        $prefix = 'THG-';
        $randomString = mb_strtoupper(mb_substr(md5(uniqid((string) mt_rand(), true)), 0, 8));
        $thingId = $prefix.$randomString;

        // Check if the generated ID already exists, if so, generate a new one
        while (Thing::where('thing_id', $thingId)->exists()) {
            $randomString = mb_strtoupper(mb_substr(md5(uniqid((string) mt_rand(), true)), 0, 8));
            $thingId = $prefix.$randomString;
        }

        return $thingId;
    }

    public function updateDeviceConfig($deviceId, $config): void
    {
        $this->deviceConfigs[$deviceId] = $config;
    }

    public function toggleDevice($deviceId): void
    {
        $index = array_search($deviceId, $this->selectedDevices);

        if ($index !== false) {
            // Remove device from selection
            unset($this->selectedDevices[$index]);
            $this->selectedDevices = array_values($this->selectedDevices); // Re-index array

            // Remove device config
            if (isset($this->deviceConfigs[$deviceId])) {
                unset($this->deviceConfigs[$deviceId]);
            }
        } else {
            // Add device to selection
            $this->selectedDevices[] = $deviceId;

            // Initialize device config if not exists
            if (!isset($this->deviceConfigs[$deviceId])) {
                $this->deviceConfigs[$deviceId] = json_encode(['pin_mapping' => []]);
            }
        }
    }

    private function resetForm(): void
    {
        $this->editingThingId = null;
        $this->isCreating = false;
        $this->name = '';
        $this->thing_id = '';
        $this->description = '';
        $this->properties = '';
        $this->timezone = 'UTC';
        $this->tags = '';
        $this->network_config = '';
        $this->sketch_id = null;
        $this->status = 'offline';
        $this->selectedDevices = [];
        $this->deviceConfigs = [];
    }
}
