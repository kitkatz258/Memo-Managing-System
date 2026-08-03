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
                            <div wire:ignore x-data x-init="
                                let ts = new TomSelect($refs.companySelect, {
                                    plugins: ['remove_button'],
                                    onChange: function(values) {
                                        $wire.set('selectedCompanies', values);
                                        if (values.length === {{ $companies->count() }}) {
                                            let checkbox = document.querySelector('[wire\\:model\\.live=forAllCompanies]');
                                            checkbox.checked = true;
                                            checkbox.dispatchEvent(new Event('change'));
                                        }
                                    }
                                });
                                Livewire.on('set-company-values', (event) => {
                                    ts.clear(true);
                                    event.ids.forEach(id => ts.addItem(id, true));
                                });
                            ">
                                <select multiple x-ref="companySelect">
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}">{{ $company->name }} ({{ $company->code }})</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        @error('selectedCompanies') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    {{-- Categories --}}
                    <div>
                        <label class="inline-flex items-center mb-1">
                            <input type="checkbox" id="allCategoriesCheckbox" onchange="toggleAllCategories(this.checked)">
                            <span class="ml-2 font-medium">All Categories</span>
                        </label>

                        <div wire:ignore x-data x-init="
                            window.categoryTomSelect = new TomSelect($refs.categorySelect, {
                                plugins: ['remove_button'],
                                onChange: function(values) {
                                    
                                    $wire.set('selectedCategories', values);
                                    document.getElementById('allCategoriesCheckbox').checked = (values.length === {{ $categories->count() }});
                                }
                            });
                            Livewire.on('set-category-values', (event) => {
                                categoryTomSelect.clear(true);
                                event.ids.forEach(id => categoryTomSelect.addItem(id, true));
                            });
                        ">
                            <select multiple x-ref="categorySelect">
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('selectedCategories') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    {{-- Employee Ranks --}}
                    <div>
                        <label class="inline-flex items-center mb-1">
                            <input type="checkbox" wire:model.live="forAllRanks">
                            <span class="ml-2 font-medium">All Employee Ranks</span>
                        </label>

                        @if (!$forAllRanks)
                            <div wire:ignore x-data x-init="
                                let ts = new TomSelect($refs.rankSelect, {
                                    plugins: ['remove_button'],
                                    onChange: function(values) {
                                        $wire.set('selectedRanks', values);
                                        if (values.length === {{ $employeeRanks->count() }}) {
                                            let checkbox = document.querySelector('[wire\\:model\\.live=forAllRanks]');
                                            checkbox.checked = true;
                                            checkbox.dispatchEvent(new Event('change'));
                                        }
                                    }
                                });
                                Livewire.on('set-rank-values', (event) => {
                                    ts.clear(true);
                                    event.ids.forEach(id => ts.addItem(id, true));
                                });
                            ">
                                <select multiple x-ref="rankSelect">
                                    @foreach($employeeRanks as $rank)
                                        <option value="{{ $rank->id }}">{{ $rank->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        @error('selectedRanks') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    {{-- Superseded memos --}}
                    <div>
                        <label class="block font-medium mb-1">Superseded Memo(s)</label>
                        <div wire:ignore x-data x-init="
                            window.supersededTomSelect = new TomSelect($refs.supersededSelect, {
                                plugins: ['remove_button'],
                                valueField: 'id',
                                labelField: 'title',
                                searchField: 'title',
                                create: false,
                                placeholder: 'Search memos to mark as superseded...',
                                load: function(query, callback) {
                                    fetch(`/memos/search-picker?query=${encodeURIComponent(query)}&exclude=${$wire.editingMemoId ?? ''}`)
                                        .then(res => res.json())
                                        .then(json => callback(json))
                                        .catch(() => callback());
                                },
                                onChange: function(values) {
                                    $wire.set('selectedSupersededMemos', values);
                                }
                            });
                            Livewire.on('set-superseded-values', (event) => {
                                supersededTomSelect.clear(true);
                                supersededTomSelect.clearOptions();
                                event.items.forEach(item => {
                                    supersededTomSelect.addOption(item);
                                    supersededTomSelect.addItem(item.id, true);
                                });
                            });
                        ">
                            <select multiple x-ref="supersededSelect"></select>
                        </div>
                    </div>

                    {{-- Related memos --}}
                    <div>
                        <label class="block font-medium mb-1">Interconnected Memo(s)</label>
                        <div wire:ignore x-data x-init="
                            window.relatedTomSelect = new TomSelect($refs.relatedSelect, {
                                plugins: ['remove_button'],
                                valueField: 'id',
                                labelField: 'title',
                                searchField: 'title',
                                create: false,
                                placeholder: 'Search memos to interconnect...',
                                load: function(query, callback) {
                                    fetch(`/memos/search-picker?query=${encodeURIComponent(query)}&exclude=${$wire.editingMemoId ?? ''}`)
                                        .then(res => res.json())
                                        .then(json => callback(json))
                                        .catch(() => callback());
                                },
                                onChange: function(values) {
                                    $wire.set('selectedRelatedMemos', values);
                                }
                            });
                            Livewire.on('set-related-values', (event) => {
                                relatedTomSelect.clear(true);
                                relatedTomSelect.clearOptions();
                                event.items.forEach(item => {
                                    relatedTomSelect.addOption(item);
                                    relatedTomSelect.addItem(item.id, true);
                                });
                            });
                        ">
                            <select multiple x-ref="relatedSelect"></select>
                        </div>
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