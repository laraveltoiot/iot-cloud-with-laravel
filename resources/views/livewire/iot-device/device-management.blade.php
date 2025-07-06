<section>
    <div class="mx-auto">
        <div class="overflow-hidden bg-white dark:bg-gray-800 sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div class="mb-6 flex items-center justify-between">
                    <h2 class="text-xl font-semibold">{{ __('Device Management') }}</h2>
                    <div class="flex items-center space-x-4">
                        <flux:input wire:model.live="search" placeholder="{{ __('Search devices...') }}" type="search" class="w-64" />
                    </div>
                </div>

                <!-- Devices Table -->
                <div class="overflow-x-auto">
                    <flux:table :paginate="$this->devices">
                        <flux:table.columns>
                            <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')">{{ __('Name') }}</flux:table.column>
                            <flux:table.column sortable :sorted="$sortBy === 'device_id'" :direction="$sortDirection" wire:click="sort('device_id')">{{ __('Device ID') }}</flux:table.column>
                            <flux:table.column sortable :sorted="$sortBy === 'type'" :direction="$sortDirection" wire:click="sort('type')">{{ __('Type') }}</flux:table.column>
                            <flux:table.column sortable :sorted="$sortBy === 'status'" :direction="$sortDirection" wire:click="sort('status')">{{ __('Status') }}</flux:table.column>
                            <flux:table.column sortable :sorted="$sortBy === 'created_at'" :direction="$sortDirection" wire:click="sort('created_at')">{{ __('Created At') }}</flux:table.column>
                            <flux:table.column>{{ __('Actions') }}</flux:table.column>
                        </flux:table.columns>

                        <flux:table.rows>
                            @forelse ($this->devices as $device)
                                <flux:table.row :key="$device->id">
                                    <flux:table.cell>{{ $device->name }}</flux:table.cell>
                                    <flux:table.cell>{{ $device->device_id }}</flux:table.cell>
                                    <flux:table.cell>{{ $device->type ?? '-' }}</flux:table.cell>
                                    <flux:table.cell>
                                        <flux:badge size="sm" :color="$device->status === 'online' ? 'success' : ($device->status === 'maintenance' ? 'warning' : 'danger')" inset="top bottom">
                                            {{ ucfirst($device->status) }}
                                        </flux:badge>
                                    </flux:table.cell>
                                    <flux:table.cell class="whitespace-nowrap">{{ $device->created_at->format('M d, Y') }}</flux:table.cell>
                                    <flux:table.cell>
                                        <div class="flex items-center justify-end space-x-2">
                                            <flux:modal.trigger name="edit-device-{{ $device->id }}">
                                                <flux:button size="sm" class="mr-2">
                                                    {{ __('Edit') }}
                                                </flux:button>
                                            </flux:modal.trigger>
                                            <flux:modal.trigger name="delete-device-{{ $device->id }}">
                                                <flux:button variant="danger" size="sm">
                                                    {{ __('Delete') }}
                                                </flux:button>
                                            </flux:modal.trigger>
                                        </div>
                                    </flux:table.cell>
                                </flux:table.row>

                                <!-- Delete Device Modal for each device -->
                                <flux:modal name="delete-device-{{ $device->id }}" class="md:w-96">
                                    <div class="space-y-6">
                                        <div>
                                            <flux:heading size="lg">{{ __('Delete Device') }}</flux:heading>
                                            <flux:text class="mt-2">{{ __('Are you sure you want to delete this device? This action cannot be undone.') }}</flux:text>
                                        </div>

                                        <div class="flex justify-end space-x-3">
                                            <flux:spacer />
                                            <flux:button wire:click="$dispatch('close-modal', 'delete-device-{{ $device->id }}')">
                                                {{ __('Cancel') }}
                                            </flux:button>
                                            <flux:button variant="danger" wire:click="deleteDevice({{ $device->id }})">
                                                {{ __('Delete Device') }}
                                            </flux:button>
                                        </div>
                                    </div>
                                </flux:modal>

                                <!-- Edit Device Modal for each device -->
                                <flux:modal name="edit-device-{{ $device->id }}" class="md:w-96" x-on:open="$wire.dispatch('modal-opened', { name: 'edit-device-{{ $device->id }}' })">
                                    <div class="space-y-6">
                                        <div>
                                            <flux:heading size="lg">{{ __('Edit Device') }}</flux:heading>
                                            <flux:text class="mt-2">{{ __('Update device details.') }}</flux:text>
                                        </div>

                                        <div wire:key="edit-form-{{ $device->id }}">
                                            <flux:input wire:model.defer="name" label="Name" placeholder="Device name" autocomplete="off" />
                                            <flux:input wire:model.defer="device_id" label="Device ID" placeholder="Unique device identifier" readonly variant="filled" autocomplete="off" />
                                            <flux:input wire:model.defer="type" label="Type" placeholder="e.g., Arduino, ESP8266" autocomplete="off" />
                                        </div>

                                        <div class="flex">
                                            <flux:spacer />
                                            <flux:button wire:click="updateDevice({{ $device->id }})" type="submit" variant="primary">
                                                {{ __('Save changes') }}
                                            </flux:button>
                                        </div>
                                    </div>
                                </flux:modal>
                            @empty
                                <flux:table.row>
                                    <flux:table.cell colspan="6" class="text-center py-4">
                                        {{ __('No devices found.') }}
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforelse
                        </flux:table.rows>
                    </flux:table>
                </div>

                <!-- Create Device Button -->
                <div class="mt-8 flex justify-end">
                    <flux:modal.trigger name="create-device">
                        <flux:button variant="primary">
                            {{ __('Create New Device') }}
                        </flux:button>
                    </flux:modal.trigger>
                </div>

                <!-- Create Device Modal -->
                <flux:modal name="create-device" class="md:w-96">
                    <div class="space-y-6">
                        <div>
                            <flux:heading size="lg">{{ __('Create New Device') }}</flux:heading>
                            <flux:text class="mt-2">{{ __('Add a new device to the system.') }}</flux:text>
                        </div>

                        <form wire:submit="createDevice" class="space-y-4">
                            <flux:input wire:model="name" label="Name" placeholder="Device name" required autocomplete="off" />
                            <flux:input wire:model="device_id" label="Device ID" placeholder="Unique device identifier" readonly variant="filled" autocomplete="off" />
                            <flux:input wire:model="type" label="Type" placeholder="e.g., Arduino, ESP8266" autocomplete="off" />

                            <div class="flex">
                                <flux:spacer />
                                <flux:button type="submit" variant="primary">
                                    {{ __('Create Device') }}
                                </flux:button>
                            </div>
                        </form>
                    </div>
                </flux:modal>

                <!-- Notifications -->
                <div x-data="{ show: false, message: '' }"
                     x-on:device-created.window="show = true; message = 'Device created successfully!'; setTimeout(() => show = false, 3000)"
                     x-on:device-updated.window="show = true; message = 'Device updated successfully!'; setTimeout(() => show = false, 3000)"
                     x-on:device-deleted.window="show = true; message = 'Device deleted successfully!'; setTimeout(() => show = false, 3000)"
                     x-show="show"
                     x-transition
                     class="fixed bottom-4 right-4 z-50">
                    <div dismissible>
                        <span x-text="message"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
