<div>
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold dark:text-white">
            Dashboard
        </h2>

        <button
            wire:click="$refresh"
            wire:loading.attr="disabled"
            class="inline-flex items-center gap-2 px-4 py-2
                   bg-white dark:bg-slate-900
                   border border-slate-200 dark:border-slate-700
                   rounded-md shadow-sm
                   text-sm font-medium
                   text-slate-700 dark:text-slate-200
                   hover:bg-slate-50 dark:hover:bg-slate-800
                   transition"
        >
            <i
                class="ri-refresh-line"
                wire:loading.class="animate-spin"
                wire:target="$refresh"
            ></i>

            <span wire:loading.remove wire:target="$refresh">
                Refresh Dashboard
            </span>

            <span wire:loading wire:target="$refresh">
                Refreshing...
            </span>
        </button>
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


    {{-- Chart --}}
    <div class="mt-8 bg-white dark:bg-slate-900 rounded-md shadow-sm p-6">
        <h3 class="text-lg font-semibold mb-4 dark:text-white">
            Memo Uploads ({{ now()->year }})
        </h3>

        <canvas id="uploadsChart" height="100"></canvas>
    </div>


    <script>
        document.addEventListener('livewire:init', () => {

            function initializeUploadsChart() {

                const canvas = document.getElementById('uploadsChart');

                if (!canvas) {
                    return;
                }

                if (window.uploadsChart) {
                    window.uploadsChart.destroy();
                }

                window.uploadsChart = new Chart(canvas, {
                    type: 'line',

                    data: {
                        labels: @json($labels),

                        datasets: [{
                            label: 'Uploads',
                            data: @json($data),
                            borderWidth: 5
                        }]
                    },

                    options: {
                        responsive: true,

                        plugins: {
                            legend: {
                                display: false
                            }
                        },

                        scales: {
                            y: {
                                beginAtZero: true,

                                ticks: {
                                    precision: 0
                                }
                            }
                        }
                    }
                });
            }


            initializeUploadsChart();


            Livewire.hook('morph.updated', () => {
                initializeUploadsChart();
            });

        });
    </script>
</div>