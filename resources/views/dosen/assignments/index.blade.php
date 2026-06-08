<x-app-layout>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-slate-950">Assignment</h1>
            <p class="mt-1 text-sm text-slate-500">Kelola tugas dan pantau submission mahasiswa.</p>
        </div>
        <div class="text-sm font-medium text-slate-500">{{ $assignments->count() }} assignment</div>
    </div>

    @if($errors->any())
        <div class="mb-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    <section class="mb-5 rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
        <form action="{{ route('dosen.assignments.store') }}" method="POST" class="grid gap-3 lg:grid-cols-[1.2fr_1.4fr_1fr_0.7fr_auto] lg:items-end">
            @csrf

            <div>
                <label for="course_id" class="block text-xs font-semibold uppercase tracking-wide text-slate-500">Course</label>
                <select id="course_id" name="course_id" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-sky-500 focus:ring-sky-500" required>
                    <option value="">Pilih course</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" @selected(old('course_id') == $course->id)>{{ $course->code }} - {{ $course->title }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="title" class="block text-xs font-semibold uppercase tracking-wide text-slate-500">Judul</label>
                <input id="title" name="title" type="text" value="{{ old('title') }}" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-sky-500 focus:ring-sky-500" placeholder="Tugas baru" required>
            </div>

            <div>
                <label for="deadline_at" class="block text-xs font-semibold uppercase tracking-wide text-slate-500">Deadline</label>
                <input id="deadline_at" name="deadline_at" type="datetime-local" value="{{ old('deadline_at') }}" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-sky-500 focus:ring-sky-500">
            </div>

            <div>
                <label for="max_score" class="block text-xs font-semibold uppercase tracking-wide text-slate-500">Maks.</label>
                <input id="max_score" name="max_score" type="number" min="0" max="999.99" step="0.01" value="{{ old('max_score', 100) }}" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-sky-500 focus:ring-sky-500" required>
            </div>

            <div class="flex items-center gap-3">
                <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700">
                    <input name="is_published" type="checkbox" value="1" class="rounded border-slate-300 text-sky-600 shadow-sm focus:ring-sky-500" @checked(old('is_published', true))>
                    Publish
                </label>
                <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">Tambah</button>
            </div>

            <div class="lg:col-span-5">
                <label for="description" class="sr-only">Deskripsi</label>
                <textarea id="description" name="description" rows="2" class="block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-sky-500 focus:ring-sky-500" placeholder="Deskripsi singkat atau instruksi tugas">{{ old('description') }}</textarea>
            </div>
        </form>
    </section>

    <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        <div class="grid grid-cols-[1fr_auto] gap-3 border-b border-slate-200 bg-slate-50 px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">
            <div>Daftar Assignment</div>
            <div>Aksi</div>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse($assignments as $assignment)
                <article class="p-4">
                    <form action="{{ route('dosen.assignments.update', $assignment) }}" method="POST" class="grid gap-3 lg:grid-cols-[1fr_220px_130px_auto] lg:items-center">
                        @csrf
                        @method('PUT')

                        <div class="min-w-0">
                            <div class="mb-1 flex flex-wrap items-center gap-2">
                                <span class="text-xs font-semibold uppercase tracking-wide text-emerald-700">{{ $assignment->course?->code }}</span>
                                <span class="rounded-full px-2 py-0.5 text-xs font-semibold {{ $assignment->is_published ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $assignment->is_published ? 'Published' : 'Draft' }}
                                </span>
                            </div>
                            <input name="title" type="text" value="{{ $assignment->title }}" class="block w-full rounded-md border-slate-300 text-sm font-semibold text-slate-900 shadow-sm focus:border-sky-500 focus:ring-sky-500" required>
                            <textarea name="description" rows="2" class="mt-2 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-sky-500 focus:ring-sky-500" placeholder="Deskripsi">{{ $assignment->description }}</textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500">Deadline</label>
                            <input name="deadline_at" type="datetime-local" value="{{ $assignment->deadline_at?->format('Y-m-d\TH:i') }}" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-sky-500 focus:ring-sky-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500">Nilai Maks.</label>
                            <input name="max_score" type="number" min="0" max="999.99" step="0.01" value="{{ $assignment->max_score }}" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-sky-500 focus:ring-sky-500" required>
                        </div>

                        <div class="flex flex-wrap items-center justify-end gap-2">
                            <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700">
                                <input name="is_published" type="checkbox" value="1" class="rounded border-slate-300 text-sky-600 shadow-sm focus:ring-sky-500" @checked($assignment->is_published)>
                                Publish
                            </label>
                            <a href="{{ route('dosen.assignments.submissions.index', $assignment) }}" class="rounded-md border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                                {{ $assignment->submissions->count() }} submit
                            </a>
                            <button type="submit" class="rounded-md bg-sky-600 px-3 py-2 text-sm font-semibold text-white hover:bg-sky-700">Simpan</button>
                        </div>
                    </form>

                    <form action="{{ route('dosen.assignments.destroy', $assignment) }}" method="POST" class="mt-2 flex justify-end" onsubmit="return confirm('Hapus assignment ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-xs font-semibold text-red-600 hover:underline">Hapus</button>
                    </form>
                </article>
            @empty
                <div class="p-8 text-center text-sm text-slate-500">Belum ada assignment.</div>
            @endforelse
        </div>
    </section>
</x-app-layout>
