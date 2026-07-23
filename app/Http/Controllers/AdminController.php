<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    public function roles(Request $request)
    {
        abort_unless(Auth::user()->hasRole('admin'), 403);

        $search = $request->query('search');

        $usersQuery = User::query();
        if ($search) {
            $usersQuery->where('email', 'like', "%{$search}%")
                ->orWhere('name', 'like', "%{$search}%");
        }

        $users = $usersQuery->latest()->paginate(10)->withQueryString();
        $roles = Role::pluck('name');

        return view('admin.assign-role', compact('users', 'search', 'roles'));
    }

    public function assignRole(Request $request)
    {
        abort_unless(Auth::user()->hasRole('admin'), 403);

        $data = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'role' => ['required', 'exists:roles,name'],
        ]);

        $user = User::where('email', $data['email'])->first();
        $user->syncRoles([$data['role']]);

        return redirect()->route('admin.roles', ['search' => $user->email])
            ->with('success', "Role '{$data['role']}' asignado a {$user->email}.");
    }
}
