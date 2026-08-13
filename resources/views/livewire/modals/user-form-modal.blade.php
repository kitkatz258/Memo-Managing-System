<div>
    @if ($showModal)
        <div
            x-data="{ show: false }"
            x-init="setTimeout(() => show = true, 10)"
            x-show="show"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-90"
            class="fixed inset-0 z-[1000] flex items-center justify-center bg-black/50"
        >
            <div
                x-show="show"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                class="bg-white dark:bg-slate-900 rounded-md shadow-lg w-full max-w-[600px] max-h-[90vh] overflow-y-auto border border-gray-100 dark:border-slate-700 p-6"
            >
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold dark:text-white">{{ $editingUserId ? 'Edit User' : 'Add User' }}</h3>
                    <button wire:click="closeModal" class="text-slate-400 hover:text-slate-700 dark:hover:text-white transition text-xl">&times;</button>
                </div>

                <form wire:submit="save" class="space-y-4">

                    <div>
                        <label class="block font-medium mb-1 dark:text-white">Full Name</label>
                        <input type="text" wire:model="name" class="w-full border rounded p-2 dark:bg-slate-800 dark:border-slate-700 dark:text-white">
                        @error('name') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-medium mb-1 dark:text-white">Username</label>
                            <input type="text" wire:model="username" class="w-full border rounded p-2 dark:bg-slate-800 dark:border-slate-700 dark:text-white">
                            @error('username') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block font-medium mb-1 dark:text-white">
                                Password {{ $editingUserId ? '(leave blank to keep current)' : '' }}
                            </label>
                            <input type="password" wire:model="password" class="w-full border rounded p-2 dark:bg-slate-800 dark:border-slate-700 dark:text-white">
                            @error('password') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block font-medium mb-1 dark:text-white">Role</label>
                        <select wire:model="role" class="w-full border rounded p-2 dark:bg-slate-800 dark:border-slate-700 dark:text-white">
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                        @error('role') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block font-medium mb-1 dark:text-white">Employee Rank</label>
                        <select wire:model="employeeRankId" class="w-full border rounded p-2 dark:bg-slate-800 dark:border-slate-700 dark:text-white">
                            <option value="">— Select Employee Rank —</option>
                            @foreach($employeeRanks as $rank)
                                <option value="{{ $rank->id }}">{{ $rank->name }}</option>
                            @endforeach
                        </select>
                        @error('employeeRankId') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block font-medium mb-1 dark:text-white">Company</label>
                        <select wire:model="companyId" class="w-full border rounded p-2 dark:bg-slate-800 dark:border-slate-700 dark:text-white">
                            <option value="">— Select Company —</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->name }} ({{ $company->code }})</option>
                            @endforeach
                        </select>
                        @error('companyId') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block font-medium mb-1 dark:text-white">Department</label>
                        <select wire:model="departmentId" class="w-full border rounded p-2 dark:bg-slate-800 dark:border-slate-700 dark:text-white">
                            <option value="">— Select Department —</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                        @error('departmentId') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 rounded border dark:border-slate-700 dark:text-white">
                            Cancel
                        </button>
                        <button type="submit" wire:loading.attr="disabled" class="px-4 py-2 rounded bg-primary text-white">
                            <span wire:loading.remove>Save</span>
                            <span wire:loading>Saving...</span>
                        </button>
                    </div>

                </form>
            </div>
        </div>
    @endif
</div>