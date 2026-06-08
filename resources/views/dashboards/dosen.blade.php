<x-app-layout>
    @php
        $moduleCount = $courses->sum(fn($course) => $course->modules->count());
        $classCount = $courses->sum('classes_count');
    @endphp

    <div class="mb-8 space-y-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-2xl font-semibold">Dashboard Dosen</h1>
                <p class="mt-2 text-sm text-slate-500">Kelola Modul, upload SCORM, dan Pantau Progres Mahasiswa.</p>
            </div>
            <div class="flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('dosen.modules.index') }}" class="rounded-md bg-sky-600 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-700">Kelola Modul</a>
                <a href="{{ route('dosen.assignments.index') }}" class="rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Kelola Assignment</a>
                <a href="{{ route('dosen.modules.index') }}" class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">Upload SCORM</a>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-3">
            <x-stat-card label="Course Anda" :value="$courses->count()" />
            <x-stat-card label="Total Modul" :value="$moduleCount" />
            <x-stat-card label="Total Kelas" :value="$classCount" />
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold">Menu Dosen</h2>
                    <p class="mt-1 text-sm text-slate-500">Menu dan hak yang tersedia untuk peran dosen.</p>
                </div>
            </div>

            <div class="mt-5 grid gap-4 sm:grid-cols-2">
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <h3 class="text-sm font-semibold text-slate-900">Modul Konten</h3>
                    <p class="mt-2 text-sm text-slate-600">Buat, edit, dan hapus modul pembelajaran untuk course yang Anda ampu.</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <h3 class="text-sm font-semibold text-slate-900">Upload SCORM</h3>
                    <p class="mt-2 text-sm text-slate-600">Unggah paket SCORM pada modul yang sudah dibuat.</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <h3 class="text-sm font-semibold text-slate-900">Monitoring Progres</h3>
                    <p class="mt-2 text-sm text-slate-600">Pantau progres mahasiswa berdasarkan modul dan paket SCORM.</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <h3 class="text-sm font-semibold text-slate-900">Assignment</h3>
                    <p class="mt-2 text-sm text-slate-600">Buat tugas, publish ke mahasiswa, dan lihat submission yang masuk.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid gap-4 lg:grid-cols-3">
        @forelse($courses as $course)
            <article class="course-card">
                <div class="text-xs font-semibold uppercase tracking-wide text-sky-700">{{ $course->code }}</div>
                <h2 class="mt-2 text-lg font-semibold">{{ $course->title }}</h2>
                <p class="mt-2 line-clamp-3 text-sm text-slate-500">{{ $course->description }}</p>

                <div class="mt-5 space-y-3 text-sm text-slate-600">
                    <div class="flex flex-wrap gap-3">
                        <span class="rounded-full bg-slate-100 px-3 py-1">{{ $course->classes_count }} kelas</span>
                        <span class="rounded-full bg-slate-100 px-3 py-1">{{ $course->modules_count }} modul</span>
                    </div>

                    @if($course->modules->isEmpty())
                        <div class="rounded-lg border border-dashed border-slate-300 bg-slate-50 p-4 text-sm text-slate-500">
                            Belum ada modul untuk course ini. Upload SCORM hanya bisa dilakukan setelah modul dibuat.
                        </div>
                    @else
                        @foreach($course->modules as $module)
                            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <div class="text-sm font-semibold text-slate-900">{{ $module->title }}</div>
                                        <div class="mt-1 text-xs text-slate-500">{{ $module->description ?? 'Tidak ada deskripsi modul.' }}</div>
                                    </div>
                                    <span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-700">
                                        {{ $module->scormPackage ? 'SCORM terpasang' : 'Belum ada SCORM' }}
                                    </span>
                                </div>

                                @unless($module->scormPackage)
                                    <form action="{{ route('dosen.scorm-packages.store') }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-3">
                                        @csrf

                                        <input type="hidden" name="learning_module_id" value="{{ $module->id }}">

                                        <div>
                                            <x-input-label for="title-{{ $module->id }}" value="Judul Paket SCORM" />
                                            <x-text-input id="title-{{ $module->id }}" name="title" type="text" value="{{ old('title') }}" class="mt-1 block w-full" placeholder="Contoh: Modul {{ $module->title }}" />
                                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                                        </div>

                                        <div>
                                            <x-input-label for="package-{{ $module->id }}" value="File ZIP SCORM" />
                                            <input id="package-{{ $module->id }}" name="package" type="file" accept=".zip" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500" />
                                            <x-input-error :messages="$errors->get('package')" class="mt-2" />
                                        </div>

                                        <div class="flex items-center justify-end">
                                            <x-primary-button type="submit">Unggah Paket</x-primary-button>
                                        </div>
                                    </form>
                                @endunless
                            </div>
                        @endforeach
                    @endif
                </div>
            </article>
        @empty
            <div class="rounded-lg border border-dashed border-slate-300 p-8 text-sm text-slate-500">
                Belum ada course yang ditugaskan.
            </div>
        @endforelse
    </div>

    <div class="mt-6">{{ $courses->links() }}</div>
</x-app-layout>
