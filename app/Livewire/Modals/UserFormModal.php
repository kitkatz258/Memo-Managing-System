<?php

namespace App\Livewire\Modals;

use App\Models\Company;
use App\Models\Department;
use App\Models\EmployeeRank;
use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;

class UserFormModal extends Component
{
    public bool $showModal = false;
    public ?int $editingUserId = null;

    public string $name = '';
    public string $username = '';
    public string $password = '';
    public string $role = 'user';

    public ?int $companyId = null;
    public ?int $departmentId = null;
    public ?int $employeeRankId = null;

    public $companies = [];
    public $departments = [];
    public $employeeRanks = [];

    public function mount()
    {
        $this->companies = Company::all();
        $this->departments = Department::all();
        $this->employeeRanks = EmployeeRank::all();
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'username')->ignore($this->editingUserId),
            ],
            'password' => $this->editingUserId ? 'nullable|string|min:6' : 'required|string|min:6',
            'role' => 'required|in:admin,user',
            'companyId' => 'required|exists:companies,id',
            'departmentId' => 'required|exists:departments,id',
            'employeeRankId' => 'required|exists:employee_ranks,id',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'companyId' => 'company',
            'departmentId' => 'department',
            'employeeRankId' => 'rank',
        ];
    }

    #[On('open-user-modal')]
    public function openCreate()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    #[On('edit-user')]
    public function edit($userId)
    {
        $this->resetForm();

        $user = User::with(['companies', 'departments'])->findOrFail($userId);

        $this->editingUserId = $user->id;
        $this->name = $user->name;
        $this->username = $user->username;
        $this->role = $user->role;
        $this->employeeRankId = $user->employee_rank_id;
        $this->companyId = $user->companies->pluck('id')->first();
        $this->departmentId = $user->departments->pluck('id')->first();

        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'username' => $this->username,
            'role' => $this->role,
            'employee_rank_id' => $this->employeeRankId,
        ];

        if ($this->password !== '') {
            $data['password'] = $this->password;
        }

        if ($this->editingUserId) {
            $user = User::findOrFail($this->editingUserId);
            $user->update($data);
        } else {
            $user = User::create($data);
        }

        $user->companies()->sync([$this->companyId]);
        $user->departments()->sync([$this->departmentId]);

        $this->showModal = false;
        $this->dispatch('user-saved');
        session()->flash('success', 'User saved successfully.');
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    private function resetForm()
    {
        $this->editingUserId = null;
        $this->name = '';
        $this->username = '';
        $this->password = '';
        $this->role = 'user';
        $this->companyId = null;
        $this->departmentId = null;
        $this->employeeRankId = null;
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.modals.user-form-modal');
    }
}