<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SaPermission;
use App\Models\SaRole;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller as BaseController;

class SaRoleController extends BaseController
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        // $this->middleware('permission:role-list|role-create|role-edit|role-delete', ['only' => ['index']]);
        // $this->middleware('permission:role-show', ['only' => ['show']]);
        // $this->middleware('permission:role-create', ['only' => ['create', 'store']]);
        // $this->middleware('permission:role-edit', ['only' => ['edit', 'update']]);
        // $this->middleware('permission:role-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): View
    {
        $roles = SaRole::orderBy('id', 'DESC')->paginate(5);
        
        return view('roles.index', compact('roles'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(): View
    {
        $permissions = SaPermission::get();
        return view('roles.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|unique:sa_roles,name',
            'permissions' => 'nullable|array',
        ]);

        $permissionIds = array_map('intval', $request->input('permissions', []));

        $role = SaRole::create([
            'name' => $request->input('name'),
            'guard_name' => 'web', // optional, depending on your system
        ]);

        // Use relationship method to sync
        $role->permissions()->sync($permissionIds);

        return redirect()->route('roles.index')
            ->with('success', 'Role created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id): View
    {
        $role = SaRole::find($id);
        $rolePermissions = SaPermission::join("sa_role_has_sa_permissions", "sa_role_has_sa_permissions.sa_permission_id", "=", "sa_permissions.id")
            ->where("sa_role_has_sa_permissions.sa_role_id", $id)
            ->get();

        return view('roles.show', compact('role', 'rolePermissions'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id): View
    {
        $role = SaRole::find($id);
        $permissions = SaPermission::get();
        $rolePermissions = DB::table("sa_role_has_sa_permissions")->where("sa_role_has_sa_permissions.sa_role_id", $id)
            ->pluck('sa_role_has_sa_permissions.sa_permission_id', 'sa_role_has_sa_permissions.sa_permission_id')
            ->all();
        return view('roles.edit', compact('role', 'permissions', 'rolePermissions'));
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
        $request->validate([
            'name' => 'required',
            'permissions' => 'nullable|array',
        ]);

        $role = SaRole::findOrFail($id);
        $role->name = $request->input('name');
        $role->save();

        $permissionIds = array_map('intval', $request->input('permissions', []));

        $role->permissions()->sync($permissionIds);

        return redirect()->route('roles.index')
            ->with('success', 'Role updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id): RedirectResponse
    {
        DB::table("sa_roles")->where('id', $id)->delete();
        return redirect()->route('roles.index')
            ->with('success', 'Role deleted successfully');
    }
}
