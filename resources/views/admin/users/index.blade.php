@extends('layouts.admin')

@section('content')
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold dark:text-white">User Management</h1>

        <button
            onclick="Livewire.dispatch('open-user-modal')"
            class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-md shadow-sm hover:opacity-90 transition text-sm font-medium"
        >
            <i class="ri-user-add-line"></i>
            Add User
        </button>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-md shadow-sm dark:shadow-gray-700 border border-gray-100 dark:border-slate-700 p-6">
        <div class="overflow-x-auto">
            <table id="usersTable" class="w-full border-collapse">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Company</th>
                        <th>Department</th>
                        <th>Rank</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <livewire:modals.user-form-modal />
@endsection

@push('styles')
    @include('layouts.partials.datatable-styles')
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script>
    let usersTable = $('#usersTable').DataTable({
        processing: true,
        serverSide: true,
        dom: '<"flex justify-between items-center"l>rt<"flex justify-between items-center"ip>',
        ajax: {
            url: "{{ route('admin.users.index') }}",
        },
        columns: [
            { data: 'name', name: 'name' },
            { data: 'username', name: 'username' },
            { data: 'role_badge', name: 'role', orderable: false, searchable: false },
            { data: 'company_name', name: 'company_name', orderable: false, searchable: false },
            { data: 'department_name', name: 'department_name', orderable: false, searchable: false },
            { data: 'rank_name', name: 'rank_name', orderable: false, searchable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false },
        ],
    });

    document.addEventListener('livewire:init', () => {
        Livewire.on('user-saved', () => {
            usersTable.ajax.reload(null, false);
        });
    });

    $(document).on('click', '.edit-user-btn', function () {
        Livewire.dispatch('edit-user', { userId: $(this).data('id') });
    });

    $(document).on('click', '.delete-user-btn', function () {
        const id = $(this).data('id');
        const name = $(this).data('name');

        Swal.fire({
            icon: 'warning',
            title: `Delete ${name}?`,
            text: 'This cannot be undone.',
            showCancelButton: true,
            confirmButtonText: 'Delete',
            confirmButtonColor: '#dc2626',
        }).then((result) => {
            if (!result.isConfirmed) return;

            fetch(`/admin/users/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            })
                .then(async (res) => {
                    const body = await res.json().catch(() => ({}));
                    if (!res.ok) {
                        Swal.fire('Error', body.message || 'Something went wrong.', 'error');
                        return;
                    }
                    usersTable.ajax.reload(null, false);
                    Swal.fire({ icon: 'success', title: 'Deleted', timer: 1200, showConfirmButton: false });
                })
                .catch(() => Swal.fire('Error', 'Something went wrong.', 'error'));
        });
    });
</script>
@endpush