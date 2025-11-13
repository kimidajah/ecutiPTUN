<aside id="sidebar"
       class="fixed top-0 left-0 h-full w-64 bg-[#A5D6A7] text-gray-800 shadow-lg flex flex-col transition-all duration-300">

    {{-- Header --}}
    <div class="flex items-center justify-between p-4 border-b border-green-300">
        <h1 class="text-xl font-bold text-green-900">eCuti Admin</h1>
        <button id="toggleSidebar" class="text-green-900 hover:text-green-700">
            <i class="bi bi-list text-2xl"></i>
        </button>
    </div>

    {{-- Menu --}}
    <nav class="flex-1 p-4 space-y-2">
        <a href="{{ route('admin.dashboard') }}"
           class="flex items-center gap-3 p-2 rounded-lg hover:bg-green-200 {{ request()->routeIs('admin.dashboard') ? 'bg-green-300 font-semibold' : '' }}">
            <i class="bi bi-speedometer2"></i> <span>Dasbor</span>
        </a>

        <a href="{{ route('admin.cuti.index') }}"
           class="flex items-center gap-3 p-2 rounded-lg hover:bg-green-200 {{ request()->routeIs('admin.cuti.*') ? 'bg-green-300 font-semibold' : '' }}">
            <i class="bi bi-calendar-check"></i> <span>Permintaan Cuti</span>
        </a>

        <a href="{{ route('admin.users.index') }}"
           class="flex items-center gap-3 p-2 rounded-lg hover:bg-green-200 {{ request()->routeIs('admin.users.*') ? 'bg-green-300 font-semibold' : '' }}">
            <i class="bi bi-people"></i> <span>Users & Karyawan</span>
        </a>
    </nav>

    {{-- Footer --}}
    <div class="p-4 border-t border-green-300">
        <div class="flex items-center gap-3">
            <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}" alt="avatar" class="w-8 h-8 rounded-full">
            <div>
                <p class="font-medium text-sm">{{ Auth::user()->name }}</p>
                <form action="{{ route('logout') }}" method="POST" class="m-0 p-0">
                    @csrf
                    <button class="text-xs text-red-600 hover:underline">Keluar</button>
                </form>
            </div>
        </div>
    </div>
</aside>
