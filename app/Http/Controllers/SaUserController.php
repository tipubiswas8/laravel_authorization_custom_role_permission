<?php

namespace App\Http\Controllers;

use App\Models\SaPermission;
use App\Models\SaRole;
use App\Models\SaUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class SaUserController extends SaPermissionController
{
    protected array $permissionMap = [
            'user-list'   => ['index'],
            'user-show'   => ['show'],
            'user-create' => ['create', 'store'],
            'user-edit'   => ['edit', 'update'],
            'user-delete'   => ['destroy'],
        ];

    function __construct()
    {
        // $this->middleware('permission:user-list|user-create|user-edit|user-delete', ['only' => ['index']]);
        // $this->middleware('permission:user-show', ['only' => ['show']]);
        // $this->middleware('permission:user-create', ['only' => ['create', 'store']]);
        // $this->middleware('permission:user-edit', ['only' => ['edit', 'update']]);
        // $this->middleware('permission:user-delete', ['only' => ['destroy']]);

         $this->applyPermissionMiddleware($this->permissionMap);

    }

    /**
     * Display a listing of users.
     */
    public function index(Request $request): View
    {
        $data = SaUser::latest()->paginate(5);

        return view('users.index', [
            'data' => $data,
            'i' => ($request->input('page', 1) - 1) * 5,
        ]);
    }

    /**
     * Show form to create a user.
     */
    public function create(): View
    {
        $roles = SaRole::pluck('name', 'id');
        $permissions = SaPermission::all();

        return view('users.create', compact('roles', 'permissions'));
    }

    /**
     * Store a new user.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:sa_users,email',
            'password' => 'required|same:confirm-password',
            'roles' => 'nullable|array',
            'permissions' => 'nullable|array',
        ]);

        $user = SaUser::create([
            'name'     => $request->input('name'),
            'email'    => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);

        // Assign roles
        $roles = $request->input('roles', []);
        // $user->assignRole($request->input('roles'));
        if (!empty($roles)) {
            $user->assignRole($roles); // your trait handles array now
        }

        // Assign permissions
        $permissions = $request->input('permissions', []);
        if (!empty($permissions)) {
            $user->givePermissionTo($permissions); // your trait handles array now
        }

        return redirect()->route('users.index')
            ->with('success', 'User created successfully');
    }

    /**
     * Display the specified user.
     */
    public function show($id): View
    {
        $user = SaUser::findOrFail($id);
        $directPermissions = $user->permissions;

        return view('users.show', compact('user', 'directPermissions'));
    }

    /**
     * Show form to edit a user.
     */
    public function edit($id): View
    {
        $user = SaUser::findOrFail($id);
        $roles = SaRole::pluck('name', 'id');
        $permissions = SaPermission::all();

        return view('users.edit', compact('user', 'roles', 'permissions'));
    }

    /**
     * Update the user.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:sa_users,email,' . $id,
            'password' => 'nullable|same:confirm-password',
            'roles' => 'nullable|array',
            'permissions' => 'nullable|array',
        ]);

        $user = SaUser::findOrFail($id);

        $updateData = [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->input('password'));
        }

        $user->update($updateData);

        // Sync roles
        $roleIds = array_map('intval', $request->input('roles', []));
        $user->syncRoles($roleIds);

        // Sync permissions
        $permissionIds = array_map('intval', $request->input('permissions', []));
        $user->syncPermissions($permissionIds);

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully');
    }

    /**
     * Delete the user.
     */
    public function destroy($id): RedirectResponse
    {
        $user = SaUser::findOrFail($id);
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully');
    }
}
