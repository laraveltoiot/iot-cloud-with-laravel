<section>
    <div class="flex justify-between items-center mb-4">
        <flux:heading size="lg">Trigger Management</flux:heading>
        <flux:modal.trigger name="create-trigger">
            <flux:button variant="primary" wire:click="createTrigger">Add Trigger</flux:button>
        </flux:modal.trigger>
    </div>

    <flux:table :paginate="$this->triggers">
        <flux:table.columns>
            <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')">Name</flux:table.column>
            <flux:table.column>Source Variable</flux:table.column>
            <flux:table.column>Condition</flux:table.column>
            <flux:table.column>Action</flux:table.column>
            <flux:table.column>Status</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'created_at'" :direction="$sortDirection" wire:click="sort('created_at')">Created</flux:table.column>
            <flux:table.column>Actions</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($this->triggers as $trigger)
                <flux:table.row :key="$trigger->id">
                    <flux:table.cell>{{ $trigger->name }}</flux:table.cell>
                    <flux:table.cell>
                        @if($trigger->sourceVariable)
                            {{ $trigger->sourceVariable->name }} ({{ $trigger->sourceVariable->thing->name }})
                        @else
                            N/A
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        {{ ucfirst(str_replace('_', ' ', $trigger->condition_type)) }}
                        @if(is_array($trigger->condition_value))
                            {{ json_encode($trigger->condition_value) }}
                        @else
                            {{ $trigger->condition_value }}
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        {{ ucfirst(str_replace('_', ' ', $trigger->action_type)) }}
                        @if($trigger->action_type === 'set_variable' && $trigger->targetVariable)
                            {{ $trigger->targetVariable->name }}
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:badge size="sm" :color="$trigger->active ? 'green' : 'gray'" inset="top bottom">
                            {{ $trigger->active ? 'Active' : 'Inactive' }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>{{ $trigger->created_at->format('Y-m-d H:i') }}</flux:table.cell>
                    <flux:table.cell>
                        <div class="flex space-x-2">
                            <flux:modal.trigger :name="'edit-trigger-' . $trigger->id">
                                <flux:button variant="ghost" size="sm" icon="pencil" wire:click="editTrigger({{ $trigger->id }})"></flux:button>
                            </flux:modal.trigger>
                            <flux:modal.trigger :name="'delete-trigger-' . $trigger->id">
                                <flux:button variant="ghost" size="sm" icon="trash"></flux:button>
                            </flux:modal.trigger>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>

    <!-- Create Trigger Modal -->
    <flux:modal name="create-trigger" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Add New Trigger</flux:heading>
                <flux:text class="mt-2">Enter the details for the new trigger.</flux:text>
            </div>

            <form wire:submit="saveTrigger">
                <div class="space-y-4">
                    <flux:input label="Name" wire:model="name" placeholder="Trigger name" />
                    @error('name') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                    <flux:textarea label="Description" wire:model="description" placeholder="Description of the trigger" />
                    @error('description') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                    <flux:checkbox label="Active" wire:model="active" />
                    @error('active') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                    <flux:select label="Source Variable" wire:model="source_variable_id">
                        <option value="">-- Select Source Variable --</option>
                        @foreach($sourceVariables as $variable)
                            <option value="{{ $variable['id'] }}">{{ $variable['name'] }} ({{ $variable['thing_name'] }}) - {{ ucfirst($variable['data_type']) }}</option>
                        @endforeach
                    </flux:select>
                    @error('source_variable_id') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                    <flux:select label="Condition Type" wire:model="condition_type">
                        @foreach($conditionTypes as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </flux:select>
                    @error('condition_type') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                    <flux:textarea label="Condition Value (JSON)" wire:model="condition_value" placeholder='{"value": 42}' />
                    @error('condition_value') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                    <flux:select label="Action Type" wire:model="action_type">
                        @foreach($actionTypes as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </flux:select>
                    @error('action_type') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                    @if($action_type === 'set_variable')
                        <flux:select label="Target Variable" wire:model="target_variable_id">
                            <option value="">-- Select Target Variable --</option>
                            @foreach($targetVariables as $variable)
                                <option value="{{ $variable['id'] }}">{{ $variable['name'] }} ({{ $variable['thing_name'] }}) - {{ ucfirst($variable['data_type']) }}</option>
                            @endforeach
                        </flux:select>
                        @error('target_variable_id') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
                    @endif

                    <flux:textarea label="Action Parameters (JSON)" wire:model="action_parameters" placeholder='{"value": 42}' />
                    @error('action_parameters') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                    <div class="flex justify-end space-x-2 pt-4">
                        <flux:modal.close>
                            <flux:button>Cancel</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary">Save Trigger</flux:button>
                    </div>
                </div>
            </form>
        </div>
    </flux:modal>

    <!-- Edit Trigger Modals -->
    @foreach ($this->triggers as $trigger)
        <flux:modal :name="'edit-trigger-' . $trigger->id" class="md:w-96">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Edit Trigger</flux:heading>
                    <flux:text class="mt-2">Update the trigger details.</flux:text>
                </div>

                <form wire:submit="saveTrigger">
                    <div class="space-y-4">
                        <flux:input label="Name" wire:model="name" placeholder="Trigger name" />
                        @error('name') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                        <flux:textarea label="Description" wire:model="description" placeholder="Description of the trigger" />
                        @error('description') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                        <flux:checkbox label="Active" wire:model="active" />
                        @error('active') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                        <flux:select label="Source Variable" wire:model="source_variable_id">
                            <option value="">-- Select Source Variable --</option>
                            @foreach($sourceVariables as $variable)
                                <option value="{{ $variable['id'] }}">{{ $variable['name'] }} ({{ $variable['thing_name'] }}) - {{ ucfirst($variable['data_type']) }}</option>
                            @endforeach
                        </flux:select>
                        @error('source_variable_id') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                        <flux:select label="Condition Type" wire:model="condition_type">
                            @foreach($conditionTypes as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </flux:select>
                        @error('condition_type') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                        <flux:textarea label="Condition Value (JSON)" wire:model="condition_value" placeholder='{"value": 42}' />
                        @error('condition_value') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                        <flux:select label="Action Type" wire:model="action_type">
                            @foreach($actionTypes as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </flux:select>
                        @error('action_type') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                        @if($action_type === 'set_variable')
                            <flux:select label="Target Variable" wire:model="target_variable_id">
                                <option value="">-- Select Target Variable --</option>
                                @foreach($targetVariables as $variable)
                                    <option value="{{ $variable['id'] }}">{{ $variable['name'] }} ({{ $variable['thing_name'] }}) - {{ ucfirst($variable['data_type']) }}</option>
                                @endforeach
                            </flux:select>
                            @error('target_variable_id') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
                        @endif

                        <flux:textarea label="Action Parameters (JSON)" wire:model="action_parameters" placeholder='{"value": 42}' />
                        @error('action_parameters') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                        <div class="flex justify-end space-x-2 pt-4">
                            <flux:modal.close>
                                <flux:button>Cancel</flux:button>
                            </flux:modal.close>
                            <flux:button type="submit" variant="primary">Update Trigger</flux:button>
                        </div>
                    </div>
                </form>
            </div>
        </flux:modal>
    @endforeach

    <!-- Delete Trigger Modals -->
    @foreach ($this->triggers as $trigger)
        <flux:modal :name="'delete-trigger-' . $trigger->id" class="min-w-[22rem]">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Delete Trigger?</flux:heading>

                    <flux:text class="mt-2">
                        <p>You're about to delete the trigger "{{ $trigger->name }}".</p>
                        <p>This action cannot be reversed.</p>
                    </flux:text>
                </div>

                <div class="flex gap-2">
                    <flux:spacer />

                    <flux:modal.close>
                        <flux:button variant="ghost">Cancel</flux:button>
                    </flux:modal.close>

                    <flux:button type="button" variant="danger" wire:click="deleteTrigger({{ $trigger->id }})">Delete Trigger</flux:button>
                </div>
            </div>
        </flux:modal>
    @endforeach
</section>
