<nav id="sidebar" class="sidebar-wrapper">
    <div class="sidebar-content">
        <div class="sidebar-brand">
            <a href="{{ route('dashboard') }}" class="flex items-center justify-center gap-2 w-full"> 
                <i class="ri-file-text-line text-2xl text-primary"></i>
                <span class="text-lg font-bold">Memo System</span>
            </a>
        </div>

        <ul class="sidebar-menu border-t border-white/10" data-simplebar style="height: calc(100% - 70px);">
            @if(auth()->user()->role === 'admin')
                <li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}">
                        <i class="ri-layout-line font-normal me-2"></i>
                        Dashboard
                    </a>
                </li>
            @endif

            <li class="{{ request()->routeIs('memos.index') ? 'active' : '' }}">
                <a href="{{ route('memos.index') }}"
                class="{{ request()->routeIs('memos.index') ? 'active' : '' }}">
                    <i class="ri-file-list-line font-normal me-2"></i>
                    All Memos
                </a>
            </li>

            @if(auth()->user()->role === 'admin')
                <li class="{{ request()->routeIs('memos.archived') ? 'active' : '' }}">
                    <a href="{{ route('memos.archived') }}"
                    class="{{ request()->routeIs('memos.archived') ? 'active' : '' }}">
                        <i class="ri-archive-line font-normal me-2"></i>
                        Archives
                    </a>
                </li>
                
                <li class="{{ request()->routeIs('admin.users.index') ? 'active' : '' }}">
                    <a href="{{ route('admin.users.index') }}">
                        <i class="ri-user-settings-line font-normal me-2"></i>
                        User Management
                    </a>
                </li>
            @endif
        </ul>
    </div>
</nav>