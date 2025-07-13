<?php declare(strict_types=1);

namespace App\Livewire\IoTTrigger;

use App\Models\Trigger;
use App\Models\Variable;
use Flux;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

final class TriggerManagement extends Component
{
    use WithPagination;

    // Validation rules
    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'active' => 'boolean',
        'source_variable_id' => 'required|exists:variables,id',
        'condition_type' => 'required|string',
        'condition_value' => 'required|json',
        'action_type' => 'required|string',
        'target_variable_id' => 'nullable|exists:variables,id',
        'action_parameters' => 'nullable|json',
    ];

    // Sorting properties
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

    // Form properties
    public $name = '';
    public $description = '';
    public $active = true;
    public $source_variable_id = null;
    public $condition_type = 'equal_to';
    public $condition_value = '';
    public $action_type = 'set_variable';
    public $target_variable_id = null;
    public $action_parameters = '';
    public $editingTriggerId = null;
    public $isCreating = false;

    // Available variables for dropdowns
    public $sourceVariables = [];
    public $targetVariables = [];

    // Available condition types and action types
    public $conditionTypes = [
        'equal_to' => 'Equal To',
        'not_equal_to' => 'Not Equal To',
        'greater_than' => 'Greater Than',
        'less_than' => 'Less Than',
        'contains' => 'Contains',
        'starts_with' => 'Starts With',
        'ends_with' => 'Ends With',
    ];

    public $actionTypes = [
        'set_variable' => 'Set Variable',
        'send_notification' => 'Send Notification',
        'send_email' => 'Send Email',
        'webhook' => 'Webhook',
    ];

    public function mount(): void
    {
        $this->loadVariables();
    }

    private function loadVariables(): void
    {
        // Get variables from things owned by the current user
        $this->sourceVariables = Variable::whereHas('thing', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->orderBy('name')
            ->get()
            ->map(function ($variable) {
                return [
                    'id' => $variable->id,
                    'name' => $variable->name,
                    'thing_name' => $variable->thing->name,
                    'data_type' => $variable->data_type,
                ];
            })
            ->toArray();

        // For target variables, only include non-read-only variables
        $this->targetVariables = Variable::whereHas('thing', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->where('read_only', false)
            ->orderBy('name')
            ->get()
            ->map(function ($variable) {
                return [
                    'id' => $variable->id,
                    'name' => $variable->name,
                    'thing_name' => $variable->thing->name,
                    'data_type' => $variable->data_type,
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
    public function triggers(): LengthAwarePaginator
    {
        return Trigger::query()
            ->with(['sourceVariable.thing', 'targetVariable.thing'])
            ->where('user_id', auth()->id())
            ->when($this->sortBy, fn ($query) => $query->orderBy($this->sortBy, $this->sortDirection))
            ->paginate(10);
    }

    public function createTrigger()
    {
        $this->resetForm();
        $this->isCreating = true;
    }

    public function editTrigger($triggerId): void
    {
        $trigger = Trigger::find($triggerId);
        if (! $trigger) {
            return;
        }

        $this->editingTriggerId = $trigger->id;
        $this->name = $trigger->name;
        $this->description = $trigger->description;
        $this->active = $trigger->active;
        $this->source_variable_id = $trigger->source_variable_id;
        $this->condition_type = $trigger->condition_type;
        $this->condition_value = is_array($trigger->condition_value) ? json_encode($trigger->condition_value) : $trigger->condition_value;
        $this->action_type = $trigger->action_type;
        $this->target_variable_id = $trigger->target_variable_id;
        $this->action_parameters = is_array($trigger->action_parameters) ? json_encode($trigger->action_parameters) : $trigger->action_parameters;
    }

    public function saveTrigger(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'active' => $this->active,
            'source_variable_id' => $this->source_variable_id,
            'condition_type' => $this->condition_type,
            'condition_value' => $this->condition_value ? json_decode($this->condition_value, true) : null,
            'action_type' => $this->action_type,
            'target_variable_id' => $this->action_type === 'set_variable' ? $this->target_variable_id : null,
            'action_parameters' => $this->action_parameters ? json_decode($this->action_parameters, true) : null,
            'user_id' => auth()->id(),
        ];

        if ($this->editingTriggerId) {
            // Update existing trigger
            $trigger = Trigger::find($this->editingTriggerId);
            if ($trigger) {
                $trigger->update($data);
                // Close the edit modal
                Flux::modal('edit-trigger-'.$this->editingTriggerId)->close();
            }
        } else {
            // Create new trigger
            Trigger::create($data);
            // Close the create modal
            Flux::modal('create-trigger')->close();
        }

        $this->resetForm();
        $this->dispatch('trigger-saved');
    }

    public function deleteTrigger($triggerId): void
    {
        $trigger = Trigger::find($triggerId);
        if ($trigger) {
            $trigger->delete();
            // Close the delete modal
            Flux::modal('delete-trigger-'.$triggerId)->close();
            $this->dispatch('trigger-deleted');
        }
    }

    public function render()
    {
        return view('livewire.iot-trigger.trigger-management');
    }

    private function resetForm(): void
    {
        $this->editingTriggerId = null;
        $this->isCreating = false;
        $this->name = '';
        $this->description = '';
        $this->active = true;
        $this->source_variable_id = null;
        $this->condition_type = 'equal_to';
        $this->condition_value = '';
        $this->action_type = 'set_variable';
        $this->target_variable_id = null;
        $this->action_parameters = '';
    }
}
