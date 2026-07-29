<x-app-layout>
    <div class="max-w-5xl mx-auto mt-8">

        <div class="mb-4 flex items-center gap-3">
            <label class="font-medium">Filter by Company:</label>
            <select id="companyFilter" class="border rounded p-2">
                <option value="">All</option>
                @foreach ($companies as $company)
                    <option value="{{ $company->id }}">{{ $company->name }} ({{ $company->code }})</option>
                @endforeach
            </select>
        </div>

        <table id="memosTable" class="w-full border-collapse">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Uploaded</th>
                </tr>
            </thead>
        </table>
    </div>

    @push('scripts')
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script>
        let table = $('#memosTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('memos.index') }}",
                data: function (d) {
                    d.company_id = $('#companyFilter').val();
                }
            },
            columns: [
                { data: 'title_link', name: 'title' },
                { data: 'created_at', name: 'created_at' },
            ]
        });

        $('#companyFilter').on('change', function () {
            table.ajax.reload();
        });
    </script>
    @endpush
</x-app-layout>