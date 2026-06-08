<x-app-layout>
    <div class="mb-6 flex flex-col gap-2">
        <h1 class="text-2xl font-semibold text-slate-950">Riwayat Kuis</h1>
        <p class="text-sm text-slate-500">Gabungan hasil kuis LMS dan kuis dari paket SCORM.</p>
    </div>

    <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-5 py-4">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Hasil Kuis Tercatat</h2>
                    <p class="mt-1 text-sm text-slate-500">{{ $attempts->count() }} attempt ditemukan.</p>
                </div>
                <a href="{{ route('mahasiswa.dashboard') }}" class="rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                    Kembali
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Jenis</th>
                        <th class="px-5 py-3">Kuis</th>
                        <th class="px-5 py-3">Course</th>
                        <th class="px-5 py-3">Skor</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3">Soal</th>
                        <th class="px-5 py-3">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($attempts as $attempt)
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-4">
                                <span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-700">{{ $attempt['type'] }}</span>
                            </td>
                            <td class="px-5 py-4 font-medium text-slate-900">{{ $attempt['title'] }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $attempt['course'] ?? '-' }}</td>
                            <td class="px-5 py-4 text-slate-900">{{ $attempt['score'] !== null ? number_format((float) $attempt['score'], 2) : '-' }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ ucfirst(str_replace('_', ' ', $attempt['status'] ?? '-')) }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $attempt['questions_count'] }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $attempt['submitted_at']?->format('d M Y H:i') ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-10 text-center text-sm text-slate-500">
                                Belum ada riwayat kuis. Attempt akan muncul setelah kuis LMS atau kuis SCORM disubmit.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</x-app-layout>
