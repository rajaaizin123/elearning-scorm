<x-app-layout>
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold">{{ isset($course) ? 'Edit Course' : 'Tambah Course' }}</h1>
            <p class="text-sm text-slate-500">Isi data course dengan semester, dosen pengampu, kode, judul, deskripsi, SKS, dan status.</p>
        </div>
        <a href="{{ route('admin.courses.index') }}" class="text-sm text-slate-600 hover:text-slate-900">Kembali ke daftar course</a>
    </div>

    <form method="post" action="{{ isset($course) ? route('admin.courses.update', $course) : route('admin.courses.store') }}" class="max-w-2xl space-y-6">
        @csrf
        @isset($course) @method('put') @endisset

        <div>
            <label for="semester" class="block text-sm font-medium text-slate-700">Semester</label>
            <input id="semester" name="semester" type="text" value="{{ old('semester', optional($course)->semester ?: '') }}" placeholder="Contoh: Ganjil 2026 / 2026-2027" class="mt-1 w-full rounded-md border-slate-300 bg-white px-4 py-2 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500" />
            <p class="mt-1 text-sm text-slate-500">Masukkan semester langsung, tanpa tabel referensi terpisah.</p>
            @error('semester')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="lecturer_id" class="block text-sm font-medium text-slate-700">Dosen Pengampu</label>
            <select id="lecturer_id" name="lecturer_id" class="mt-1 w-full rounded-md border-slate-300 bg-white px-4 py-2 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500">
                <option value="">Pilih dosen</option>
                @foreach($lecturers as $lecturer)
                    <option value="{{ $lecturer->id }}" {{ old('lecturer_id', optional($course)->lecturer_id) == $lecturer->id ? 'selected' : '' }}>
                        {{ $lecturer->name }} ({{ $lecturer->email }})
                    </option>
                @endforeach
            </select>
            <p class="mt-1 text-sm text-slate-500">Pilih dosen yang menjadi penanggung jawab course ini.</p>
            @error('lecturer_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="code" class="block text-sm font-medium text-slate-700">Kode Course</label>
            <input id="code" class="mt-1 w-full rounded-md border-slate-300 bg-white px-4 py-2 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500" name="code" value="{{ old('code', optional($course)->code ?: '') }}" placeholder="Contoh: CSC101" />
            @error('code')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="title" class="block text-sm font-medium text-slate-700">Judul Course</label>
            <input id="title" class="mt-1 w-full rounded-md border-slate-300 bg-white px-4 py-2 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500" name="title" value="{{ old('title', optional($course)->title ?: '') }}" placeholder="Contoh: Pemrograman Web" />
            @error('title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-slate-700">Deskripsi</label>
            <textarea id="description" class="mt-1 w-full rounded-md border-slate-300 bg-white px-4 py-3 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500" name="description" placeholder="Deskripsikan course ini">{{ old('description', optional($course)->description ?: '') }}</textarea>
            @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label for="credit" class="block text-sm font-medium text-slate-700">SKS</label>
                <input id="credit" type="number" min="1" max="10" class="mt-1 w-full rounded-md border-slate-300 bg-white px-4 py-2 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500" name="credit" value="{{ old('credit', optional($course)->credit ?? 3) }}" placeholder="Jumlah SKS" />
                @error('credit')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-slate-700">Status</label>
                <select id="status" name="status" class="mt-1 w-full rounded-md border-slate-300 bg-white px-4 py-2 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500">
                    <option value="draft" {{ old('status', optional($course)->status ?? 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ old('status', optional($course)->status ?? '') === 'published' ? 'selected' : '' }}>Published</option>
                    <option value="archived" {{ old('status', optional($course)->status ?? '') === 'archived' ? 'selected' : '' }}>Archived</option>
                </select>
                <p class="mt-1 text-sm text-slate-500">Tentukan apakah course siap dipublikasikan.</p>
                @error('status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <button class="rounded-md bg-sky-600 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-700">Simpan</button>
    </form>
</x-app-layout>
