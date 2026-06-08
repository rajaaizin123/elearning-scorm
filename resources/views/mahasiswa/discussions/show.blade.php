<x-app-layout>
    <div class="mb-6 flex flex-col gap-2">
        <a href="{{ route('mahasiswa.discussions.index') }}" class="text-sm font-semibold text-sky-700 hover:underline">Kembali ke ruang diskusi</a>
        <div class="text-xs font-semibold uppercase tracking-wide text-orange-700">{{ $discussion->course?->code }}</div>
        <h1 class="text-2xl font-semibold text-slate-950">{{ $discussion->title }}</h1>
        <p class="text-sm text-slate-500">{{ $discussion->course?->title }}</p>
    </div>

    @if($errors->any())
        <div class="mb-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    <section class="space-y-4">
        @forelse($discussion->posts as $post)
            <article class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <div class="font-semibold text-slate-900">{{ $post->author?->name ?? 'User' }}</div>
                        <div class="text-xs text-slate-500">{{ $post->created_at?->format('d M Y H:i') }}</div>
                    </div>
                </div>
                <p class="mt-4 whitespace-pre-line text-sm leading-6 text-slate-700">{{ $post->body }}</p>
            </article>
        @empty
            <div class="rounded-lg border border-dashed border-slate-300 bg-white p-8 text-center text-sm text-slate-500">
                Belum ada post.
            </div>
        @endforelse
    </section>

    <section class="mt-6 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
        @if($discussion->is_locked)
            <p class="text-sm font-medium text-slate-600">Diskusi ini dikunci. Balasan baru tidak dapat dikirim.</p>
        @else
            <h2 class="text-base font-semibold text-slate-900">Balas Diskusi</h2>
            <form action="{{ route('mahasiswa.discussions.reply', $discussion) }}" method="POST" class="mt-4 space-y-3">
                @csrf
                <textarea name="body" rows="4" class="block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-sky-500 focus:ring-sky-500" required>{{ old('body') }}</textarea>
                <div class="flex justify-end">
                    <button type="submit" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">Kirim Balasan</button>
                </div>
            </form>
        @endif
    </section>
</x-app-layout>
