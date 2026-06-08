<?php

namespace App\Http\Controllers\Admin;

use App\Domain\User;
use App\Enums\RoleName;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return view('admin.users.index', ['users' => User::query()->latest()->paginate(15)]);
    }

    public function create()
    {
        return view('admin.users.form', [
            'roles' => RoleName::cases(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'role' => ['required', 'string', 'in:'.implode(',', array_map(fn(RoleName $role) => $role->value, RoleName::cases()))],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        User::query()->create($data);

        return redirect()->route('admin.users.index')->with('status', 'User berhasil dibuat.');
    }

    public function edit(User $user)
    {
        return view('admin.users.form', [
            'user' => $user,
            'roles' => RoleName::cases(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $user->update($request->validate([
            'role' => ['required', 'string', 'in:'.implode(',', array_map(fn(RoleName $role) => $role->value, RoleName::cases()))],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,'.$user->id],
            'status' => ['required', 'string', 'max:50'],
        ]));

        return redirect()->route('admin.users.index')->with('status', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return back()->with('status', 'User berhasil dihapus.');
    }
}
