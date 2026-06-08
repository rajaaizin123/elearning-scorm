<x-app-layout>
    <div class="mb-6 flex flex-col gap-2">
        <a href="{{ route('dosen.assignments.index') }}" class="text-sm font-semibold text-sky-700 hover:underline">Kembali ke assignment</a>
        <h1 class="text-2xl font-semibold text-slate-950">Submission: {{ $assignment->title }}</h1>
        <p class="text-sm text-slate-500">{{ $assignment->course?->code }} - {{ $assignment->course?->title }}</p>
    </div>

    <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-5 py-4">
            <h2 class="text-base font-semibold text-slate-900">{{ $assignment->submissions->count() }} submission masuk</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Mahasiswa</th>
                        <th class="px-5 py-3">Catatan</th>
                        <th class="px-5 py-3">File</th>
                        <th class="px-5 py-3">Submitted</th>
                        <th class="px-5 py-3">Penilaian</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($assignment->submissions as $submission)
                        <tr>
                            <td class="px-5 py-4 font-medium text-slate-900">{{ $submission->student?->name ?? 'Mahasiswa' }}</td>
                            <td class="max-w-md px-5 py-4 text-slate-600">{{ $submission->notes ?? '-' }}</td>
                            <td class="px-5 py-4">
                                @if($submission->file_path)
                                    <a href="{{ asset('storage/'.$submission->file_path) }}" target="_blank" class="font-semibold text-sky-700 hover:underline">Lihat file</a>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-slate-600">{{ $submission->submitted_at?->format('d M Y H:i') ?? '-' }}</td>
                            <td class="px-5 py-4">
                                <form action="{{ route('dosen.assignments.submissions.grade', [$assignment, $submission]) }}" method="POST" class="min-w-72 space-y-2">
                                    @csrf
                                    @method('PUT')
                                    <div class="flex items-center gap-2">
                                        <input name="score" type="number" min="0" max="{{ $assignment->max_score }}" step="0.01" value="{{ old('score', $submission->score) }}" class="w-28 rounded-md border-slate-300 text-sm shadow-sm focus:border-sky-500 focus:ring-sky-500" placeholder="Nilai" required>
                                        <span class="text-xs text-slate-500">/ {{ number_format((float) $assignment->max_score, 2) }}</span>
                                    </div>
                                    <textarea name="feedback" rows="2" class="block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-sky-500 focus:ring-sky-500" placeholder="Feedback opsional">{{ old('feedback', $submission->feedback) }}</textarea>
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-xs text-slate-500">
                                            {{ $submission->graded_at ? 'Dinilai '.$submission->graded_at->format('d M Y H:i') : 'Belum dinilai' }}
                                        </span>
                                        <button type="submit" class="rounded-md bg-sky-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-sky-700">Simpan Nilai</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-10 text-center text-sm text-slate-500">Belum ada submission.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</x-app-layout>
