<x-app-layout>
    <div class="mb-8 flex flex-col gap-2">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.classes.index') }}" class="rounded-md bg-slate-100 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-200">← Kelas</a>
            <h1 class="text-2xl font-semibold">Daftar Peserta</h1>
        </div>
        <p class="text-sm text-slate-500">{{ $classGroup->course->title }} – {{ $classGroup->name }}</p>
    </div>

    @if(session('status'))
        <div class="mb-8 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('status') }}
        </div>
    @endif

    <div class="mb-8 flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold">Peserta Terdaftar ({{ $enrolledStudents->count() }})</h2>
            <p class="text-sm text-slate-500">Mahasiswa yang sudah didaftarkan ke kelas ini.</p>
        </div>
        <a href="{{ route('admin.classes.enrollments.create', $classGroup) }}" class="rounded-md bg-sky-600 px-4 py-2 text-sm font-medium text-white hover:bg-sky-700">
            + Tambah Peserta
        </a>
    </div>

    <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        @forelse($enrolledStudents as $enrollment)
            <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4 last:border-b-0 hover:bg-slate-50">
                <div class="min-w-0">
                    <p class="font-medium text-slate-900">{{ $enrollment->student->name }}</p>
                    <p class="text-sm text-slate-500">{{ $enrollment->student->email }}</p>
                </div>
                <div class="flex items-center gap-4">
                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold uppercase text-emerald-700">{{ $enrollment->status }}</span>
                    <form method="POST" action="{{ route('admin.classes.enrollments.destroy', [$classGroup, $enrollment]) }}" class="inline" onsubmit="return confirm('Hapus peserta ini dari kelas?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-700">Hapus</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="px-6 py-8 text-center">
                <p class="text-sm text-slate-500">Belum ada peserta terdaftar di kelas ini.</p>
            </div>
        @endforelse
    </div>
</x-app-layout>
