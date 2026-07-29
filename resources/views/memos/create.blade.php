{{-- resources/views/memos/create.blade.php --}}
<x-app-layout>
    <div class="max-w-xl mx-auto mt-8">
        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('memos.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label class="block font-medium">Title</label>
                <input type="text" name="title" value="{{ old('title') }}" class="w-full border rounded p-2">
                @error('title') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block font-medium">PDF File</label>
                <input type="file" name="file" accept="application/pdf" class="w-full border rounded p-2">
                @error('file') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="for_all_companies" value="1" id="forAllCheckbox">
                    <span class="ml-2">All Companies</span>
                </label>
            </div>

            <div class="mb-4" id="companyCheckboxes">
                <label class="block font-medium mb-1">Select Companies</label>
                @foreach ($companies as $company)
                    <label class="flex items-center">
                        <input type="checkbox" name="companies[]" value="{{ $company->id }}">
                        <span class="ml-2">{{ $company->name }} ({{ $company->code }})</span>
                    </label>
                @endforeach
                @error('companies') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Upload</button>
        </form>
    </div>

    <script>
        document.getElementById('forAllCheckbox').addEventListener('change', function () {
            document.getElementById('companyCheckboxes').style.display = this.checked ? 'none' : 'block';
        });
    </script>
</x-app-layout>