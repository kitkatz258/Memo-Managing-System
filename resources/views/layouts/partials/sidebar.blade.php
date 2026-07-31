<nav id="sidebar" class="sidebar-wrapper">
    <div class="sidebar-content">
        <div class="sidebar-brand">
            <a href="{{ route('dashboard') }}" class="flex items-center justify-center gap-2 w-full"> 
                <i class="ri-file-text-line text-2xl text-primary"></i>
                <span class="text-lg font-bold text-white">Memo System</span>
            </a>
        </div>

        <ul class="sidebar-menu border-t border-white/10" data-simplebar style="height: calc(100% - 70px);">
            @if(auth()->user()->role === 'admin')
                <li>
                    <a href="{{ route('dashboard') }}"><i class="ri-layout-line font-normal me-2"></i>Dashboard</a>
                </li>
            @endif    

            <li>
                <a href="{{ route('memos.index') }}"><i class="ri-file-list-line font-normal me-2"></i>All Memos</a>
            </li>

            @if(auth()->user()->role === 'admin')
                <li>
                    <a href="{{ route('memos.archived') }}"><i class="ri-archive-line font-normal me-2"></i>Archives</a>
                </li>
            @endif
        </ul>
    </div>
</nav>