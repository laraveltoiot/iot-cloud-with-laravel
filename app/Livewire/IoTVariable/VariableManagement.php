<?php declare(strict_types=1);

namespace App\Livewire\IoTVariable;

use App\Models\Thing;
use App\Models\Variable;
use Flux;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

final class VariableManagement extends Component
{
    use WithPagination;

    // Validation rules
    protected $rules = [
        'name' => 'required|string|max:255',
        'variable_id' => 'required|string|max:255',
        'thing_id' => 'required|exists:things,id',
        'data_type' => 'required|string|in:int,float,string,boolean,color',
        'description' => 'nullable|string',
        'unit' => 'nullable|string|max:50',
        'metadata' => 'nullable|json',
        'current_value' => 'nullable|json',
        'read_only' => 'boolean',
    ];

    // Sorting properties
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

    // Form properties
    public $variable_id = '';
    public $name = '';
    public $thing_id = null;
    public $data_type = 'string';
    public $description = '';
    public $unit = '';
    public $metadata = '';
    public $current_value = '';
    public $read_only = false;
    public $editingVariableId = null;
    public $isCreating = false;

    // Available things for dropdown
    public $things = [];

    public function mount(): void
    {
        $this->loadThings();
    }

    private function loadThings(): void
    {
        $this->things = Thing::where('user_id', auth()->id())
            ->orderBy('name')
            ->get()
            ->map(function ($thing) {
                return [
                    'id' => $thing->id,
                    'name' => $thing->name,
                ];
            })
            ->toArray();
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

    #[Computed]
    public function variables(): LengthAwarePaginator
    {
        return Variable::query()
            ->with('thing')
            ->whereHas('thing', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->when($this->sortBy, fn ($query) => $query->orderBy($this->sortBy, $this->sortDirection))
            ->paginate(10);
    }

    public function createVariable()
    {
        $this->resetForm();
        $this->isCreating = true;
        $this->variable_id = $this->generateUniqueVariableId();
    }

    public function editVariable($variableId): void
    {
        $variable = Variable::find($variableId);
        if (! $variable) {
            return;
        }

        $this->editingVariableId = $variable->id;
        $this->name = $variable->name;
        $this->variable_id = $variable->variable_id;
        $this->thing_id = $variable->thing_id;
        $this->data_type = $variable->data_type;
        $this->description = $variable->description;
        $this->unit = $variable->unit;
        $this->metadata = is_array($variable->metadata) ? json_encode($variable->metadata) : $variable->metadata;
        $this->current_value = is_array($variable->current_value) ? json_encode($variable->current_value) : $variable->current_value;
        $this->read_only = $variable->read_only;
    }

    public function saveVariable(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'variable_id' => $this->variable_id,
            'thing_id' => $this->thing_id,
            'data_type' => $this->data_type,
            'description' => $this->description,
            'unit' => $this->unit,
            'metadata' => $this->metadata ? json_decode($this->metadata, true) : null,
            'current_value' => $this->current_value ? json_decode($this->current_value, true) : null,
            'read_only' => $this->read_only,
        ];

        if ($this->editingVariableId) {
            // Update existing variable
            $variable = Variable::find($this->editingVariableId);
            if ($variable) {
                $variable->update($data);
                // Close the edit modal
                Flux::modal('edit-variable-'.$this->editingVariableId)->close();
            }
        } else {
            // Create new variable
            Variable::create($data);
            // Close the create modal
            Flux::modal('create-variable')->close();
        }

        $this->resetForm();
        $this->dispatch('variable-saved');
    }

    public function deleteVariable($variableId): void
    {
        $variable = Variable::find($variableId);
        if ($variable) {
            $variable->delete();
            // Close the delete modal
            Flux::modal('delete-variable-'.$variableId)->close();
            $this->dispatch('variable-deleted');
        }
    }

    public function render()
    {
        return view('livewire.iot-variable.variable-management');
    }

    private function generateUniqueVariableId(): string
    {
        // Generate a unique variable ID with format: VAR-{random-alphanumeric-string}
        $prefix = 'VAR-';
        $randomString = mb_strtoupper(mb_substr(md5(uniqid((string) mt_rand(), true)), 0, 8));
        $variableId = $prefix.$randomString;

        // Check if the generated ID already exists, if so, generate a new one
        while (Variable::where('variable_id', $variableId)->exists()) {
            $randomString = mb_strtoupper(mb_substr(md5(uniqid((string) mt_rand(), true)), 0, 8));
            $variableId = $prefix.$randomString;
        }

        return $variableId;
    }

    private function resetForm(): void
    {
        $this->editingVariableId = null;
        $this->isCreating = false;
        $this->name = '';
        $this->variable_id = '';
        $this->thing_id = null;
        $this->data_type = 'string';
        $this->description = '';
        $this->unit = '';
        $this->metadata = '';
        $this->current_value = '';
        $this->read_only = false;
    }
}
