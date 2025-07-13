<section>
    <div class="flex justify-between items-center mb-4">
        <flux:heading size="lg">Thing Management</flux:heading>
        <flux:modal.trigger name="create-thing">
            <flux:button variant="primary" wire:click="createThing">Add Thing</flux:button>
        </flux:modal.trigger>
    </div>

    <flux:table :paginate="$this->things">
        <flux:table.columns>
            <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')">Name</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'thing_id'" :direction="$sortDirection" wire:click="sort('thing_id')">Thing ID</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'description'" :direction="$sortDirection" wire:click="sort('description')">Description</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'status'" :direction="$sortDirection" wire:click="sort('status')">Status</flux:table.column>
            <flux:table.column>Devices</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'created_at'" :direction="$sortDirection" wire:click="sort('created_at')">Created</flux:table.column>
            <flux:table.column>Actions</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($this->things as $thing)
                <flux:table.row :key="$thing->id">
                    <flux:table.cell>{{ $thing->name }}</flux:table.cell>
                    <flux:table.cell>{{ $thing->thing_id }}</flux:table.cell>
                    <flux:table.cell>{{ $thing->description ?? 'N/A' }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge size="sm" :color="$thing->status === 'online' ? 'green' : ($thing->status === 'offline' ? 'gray' : 'red')" inset="top bottom">
                            {{ ucfirst($thing->status) }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:badge size="sm" color="blue" inset="top bottom">
                            {{ $thing->devices->count() }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>{{ $thing->created_at->format('Y-m-d H:i') }}</flux:table.cell>
                    <flux:table.cell>
                        <div class="flex space-x-2">
                            <flux:modal.trigger :name="'edit-thing-' . $thing->id">
                                <flux:button variant="ghost" size="sm" icon="pencil" wire:click="editThing({{ $thing->id }})"></flux:button>
                            </flux:modal.trigger>
                            <flux:modal.trigger :name="'delete-thing-' . $thing->id">
                                <flux:button variant="ghost" size="sm" icon="trash"></flux:button>
                            </flux:modal.trigger>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>

    <!-- Create Thing Modal -->
    <flux:modal name="create-thing" class="w-5xl">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Add New Thing</flux:heading>
                <flux:text class="mt-2">Enter the details for the new thing.</flux:text>
            </div>

            <form wire:submit="saveThing">
                <div class="space-y-4">
                    <flux:input label="Name" wire:model="name" placeholder="Thing name" />
                    @error('name') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                    <flux:input label="Thing ID" wire:model="thing_id" placeholder="Unique thing identifier" readonly disabled />
                    <div class="text-gray-500 text-sm mt-1">A unique thing ID has been automatically generated.</div>
                    @error('thing_id') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                    <flux:textarea label="Description" wire:model="description" placeholder="Description of the thing" />
                    @error('description') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                    <flux:textarea label="Properties (JSON)" wire:model="properties" placeholder='{"key": "value"}' />
                    @error('properties') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                    <flux:input label="Timezone" wire:model="timezone" placeholder="UTC" />
                    @error('timezone') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                    <flux:textarea label="Tags (JSON)" wire:model="tags" placeholder='["home", "temperature"]' />
                    @error('tags') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                    <flux:textarea label="Network Config (JSON)" wire:model="network_config" placeholder='{"wifi": {"ssid": "MyNetwork", "password": "secret"}}' />
                    @error('network_config') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                    <flux:select label="Sketch" wire:model="sketch_id">
                        <option value="">-- Select Sketch --</option>
                        @foreach($sketches as $sketch)
                            <option value="{{ $sketch['id'] }}">{{ $sketch['name'] }}</option>
                        @endforeach
                    </flux:select>
                    @error('sketch_id') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                    <flux:select label="Status" wire:model="status">
                        <option value="online">Online</option>
                        <option value="offline">Offline</option>
                        <option value="error">Error</option>
                    </flux:select>
                    @error('status') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                    <div>
                        <flux:label>Associated Devices</flux:label>
                        <div class="mt-2 border rounded-md p-3 max-h-60 overflow-y-auto">
                            @if(count($devices) > 0)
                                @foreach($devices as $device)
                                    <div class="flex items-center justify-between py-2 border-b last:border-b-0">
                                        <div class="flex items-center">
                                            <flux:checkbox
                                                wire:click="toggleDevice({{ $device['id'] }})"
                                                :checked="in_array($device['id'], $selectedDevices)"
                                            />
                                            <span class="ml-2">{{ $device['name'] }} ({{ $device['device_id'] }})</span>
                                            <flux:badge size="sm" :color="$device['status'] === 'online' ? 'green' : ($device['status'] === 'offline' ? 'gray' : 'yellow')" class="ml-2">
                                                {{ ucfirst($device['status']) }}
                                            </flux:badge>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-gray-500 py-2">No devices available. Create devices first.</div>
                            @endif
                        </div>
                    </div>

                    @if(count($selectedDevices) > 0)
                        <div>
                            <flux:label>Device Configurations</flux:label>
                            <div class="mt-2 space-y-4">
                                @foreach($selectedDevices as $deviceId)
                                    @php
                                        $device = collect($devices)->firstWhere('id', $deviceId);
                                    @endphp
                                    @if($device)
                                        <div class="border rounded-md p-3">
                                            <div class="font-medium mb-2">{{ $device['name'] }} ({{ $device['device_id'] }})</div>
                                            <flux:textarea
                                                label="Configuration (JSON)"
                                                wire:model="deviceConfigs.{{ $deviceId }}"
                                                placeholder='{"pin_mapping": {"pin_1": "digital_input", "pin_2": "digital_output"}}'
                                            />
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="flex justify-end space-x-2 pt-4">
                        <flux:modal.close>
                            <flux:button>Cancel</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary">Save Thing</flux:button>
                    </div>
                </div>
            </form>
        </div>
    </flux:modal>

    <!-- Edit Thing Modals -->
    @foreach ($this->things as $thing)
        <flux:modal :name="'edit-thing-' . $thing->id" class="w-5xl">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Edit Thing</flux:heading>
                    <flux:text class="mt-2">Update the thing details.</flux:text>
                </div>

                <form wire:submit="saveThing">
                    <div class="space-y-4">
                        <flux:input label="Name" wire:model="name" placeholder="Thing name" />
                        @error('name') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                        <flux:input label="Thing ID" wire:model="thing_id" placeholder="Unique thing identifier" />
                        @error('thing_id') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                        <flux:textarea label="Description" wire:model="description" placeholder="Description of the thing" />
                        @error('description') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                        <flux:textarea label="Properties (JSON)" wire:model="properties" placeholder='{"key": "value"}' />
                        @error('properties') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                        <flux:input label="Timezone" wire:model="timezone" placeholder="UTC" />
                        @error('timezone') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                        <flux:textarea label="Tags (JSON)" wire:model="tags" placeholder='["home", "temperature"]' />
                        @error('tags') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                        <flux:textarea label="Network Config (JSON)" wire:model="network_config" placeholder='{"wifi": {"ssid": "MyNetwork", "password": "secret"}}' />
                        @error('network_config') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                        <flux:select label="Sketch" wire:model="sketch_id">
                            <option value="">-- Select Sketch --</option>
                            @foreach($sketches as $sketch)
                                <option value="{{ $sketch['id'] }}">{{ $sketch['name'] }}</option>
                            @endforeach
                        </flux:select>
                        @error('sketch_id') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                        <flux:select label="Status" wire:model="status">
                            <option value="online">Online</option>
                            <option value="offline">Offline</option>
                            <option value="error">Error</option>
                        </flux:select>
                        @error('status') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                        <div>
                            <flux:label>Associated Devices</flux:label>
                            <div class="mt-2 border rounded-md p-3 max-h-60 overflow-y-auto">
                                @if(count($devices) > 0)
                                    @foreach($devices as $device)
                                        <div class="flex items-center justify-between py-2 border-b last:border-b-0">
                                            <div class="flex items-center">
                                                <flux:checkbox
                                                    wire:click="toggleDevice({{ $device['id'] }})"
                                                    :checked="in_array($device['id'], $selectedDevices)"
                                                />
                                                <span class="ml-2">{{ $device['name'] }} ({{ $device['device_id'] }})</span>
                                                <flux:badge size="sm" :color="$device['status'] === 'online' ? 'green' : ($device['status'] === 'offline' ? 'gray' : 'yellow')" class="ml-2">
                                                    {{ ucfirst($device['status']) }}
                                                </flux:badge>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-gray-500 py-2">No devices available. Create devices first.</div>
                                @endif
                            </div>
                        </div>

                        @if(count($selectedDevices) > 0)
                            <div>
                                <flux:label>Device Configurations</flux:label>
                                <div class="mt-2 space-y-4">
                                    @foreach($selectedDevices as $deviceId)
                                        @php
                                            $device = collect($devices)->firstWhere('id', $deviceId);
                                        @endphp
                                        @if($device)
                                            <div class="border rounded-md p-3">
                                                <div class="font-medium mb-2">{{ $device['name'] }} ({{ $device['device_id'] }})</div>
                                                <flux:textarea
                                                    label="Configuration (JSON)"
                                                    wire:model="deviceConfigs.{{ $deviceId }}"
                                                    placeholder='{"pin_mapping": {"pin_1": "digital_input", "pin_2": "digital_output"}}'
                                                />
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="flex justify-end space-x-2 pt-4">
                            <flux:modal.close>
                                <flux:button>Cancel</flux:button>
                            </flux:modal.close>
                            <flux:button type="submit" variant="primary">Update Thing</flux:button>
                        </div>
                    </div>
                </form>
            </div>
        </flux:modal>
    @endforeach

    <!-- Delete Thing Modals -->
    @foreach ($this->things as $thing)
        <flux:modal :name="'delete-thing-' . $thing->id" class="min-w-[22rem]">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Delete Thing?</flux:heading>

                    <flux:text class="mt-2">
                        <p>You're about to delete the thing "{{ $thing->name }}".</p>
                        <p>This action cannot be reversed.</p>
                    </flux:text>
                </div>

                <div class="flex gap-2">
                    <flux:spacer />

                    <flux:modal.close>
                        <flux:button variant="ghost">Cancel</flux:button>
                    </flux:modal.close>

                    <flux:button type="button" variant="danger" wire:click="deleteThing({{ $thing->id }})">Delete Thing</flux:button>
                </div>
            </div>
        </flux:modal>
    @endforeach
</section>
