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

        <label class="font-medium">Department:</label>
        <select id="departmentFilter" class="border rounded-md p-2 dark:bg-slate-800 dark:border-slate-700 dark:text-white">
            <option value="">All</option>
            @foreach($departments as $department)
                <option value="{{ $department->id }}">{{ $department->name }}</option>
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
                        <th>Author</th>
                        <th>Department</th>
                        <th>Employee Rank</th>
                        <th>Superseded</th>
                        <th>Related</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <div id="pdfModal" class="fixed inset-0 z-[1000] hidden items-center justify-center bg-black/50">
        <div class="bg-white dark:bg-slate-900 rounded-md shadow-lg w-full max-w-[85vw] h-[90vh] flex flex-col border border-gray-100 dark:border-slate-700 overflow-hidden">

            <div class="flex justify-between items-start p-4 border-b border-gray-100 dark:border-slate-700 shrink-0">
                <div>
                    <p class="text-sm text-slate-400">Memo No. <span id="pdfMemoNo" class="font-semibold text-slate-700 dark:text-slate-200"></span></p>
                    <h3 id="pdfTitle" class="font-bold text-lg dark:text-white mt-1"></h3>
                    <p id="pdfDepartment" class="text-sm text-slate-400"></p>
                </div>
                <button id="closePdfModal" class="text-slate-400 hover:text-slate-700 text-xl">&times;</button>
            </div>

            <div class="flex flex-row flex-1 overflow-hidden">
                <iframe id="pdfFrame" class="flex-1 w-full"></iframe>

                <div class="w-72 shrink-0 border-l border-gray-100 dark:border-slate-700 overflow-y-auto p-4 space-y-4 text-sm">
                    <div>
                        <p class="text-m uppercase text-slate-400 flex items-center gap-2">
                            <i class="ri-building-line"></i>
                            Company
                        </p>
                        <p id="pdfCompany" class="font-medium dark:text-white"></p>
                    </div>
                    <div>
                        <p class="text-m uppercase text-slate-400 flex items-center gap-2">
                            <i class="ri-user-line"></i>
                            Author
                        </p>
                        <p id="pdfAuthor" class="font-medium dark:text-white"></p>
                    </div>
                    <div>
                        <p class="text-m uppercase text-slate-400 flex items-center gap-2">
                            <i class="ri-time-line"></i>
                            Uploaded
                        </p>
                        <p id="pdfUploaded" class="font-medium dark:text-white"></p>
                    </div>

                    <div class="border-t border-gray-100 dark:border-slate-700 pt-3">
                        <p class="text-m uppercase text-slate-400 flex items-center gap-2">
                            <i class="ri-links-line"></i>
                            Related Memos
                        </p>
                        <ul id="pdfRelated" class="space-y-1"></ul>
                    </div>
                    <div>
                        <p class="text-m uppercase text-slate-400 flex items-center gap-2">
                            <i class="ri-arrow-right-line"></i>
                            Superseded Memos
                        </p>
                        <ul id="pdfSuperseded" class="space-y-1"></ul>
                    </div>
                    <div>
                        <p class="text-m uppercase text-slate-400 flex items-center gap-2">
                            <i class="ri-arrow-left-line"></i>
                            Superseded By
                        </p>
                        <ul id="pdfSupersededBy" class="space-y-1"></ul>
                    </div>

                    <div class="border-t border-gray-100 dark:border-slate-700 pt-3 space-y-2">
                        <a id="pdfDownload" href="#" class="block text-center px-3 py-1.5 rounded-md bg-primary hover:bg-primary-700 text-white text-sm transition"><i class="ri-download-2-fill"></i> Download</a>
                            <button id="pdfEdit" class="block w-full text-center px-3 py-1.5 rounded-md border border-blue-200 dark:border-blue-800 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-950 text-sm transition"><i class="ri-pencil-line"></i> Edit</button>
                            <button id="pdfArchive" class="block w-full text-center px-3 py-1.5 rounded-md border border-red-200 dark:border-red-800 text-red-600 hover:bg-red-50 dark:hover:bg-red-950 text-sm transition"><i class="ri-archive-line"></i> Archive</button>
                        <button onclick="document.getElementById('closePdfModal').click()" class="block w-full text-center px-3 py-1.5 rounded-md border border-gray-200 dark:border-slate-700 hover:bg-gray-50 dark:hover:bg-slate-800 dark:text-white text-sm transition">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- History Modal --}}
    <div id="historyModal" class="hidden fixed inset-0 z-[1000] items-center justify-center bg-black/50">
        <div class="bg-white dark:bg-slate-900 rounded-md shadow-lg w-full max-w-[900px] max-h-[85vh] overflow-y-auto border border-gray-100 dark:border-slate-700 p-6 m-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold dark:text-white">Memo History</h3>
                <button onclick="closeHistoryModal()" class="text-slate-400 hover:text-slate-700 dark:hover:text-white transition text-xl">&times;</button>
            </div>
            <div class="overflow-x-auto">
                <table id="historyTable" class="w-full border-collapse">
                    <thead>
                        <tr>
                            <th>Action</th>
                            <th>By</th>
                            <th>Remarks</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <livewire:modals.memo-form-modal />
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
                d.department_id = $('#departmentFilter').val();
                d.rank_id = $('#rankFilter').val();
            }
        },
        columns: [
            { data: 'memo_no_link', name: 'memo_no' },
            { data: 'title_with_preview', name: 'title', className: 'align-middle' },
            { data: 'company_list', name: 'company_list', orderable: false, searchable: false },
            { data: 'author', name: 'author' },
            { data: 'department_list', name: 'department_list', orderable: false, searchable: false },
            { data: 'rank_list', name: 'rank_list', orderable: false, searchable: false },
            { data: 'superseded_list', name: 'superseded_list', orderable: false, searchable: false },
            { data: 'related_list', name: 'related_list', orderable: false, searchable: false },
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

    $('#companyFilter, #departmentFilter, #rankFilter').on('change', function () {
        table.ajax.reload();
    });

    document.addEventListener('livewire:init', () => {
        Livewire.on('memo-saved', () => {
            table.ajax.reload(null, false);
        });
    });

    function archiveMemo(id) {
        Swal.fire({
            icon: 'warning',
            title: 'Archive this memo?',
            input: 'textarea',
            inputLabel: 'Reason for archiving (required)',
            inputPlaceholder: 'Type your reason here...',
            inputValidator: (value) => {
                if (!value || !value.trim()) {
                    return 'A remark is required to archive this memo.';
                }
            },
            showCancelButton: true,
            confirmButtonText: 'Archive',
            confirmButtonColor: '#dc2626',
        }).then((result) => {
            if (!result.isConfirmed) return;

            fetch(`/memos/${id}/archive`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ remarks: result.value }),
            }).then(async (res) => {
                    const text = await res.text();
                    let body = {};
                    try { body = JSON.parse(text); } catch (e) {
                        console.error('Non-JSON response:', text);
                    }
                    if (!res.ok) {
                        Swal.fire('Error', body.message || 'Something went wrong.', 'error');
                        return;
                    }
                    table.ajax.reload(null, false);
                    Swal.fire({ icon: 'success', title: 'Archived', timer: 1200, showConfirmButton: false });
                })
                .catch(() => Swal.fire('Error', 'Something went wrong.', 'error'));
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
                $('#pdfDepartment').text(data.department);
                $('#pdfCompany').text(data.company);
                $('#pdfUploaded').text(data.uploaded);
                $('#pdfAuthor').text(data.author);
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
    
    
    let historyTable = null;

    function openHistoryModal(memoId) {
        document.getElementById('historyModal').classList.remove('hidden');
        document.getElementById('historyModal').classList.add('flex');

        if (historyTable) {
            historyTable.ajax.url(`/memos/${memoId}/logs`).load();
            return;
        }

        historyTable = $('#historyTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: { url: `/memos/${memoId}/logs` },
            columns: [
                { data: 'action_badge', name: 'action_badge', orderable: false, searchable: false },
                { data: 'user_name', name: 'user_name', orderable: false, searchable: false },
                { data: 'remarks_display', name: 'remarks_display', orderable: false, searchable: false },
                { data: 'created_at', name: 'created_at', orderable: false, searchable: false },
            ],
        });
    }

    function closeHistoryModal() {
        document.getElementById('historyModal').classList.add('hidden');
        document.getElementById('historyModal').classList.remove('flex');
    }
</script>
@endpush

@push('styles')
    @include('layouts.partials.datatable-styles')
@endpush