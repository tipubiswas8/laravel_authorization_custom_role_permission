<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SaPermission;
use App\Models\SaRole;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Models\SaUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller as BaseController;

class SaUserController extends BaseController
{
    use ValidatesRequests;

    function __construct()
    {
        // $this->middleware('permission:user-list|user-create|user-edit|user-delete', ['only' => ['index']]);
        // $this->middleware('permission:user-show', ['only' => ['show']]);
        // $this->middleware('permission:user-create', ['only' => ['create', 'store']]);
        // $this->middleware('permission:user-edit', ['only' => ['edit', 'update']]);
        // $this->middleware('permission:user-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): View
    {
        $data = SaUser::latest()->paginate(5);

        return view('users.index', compact('data'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(): View
    {
        $roles = SaRole::pluck('name', 'name')->all();
        $permissions = SaPermission::get();
        return view('users.create', compact('roles', 'permissions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:sa_users,email',
            'password' => 'required|same:confirm-password',
            'roles' => 'nullable',
            'permissions' => 'nullable',
        ]);

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);

        $permissionsID = [];
        if ($request->input('permissions')) {
            $permissionsID = array_map(
                function ($value) {
                    return (int)$value;
                },
                $request->input('permissions')
            );
        }

        $permissions = SaPermission::whereIn('id', $permissionsID)->pluck('name')->toArray();
        $user = SaUser::create($input);
        if(!empty($request->input('roles'))){
            $user->assignRole($request->input('roles'));
        }
        if ($permissions) {
            $user->givePermissionTo($permissions);
        }

        return redirect()->route('users.index')
            ->with('success', 'User created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id): View
    {
        $user = SaUser::find($id);
        $directPermissions = $user->permissions;
        return view('users.show', compact('user', 'directPermissions'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function edit($id)
    {
        $user = SaUser::findOrFail($id);
        $roles = SaRole::pluck('name', 'name'); // or custom id => label
        $permissions = SaPermission::all();

        return view('users.edit', compact('user', 'roles', 'permissions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, $id): RedirectResponse
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:sa_users,email,' . $id,
            'password' => 'nullable|same:confirm-password',
            'roles' => 'nullable',
            'permissions' => 'nullable',
        ]);

        $user = SaUser::findOrFail($id);

        $input = $request->all();

        // Only hash password if provided
        if (!empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            unset($input['password']);
        }

        $user->update($input);

        // Remove existing roles and permissions
        $user->syncRoles($request->input('roles', []));

        $permissionsID = $request->input('permissions') ? array_map('intval', $request->input('permissions')) : [];

        $permissions = SaPermission::whereIn('id', $permissionsID)->pluck('name')->toArray();
        $user->syncPermissions($permissions);

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id): RedirectResponse
    {
        SaUser::find($id)->delete();
        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully');
    }
}
