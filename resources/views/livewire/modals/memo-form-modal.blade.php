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
                class="bg-white dark:bg-slate-900 rounded-md shadow-lg w-full max-w-[85vw] max-h-[90vh] overflow-y-auto border border-gray-100 dark:border-slate-700 p-6"
            >
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold dark:text-white">{{ $editingMemoId ? 'Edit Memo' : 'Upload Memo' }}</h3>
                    <button wire:click="closeModal" class="text-slate-400 hover:text-slate-700 dark:hover:text-white transition text-xl">&times;</button>
                </div>

                <form wire:submit="save" class="space-y-4">

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">

                        {{-- LEFT COLUMN --}}
                        <div class="space-y-5">
                            <div>
                                <label class="block font-medium mb-1 dark:text-white">Title</label>
                                <input type="text" wire:model.blur="title" class="w-full border rounded p-2 dark:bg-slate-800 dark:border-slate-700 dark:text-white">
                                @error('title') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block font-medium mb-1 dark:text-white">Memo No.</label>
                                    <input type="text" wire:model="memoNo" class="w-full border rounded p-2 dark:bg-slate-800 dark:border-slate-700 dark:text-white">
                                    @error('memoNo') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block font-medium mb-1 dark:text-white">Year</label>
                                    <input type="number" wire:model="year" class="w-full border rounded p-2 dark:bg-slate-800 dark:border-slate-700 dark:text-white">
                                    @error('year') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block font-medium mb-1 dark:text-white">Author</label>
                                <input type="text" wire:model="author" class="w-full border rounded p-2 dark:bg-slate-800 dark:border-slate-700 dark:text-white">
                                @error('author') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                            </div>

                            {{-- Superseded memos --}}
                            <div>
                                <label class="block font-medium mb-1 dark:text-white">Superseded Memo(s)</label>
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
                                        },
                                        onItemAdd: function(value, item) {
                                            this.setTextboxValue('');
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
                        </div>

                        {{-- RIGHT COLUMN --}}
                        <div class="space-y-5">

                            {{-- Companies --}}
                            <div>
                                <label class="inline-flex items-center mb-1">
                                    <input type="checkbox" wire:model.live="forAllCompanies">
                                    <span class="ml-2 font-medium dark:text-white">All Companies</span>
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

                            {{-- Departments --}}
                            <div>
                                <label class="inline-flex items-center mb-1">
                                    <input type="checkbox" id="allDepartmentsCheckbox" onchange="toggleAllDepartments(this.checked)">
                                    <span class="ml-2 font-medium dark:text-white">All Departments</span>
                                </label>

                                <div wire:ignore x-data x-init="
                                    window.departmentTomSelect = new TomSelect($refs.departmentSelect, {
                                        plugins: ['remove_button'],
                                        onChange: function(values) {
                                            $wire.set('selectedDepartments', values);
                                            document.getElementById('allDepartmentsCheckbox').checked = (values.length === {{ $departments->count() }});
                                        }
                                    });
                                    Livewire.on('set-department-values', (event) => {
                                        departmentTomSelect.clear(true);
                                        event.ids.forEach(id => departmentTomSelect.addItem(id, true));
                                    });
                                ">
                                    <select multiple x-ref="departmentSelect">
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('selectedDepartments') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                            </div>

                            {{-- Employee Ranks --}}
                            <div>
                                <label class="inline-flex items-center mb-1">
                                    <input type="checkbox" wire:model.live="forAllRanks">
                                    <span class="ml-2 font-medium dark:text-white">All Employee Ranks</span>
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
                            
                            {{-- Related memos --}}
                            <div>
                                <label class="block font-medium mb-1 dark:text-white">Interconnected Memo(s)</label>
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
                                        },
                                        onItemAdd: function(value, item) {
                                            this.setTextboxValue('');
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
                            </div>
                        </div>

                        <div
                            id="dropZone"
                            x-data
                            @dragover.prevent="$el.classList.add('border-indigo-500')"
                            @dragleave="$el.classList.remove('border-indigo-500')"
                            @drop.prevent="
                                $el.classList.remove('border-indigo-500');
                                const dt = new DataTransfer();
                                dt.items.add($event.dataTransfer.files[0]);
                                $refs.file.files = dt.files;
                                $refs.file.dispatchEvent(new Event('change', { bubbles: true }));
                            "
                        >
                            <label
                                id="dropZone"
                                for="memoFile"
                                class="
                                    block border-2 border-dashed rounded-xl
                                    border-slate-300 dark:border-slate-700
                                    py-8 px-10 text-center cursor-pointer
                                    transition
                                    hover:border-indigo-500
                                    hover:bg-slate-800/20
                                ">

                                <input
                                    x-ref="file"
                                    id="memoFile"
                                    type="file"
                                    wire:model="file"
                                    class="hidden"
                                    accept="application/pdf">

                               @if(!$file)
                                    <i class="ri-file-pdf-2-line text-5xl text-red-500"></i>

                                    <p class="mt-3 text-xl">Drag & Drop PDF</p>
                                    <p class="text-slate-400">or click to browse</p>
                                @endif

                                @if($file)
                                    <div class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-emerald-900/20 border border-emerald-700 text-green-400">
                                        <i class="ri-file-pdf-line text-red-500"></i>

                                        <span>{{ $file->getClientOriginalName() }}</span>

                                        <button
                                            type="button"
                                            wire:click="removeFile"
                                            class="text-red-400 hover:text-red-300 transition"
                                            title="Remove file">
                                            <i class="ri-close-circle-fill text-lg"></i>
                                        </button>
                                    </div>

                                @elseif($existingFileName)
                                    <div class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-slate-800 border border-slate-700 text-slate-300">
                                        <i class="ri-file-pdf-line text-red-500"></i>

                                        <span>{{ $existingFileName }}</span>
                                    </div>
                                @endif
                            </label>
                        </div>

                        <div class="flex justify-end gap-2 pt-4 border-t border-gray-100 dark:border-slate-700 mt-2">
                            <button type="button" wire:click="closeModal" class="px-4 py-2 rounded border hover:bg-gray-50 dark:hover:bg-slate-800 dark:text-white transition">Cancel</button>
                            <button type="submit" class="px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white transition" wire:loading.attr="disabled" wire:target="save">
                                {{ $editingMemoId ? 'Update' : 'Upload' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

<script>
document.addEventListener('livewire:init', () => {

    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('memoFile');

    ['dragenter','dragover'].forEach(event => {
        dropZone.addEventListener(event, e => {
            e.preventDefault();
            dropZone.classList.add(
                'border-indigo-500',
                'bg-slate-800/20'
            );
        });
    });

    ['dragleave','drop'].forEach(event => {
        dropZone.addEventListener(event, e => {
            e.preventDefault();
            dropZone.classList.remove(
                'border-indigo-500',
                'bg-slate-800/20'
            );
        });
    });

    dropZone.addEventListener('drop', e => {
        const files = e.dataTransfer.files;

        if (!files.length) return;

        fileInput.files = files;
        fileInput.dispatchEvent(new Event('change', { bubbles: true }));
    });

    Livewire.on('file-removed', () => {
        fileInput.value = '';
    });
});
</script>
<style>
    .ts-control {
        min-height: 42px !important;
        padding: 8px 12px !important;
    }

    .ts-wrapper.multi .ts-control {
        align-items: center;
    }
</style>