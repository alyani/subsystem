<?php

namespace Alyani\Subsystem\Http\Controllers\Web;

use Alyani\Subsystem\DataTables\RoleDataTable;
use Alyani\Subsystem\Http\Requests\Admin\Role\UpdateRequest;
use Alyani\Subsystem\Http\Requests\Admin\Role\CreateRequest;
use Alyani\Subsystem\Models\Role;
use Alyani\Subsystem\Models\UserRole;

class RoleController extends Controller
{
    public function create()
    {
        return view('subsystem::admin.role.create');
    }

    public function store(CreateRequest $request)
    {
        $data = $request->validated();

        Role::create($data);

        return back()->with('success', st('Operation done successfully'));
    }

    public function edit(Role $role)
    {
        foreach (config('subsystem.defaultRoles') as $defaultRole) {
            if (in_array($role->name, $defaultRole)) {
                return back()->withErrors(st('This action is restricted'));
            }
        }
        return view('subsystem::admin.role.edit', compact('role'));
    }

    public function update(UpdateRequest $request,Role $role)
    {
        foreach (config('subsystem.defaultRoles') as $defaultRole) {
            if (in_array($role->name, $defaultRole)) {
                return back()->withErrors(st('This action is restricted'));
            }
        }

        $data = $request->validated();

        $role->update($data);

        return redirect()->route('admin.role.list')->with('success', st('Operation done successfully'));
    }

    public function list(RoleDataTable $dataTable)
    {
        return $dataTable->render('subsystem::admin.role.list');
    }

    public function delete(Role $role)
    {
        foreach (config('subsystem.defaultRoles') as $defaultRole) {
            if (in_array($role->name, $defaultRole)) {
                return back()->withErrors(st('This action is restricted'));
            }
        }

        $isInUse = UserRole::query()
            ->where('roleID', $role->id)
            ->exists();
        if ($isInUse) {
            return back()->withErrors(st('Role deletion error'));
        }

        $role->markAsDeleted();

        return redirect()->route('admin.role.list')->with('success', st('Operation done successfully'));
    }
}
