@php
    $user = Auth::user();
    $role = $user?->hasRole('admin') ? 'Admin' : ($user?->hasRole('dosen') ? 'Dosen' : ($user?->hasRole('mahasiswa') ? 'Mahasiswa' : 'User'));

    $navItems = [];

    if ($user?->hasRole('admin')) {
        $navItems = [
            ['label' => 'Dashboard', 'href' => route('admin.dashboard'), 'active' => request()->routeIs('admin.dashboard')],
            ['label' => 'Users', 'href' => route('admin.users.index'), 'active' => request()->routeIs('admin.users.*')],
            ['label' => 'Mata Kuliah', 'href' => route('admin.courses.index'), 'active' => request()->routeIs('admin.courses.*')],
            ['label' => 'Kelas', 'href' => route('admin.classes.index'), 'active' => request()->routeIs('admin.classes.*')],
        ];
    } elseif ($user?->hasRole('dosen')) {
        $navItems = [
            ['label' => 'Dashboard', 'href' => route('dosen.dashboard'), 'active' => request()->routeIs('dosen.dashboard')],
            ['label' => 'Modul & SCORM', 'href' => route('dosen.modules.index'), 'active' => request()->routeIs('dosen.modules.*') || request()->routeIs('dosen.scorm-packages.*')],
            ['label' => 'Tugas', 'href' => route('dosen.assignments.index'), 'active' => request()->routeIs('dosen.assignments.*')],
        ];
    } elseif ($user?->hasRole('mahasiswa')) {
        $navItems = [
            ['label' => 'Dashboard', 'href' => route('mahasiswa.dashboard'), 'active' => request()->routeIs('mahasiswa.dashboard')],
            ['label' => 'Tugas', 'href' => route('mahasiswa.assignments.index'), 'active' => request()->routeIs('mahasiswa.assignments.*')],
            ['label' => 'Riwayat Kuis', 'href' => route('mahasiswa.quizzes.index'), 'active' => request()->routeIs('mahasiswa.quizzes.*')],
            ['label' => 'Diskusi', 'href' => route('mahasiswa.discussions.index'), 'active' => request()->routeIs('mahasiswa.discussions.*')],
        ];
    } else {
        $navItems = [
            ['label' => 'Dashboard', 'href' => route('dashboard'), 'active' => request()->routeIs('dashboard')],
        ];
    }
@endphp

<nav x-data="{ open: false }">
    <div class="sticky top-0 z-40 border-b border-slate-200 bg-white/95 px-4 py-3 backdrop-blur lg:hidden">
        <div class="flex items-center justify-between gap-3">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <x-application-logo class="h-8 w-auto fill-current text-slate-900" />
                <div>
                    <div class="text-sm font-semibold text-slate-950">{{ config('app.name', 'LMS') }}</div>
                    <div class="text-xs text-slate-500">{{ $role }}</div>
                </div>
            </a>

            <button type="button" @click="open = ! open" class="rounded-md border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700">
                Menu
            </button>
        </div>

        <div x-show="open" x-transition class="mt-3 space-y-1">
            @foreach($navItems as $item)
                <a href="{{ $item['href'] }}" class="block rounded-md px-3 py-2 text-sm font-semibold {{ $item['active'] ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                    {{ $item['label'] }}
                </a>
            @endforeach

            <a href="{{ route('profile.edit') }}" class="block rounded-md px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">Profile</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="block w-full rounded-md px-3 py-2 text-left text-sm font-semibold text-red-600 hover:bg-red-50">Log Out</button>
            </form>
        </div>
    </div>

    <aside class="fixed inset-y-0 left-0 z-30 hidden w-72 border-r border-slate-200 bg-white lg:flex lg:flex-col">
        <div class="flex h-full flex-col">
            <div class="border-b border-slate-200 px-6 py-5">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                    <x-application-logo class="h-9 w-auto fill-current text-slate-900" />
                    <div class="min-w-0">
                        <div class="truncate text-base font-semibold text-slate-950">{{ config('app.name', 'LMS') }}</div>
                        <div class="text-xs font-medium uppercase tracking-wide text-slate-500">{{ $role }}</div>
                    </div>
                </a>
            </div>

            <div class="flex-1 overflow-y-auto px-4 py-5">
                <div class="mb-3 px-2 text-xs font-semibold uppercase tracking-wide text-slate-400">Navigasi</div>
                <div class="space-y-1">
                    @foreach($navItems as $item)
                        <a href="{{ $item['href'] }}" class="flex items-center justify-between rounded-md px-3 py-2.5 text-sm font-semibold transition {{ $item['active'] ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-950' }}">
                            <span>{{ $item['label'] }}</span>
                            @if($item['active'])
                                <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="border-t border-slate-200 p-4">
                <div class="mb-3 rounded-md bg-slate-50 px-3 py-2">
                    <div class="truncate text-sm font-semibold text-slate-900">{{ $user?->name }}</div>
                    <div class="truncate text-xs text-slate-500">{{ $user?->email }}</div>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('profile.edit') }}" class="rounded-md border border-slate-300 px-3 py-2 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50">Profile</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full rounded-md bg-red-50 px-3 py-2 text-sm font-semibold text-red-700 hover:bg-red-100">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </aside>
</nav>
