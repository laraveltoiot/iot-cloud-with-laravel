<section>
    <div class="flex justify-between items-center mb-4">
        <flux:heading size="lg">Device Management</flux:heading>
        <flux:modal.trigger name="create-device">
            <flux:button variant="primary" wire:click="createDevice">Add Device</flux:button>
        </flux:modal.trigger>
    </div>

    <flux:table :paginate="$this->devices">
        <flux:table.columns>
            <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')">Name</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'device_id'" :direction="$sortDirection" wire:click="sort('device_id')">Device ID</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'type'" :direction="$sortDirection" wire:click="sort('type')">Type</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'status'" :direction="$sortDirection" wire:click="sort('status')">Status</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'created_at'" :direction="$sortDirection" wire:click="sort('created_at')">Created</flux:table.column>
            <flux:table.column>Actions</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($this->devices as $device)
                <flux:table.row :key="$device->id">
                    <flux:table.cell>{{ $device->name }}</flux:table.cell>
                    <flux:table.cell>{{ $device->device_id }}</flux:table.cell>
                    <flux:table.cell>{{ $device->type ?? 'N/A' }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge size="sm" :color="$device->status === 'online' ? 'green' : ($device->status === 'offline' ? 'gray' : 'yellow')" inset="top bottom">
                            {{ ucfirst($device->status) }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>{{ $device->created_at->format('Y-m-d H:i') }}</flux:table.cell>
                    <flux:table.cell>
                        <div class="flex space-x-2">
                            <flux:modal.trigger :name="'edit-device-' . $device->id">
                                <flux:button variant="ghost" size="sm" icon="pencil" wire:click="editDevice({{ $device->id }})"></flux:button>
                            </flux:modal.trigger>
                            <flux:modal.trigger :name="'delete-device-' . $device->id">
                                <flux:button variant="ghost" size="sm" icon="trash"></flux:button>
                            </flux:modal.trigger>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>

    <!-- Create Device Modal -->
    <flux:modal name="create-device" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Add New Device</flux:heading>
                <flux:text class="mt-2">Enter the details for the new device.</flux:text>
            </div>

            <form wire:submit="saveDevice">
                <div class="space-y-4">
                    <flux:input label="Name" wire:model="name" placeholder="Device name" />
                    @error('name') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                    <flux:input label="Device ID" wire:model="device_id" placeholder="Unique device identifier" readonly disabled />
                    <div class="text-gray-500 text-sm mt-1">A unique device ID has been automatically generated.</div>
                    @error('device_id') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                    <flux:input label="Type" wire:model="type" placeholder="Device type (e.g., Arduino, ESP8266)" />
                    @error('type') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                    <flux:select label="Status" wire:model="status">
                        <option value="online">Online</option>
                        <option value="offline">Offline</option>
                        <option value="maintenance">Maintenance</option>
                    </flux:select>
                    @error('status') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                    <flux:textarea label="Metadata (JSON)" wire:model="metadata" placeholder='{"key": "value"}' />
                    @error('metadata') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                    <div class="flex justify-end space-x-2 pt-4">
                        <flux:modal.close>
                            <flux:button>Cancel</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary">Save Device</flux:button>
                    </div>
                </div>
            </form>
        </div>
    </flux:modal>

    <!-- Edit Device Modals -->
    @foreach ($this->devices as $device)
        <flux:modal :name="'edit-device-' . $device->id" class="md:w-96">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Edit Device</flux:heading>
                    <flux:text class="mt-2">Update the device details.</flux:text>
                </div>

                <form wire:submit="saveDevice">
                    <div class="space-y-4">
                        <flux:input label="Name" wire:model="name" placeholder="Device name" />
                        @error('name') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                        <flux:input label="Device ID" wire:model="device_id" placeholder="Unique device identifier" />
                        @error('device_id') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                        <flux:input label="Type" wire:model="type" placeholder="Device type (e.g., Arduino, ESP8266)" />
                        @error('type') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                        <flux:select label="Status" wire:model="status">
                            <option value="online">Online</option>
                            <option value="offline">Offline</option>
                            <option value="maintenance">Maintenance</option>
                        </flux:select>
                        @error('status') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                        <flux:textarea label="Metadata (JSON)" wire:model="metadata" placeholder='{"key": "value"}' />
                        @error('metadata') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror

                        <div class="flex justify-end space-x-2 pt-4">
                            <flux:modal.close>
                                <flux:button>Cancel</flux:button>
                            </flux:modal.close>
                            <flux:button type="submit" variant="primary">Update Device</flux:button>
                        </div>
                    </div>
                </form>
            </div>
        </flux:modal>
    @endforeach

    <!-- Delete Device Modals -->
    @foreach ($this->devices as $device)
        <flux:modal :name="'delete-device-' . $device->id" class="min-w-[22rem]">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Delete Device?</flux:heading>

                    <flux:text class="mt-2">
                        <p>You're about to delete the device "{{ $device->name }}".</p>
                        <p>This action cannot be reversed.</p>
                    </flux:text>
                </div>

                <div class="flex gap-2">
                    <flux:spacer />

                    <flux:modal.close>
                        <flux:button variant="ghost">Cancel</flux:button>
                    </flux:modal.close>

                    <flux:button type="button" variant="danger" wire:click="deleteDevice({{ $device->id }})">Delete Device</flux:button>
                </div>
            </div>
        </flux:modal>
    @endforeach
</section>
