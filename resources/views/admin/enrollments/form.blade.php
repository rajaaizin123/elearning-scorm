<x-app-layout>
    <div class="mb-8 flex flex-col gap-2">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.classes.enrollments.index', $classGroup) }}" class="rounded-md bg-slate-100 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-200">← Peserta</a>
            <h1 class="text-2xl font-semibold">Tambah Peserta</h1>
        </div>
        <p class="text-sm text-slate-500">{{ $classGroup->course->title }} – {{ $classGroup->name }}</p>
    </div>

    <div class="mx-auto max-w-2xl">
        <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <form method="POST" action="{{ route('admin.classes.enrollments.store', $classGroup) }}">
                @csrf

                <div class="mb-6">
                    <label for="user_ids" class="block text-sm font-medium text-slate-900">Pilih Mahasiswa</label>
                    <p class="mt-1 text-xs text-slate-500">Pilih satu atau lebih mahasiswa untuk didaftarkan ke kelas ini.</p>

                    <div class="mt-3 space-y-2 max-h-80 overflow-y-auto border border-slate-200 rounded-md p-3 bg-slate-50">
                        @forelse($students as $student)
                            <label class="flex items-center gap-3 rounded-md p-2 hover:bg-slate-100 cursor-pointer">
                                <input type="checkbox" name="user_ids[]" value="{{ $student->id }}" class="h-4 w-4 rounded border-slate-300 text-sky-600">
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-slate-900">{{ $student->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $student->email }}</p>
                                </div>
                            </label>
                        @empty
                            <div class="text-center py-6 text-sm text-slate-500">
                                Semua mahasiswa sudah terdaftar di kelas ini.
                            </div>
                        @endforelse
                    </div>

                    @error('user_ids')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="rounded-md bg-sky-600 px-6 py-2 text-sm font-medium text-white hover:bg-sky-700">
                        Daftarkan
                    </button>
                    <a href="{{ route('admin.classes.enrollments.index', $classGroup) }}" class="rounded-md border border-slate-300 bg-white px-6 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
