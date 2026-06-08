<x-app-layout>
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold">{{ isset($classGroup) ? 'Edit Kelas' : 'Tambah Kelas' }}</h1>
            <p class="text-sm text-slate-500">Pilih course dan dosen pengampu, lalu lengkapi kapasitas dan status kelas.</p>
        </div>
        <a href="{{ route('admin.classes.index') }}" class="text-sm text-slate-600 hover:text-slate-900">Kembali ke daftar kelas</a>
    </div>

    <form method="post" action="{{ isset($classGroup) ? route('admin.classes.update', $classGroup) : route('admin.classes.store') }}" class="max-w-2xl space-y-6">
        @csrf
        @isset($classGroup) @method('put') @endisset

        <div>
            <label for="course_id" class="block text-sm font-medium text-slate-700">Course</label>
            <select id="course_id" name="course_id" class="mt-1 w-full rounded-md border-slate-300 bg-white px-4 py-2 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500">
                <option value="">Pilih course</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}" {{ old('course_id', $classGroup->course_id ?? '') == $course->id ? 'selected' : '' }}>
                        {{ $course->code ?? 'Course ' . $course->id }} - {{ $course->title }}
                    </option>
                @endforeach
            </select>
            <p class="mt-1 text-sm text-slate-500">Ini adalah course yang akan menjadi basis kelas. Pastikan Anda memilih course yang sesuai.</p>
            @error('course_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="lecturer_id" class="block text-sm font-medium text-slate-700">Dosen Pengampu</label>
            <select id="lecturer_id" name="lecturer_id" class="mt-1 w-full rounded-md border-slate-300 bg-white px-4 py-2 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500">
                <option value="">Pilih dosen (opsional)</option>
                @foreach($lecturers as $lecturer)
                    <option value="{{ $lecturer->id }}" {{ old('lecturer_id', $classGroup->lecturer_id ?? '') == $lecturer->id ? 'selected' : '' }}>
                        {{ $lecturer->name }} ({{ $lecturer->email }})
                    </option>
                @endforeach
            </select>
            <p class="mt-1 text-sm text-slate-500">Pilih dosen pengampu untuk kelas ini. Biarkan kosong jika belum ditetapkan.</p>
            @error('lecturer_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="name" class="block text-sm font-medium text-slate-700">Nama Kelas</label>
            <input id="name" class="mt-1 w-full rounded-md border-slate-300 bg-white px-4 py-2 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500" name="name" value="{{ old('name', $classGroup->name ?? '') }}" placeholder="Contoh: Kelas A - Pagi" />
            @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="capacity" class="block text-sm font-medium text-slate-700">Kapasitas</label>
            <input id="capacity" type="number" min="1" max="500" class="mt-1 w-full rounded-md border-slate-300 bg-white px-4 py-2 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500" name="capacity" value="{{ old('capacity', $classGroup->capacity ?? 40) }}" placeholder="Jumlah maksimum peserta" />
            @error('capacity')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="status" class="block text-sm font-medium text-slate-700">Status</label>
            <select id="status" name="status" class="mt-1 w-full rounded-md border-slate-300 bg-white px-4 py-2 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500">
                <option value="active" {{ old('status', $classGroup->status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status', $classGroup->status ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            <p class="mt-1 text-sm text-slate-500">Tentukan apakah kelas tersedia untuk pendaftaran atau tidak.</p>
            @error('status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <button class="rounded-md bg-sky-600 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-700">Simpan</button>
    </form>
</x-app-layout>
