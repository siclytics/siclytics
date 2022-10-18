<?php

namespace App\Http\Controllers\Api;

use DB;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Traits\APIResponse;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesController extends Controller
{
    use APIResponse;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $roles = Role::paginate(50);
        return $this->success($roles, 'All Roles!');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permissions = Permission::get();
        return view('roles.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|unique:roles,name',
                'permission' => 'required',
            ]
        );
        if ($validator->fails()) {
            return $this->error($validator->messages(), '', 403);
        }
        $role = Role::create(
            [
                'name' => $request->get('name'),
                'service_provider_id' => $request->get('service_provider_id'),
                'organization_id' => $request->get('organization_id'),
        ]);

        $role->syncPermissions($request->get('permission'));
        if ($role) {
            return $this->success(['role name' => $role->name], 'role created successfully!');
        } else {
            return $this->error('Something went wrong!', 'No results', 403);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        $role = $role;
        $rolePermissions = $role->permissions;
        return view('roles.show', compact('role', 'rolePermissions'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role)
    {
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        $permissions = Permission::get();
        return $this->success($role, 'role data');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Role $role, Request $request)
    {

        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'permission' => 'required',
            ]
        );
        if ($validator->fails()) {
            return $this->error($validator->messages(), '', 403);
        }

        $role->update($request->only('name'));

        $role->syncPermissions($request->get('permission'));

        return $this->success($role, 'role updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        $role->delete();
        return $this->success($role, 'role deleted successfully');
    }

    public function assignRoleToUser(Request $request)
    {
        $user = User::find($request->user_id);
        if ($user->syncRoles($request->role_id)) {
            return $this->success($user, 'role assigned successfully');
        }
    }
    public function getUserRole($id)
    {
        $user = User::find($id);
        $roles = $user->getRoleNames();
        if (!empty($roles)) {
            return $this->success($roles, 'role assigned successfully');
        }
    }

    public function assignPermissionRole(Request $request)
    {
        $role = Role::find($request->role_id);

        if ($role->givePermissionTo($request->permissions)) {
            return $this->success($role, 'permission assigned successfully');
        } else {
        }
    }
}
