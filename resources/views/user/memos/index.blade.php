@extends('layouts.user')

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
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-md shadow-sm dark:shadow-gray-700 border border-gray-100 dark:border-slate-700 p-6">
        <table id="memosTable" class="w-full border-collapse">
            <thead>
                <tr>
                    <th>Memo No.</th>
                    <th>Title</th>
                    <th>Company</th>
                    <th>Author</th>
                    <th>Department</th>
                    <th>Superseded</th>
                    <th>Related</th>
                </tr>
            </thead>
        </table>
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
                        <a id="pdfDownload" href="#" class="block text-center px-3 py-1.5 rounded-md bg-primary text-white text-sm"><i class="ri-download-2-fill"></i> Download</a>
                        <button onclick="document.getElementById('closePdfModal').click()" class="block w-full text-center px-3 py-1.5 rounded-md border border-gray-200 dark:border-slate-700 text-sm dark:text-white">Close</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script>
    let table = $('#memosTable').DataTable({
        processing: true,
        serverSide: true,
        dom: '<"flex justify-between items-center"l>rt<"flex justify-between items-center"ip>',
        ajax: {
            url: "{{ route('user.memos.index') }}",
            data: function (d) {
                d.company_id = $('#companyFilter').val();
                d.department_id = $('#departmentFilter').val();
                d.rank_id = $('#rankFilter').val();
            }
        },
        columns: [
            { data: 'memo_no_link', name: 'memo_no' },
            { data: 'title_with_preview', name: 'title' },
            { data: 'company_list', name: 'company_list', orderable: false, searchable: false },
            { data: 'author', name: 'author' },
            { data: 'department_list', name: 'department_list', orderable: false, searchable: false },
            { data: 'superseded_list', name: 'superseded_list', orderable: false, searchable: false },
            { data: 'related_list', name: 'related_list', orderable: false, searchable: false },
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
            .then(async (res) => {
                if (res.status === 403) {
                    const body = await res.json().catch(() => ({}));
                    Swal.fire({
                        icon: 'warning',
                        title: 'Access Restricted',
                        text: body.message || 'You do not have access to view this memo.',
                        confirmButtonColor: '#635BFF',
                    });
                    return null;
                }

                if (!res.ok) {
                    throw new Error('Failed to load memo preview');
                }

                return res.json();
            })
            .then(data => {
                if (!data) return;

                $('#pdfTitle').text(data.title);
                $('#pdfMemoNo').text(data.memo_no);
                $('#pdfDepartment').text(data.departments);
                $('#pdfCompany').text(data.company);
                $('#pdfUploaded').text(data.uploaded);
                $('#pdfAuthor').text(data.author);

                $('#pdfFrame').attr('src', `/memos/${id}/view`);
                $('#pdfDownload').attr('href', `/memos/${id}/download`);

                renderMemoLinks('pdfRelated', data.related);
                renderMemoLinks('pdfSuperseded', data.superseded);
                renderMemoLinks('pdfSupersededBy', data.superseded_by);

                $('#pdfModal').removeClass('hidden').addClass('flex');
            })
            .catch(error => {
                console.error('Failed to load memo preview:', error);
            });
    }

    $(document).on('click', '.view-pdf-btn', function(){
        openPdfViewer($(this).data('id'));
    });

    $(document).on('click', '.open-related-memo', function(e){
        e.preventDefault();
        openPdfViewer($(this).data('id'));
    });

    $('#closePdfModal').on('click', function(){
        $('#pdfModal').removeClass('flex').addClass('hidden');
        $('#pdfFrame').attr('src', '');
    });

</script>
@endpush

@push('styles')
    @include('layouts.partials.datatable-styles')
@endpush