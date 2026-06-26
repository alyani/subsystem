<?php

namespace Alyani\Subsystem\Http\Controllers\Web;

use Alyani\Subsystem\DataTables\RoleDataTable;
use Alyani\Subsystem\Http\Requests\Admin\Role\UpdateRequest;
use Alyani\Subsystem\Http\Requests\Admin\Role\CreateRequest;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function create()
    {
        return view('subsystem::admin.role.create-edit', [
            'role' => new Role(),
            'permissions' => config('subsystemPermissions')
        ]);
    }

    public function store(CreateRequest $request)
    {
        $data = $request->validated();

        $role = Role::create([
            'name' => $data['name'],
            'guard_name' => 'web',
        ]);

        $role->syncPermissions($data['permissions'] ?? []);

        return redirect()->route('admin.role.list')->with('success', st('Operation done successfully'));
    }

    public function edit(Role $role)
    {
        if ($role->name == 'Super Admin') {
            return redirect()->route('admin.role.list')->with('error', st('this action is restricted'));
        }

        return view('subsystem::admin.role.create-edit', [
            'role' => $role,
            'permissions' => config('subsystemPermissions')
        ]);
    }

    public function update(UpdateRequest $request,Role $role)
    {
        $data = $request->validated();

        if ($role->name == 'Super Admin') {
            return redirect()->route('admin.role.list')->with('error', st('this action is restricted'));
        }

        $role->update([
            'name' => $data['name'],
        ]);

        $role->syncPermissions($request->input('permissions', []));

        return redirect()->route('admin.role.list')->with('success', st('Operation done successfully'));
    }

    public function list(RoleDataTable $dataTable)
    {
        return $dataTable->render('subsystem::admin.role.list');
    }

    public function delete(Role $role)
    {
        if ($role->name == 'Super Admin') {
            return redirect()->route('admin.role.list')->with('error', st('this action is restricted'));
        }

        if ($role->users()->exists()) {
            return redirect()->route('admin.role.list')->with('error', st('this role is assigned to one or more managers and cannot be deleted'));
        }

        $role->delete();

        return redirect()->route('admin.role.list')->with('success', st('Operation done successfully'));
    }
}
