<div>
    @if ($showModal)
        <div class="fixed inset-0 z-[1000] flex items-center justify-center bg-black/50">
            <div class="bg-white dark:bg-slate-900 rounded-md shadow-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto border border-gray-100 dark:border-slate-700 p-6">

                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold">{{ $editingMemoId ? 'Edit Memo' : 'Upload Memo' }}</h3>
                    <button wire:click="closeModal" class="text-slate-400 hover:text-slate-700">&times;</button>
                </div>

                <form wire:submit="save" class="space-y-4">

                    <div>
                        <label class="block font-medium mb-1">Title</label>
                        <input type="text" wire:model="title" class="w-full border rounded p-2">
                        @error('title') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-medium mb-1">Memo No.</label>
                            <input type="text" wire:model="memoNo" class="w-full border rounded p-2">
                        </div>
                        <div>
                            <label class="block font-medium mb-1">Year</label>
                            <input type="number" wire:model="year" class="w-full border rounded p-2">
                            @error('year') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block font-medium mb-1">Author</label>
                        <input type="text" wire:model="author" class="w-full border rounded p-2">
                    </div>

                    <div>
                        <label class="block font-medium mb-1">PDF File @if($editingMemoId)(leave blank to keep current file)@endif</label>
                        @if ($existingFileName)
                            <p class="text-sm text-slate-500 mb-1">Current: {{ $existingFileName }}</p>
                        @endif
                        <input type="file" wire:model="file" accept="application/pdf" class="w-full border rounded p-2">
                        @error('file') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                        <div wire:loading wire:target="file" class="text-sm text-slate-400">Uploading...</div>
                    </div>

                    {{-- Companies --}}
                    <div>
                        <label class="inline-flex items-center mb-1">
                            <input type="checkbox" wire:model.live="forAllCompanies">
                            <span class="ml-2 font-medium">All Companies</span>
                        </label>
                        @if (!$forAllCompanies)
                            <div class="grid grid-cols-2 gap-1">
                                @foreach ($companies as $company)
                                    <label class="flex items-center">
                                        <input type="checkbox" wire:model="selectedCompanies" value="{{ $company->id }}">
                                        <span class="ml-2">{{ $company->name }} ({{ $company->code }})</span>
                                    </label>
                                @endforeach
                            </div>
                        @endif
                        @error('selectedCompanies') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    {{-- Categories --}}
                    <div>
                        <label class="inline-flex items-center mb-1">
                            <input type="checkbox" wire:model.live="forAllCategories">
                            <span class="ml-2 font-medium">All Categories</span>
                        </label>
                        @if (!$forAllCategories)
                            <div class="grid grid-cols-3 gap-1">
                                @foreach ($categories as $category)
                                    <label class="flex items-center">
                                        <input type="checkbox" wire:model="selectedCategories" value="{{ $category->id }}">
                                        <span class="ml-2">{{ $category->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        @endif
                        @error('selectedCategories') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    {{-- Employee Ranks --}}
                    <div>
                        <label class="inline-flex items-center mb-1">
                            <input type="checkbox" wire:model.live="forAllRanks">
                            <span class="ml-2 font-medium">All Employee Ranks</span>
                        </label>
                        @if (!$forAllRanks)
                            <div class="grid grid-cols-3 gap-1">
                                @foreach ($employeeRanks as $rank)
                                    <label class="flex items-center">
                                        <input type="checkbox" wire:model="selectedRanks" value="{{ $rank->id }}">
                                        <span class="ml-2">{{ $rank->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        @endif
                        @error('selectedRanks') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    {{-- Superseded memos --}}
                    <div>
                        <label class="block font-medium mb-1">Superseded Memo(s)</label>
                        <select wire:model="selectedSupersededMemos" multiple class="w-full border rounded p-2 h-28">
                            @foreach ($existingMemos as $m)
                                <option value="{{ $m->id }}">{{ $m->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Related memos --}}
                    <div>
                        <label class="block font-medium mb-1">Interconnected Memo(s)</label>
                        <select wire:model="selectedRelatedMemos" multiple class="w-full border rounded p-2 h-28">
                            @foreach ($existingMemos as $m)
                                <option value="{{ $m->id }}">{{ $m->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 rounded border">Cancel</button>
                        <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white" wire:loading.attr="disabled" wire:target="save">
                            {{ $editingMemoId ? 'Update' : 'Upload' }}
                        </button>
                    </div>

                </form>
            </div>
        </div>
    @endif
</div>