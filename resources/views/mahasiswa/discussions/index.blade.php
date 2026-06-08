<x-app-layout>
    <div class="mb-6 flex flex-col gap-2">
        <h1 class="text-2xl font-semibold text-slate-950">Ruang Diskusi</h1>
        <p class="text-sm text-slate-500">Buat pertanyaan atau diskusi untuk course yang kamu ikuti.</p>
    </div>

    @if($errors->any())
        <div class="mb-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    <section class="mb-6 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="text-base font-semibold text-slate-900">Diskusi Baru</h2>
        <form action="{{ route('mahasiswa.discussions.store') }}" method="POST" class="mt-4 space-y-4">
            @csrf
            <div>
                <label for="course_id" class="block text-sm font-medium text-slate-700">Course</label>
                <select id="course_id" name="course_id" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-sky-500 focus:ring-sky-500" required>
                    <option value="">Pilih course</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" @selected(old('course_id') == $course->id)>{{ $course->code }} - {{ $course->title }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="title" class="block text-sm font-medium text-slate-700">Judul</label>
                <input id="title" name="title" type="text" value="{{ old('title') }}" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-sky-500 focus:ring-sky-500" required>
            </div>
            <div>
                <label for="body" class="block text-sm font-medium text-slate-700">Isi Diskusi</label>
                <textarea id="body" name="body" rows="4" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-sky-500 focus:ring-sky-500" required>{{ old('body') }}</textarea>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">Buat Diskusi</button>
            </div>
        </form>
    </section>

    <section class="rounded-lg border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-5 py-4">
            <h2 class="text-base font-semibold text-slate-900">Thread Diskusi</h2>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse($discussions as $discussion)
                <a href="{{ route('mahasiswa.discussions.show', $discussion) }}" class="block p-5 transition hover:bg-slate-50">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <div class="text-xs font-semibold uppercase tracking-wide text-orange-700">{{ $discussion->course?->code }}</div>
                            <h3 class="mt-1 text-base font-semibold text-slate-900">{{ $discussion->title }}</h3>
                            <p class="mt-2 line-clamp-2 text-sm text-slate-600">{{ $discussion->body }}</p>
                        </div>
                        <div class="flex flex-wrap gap-2 text-xs font-semibold">
                            @if($discussion->is_pinned)
                                <span class="rounded-full bg-sky-100 px-2 py-1 text-sky-700">Pinned</span>
                            @endif
                            @if($discussion->is_locked)
                                <span class="rounded-full bg-slate-100 px-2 py-1 text-slate-700">Locked</span>
                            @endif
                            <span class="rounded-full bg-orange-100 px-2 py-1 text-orange-700">{{ $discussion->posts_count }} post</span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="p-8 text-center text-sm text-slate-500">Belum ada diskusi untuk course yang kamu ikuti.</div>
            @endforelse
        </div>
    </section>
</x-app-layout>
