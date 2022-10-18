<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use App\Http\Traits\APIResponse;
use Validator;

class PermissionsController extends Controller
{
    use APIResponse;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $permissions = Permission::paginate(50);

        return $this->success($permissions, 'All permissions!');
    }

    /**
     * Show form for creating permissions
     * 
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('permissions.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name',
        ]);
        // $validator = Validator::make($request->all(),[
        //     'name' => 'required|unique:permissions,name'
        // ]);
        // if ($validator->fails()) {
        //     return $this->error($validator->messages(), '' , 403);
        //   }
        //   dd($request->name);
        $permission = Permission::create(['guard_name' => 'web', 'name' => $request->name]);

        if ($permission) {
            return $this->success(['permission name' => $permission->name], 'permission created successfully!');
        } else {
            return $this->error('Something went wrong!', 'No results', 403);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Permission  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Permission $permission)
    {
        return $this->success($permission, 'permission data!');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Permission $permission)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:permissions,name,' . $permission->id
        ]);
        if ($validator->fails()) {
            return $this->error($validator->messages(), '', 403);
        }

        $permission->update($request->only('name'));
        return $this->success($permission, 'permission updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Permission $permission)
    {
        $permission->delete();

        return $this->success($permission, 'permission deleted successfully');
    }
}
