@extends('layouts.admin')

@section('content')
    <h1 class="text-2xl font-bold mb-4 dark:text-white">Memos</h1>

    <div class="mb-4 flex items-center gap-3 flex-wrap">
        <label class="font-medium">Filter by Company:</label>
        <select id="companyFilter" class="border rounded-md p-2 dark:bg-slate-800 dark:border-slate-700 dark:text-white">
            <option value="">All</option>
            @foreach ($companies as $company)
                <option value="{{ $company->id }}">{{ $company->name }} ({{ $company->code }})</option>
            @endforeach
        </select>

        <label class="font-medium">Category:</label>
        <select id="categoryFilter" class="border rounded-md p-2 dark:bg-slate-800 dark:border-slate-700 dark:text-white">
            <option value="">All</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>

        <label class="font-medium">Employee Rank:</label>
        <select id="rankFilter" class="border rounded-md p-2 dark:bg-slate-800 dark:border-slate-700 dark:text-white">
            <option value="">All</option>
            @foreach($employeeRanks as $rank)
                <option value="{{ $rank->id }}">{{ $rank->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-md shadow-sm dark:shadow-gray-700 border border-gray-100 dark:border-slate-700 p-6">
        <div class="overflow-x-auto">
            <table id="memosTable" class="w-full border-collapse">
                <thead>
                    <tr>
                        <th>Memo No.</th>
                        <th>Title</th>
                        <th>Company</th>
                        <th>Year</th>
                        <th>Author</th>
                        <th>Category</th>
                        <th>Employee Rank</th>
                        <th>Superseded</th>
                        <th>Related</th>
                        <th>Uploaded</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    {{-- <div x-data x-show="$store.pdfViewer.open" x-cloak class="fixed inset-0 z-[1000] flex items-center justify-center bg-black/50" style="display: none;">
        <div class="bg-white dark:bg-slate-900 rounded-md shadow-lg w-full max-w-4xl h-[85vh] flex flex-col border border-gray-100 dark:border-slate-700">

            <div class="flex justify-between items-center p-4 border-b border-gray-100 dark:border-slate-700">
                <div>
                    <h3 class="font-bold dark:text-white" x-text="$store.pdfViewer.title"></h3>
                    <p class="text-sm text-slate-400">Memo No. <span x-text="$store.pdfViewer.memoNo"></span></p>
                </div>
                <button @click="$store.pdfViewer.close()" class="text-slate-400 hover:text-slate-700 text-xl">&times;</button>
            </div>

            <iframe id="pdfFrame" :src="$store.pdfViewer.url" class="flex-1 w-full"></iframe>
        </div>
    </div> --}}

    <div id="pdfModal" class="fixed inset-0 z-[1000] hidden items-center justify-center bg-black/50">
        <div class="bg-white dark:bg-slate-900 rounded-md shadow-lg w-full max-w-4xl h-[90vh] flex flex-col border border-gray-100 dark:border-slate-700 overflow-hidden">

            <div class="flex justify-between items-start p-4 border-b border-gray-100 dark:border-slate-700">
                <div>
                    <p class="text-sm text-slate-400">Memo No. <span id="pdfMemoNo" class="font-semibold text-slate-700 dark:text-slate-200"></span></p>
                    <h3 id="pdfTitle" class="font-bold text-lg dark:text-white mt-1"></h3>
                    <p id="pdfCategory" class="text-sm text-slate-400"></p>
                </div>
                <button id="closePdfModal" class="text-slate-400 hover:text-slate-700 text-xl">&times;</button>
            </div>

            <div class="grid grid-cols-12 h-[70vh]">
                <!-- PDF -->
                <div class="col-span-9 border-r border-slate-700">
                    <iframe
                        id="pdfFrame"
                        :src="$store.pdfViewer.url"
                        class="w-full h-full">
                    </iframe>
                </div>

                <!-- Sidebar -->
                <div class="col-span-3 flex flex-col">

                    <!-- Scrollable Details -->
                    <div class="flex-1 overflow-y-auto p-5 space-y-5">

                        <!-- Company -->
                        <div>
                            <p class="text-xs uppercase text-slate-400">Company</p>
                            <div id="modalCompany"></div>
                        </div>

                        <!-- Categories -->
                        <div>
                            <p class="text-xs uppercase text-slate-400">Categories</p>
                            <div id="modalCategories" class="flex flex-wrap gap-2"></div>
                        </div>

                        <!-- Employee Rank -->
                        <div>
                            <p class="text-xs uppercase text-slate-400">Employee Rank</p>
                            <div id="modalRanks"></div>
                        </div>

                        <!-- Author -->
                        <div>
                            <p class="text-xs uppercase text-slate-400">Author</p>
                            <p id="modalAuthor"></p>
                        </div>

                        <!-- Year -->
                        <div>
                            <p class="text-xs uppercase text-slate-400">Year</p>
                            <p id="modalYear"></p>
                        </div>

                        <!-- Uploaded -->
                        <div>
                            <p class="text-xs uppercase text-slate-400">Uploaded</p>
                            <p id="modalUploaded"></p>
                        </div>

                        <!-- Related -->
                        <div>
                            <p class="text-xs uppercase text-slate-400">Related</p>
                            <div id="modalRelated" class="flex flex-wrap gap-2"></div>
                        </div>

                        <!-- Superseded -->
                        <div>
                            <p class="text-xs uppercase text-slate-400">Superseded</p>
                            <div id="modalSuperseded" class="flex flex-wrap gap-2"></div>
                        </div>

                        <!-- Superseded By -->
                        <div>
                            <p class="text-xs uppercase text-slate-400">Superseded By</p>
                            <div id="modalSupersededBy" class="flex flex-wrap gap-2"></div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="border-t border-slate-700 p-4 space-y-2">

                        <a id="downloadBtn"
                        class="w-full flex justify-center rounded-lg bg-indigo-600 hover:bg-indigo-700 py-2 text-white">

                            <i class="ri-download-line mr-2"></i>
                            Download
                        </a>

                        <button id="editBtn"
                            class="w-full rounded-lg bg-blue-600 hover:bg-blue-700 py-2 text-white">
                            Edit
                        </button>

                        <button id="archiveBtn"
                            class="w-full rounded-lg bg-red-600 hover:bg-red-700 py-2 text-white">
                            Archive
                        </button>

                        <button
                            @click="$store.pdfViewer.close()"
                            class="w-full rounded-lg border border-slate-600 py-2">

                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(auth()->user()->role === 'admin')
        <livewire:modals.memo-form-modal />
    @endif
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
<script>
    let table = $('#memosTable').DataTable({
        processing: true,
        serverSide: true,

        autoWidth: false,
        scrollX: true,
        dom: '<"flex justify-between items-center"l>rt<"flex justify-between items-center"ip>',
        ajax: {
            url: "{{ route('memos.index') }}",
            data: function (d) {
                d.company_id = $('#companyFilter').val();
                d.category_id = $('#categoryFilter').val();
                d.rank_id = $('#rankFilter').val();
            }
        },
        columns: [
            { data: 'memo_no_link', name: 'memo_no' },
            { data: 'title', name: 'title' },
            { data: 'company_list', name: 'company_list', orderable: false, searchable: false },
            { data: 'year', name: 'year' },
            { data: 'author', name: 'author' },
            { data: 'category_list', name: 'category_list', orderable: false, searchable: false },
            { data: 'rank_list', name: 'rank_list', orderable: false, searchable: false },
            { data: 'superseded_list', name: 'superseded_list', orderable: false, searchable: false },
            { data: 'related_list', name: 'related_list', orderable: false, searchable: false },
            { data: 'created_at_formatted', name: 'created_at' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false },
        ],
    });

    let searchTimeout;
    $('#globalSearch').on('keyup', function () {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            table.search(this.value).draw();
        }, 300);
    });

    $('#companyFilter, #categoryFilter, #rankFilter').on('change', function () {
        table.ajax.reload();
    });

    document.addEventListener('livewire:init', () => {
        Livewire.on('memo-saved', () => {
            table.ajax.reload(null, false);
        });
    });

    function archiveMemo(id) {
        Swal.fire({
            title: 'Archive this memo?',
            text: 'It will be moved to Archives and hidden from users.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Archive',
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/memos/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    }
                }).then(res => res.json()).then(() => {
                    table.ajax.reload(null, false);
                    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Memo archived', showConfirmButton: false, timer: 2000 });
                });
            }
        });
    }

    function renderMemoLinks(containerId, items){
        const el = $('#' + containerId);
        el.empty();

        if(!items || items.length === 0){
            el.append('<li class="text-slate-400">—</li>');
            return;
        }
        items.forEach(item => {
            el.append(`
            <li>
                <a href="#"
                    class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-xs font-medium open-related-memo"
                    data-id="${item.id}"
                >
                    <i class="ri-file-text-line"></i>
                    <strong>${item.memo_no}</strong>
                </a>
            </li>
            `);
        })
    }

    function openPdfViewer(id){
        fetch(`/memos/${id}/details`)
            .then(res => res.json())
            .then(data => {
                $('#pdfTitle').text(data.title);
                $('#pdfMemoNo').text(data.memo_no);
                $('#pdfCategory').text(data.category);
                $('#pdfCompany').text(data.company);
                $('#pdfAuthor').text(data.author);
                $('#pdfYear').text(data.year);
                $('#pdfFrame').attr('src', `/memos/${id}/view`);
                $('#pdfDownload').attr('href', `/memos/${id}/download`);
                $('#pdfModal').data('memo-id', id);

                renderMemoLinks('pdfRelated', data.related);
                renderMemoLinks('pdfSuperseded', data.superseded);
                renderMemoLinks('pdfSupersededBy', data.superseded_by);

                $('#pdfModal').removeClass('hidden').addClass('flex');
            });           
    }

    $(document).on('click', '.view-pdf-btn', function(){
        openPdfViewer($(this).data('id'));
    });

    $(document).on('click', '.memo-pill', function () {
        openPdfViewer($(this).data('id'));
    });

    $(document).on('click', '.open-related-memo', function(e){
        e.preventDefault();
        openPdfViewer($(this).data('id'));
    });

    $(document).on('click', '#pdfEdit', function (e) {
        e.preventDefault();
        e.stopPropagation();

        const id = $('#pdfModal').data('memo-id');
        $('#closePdfModal').click();

        Livewire.dispatch('open-memo-modal-edit', {
            memoId: id
        });
    });

    $('#pdfArchive').on('click', function () {
        const id = $('#pdfModal').data('memo-id');
        $('#closePdfModal').click();
        archiveMemo(id);
    });

    $('#closePdfModal').on('click', function(){
        $('#pdfModal').removeClass('flex').addClass('hidden');
        $('#pdfFrame').attr('src', '');
    });

    $('#pdfModal').on('click', function(e){
        if(e.target === this){
            $('#closePdfModal').click();
        }
    });
</script>
@endpush

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
<style>
    table.dataTable thead th {
        font-weight: 600;
        font-size: 0.8125rem;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        color: #64748b;
        padding: 0.85rem 1rem;
        border-bottom: 2px solid #e2e8f0;
    }
    .dark table.dataTable thead th {
        color: #cbd5e1;
        border-bottom-color: #475569;
    }

    table.dataTable tbody td {
        vertical-align: middle;
        line-height: 1.35;
        padding: 0.9rem 1rem;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.9rem;
    }
    .dark table.dataTable tbody td {
        color: #f1f5f9;
        border-bottom-color: #334155;
    }
        
    .view-pdf-btn {
    cursor: pointer;
    }

    .view-pdf-btn:hover {
        cursor: pointer;
    }

    table.dataTable tbody tr:hover { background-color: #f8fafc; }
    .dark table.dataTable tbody tr:hover { background-color: #1e293b; }

    table.dataTable tbody tr:last-child td { border-bottom: none; }

    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter,
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate {
        margin-bottom: 1rem;
        color: #64748b;
        font-size: 0.875rem;
    }
    .dark .dataTables_wrapper .dataTables_length,
    .dark .dataTables_wrapper .dataTables_filter,
    .dark .dataTables_wrapper .dataTables_info,
    .dark .dataTables_wrapper .dataTables_paginate {
        color: #94a3b8;
    }

    .dataTables_filter input,
    .dataTables_length select {
        border: 1px solid #e2e8f0;
        border-radius: 0.375rem;
        padding: 0.375rem 0.75rem;
        margin-left: 0.5rem;
        background-color: white;
    }
    .dark .dataTables_filter input,
    .dark .dataTables_length select {
        background-color: #1e293b;
        color: white;
        border-color: #334155;
    }

    .dataTables_paginate .paginate_button {
        padding: 0.375rem 0.75rem;
        margin: 0 0.125rem;
        border-radius: 0.375rem;
        cursor: pointer;
    }
    .dark .dataTables_paginate .paginate_button { color: #e2e8f0 !important; }
    .dataTables_paginate .paginate_button.current {
        background: #4f39f6 !important;
        color: white !important;
        border: none !important;
    }
    .dataTables_paginate .paginate_button.disabled { opacity: 0.4; cursor: default; }
</style>
@endpush