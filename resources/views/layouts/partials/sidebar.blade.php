<nav id="sidebar" class="sidebar-wrapper sidebar-dark">
    <div class="sidebar-content">
        <div class="sidebar-brand">
            <a href="{{ route('dashboard') }}">
                <img src="{{ asset('vendor/techwind/images/logo-light.png') }}" height="24" alt="">
            </a>
        </div>

        <ul class="sidebar-menu border-t border-white/10" data-simplebar style="height: calc(100% - 70px);">
            <li>
                <a href="{{ route('dashboard') }}"><i class="ri-layout-line font-normal me-2"></i>Dashboard</a>
            </li>

            <li>
                <a href="{{ route('memos.index') }}"><i class="ri-file-list-line font-normal me-2"></i>All Memos</a>
            </li>

            @if(auth()->user()->role === 'admin')
                <li>
                    <a href="{{ route('memos.create') }}"><i class="ri-upload-2-line font-normal me-2"></i>Upload Memo</a>
                </li>
                <li>
                    <a href="#"><i class="ri-archive-line font-normal me-2"></i>Archives</a>
                </li>
            @endif
        </ul>
    </div>
</nav>