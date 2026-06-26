<?php

namespace Alyani\Subsystem\Http\Controllers\Web;

use Alyani\Subsystem\DataTables\Manager\ManagerDataTable;
use Alyani\Subsystem\Enums\ManagerStatus;
use Alyani\Subsystem\Http\Requests\Admin\Manager\CreateManagerRequest;
use Alyani\Subsystem\Http\Requests\Admin\Manager\UpdateManagerRequest;
use Alyani\Subsystem\Models\Manager;
use Alyani\Subsystem\Models\Storage;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class ManagerController extends Controller
{
    /**
     * @param ManagerDataTable $datatable
     * @return mixed
     */
    public function list(ManagerDataTable $datatable)
    {
        $roles = Role::query()
            ->where('guard_name', 'web')
            ->orderBy('id', 'asc')
            ->pluck('name', 'id');

        return $datatable->render('subsystem::admin/manager/list', [
            'statuses' => ManagerStatus::valuesTranslate(),
            'roles' => $roles
        ]);
    }

    /**
     * @return View
     */
    public function create()
    {
        $roles = Role::query()
            ->where('guard_name', 'web')
            ->orderBy('id', 'asc')
            ->pluck('name', 'id');

        return view('subsystem::admin.manager.create-edit', [
            'manager' => new Manager(),
            'roles' => $roles
        ]);
    }

    /**
     * @param CreateManagerRequest $request
     * @return RedirectResponse
     */
    public function store(CreateManagerRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        $role = Role::findOrFail($data['role_id']);
    
        $storage = null;
        if (isset($data['avatar'])) {
            $storage = Storage::uploadFile(['file' => $data['avatar'], 'type' => 'image']);
            $data['avatarSID'] = $storage->SID;
            unset($data['avatar']);
        }
        $manager = Manager::create($data);
        $storage?->used($manager, true);
        $manager->assignRole($role);

        return back()->with('success', st('Operation done successfully'));
    }

    /**
     * @param Manager $manager
     * @return Factory|View|Application|\Illuminate\View\View
     */
    public function edit(Manager $manager)
    {
        $roles = Role::query()
            ->where('guard_name', 'web')
            ->orderBy('id', 'asc')
            ->pluck('name', 'id');

        if ($manager->avatarSID) {
            $manager->load('storage');
            $manager->avatarSID = $manager->avatarSID . '.' . $manager->storage->extension ?? '';
        }

        return view('subsystem::admin.manager.create-edit', [
            'manager' => $manager,
            'statuses' => ManagerStatus::valuesTranslate(),
            'roles' => $roles
        ]);
    }

    /**
     * @param Manager $manager
     * @param UpdateManagerRequest $request
     * @return RedirectResponse
     */
    public function update(Manager $manager, UpdateManagerRequest $request)
    {
        $data = $request->validated();

        $role = Role::findOrFail($data['role_id']);

        if (
            auth()->id() == $manager->id &&
            !empty($data['status']) &&
            $data['status'] != ManagerStatus::Active->value
        ) {
            return redirect()->route('admin.manager.edit', $manager)->with('error', st('you can not change your status'));
        }

        if (
            auth()->id() == $manager->id &&
            $manager->roles->first()?->id != $data['role_id']
        ) {
            return redirect()->route('admin.manager.edit', $manager)->with('error', st('you can not change your role'));
        }

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $storage = null;
        if (isset($data['avatar'])) {
            Storage::deleteBySID($manager->avatarSID);
            $storage = Storage::uploadFile(['file' => $data['avatar'], 'type' => 'image']);
            $data['avatarSID'] = $storage->SID;
            unset($data['avatar']);
        }

        $manager->fill($data);
        $manager->save();
        $storage?->used($manager, true);
        $manager->syncRoles($role);

        return redirect()->route('admin.manager.list')->with('success', st('Operation done successfully'));
    }
}
