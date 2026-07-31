@extends('layouts.admin')

@section('content')
    <h1 class="text-2xl font-bold mb-4 dark:text-white">Archived Memos</h1>

    <div class="bg-white dark:bg-slate-900 rounded-md shadow-sm dark:shadow-gray-700 p-6">
        <table id="archivedTable" class="w-full border-collapse">
            <thead>
                <tr>
                    <th>Memo No.</th>
                    <th>Title</th>
                    <th>Company</th>
                    <th>Category</th>
                    <th>Archived On</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
{{-- reuse the same dark-mode table styles from memos/index.blade.php --}}
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script>
    let table = $('#archivedTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('memos.archived') }}",
        columns: [
            { data: 'memo_no', name: 'memo_no' },
            { data: 'title', name: 'title' },
            { data: 'company_list', name: 'company_list', orderable: false, searchable: false },
            { data: 'category_list', name: 'category_list', orderable: false, searchable: false },
            { data: 'deleted_at_formatted', name: 'deleted_at' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false },
        ]
    });

    function restoreMemo(id) {
        Swal.fire({
            title: 'Restore this memo?',
            text: 'It will become visible in All Memos again.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Restore',
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/memos/${id}/restore`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    }
                }).then(res => res.json()).then(() => {
                    table.ajax.reload(null, false);
                    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Memo restored', showConfirmButton: false, timer: 2000 });
                });
            }
        });
    }
</script>
@endpush