<x-app-layout>
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-semibold">Manajemen Mata Kuliah</h1>
        <a class="rounded-md bg-sky-600 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-700" href="{{ route('admin.courses.create') }}">Tambah Course</a>
    </div>
    <div class="grid gap-4 lg:grid-cols-3">
        @foreach($courses as $course)
            <article class="course-card">
                <div class="text-xs font-semibold uppercase tracking-wide text-sky-700">{{ $course->code }}</div>
                <h2 class="mt-2 text-lg font-semibold">{{ $course->title }}</h2>
                <p class="mt-1 text-sm text-slate-500">Semester: {{ $course->semester }}</p>
                <p class="mt-2 text-sm text-slate-500">{{ $course->lecturer?->name ?? 'Belum ada dosen' }}</p>
                <p class="mt-4 text-sm">{{ $course->status }}</p>
                <div class="mt-4 flex gap-3">
                    <a href="{{ route('admin.courses.edit', $course) }}" class="text-sm text-sky-600 hover:underline">Edit</a>
                    <form method="post" action="{{ route('admin.courses.destroy', $course) }}" onsubmit="return confirm('Hapus course ini?')">
                        @csrf
                        @method('delete')
                        <button class="text-sm text-red-600 hover:underline">Hapus</button>
                    </form>
                </div>
            </article>
        @endforeach
    </div>
    <div class="mt-5">{{ $courses->links() }}</div>
</x-app-layout>
