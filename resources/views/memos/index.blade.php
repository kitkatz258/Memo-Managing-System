@extends('layouts.admin')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Memos</h1>

    <div class="mb-4 flex items-center gap-3">
        <label class="font-medium">Filter by Company:</label>
        <select id="companyFilter" class="border rounded p-2 dark:bg-slate800 dark:border-slate-700 dark:text-white">
            <option value="">All</option>
            @foreach ($companies as $company)
                <option value="{{ $company->id }}">{{ $company->name }} ({{ $company->code }})</option>
            @endforeach
        </select>

        <label class="font-medium">Category:</label>
        <select id="categoryFilter" class="border rounded p-2 dark:bg-slate800 dark:border-slate-700 dark:text-white">
            <option value="">All</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>

        <label class="font-medium">Employee Rank:</label>
        <select id="rankFilter" class="border rounded p-2 dark:bg-slate800 dark:border-slate-700 dark:text-white">
            <option value="">All</option>
            @foreach($employeeRanks as $rank)
                <option value="{{ $rank->id }}">{{ $rank->name }}</option>
            @endforeach
        </select>
    </div>

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

    @if(auth()->user()->role === 'admin')
        <livewire:modals.memo-form-modal />
    @endif
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script>
    let table = $('#memosTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('memos.index') }}",
            data: function (d) {
                d.company_id = $('#companyFilter').val();
                d.category_id = $('#categoryFilter').val();
                d.rank_id = $('#rankFilter').val();
            }
        },
        columns: [
            { data: 'memo_no', name: 'memo_no' },
            { data: 'title_link', name: 'title' },
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
</script>
@endpush

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
<style>
    #memosTable_wrapper { font-family: inherit; }
    table.dataTable thead th {
        background-color: #f8fafc;
        font-weight: 600;
        padding: 0.75rem 1rem;
        border-bottom: 2px solid #e2e8f0;
    }
    table.dataTable tbody td {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #f1f5f9;
    }
    table.dataTable tbody tr:hover { background-color: #f8fafc; }
    .dataTables_filter input,
    .dataTables_length select {
        border: 1px solid #e2e8f0;
        border-radius: 0.375rem;
        padding: 0.375rem 0.75rem;
        margin-left: 0.5rem;
    }
    .dataTables_paginate .paginate_button {
        padding: 0.375rem 0.75rem;
        margin: 0 0.125rem;
        border-radius: 0.375rem;
        cursor: pointer;
    }
    .dataTables_paginate .paginate_button.current {
        background: #4f39f6 !important;
        color: white !important;
        border: none !important;
    }
    .dark table.dataTable thead th {
        background-color: #1e293b;
        color: #fff;
        border-bottom-color: #334155;
    }
    .dark table.dataTable tbody td {
        color: #e2e8f0;
        border-bottom-color: #334155;
    }
    .dark table.dataTable tbody tr:hover { background-color: #1e293b; }
    .dark .dataTables_filter input,
    .dark .dataTables_length select {
        background-color: #1e293b;
        color: #fff;
        border-color: #334155;
    }
    .dark .dataTables_paginate .paginate_button { color: #e2e8f0 !important; }
    .dark label, .dark .dataTables_info { color: #e2e8f0; }
</style>
@endpush