<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index()
    {
        $admins = User::orderByDesc('id')->get();
        $edit = request('edit') ? User::find(request('edit')) : null;
        $logsQuery = ActivityLog::with('user');

        if (request()->filled('log_user')) {
            request('log_user') === 'system'
                ? $logsQuery->whereNull('user_id')
                : $logsQuery->where('user_id', request('log_user'));
        }

        if (request()->filled('log_action')) {
            $logsQuery->where('action', request('log_action'));
        }

        if (request()->filled('log_model')) {
            $logsQuery->where('model_type', request('log_model'));
        }

        if (request()->filled('log_dari')) {
            $logsQuery->whereDate('created_at', '>=', request('log_dari'));
        }

        if (request()->filled('log_sampai')) {
            $logsQuery->whereDate('created_at', '<=', request('log_sampai'));
        }

        $logs = $logsQuery->orderByDesc('created_at')->paginate(50)->withQueryString();
        $usersForFilter = User::orderBy('username')->get(['id', 'username']);
        $actionList = ActivityLog::whereNotNull('action')->distinct()->orderBy('action')->pluck('action');
        $modelList = ActivityLog::whereNotNull('model_type')->distinct()->orderBy('model_type')->pluck('model_type');

        return view('admin.admin', compact('admins', 'edit', 'logs', 'usersForFilter', 'actionList', 'modelList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id' => 'nullable|exists:users,id',
            'username' => 'required|string|max:50|unique:users,username,'.$request->id,
            'role' => 'required|in:admin,operator',
            'password' => ($request->id ? 'nullable' : 'required').'|string|min:8|confirmed',
        ]);

        if ($request->id) {
            $user = User::findOrFail($request->id);
            if ($this->wouldRemoveLastAdmin($user, $validated['role'])) {
                return back()
                    ->withInput()
                    ->with('error', 'Minimal harus ada satu akun admin aktif.');
            }

            $data = ['username' => $validated['username'], 'role' => $validated['role']];
            if ($request->filled('password')) {
                $data['password'] = Hash::make($validated['password']);
                $data['active_session_id'] = null;
            }
            $user->update($data);
            ActivityLogger::log('update_admin', $user, "Memperbarui data admin {$user->username}");
        } else {
            $user = User::create([
                'username' => $validated['username'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
            ]);
            ActivityLogger::log('create_admin', $user, "Menambahkan admin baru {$user->username}");
        }

        return redirect()->route('admin.admin')->with('success', 'Admin berhasil disimpan');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        if ($user->is(Auth::user())) {
            return redirect()->route('admin.admin')->with('error', 'Akun yang sedang digunakan tidak bisa dihapus.');
        }

        if ($this->wouldRemoveLastAdmin($user, null)) {
            return redirect()->route('admin.admin')->with('error', 'Minimal harus ada satu akun admin aktif.');
        }

        if ($user->username === 'admin') {
            return redirect()->route('admin.admin')->with('error', 'Super admin tidak bisa dihapus');
        }
        $username = $user->username;
        $user->delete();
        ActivityLogger::log('delete_admin', null, "Menghapus admin {$username}");

        return redirect()->route('admin.admin')->with('success', 'Admin berhasil dihapus');
    }

    private function wouldRemoveLastAdmin(User $user, ?string $newRole): bool
    {
        if ($user->role !== 'admin') {
            return false;
        }

        if ($newRole === 'admin') {
            return false;
        }

        return ! User::where('role', 'admin')
            ->whereKeyNot($user->getKey())
            ->exists();
    }
}
