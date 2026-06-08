<x-app-layout>
    <div class="mb-6 flex flex-col gap-2">
        <h1 class="text-2xl font-semibold text-slate-950">Dashboard Mahasiswa</h1>
        <p class="text-sm text-slate-500">Akses materi, tugas, kuis, dan diskusi dari course yang kamu ikuti.</p>
    </div>

    <div class="mb-6 grid gap-3 md:grid-cols-3">
        <x-stat-card label="Course Diikuti" :value="$enrollmentCount" />
        <x-stat-card label="Tugas Aktif" :value="$assignments" />
        <x-stat-card label="Kuis Dijalankan" :value="$quizAttempts" />
    </div>

    <div class="mb-8 grid gap-3 xl:grid-cols-4">
        <a href="#courses" class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm transition hover:border-sky-300 hover:bg-sky-50/40">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Belajar</p>
            <h2 class="mt-2 text-base font-semibold text-slate-900">Materi & SCORM</h2>
            <p class="mt-2 text-sm leading-6 text-slate-500">Lanjutkan modul dari course aktif.</p>
        </a>

        <a href="{{ route('mahasiswa.assignments.index') }}" class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm transition hover:border-emerald-300 hover:bg-emerald-50/40">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Tugas</p>
            <h2 class="mt-2 text-base font-semibold text-slate-900">Submission</h2>
            <p class="mt-2 text-sm leading-6 text-slate-500">Upload tugas dan cek feedback.</p>
        </a>

        <a href="{{ route('mahasiswa.quizzes.index') }}" class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm transition hover:border-violet-300 hover:bg-violet-50/40">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Kuis</p>
            <h2 class="mt-2 text-base font-semibold text-slate-900">Riwayat Kuis</h2>
            <p class="mt-2 text-sm leading-6 text-slate-500">Lihat skor LMS dan SCORM.</p>
        </a>

        <a href="{{ route('mahasiswa.discussions.index') }}" class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm transition hover:border-orange-300 hover:bg-orange-50/40">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Diskusi</p>
            <h2 class="mt-2 text-base font-semibold text-slate-900">Ruang Diskusi</h2>
            <p class="mt-2 text-sm leading-6 text-slate-500">Bertanya dan balas thread.</p>
        </a>
    </div>

    <section id="courses" class="grid gap-4 xl:grid-cols-3">
        @forelse($courses as $course)
            <article class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wide text-sky-700">{{ $course->code }}</div>
                        <h2 class="mt-2 text-lg font-semibold text-slate-900">{{ $course->title }}</h2>
                    </div>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">{{ $course->modules->count() }} modul</span>
                </div>

                @if($course->description)
                    <p class="mt-3 line-clamp-3 text-sm leading-6 text-slate-600">{{ $course->description }}</p>
                @endif

                <div class="mt-5 space-y-2">
                    @forelse($course->modules as $module)
                        @if($module->scormPackage)
                            <a href="{{ route('mahasiswa.scorm.show', $module->scormPackage) }}" class="block rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-medium text-slate-700 transition hover:border-sky-300 hover:bg-white">
                                {{ $module->title }}
                            </a>
                        @endif
                    @empty
                        <div class="rounded-md bg-slate-50 px-3 py-2 text-sm text-slate-500">Belum ada modul SCORM tersedia.</div>
                    @endforelse
                </div>
            </article>
        @empty
            <div class="rounded-lg border border-dashed border-slate-300 bg-white p-8 text-sm text-slate-500">
                Belum ada course aktif. Cek kembali setelah dosen menerbitkan materi.
            </div>
        @endforelse
    </section>

    <section class="mt-8 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="text-base font-semibold text-slate-900">Aktivitas Terakhir</h2>
                <p class="mt-1 text-sm text-slate-500">Progres SCORM yang baru saja kamu akses.</p>
            </div>
            <div class="text-sm text-slate-600">{{ $progress->count() }} item</div>
        </div>

        <div class="mt-5 grid gap-3 sm:grid-cols-2">
            @forelse($progress as $track)
                <div class="rounded-md border border-slate-200 bg-slate-50 p-4">
                    <div class="flex items-center justify-between gap-3">
                        <p class="font-medium text-slate-900">{{ $track->lesson_status ?? 'Sedang belajar' }}</p>
                        <span class="rounded-full bg-white px-2 py-1 text-xs font-semibold text-slate-600">{{ optional($track->last_accessed_at)->format('d M Y') ?? '-' }}</span>
                    </div>
                    <p class="mt-2 text-sm text-slate-600">Progress: {{ round($track->progress ?? 0) }}%</p>
                </div>
            @empty
                <div class="rounded-md border border-dashed border-slate-300 bg-slate-50 p-6 text-sm text-slate-500">
                    Belum ada aktivitas SCORM. Mulai dengan membuka salah satu modul di atas.
                </div>
            @endforelse
        </div>
    </section>
</x-app-layout>
