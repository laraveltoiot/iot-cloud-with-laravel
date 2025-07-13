<section>
    <div class="flex justify-between items-center mb-4">
        <flux:heading size="lg">Variable Management</flux:heading>
        <flux:modal.trigger name="create-variable">
            <flux:button variant="primary" wire:click="createVariable">Add Variable</flux:button>
        </flux:modal.trigger>
    </div>

    <flux:table :paginate="$this->variables">
        <flux:table.columns>
            <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')">Name</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'variable_id'" :direction="$sortDirection" wire:click="sort('variable_id')">Variable ID</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'data_type'" :direction="$sortDirection" wire:click="sort('data_type')">Data Type</flux:table.column>
            <flux:table.column>Thing</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'unit'" :direction="$sortDirection" wire:click="sort('unit')">Unit</flux:table.column>
            <flux:table.column>Read Only</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'created_at'" :direction="$sortDirection" wire:click="sort('created_at')">Created</flux:table.column>
            <flux:table.column>Actions</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($this->variables as $variable)
                <flux:table.row :key="$variable->id">
                    <flux:table.cell>{{ $variable->name }}</flux:table.cell>
                    <flux:table.cell>{{ $variable->variable_id }}</flux:table.cell>
                    <flux:table.cell>{{ $variable->data_type }}</flux:table.cell>
                    <flux:table.cell>{{ $variable->thing->name }}</flux:table.cell>
                    <flux:table.cell>{{ $variable->unit ?? 'N/A' }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge size="sm" :color="$variable->read_only ? 'yellow' : 'green'" inset="top bottom">
                            {{ $variable->read_only ? 'Yes' : 'No' }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>{{ $variable->created_at->format('Y-m-d H:i') }}</flux:table.cell>
                    <flux:table.cell>
                        <div class="flex space-x-2">
                            <flux:modal.trigger :name="'edit-variable-' . $variable->id">
                                <flux:button variant="ghost" size="sm" icon="pencil" wire:click="editVariable({{ $variable->id }})"></flux:button>
                            </flux:modal.trigger>
                            <flux:modal.trigger :name="'delete-variable-' . $variable->id">
                                <flux:button variant="ghost" size="sm" icon="trash"></flux:button>
                            </flux:modal.trigger>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>

    <!-- Create Variable Modal -->
    <flux:modal name="create-variable" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Add New Variable</flux:heading>
                <flux:text class="mt-2">Enter the details for the new variable.</flux:text>
            </div>

            <form wire:submit="saveVariable">
                <div class="space-y-4">
                    <flux:input label="Name" wire:model="name" placeholder="Variable name" />
                    @error('name') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                    <flux:input label="Variable ID" wire:model="variable_id" placeholder="Unique variable identifier" readonly disabled />
                    <div class="text-gray-500 text-sm mt-1">A unique variable ID has been automatically generated.</div>
                    @error('variable_id') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                    <flux:select label="Thing" wire:model="thing_id">
                        <option value="">-- Select Thing --</option>
                        @foreach($things as $thing)
                            <option value="{{ $thing['id'] }}">{{ $thing['name'] }}</option>
                        @endforeach
                    </flux:select>
                    @error('thing_id') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                    <flux:select label="Data Type" wire:model="data_type">
                        <option value="int">Integer</option>
                        <option value="float">Float</option>
                        <option value="string">String</option>
                        <option value="boolean">Boolean</option>
                        <option value="color">Color</option>
                    </flux:select>
                    @error('data_type') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                    <flux:textarea label="Description" wire:model="description" placeholder="Description of the variable" />
                    @error('description') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                    <flux:input label="Unit" wire:model="unit" placeholder="e.g., celsius, meters, etc." />
                    @error('unit') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                    <flux:textarea label="Metadata (JSON)" wire:model="metadata" placeholder='{"min": 0, "max": 100}' />
                    @error('metadata') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                    <flux:textarea label="Current Value (JSON)" wire:model="current_value" placeholder='{"value": 42}' />
                    @error('current_value') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                    <flux:checkbox label="Read Only" wire:model="read_only" />
                    @error('read_only') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                    <div class="flex justify-end space-x-2 pt-4">
                        <flux:modal.close>
                            <flux:button>Cancel</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary">Save Variable</flux:button>
                    </div>
                </div>
            </form>
        </div>
    </flux:modal>

    <!-- Edit Variable Modals -->
    @foreach ($this->variables as $variable)
        <flux:modal :name="'edit-variable-' . $variable->id" class="md:w-96">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Edit Variable</flux:heading>
                    <flux:text class="mt-2">Update the variable details.</flux:text>
                </div>

                <form wire:submit="saveVariable">
                    <div class="space-y-4">
                        <flux:input label="Name" wire:model="name" placeholder="Variable name" />
                        @error('name') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                        <flux:input label="Variable ID" wire:model="variable_id" placeholder="Unique variable identifier" />
                        @error('variable_id') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                        <flux:select label="Thing" wire:model="thing_id">
                            <option value="">-- Select Thing --</option>
                            @foreach($things as $thing)
                                <option value="{{ $thing['id'] }}">{{ $thing['name'] }}</option>
                            @endforeach
                        </flux:select>
                        @error('thing_id') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                        <flux:select label="Data Type" wire:model="data_type">
                            <option value="int">Integer</option>
                            <option value="float">Float</option>
                            <option value="string">String</option>
                            <option value="boolean">Boolean</option>
                            <option value="color">Color</option>
                        </flux:select>
                        @error('data_type') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                        <flux:textarea label="Description" wire:model="description" placeholder="Description of the variable" />
                        @error('description') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                        <flux:input label="Unit" wire:model="unit" placeholder="e.g., celsius, meters, etc." />
                        @error('unit') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                        <flux:textarea label="Metadata (JSON)" wire:model="metadata" placeholder='{"min": 0, "max": 100}' />
                        @error('metadata') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                        <flux:textarea label="Current Value (JSON)" wire:model="current_value" placeholder='{"value": 42}' />
                        @error('current_value') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                        <flux:checkbox label="Read Only" wire:model="read_only" />
                        @error('read_only') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                        <div class="flex justify-end space-x-2 pt-4">
                            <flux:modal.close>
                                <flux:button>Cancel</flux:button>
                            </flux:modal.close>
                            <flux:button type="submit" variant="primary">Update Variable</flux:button>
                        </div>
                    </div>
                </form>
            </div>
        </flux:modal>
    @endforeach

    <!-- Delete Variable Modals -->
    @foreach ($this->variables as $variable)
        <flux:modal :name="'delete-variable-' . $variable->id" class="min-w-[22rem]">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Delete Variable?</flux:heading>

                    <flux:text class="mt-2">
                        <p>You're about to delete the variable "{{ $variable->name }}".</p>
                        <p>This action cannot be reversed.</p>
                    </flux:text>
                </div>

                <div class="flex gap-2">
                    <flux:spacer />

                    <flux:modal.close>
                        <flux:button variant="ghost">Cancel</flux:button>
                    </flux:modal.close>

                    <flux:button type="button" variant="danger" wire:click="deleteVariable({{ $variable->id }})">Delete Variable</flux:button>
                </div>
            </div>
        </flux:modal>
    @endforeach
</section>
