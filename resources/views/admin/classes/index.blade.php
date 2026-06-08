<x-app-layout>
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-semibold">Manajemen Kelas</h1>
        <a class="rounded-md bg-sky-600 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-700" href="{{ route('admin.classes.create') }}">Tambah Kelas</a>
    </div>
    <div class="overflow-hidden rounded-lg border border-slate-200 bg-white">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-100 text-slate-600">
                <tr>
                    <th class="px-4 py-3">Kelas</th>
                    <th class="px-4 py-3">Course</th>
                    <th class="px-4 py-3">Kapasitas</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @foreach($classes as $class)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $class->name }}</td>
                        <td class="px-4 py-3">{{ $class->course?->title }}</td>
                        <td class="px-4 py-3">{{ $class->capacity }}</td>
                        <td class="px-4 py-3">{{ $class->status }}</td>
                        <td class="px-4 py-3">
                            <div class="flex gap-2">
                                <a href="{{ route('admin.classes.enrollments.index', $class) }}" class="text-sm text-emerald-600 hover:underline">Peserta</a>
                                <a href="{{ route('admin.classes.edit', $class) }}" class="text-sm text-sky-600 hover:underline">Edit</a>
                                <form method="post" action="{{ route('admin.classes.destroy', $class) }}" onsubmit="return confirm('Hapus kelas ini?')">
                                    @csrf
                                    @method('delete')
                                    <button class="text-sm text-red-600 hover:underline">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-5">{{ $classes->links() }}</div>
</x-app-layout>
