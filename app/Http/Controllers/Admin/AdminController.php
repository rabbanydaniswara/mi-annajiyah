<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index()
    {
        $admins = User::orderByDesc('id')->get();
        $edit = request('edit') ? User::find(request('edit')) : null;
        $logs = \App\Models\ActivityLog::with('user')
            ->orderByDesc('created_at')
            ->paginate(50);
            
        return view('admin.admin', compact('admins', 'edit', 'logs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:50|unique:users,username,' . $request->id,
            'role' => 'required|in:admin,operator',
        ]);

        if ($request->id) {
            $user = User::findOrFail($request->id);
            $data = ['username' => $request->username, 'role' => $request->role];
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }
            $user->update($data);
            \App\Helpers\ActivityLogger::log('update_admin', $user, "Memperbarui data admin {$user->username}");
        } else {
            $request->validate(['password' => 'required|min:6']);
            $user = User::create([
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'role' => $request->role,
            ]);
            \App\Helpers\ActivityLogger::log('create_admin', $user, "Menambahkan admin baru {$user->username}");
        }

        return redirect()->route('admin.admin')->with('success', 'Admin berhasil disimpan');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        if ($user->username === 'admin') {
            return redirect()->route('admin.admin')->with('error', 'Super admin tidak bisa dihapus');
        }
        $username = $user->username;
        $user->delete();
        \App\Helpers\ActivityLogger::log('delete_admin', null, "Menghapus admin {$username}");
        return redirect()->route('admin.admin')->with('success', 'Admin berhasil dihapus');
    }
}
