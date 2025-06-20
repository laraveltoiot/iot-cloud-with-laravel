<?php declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;
use Livewire\WithPagination;

final class UserManagement extends Component
{
    use WithPagination;

    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public ?int $editingUserId = null;

    public string $search = '';
    public string $sortBy = 'name';
    public string $sortDirection = 'asc';

    protected $queryString = [
        'search' => ['except' => ''],
        'sortBy' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
    ];

    /**
     * Sort the data by the given column.
     */
    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    /**
     * Get the users with pagination and sorting.
     */
    #[\Livewire\Attributes\Computed]
    public function users()
    {
        return User::query()
            ->when($this->search, function ($query, $search) {
                return $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(10);
    }

    /**
     * Render the component.
     */
    public function render(): View|Application|Factory|\Illuminate\View\View
    {
        return view('livewire.admin.user-management');
    }

    /**
     * Reset the form fields.
     */
    public function resetForm(): void
    {
        $this->reset(['name', 'email', 'password', 'password_confirmation', 'editingUserId']);
        $this->resetValidation();
    }

    /**
     * Create a new user.
     */
    public function createUser(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $this->resetForm();
        $this->dispatch('user-created');
        $this->dispatch('close-modal', 'create-user');
    }

    /**
     * Edit an existing user.
     */
    public function editUser(int $userId): void
    {
        $this->resetForm();

        $user = User::findOrFail($userId);
        $this->editingUserId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
    }

    /**
     * Update an existing user.
     */
    public function updateUser(int $userId): void
    {
        $user = User::findOrFail($userId);

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'confirmed', Password::defaults()],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if ($validated['password']) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        $this->resetForm();
        $this->dispatch('user-updated');
        $this->dispatch('close-modal', 'edit-user-' . $userId);
    }

    /**
     * Delete a user.
     */
    public function deleteUser(int $userId): void
    {
        $user = User::findOrFail($userId);
        $user->delete();

        $this->dispatch('user-deleted');
        $this->dispatch('close-modal', 'delete-user-' . $userId);
    }

}
