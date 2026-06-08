<x-app-layout>
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold">{{ isset($user) ? 'Edit User' : 'Tambah User' }}</h1>
            <p class="text-sm text-slate-500">Isi data user dengan nama, email, peran, dan status akses.</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="text-sm text-slate-600 hover:text-slate-900">Kembali ke daftar user</a>
    </div>

    <form method="post" action="{{ isset($user) ? route('admin.users.update', $user) : route('admin.users.store') }}" class="max-w-2xl space-y-6">
        @csrf
        @isset($user) @method('put') @endisset

        <div>
            <label for="name" class="block text-sm font-medium text-slate-700">Nama</label>
            <input id="name" class="mt-1 w-full rounded-md border-slate-300 bg-white px-4 py-2 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500" name="name" value="{{ old('name', $user->name ?? '') }}" placeholder="Contoh: Ahmad S." />
            @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
            <input id="email" class="mt-1 w-full rounded-md border-slate-300 bg-white px-4 py-2 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500" name="email" value="{{ old('email', $user->email ?? '') }}" placeholder="Contoh: user@example.com" />
            @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="role" class="block text-sm font-medium text-slate-700">Role</label>
            <select id="role" name="role" class="mt-1 w-full rounded-md border-slate-300 bg-white px-4 py-2 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500">
                <option value="">Pilih role</option>
                @foreach($roles as $role)
                    <option value="{{ $role->value }}" {{ old('role', $user->role ?? '') === $role->value ? 'selected' : '' }}>
                        {{ ucfirst($role->value) }}
                    </option>
                @endforeach
            </select>
            <p class="mt-1 text-sm text-slate-500">Pilih role berdasarkan fungsi akses. Contoh: admin = manajemen sistem, dosen = ajar, kelola konten, grading, monitoring progres, mahasiswa = akses materi.</p>
            @error('role')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        @unless(isset($user))
            <div>
                <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                <input id="password" type="password" class="mt-1 w-full rounded-md border-slate-300 bg-white px-4 py-2 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500" name="password" placeholder="Minimal 8 karakter" />
                @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        @endunless

        @isset($user)
            <div>
                <label for="status" class="block text-sm font-medium text-slate-700">Status</label>
                <select id="status" name="status" class="mt-1 w-full rounded-md border-slate-300 bg-white px-4 py-2 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500">
                    <option value="active" {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $user->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                <p class="mt-1 text-sm text-slate-500">Atur apakah akun user aktif atau nonaktif.</p>
                @error('status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        @endisset

        <button class="rounded-md bg-sky-600 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-700">Simpan</button>
    </form>
</x-app-layout>
