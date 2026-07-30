@extends('layouts.admin')

@section('content')
    <h2 class="text-2xl font-bold mb-6">Dashboard</h2>

    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <a href="{{ route('memos.index') }}" class="p-6 bg-white dark:bg-slate-900 rounded-md shadow-sm hover:shadow-md transition">
            <i class="ri-file-list-line text-3xl text-primary"></i>
            <h3 class="text-lg font-semibold mt-3">All Memos</h3>
            <p class="text-slate-400 text-sm mt-1">Browse and search all memos</p>
        </a>

        @if(auth()->user()->role === 'admin')
            <button onclick="Livewire.dispatch('open-memo-modal')" class="p-6 bg-white dark:bg-slate-900 rounded-md shadow-sm hover:shadow-md transition text-start w-full">
                <i class="ri-upload-2-line text-3xl text-primary"></i>
                <h3 class="text-lg font-semibold mt-3">Upload Memo</h3>
                <p class="text-slate-400 text-sm mt-1">Add a new memo to the system</p>
            </button>
        @endif
    </div>

    <livewire:modals.memo-form-modal />
@endsection