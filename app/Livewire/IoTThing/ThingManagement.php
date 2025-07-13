<?php declare(strict_types=1);

namespace App\Livewire\IoTThing;

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
    ];

    // Sorting properties
    public $sortBy = 'created_at';

    public $sortDirection = 'desc';

    // Form properties
    public $thing_id = '';

    public $name = '';

    public $description = '';

    public $properties = '';

    public $editingThingId = null;

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
    public function things(): LengthAwarePaginator
    {
        return Thing::query()
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
        $thing = Thing::find($thingId);
        if (! $thing) {
            return;
        }

        $this->editingThingId = $thing->id;
        $this->name = $thing->name;
        $this->thing_id = $thing->thing_id;
        $this->description = $thing->description;
        $this->properties = is_array($thing->properties) ? json_encode($thing->properties) : $thing->properties;
    }

    public function saveThing(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'thing_id' => $this->thing_id,
            'description' => $this->description,
            'properties' => $this->properties ? json_decode($this->properties, true) : null,
            'user_id' => auth()->id(),
        ];

        if ($this->editingThingId) {
            // Update existing thing
            $thing = Thing::find($this->editingThingId);
            if ($thing) {
                $thing->update($data);
                // Close the edit modal
                Flux::modal('edit-thing-'.$this->editingThingId)->close();
            }
        } else {
            // Create new thing
            Thing::create($data);
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

    private function resetForm(): void
    {
        $this->editingThingId = null;
        $this->isCreating = false;
        $this->name = '';
        $this->thing_id = '';
        $this->description = '';
        $this->properties = '';
    }
}
