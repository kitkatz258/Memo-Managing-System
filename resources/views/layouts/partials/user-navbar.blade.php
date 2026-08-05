<div class="top-header">
    <div class="header-bar flex justify-between">
        <div class="flex items-center gap-4">   
            <i class="ri-file-text-line text-2xl text-primary"></i>    
            <h1 class="text-xl font-bold dark:text-white">Memo System</h1>
            <div class="ps-1.5">
                <div class="form-icon relative sm:block hidden">
                    <i class="ri-search-line absolute top-1/2 -translate-y-1/2 inset-s-3"></i>
                    <input type="text" id="globalSearch" class="form-input w-56 ps-9 py-2 px-3 h-8 bg-transparent dark:bg-slate-900 dark:text-slate-200 rounded-3xl outline-none border border-gray-100 dark:border-gray-800 focus:ring-0" name="s" placeholder="Search memos...">
                </div>
            </div>
        </div>

        <ul class="list-none mb-0 space-x-1">
            <li class="inline-block relative">
                <button id="theme-toggle" type="button" class="size-8 inline-flex items-center justify-center tracking-wide align-middle duration-500 text-[20px] text-center bg-gray-50 dark:bg-slate-800 hover:bg-gray-100 dark:hover:bg-slate-700 border border-gray-100 dark:border-gray-800 text-slate-900 dark:text-white rounded-full">
                    <i class="ri-moon-line dark:hidden"></i>
                    <i class="ri-sun-line hidden dark:inline-block"></i>
                </button>
            </li>

            <li class="dropdown inline-block relative">
                <button data-dropdown-toggle="dropdown" class="dropdown-toggle items-center" type="button">
                    <span class="size-8 inline-flex items-center justify-center tracking-wide align-middle duration-500 text-[20px] text-center bg-gray-50 dark:bg-slate-800 hover:bg-gray-100 dark:hover:bg-slate-700 border border-gray-100 dark:border-gray-800 text-slate-900 dark:text-white rounded-full ">
                        <i class="ri-user-line"></i>
                    </span>
                    <span class="font-semibold text-[16px] ms-1 sm:inline-block hidden">{{ auth()->user()->name }}</span>
                </button>

                <div class="dropdown-menu absolute inset-e-0 m-0 mt-4 z-10 w-44 rounded-md overflow-hidden bg-white dark:bg-slate-900 shadow-sm dark:shadow-gray-700 hidden" onclick="event.stopPropagation();">
                    <ul class="py-2 text-start">
                        <li class="border-t border-gray-100 dark:border-gray-800 my-2"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-start block font-medium py-1 px-4 dark:text-white/70 hover:text-primary dark:hover:text-white">
                                    <i class="ri-logout-circle-r-line me-2"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </li>
        </ul>
    </div>
</div>