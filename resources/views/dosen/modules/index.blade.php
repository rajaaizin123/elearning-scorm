<x-app-layout>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-slate-950">Modul & SCORM</h1>
            <p class="mt-1 text-sm text-slate-500">Kelola modul course dan unggah paket SCORM.</p>
        </div>
        <a href="{{ route('dosen.modules.create') }}" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">Tambah Modul</a>
    </div>

    @if($errors->any())
        <div class="mb-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        <div class="grid grid-cols-[1fr_auto] gap-3 border-b border-slate-200 bg-slate-50 px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500">
            <div>Daftar Modul</div>
            <div>{{ $modules->total() }} item</div>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse($modules as $module)
                <article class="p-4">
                    <div class="grid gap-4 lg:grid-cols-[1fr_360px] lg:items-start">
                        <div class="min-w-0">
                            <div class="mb-2 flex flex-wrap items-center gap-2">
                                <span class="text-xs font-semibold uppercase tracking-wide text-sky-700">{{ $module->course->code }}</span>
                                <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-600">Urutan {{ $module->sort_order }}</span>
                                <span class="rounded-full px-2 py-0.5 text-xs font-semibold {{ $module->is_published ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $module->is_published ? 'Terbit' : 'Draft' }}
                                </span>
                                <span class="rounded-full px-2 py-0.5 text-xs font-semibold {{ $module->scormPackage ? 'bg-sky-100 text-sky-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ $module->scormPackage ? 'SCORM tersedia' : 'Belum ada SCORM' }}
                                </span>
                            </div>

                            <h2 class="text-base font-semibold text-slate-900">{{ $module->title }}</h2>
                            <p class="mt-1 text-sm text-slate-500">{{ $module->course->title }}</p>
                            <p class="mt-2 line-clamp-2 text-sm leading-6 text-slate-600">{{ $module->description ?? 'Tidak ada deskripsi modul.' }}</p>

                            @if($module->scormPackage)
                                <div class="mt-3 rounded-md bg-slate-50 px-3 py-2 text-sm text-slate-600">
                                    {{ $module->scormPackage->title }} · SCORM {{ $module->scormPackage->version }}
                                </div>
                            @endif
                        </div>

                        <div class="space-y-3">
                            @if($module->scormPackage)
                                <div class="flex flex-wrap justify-end gap-2">
                                    <a href="{{ route('dosen.modules.edit', $module) }}" class="rounded-md border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Edit Modul</a>
                                    <form method="post" action="{{ route('dosen.modules.destroy', $module) }}" onsubmit="return confirm('Hapus modul ini?');">
                                        @csrf
                                        @method('delete')
                                        <button class="rounded-md px-3 py-2 text-sm font-semibold text-red-600 hover:bg-red-50">Hapus</button>
                                    </form>
                                </div>
                            @else
                                <form action="{{ route('dosen.scorm-packages.store') }}" method="POST" enctype="multipart/form-data" class="rounded-md border border-slate-200 bg-slate-50 p-3">
                                    @csrf
                                    <input type="hidden" name="learning_module_id" value="{{ $module->id }}">

                                    <div class="grid gap-2">
                                        <input name="title" type="text" value="{{ old('title', $module->title) }}" class="block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-sky-500 focus:ring-sky-500" placeholder="Judul paket SCORM" required>
                                        <input name="package" type="file" accept=".zip" class="block w-full rounded-md border-slate-300 bg-white text-sm shadow-sm focus:border-sky-500 focus:ring-sky-500" required>
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('dosen.modules.edit', $module) }}" class="rounded-md border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-white">Edit</a>
                                            <button type="submit" class="rounded-md bg-sky-600 px-3 py-2 text-sm font-semibold text-white hover:bg-sky-700">Upload SCORM</button>
                                        </div>
                                    </div>
                                </form>
                            @endif
                        </div>
                    </div>
                </article>
            @empty
                <div class="p-8 text-center text-sm text-slate-500">
                    Belum ada modul. Buat modul baru untuk menyiapkan course Anda.
                </div>
            @endforelse
        </div>
    </section>

    <div class="mt-5">{{ $modules->links() }}</div>
</x-app-layout>
