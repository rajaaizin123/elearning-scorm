<x-app-layout>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-slate-950">{{ isset($module) ? 'Edit Modul' : 'Tambah Modul' }}</h1>
            <p class="mt-1 text-sm text-slate-500">Atur informasi modul sebelum memasang paket SCORM.</p>
        </div>
        <a href="{{ route('dosen.modules.index') }}" class="text-sm font-semibold text-sky-700 hover:underline">Kembali</a>
    </div>

    <section class="max-w-3xl rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <form method="post" action="{{ isset($module) ? route('dosen.modules.update', $module) : route('dosen.modules.store') }}" class="space-y-4">
            @csrf
            @isset($module)
                @method('put')
            @endisset

            <div>
                <label for="course_id" class="block text-xs font-semibold uppercase tracking-wide text-slate-500">Course</label>
                <select id="course_id" name="course_id" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-sky-500 focus:ring-sky-500" required>
                    <option value="">Pilih course</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" @selected(old('course_id', optional($module ?? null)->course_id) == $course->id)>
                            {{ $course->code }} - {{ $course->title }}
                        </option>
                    @endforeach
                </select>
                @error('course_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="title" class="block text-xs font-semibold uppercase tracking-wide text-slate-500">Judul Modul</label>
                <input id="title" name="title" type="text" value="{{ old('title', optional($module ?? null)->title ?: '') }}" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-sky-500 focus:ring-sky-500" placeholder="Contoh: Pengenalan Keamanan Siber" required>
                @error('title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="description" class="block text-xs font-semibold uppercase tracking-wide text-slate-500">Deskripsi</label>
                <textarea id="description" name="description" rows="3" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-sky-500 focus:ring-sky-500" placeholder="Deskripsi singkat modul">{{ old('description', optional($module ?? null)->description ?: '') }}</textarea>
                @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-[180px_1fr] sm:items-end">
                <div>
                    <label for="sort_order" class="block text-xs font-semibold uppercase tracking-wide text-slate-500">Urutan</label>
                    <input id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', optional($module ?? null)->sort_order ?? 0) }}" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-sky-500 focus:ring-sky-500">
                    @error('sort_order')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <label class="flex items-center gap-2 rounded-md bg-slate-50 px-3 py-2 text-sm font-medium text-slate-700">
                    <input id="is_published" name="is_published" type="checkbox" value="1" class="rounded border-slate-300 text-sky-600 shadow-sm focus:ring-sky-500" @checked(old('is_published', optional($module ?? null)->is_published))>
                    Terbitkan modul
                </label>
            </div>

            <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
                <a href="{{ route('dosen.modules.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</a>
                <button class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Simpan Modul</button>
            </div>
        </form>
    </section>
</x-app-layout>
