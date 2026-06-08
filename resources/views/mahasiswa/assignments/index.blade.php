<x-app-layout>
    <div class="mb-6 flex flex-col gap-2">
        <h1 class="text-2xl font-semibold text-slate-950">Submission Tugas</h1>
        <p class="text-sm text-slate-500">Upload file atau catatan untuk tugas yang tersedia.</p>
    </div>

    @if($errors->any())
        <div class="mb-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="space-y-4">
        @forelse($assignments as $assignment)
            @php
                $submission = $assignment->submissions->first();
                $isLate = $assignment->deadline_at && now()->greaterThan($assignment->deadline_at);
            @endphp

            <article class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wide text-emerald-700">{{ $assignment->course?->code }}</div>
                        <h2 class="mt-1 text-lg font-semibold text-slate-900">{{ $assignment->title }}</h2>
                        @if($assignment->description)
                            <p class="mt-2 text-sm leading-6 text-slate-600">{{ $assignment->description }}</p>
                        @endif
                    </div>
                    <div class="flex flex-wrap gap-2 text-xs font-semibold">
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-700">
                            Deadline: {{ $assignment->deadline_at?->format('d M Y H:i') ?? '-' }}
                        </span>
                        <span class="rounded-full {{ $submission ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }} px-3 py-1">
                            {{ $submission ? 'Sudah submit' : 'Belum submit' }}
                        </span>
                    </div>
                </div>

                @if($submission)
                    <div class="mt-4 rounded-md bg-slate-50 px-4 py-3 text-sm text-slate-600">
                        Submit terakhir: {{ $submission->submitted_at?->format('d M Y H:i') ?? '-' }}
                        @if($submission->file_path)
                            <span class="mx-2 text-slate-300">|</span>
                            <a href="{{ asset('storage/'.$submission->file_path) }}" target="_blank" class="font-semibold text-sky-700 hover:underline">Lihat file</a>
                        @endif
                        <div class="mt-2">
                            Nilai: {{ $submission->score !== null ? number_format((float) $submission->score, 2).' / '.number_format((float) $assignment->max_score, 2) : 'Belum dinilai' }}
                        </div>
                        @if($submission->feedback)
                            <div class="mt-1">Feedback: {{ $submission->feedback }}</div>
                        @endif
                    </div>
                @endif

                <form action="{{ route('mahasiswa.assignments.submissions.store', $assignment) }}" method="POST" enctype="multipart/form-data" class="mt-4 grid gap-3 lg:grid-cols-[1fr_1fr_auto] lg:items-end">
                    @csrf
                    <div>
                        <label for="file-{{ $assignment->id }}" class="block text-sm font-medium text-slate-700">File</label>
                        <input id="file-{{ $assignment->id }}" name="file" type="file" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-sky-500 focus:ring-sky-500">
                    </div>
                    <div>
                        <label for="notes-{{ $assignment->id }}" class="block text-sm font-medium text-slate-700">Catatan</label>
                        <input id="notes-{{ $assignment->id }}" name="notes" type="text" value="{{ old('notes', $submission?->notes) }}" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-sky-500 focus:ring-sky-500" placeholder="Opsional">
                    </div>
                    <button type="submit" class="inline-flex h-10 items-center justify-center rounded-md bg-slate-900 px-4 text-sm font-semibold text-white transition hover:bg-slate-800">
                        {{ $submission ? 'Update' : 'Submit' }}
                    </button>
                </form>

                @if($isLate)
                    <p class="mt-3 text-xs font-medium text-amber-700">Deadline sudah lewat. Submission tetap disimpan jika dosen masih mengizinkan dari sisi aplikasi.</p>
                @endif
            </article>
        @empty
            <div class="rounded-lg border border-dashed border-slate-300 bg-white p-8 text-center text-sm text-slate-500">
                Belum ada tugas aktif untuk course yang kamu ikuti.
            </div>
        @endforelse
    </div>
</x-app-layout>
