<section class="w-full">
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-6 flex items-center justify-between">
                        <h2 class="text-xl font-semibold">{{ __('User Management') }}</h2>
                        <div class="flex items-center space-x-4">
                            <flux:input wire:model.live="search" placeholder="{{ __('Search users...') }}" type="search" class="w-64" />
                        </div>
                    </div>

                    <!-- Users Table -->
                    <div class="overflow-x-auto">
                        <flux:table :paginate="$this->users">
                            <flux:table.columns>
                                <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')">{{ __('Name') }}</flux:table.column>
                                <flux:table.column sortable :sorted="$sortBy === 'email'" :direction="$sortDirection" wire:click="sort('email')">{{ __('Email') }}</flux:table.column>
                                <flux:table.column sortable :sorted="$sortBy === 'created_at'" :direction="$sortDirection" wire:click="sort('created_at')">{{ __('Created At') }}</flux:table.column>
                                <flux:table.column>{{ __('Actions') }}</flux:table.column>
                            </flux:table.columns>

                            <flux:table.rows>
                                @forelse ($this->users as $user)
                                    <flux:table.row :key="$user->id">
                                        <flux:table.cell>{{ $user->name }}</flux:table.cell>
                                        <flux:table.cell>{{ $user->email }}</flux:table.cell>
                                        <flux:table.cell class="whitespace-nowrap">{{ $user->created_at->format('M d, Y') }}</flux:table.cell>
                                        <flux:table.cell>
                                            <div class="flex items-center justify-end space-x-2">
                                                <flux:modal.trigger name="edit-user-{{ $user->id }}">
                                                    <flux:button size="sm">
                                                        {{ __('Edit') }}
                                                    </flux:button>
                                                </flux:modal.trigger>
                                                <flux:modal.trigger name="delete-user-{{ $user->id }}">
                                                    <flux:button variant="danger" size="sm">
                                                        {{ __('Delete') }}
                                                    </flux:button>
                                                </flux:modal.trigger>
                                            </div>
                                        </flux:table.cell>
                                    </flux:table.row>

                                    <!-- Delete User Modal for each user -->
                                    <flux:modal name="delete-user-{{ $user->id }}" class="md:w-96">
                                        <div class="space-y-6">
                                            <div>
                                                <flux:heading size="lg">{{ __('Delete User') }}</flux:heading>
                                                <flux:text class="mt-2">{{ __('Are you sure you want to delete this user? This action cannot be undone.') }}</flux:text>
                                            </div>

                                            <div class="flex justify-end space-x-3">
                                                <flux:spacer />
                                                <flux:button x-on:click="$dispatch('close-modal', 'delete-user-{{ $user->id }}')">
                                                    {{ __('Cancel') }}
                                                </flux:button>
                                                <flux:button variant="danger" wire:click="deleteUser({{ $user->id }})">
                                                    {{ __('Delete User') }}
                                                </flux:button>
                                            </div>
                                        </div>
                                    </flux:modal>

                                    <!-- Edit User Modal for each user -->
                                    <flux:modal name="edit-user-{{ $user->id }}" class="md:w-96">
                                        <div class="space-y-6">
                                            <div>
                                                <flux:heading size="lg">{{ __('Edit User') }}</flux:heading>
                                                <flux:text class="mt-2">{{ __('Update user details.') }}</flux:text>
                                            </div>

                                            <div wire:key="edit-form-{{ $user->id }}">
                                                <flux:input wire:model="name" label="{{ __('Name') }}" placeholder="{{ __('Your name') }}" />
                                                <flux:input wire:model="email" label="{{ __('Email') }}" type="email" class="mt-4" />
                                                <flux:input wire:model="password" label="{{ __('Password') }}" type="password" class="mt-4" />
                                                <flux:input wire:model="password_confirmation" label="{{ __('Confirm Password') }}" type="password" class="mt-4" />
                                            </div>

                                            <div class="flex">
                                                <flux:spacer />
                                                <flux:button wire:click="editUser({{ $user->id }})" type="button" class="mr-2">
                                                    {{ __('Load User Data') }}
                                                </flux:button>
                                                <flux:button wire:click="updateUser({{ $user->id }})" type="submit" variant="primary">
                                                    {{ __('Save changes') }}
                                                </flux:button>
                                            </div>
                                        </div>
                                    </flux:modal>
                                @empty
                                    <flux:table.row>
                                        <flux:table.cell colspan="4" class="text-center py-4">
                                            {{ __('No users found.') }}
                                        </flux:table.cell>
                                    </flux:table.row>
                                @endforelse
                            </flux:table.rows>
                        </flux:table>
                    </div>

                    <!-- Create User Button -->
                    <div class="mt-8 flex justify-end">
                        <flux:modal.trigger name="create-user">
                            <flux:button variant="primary">
                                {{ __('Create New User') }}
                            </flux:button>
                        </flux:modal.trigger>
                    </div>

                    <!-- Create User Modal -->
                    <flux:modal name="create-user" class="md:w-96">
                        <div class="space-y-6">
                            <div>
                                <flux:heading size="lg">{{ __('Create New User') }}</flux:heading>
                                <flux:text class="mt-2">{{ __('Add a new user to the system.') }}</flux:text>
                            </div>

                            <form wire:submit="createUser" class="space-y-4">
                                <flux:input wire:model="name" label="{{ __('Name') }}" placeholder="{{ __('User name') }}" required />
                                <flux:input wire:model="email" label="{{ __('Email') }}" type="email" required />
                                <flux:input wire:model="password" label="{{ __('Password') }}" type="password" required />
                                <flux:input wire:model="password_confirmation" label="{{ __('Confirm Password') }}" type="password" required />

                                <div class="flex">
                                    <flux:spacer />
                                    <flux:button type="submit" variant="primary">
                                        {{ __('Create User') }}
                                    </flux:button>
                                </div>
                            </form>
                        </div>
                    </flux:modal>


                    <!-- Notifications -->
                    <div x-data="{ show: false, message: '' }"
                         x-on:user-created.window="show = true; message = 'User created successfully!'; setTimeout(() => show = false, 3000)"
                         x-on:user-updated.window="show = true; message = 'User updated successfully!'; setTimeout(() => show = false, 3000)"
                         x-on:user-deleted.window="show = true; message = 'User deleted successfully!'; setTimeout(() => show = false, 3000)"
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
    </div>
</section>
