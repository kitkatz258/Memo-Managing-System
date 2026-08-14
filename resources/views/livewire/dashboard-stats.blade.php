<div>
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold dark:text-white">
            Dashboard
        </h2>
    </div>

    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">

        {{-- Total Memos --}}
        <a href="{{ route('memos.index') }}"
           class="p-6 bg-white dark:bg-slate-900 rounded-md shadow-sm hover:shadow-md transition">

            <i class="ri-file-list-3-line text-3xl text-primary"></i>

            <h3 class="text-lg font-semibold mt-3">
                Total Memos
            </h3>

            <p class="text-3xl font-bold mt-2 dark:text-white">
                {{ $totalMemos }}
            </p>

            <p class="text-sm text-slate-400 mt-2">
                View all memos →
            </p>
        </a>


        {{-- Archived Memos --}}
        <a href="{{ route('memos.archived') }}"
           class="p-6 bg-white dark:bg-slate-900 rounded-md shadow-sm hover:shadow-md transition">

            <i class="ri-archive-line text-3xl text-amber-500"></i>

            <h3 class="text-lg font-semibold mt-3">
                Archived Memos
            </h3>

            <p class="text-3xl font-bold mt-2 dark:text-white">
                {{ $archivedMemos }}
            </p>

            <p class="text-sm text-slate-400 mt-2">
                View archived memos →
            </p>
        </a>


        {{-- Upload --}}
        <button
            onclick="Livewire.dispatch('open-memo-modal')"
            class="p-6 bg-white dark:bg-slate-900 rounded-md
                   shadow-sm hover:shadow-md transition
                   text-start w-full"
        >
            <i class="ri-upload-2-line text-3xl text-primary"></i>

            <h3 class="text-lg font-semibold mt-3">
                Upload Memo
            </h3>

            <p class="text-slate-400 text-sm mt-1">
                Add a new memo to the system
            </p>
        </button>

    </div>
</div>