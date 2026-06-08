<x-app-layout>
    <div class="mb-8 flex flex-col gap-2">
        <h1 class="text-2xl font-semibold">Dashboard Admin</h1>
        <p class="text-sm text-slate-500">Monitoring sistem LMS, course aktif, pengguna, dan paket SCORM.</p>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <x-stat-card label="Total Pengguna" :value="$stats['users']" />
        <x-stat-card label="Total Course" :value="$stats['courses']" />
        <x-stat-card label="Course Aktif" :value="$stats['active_courses']" />
        <x-stat-card label="Paket SCORM" :value="$stats['scorm_packages']" />
    </div>

    <section class="mt-8 rounded-lg border border-slate-200 bg-white p-5">
        <h2 class="text-base font-semibold">Operasi Sistem</h2>
        <div class="mt-4 grid gap-3 md:grid-cols-3">
            <a class="rounded-md border border-slate-200 px-4 py-3 text-sm font-medium hover:bg-slate-50" href="{{ route('admin.users.index') }}">Manajemen User</a>
            <a class="rounded-md border border-slate-200 px-4 py-3 text-sm font-medium hover:bg-slate-50" href="{{ route('admin.courses.index') }}">Mata Kuliah</a>
            <a class="rounded-md border border-slate-200 px-4 py-3 text-sm font-medium hover:bg-slate-50" href="{{ route('admin.classes.index') }}">Kelas</a>
        </div>
    </section>
</x-app-layout>
