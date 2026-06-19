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

class ManagerController extends Controller
{
    /**
     * @param ManagerDataTable $datatable
     * @return mixed
     */
    public function list(ManagerDataTable $datatable)
    {
        return $datatable->render('subsystem::admin/manager/list', [
            'statuses' => ManagerStatus::valuesTranslate(),
        ]);
    }

    /**
     * @return View
     */
    public function create()
    {
        return view('subsystem::admin.manager.create-edit', [
            'manager' => new Manager()
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

        $storage = null;
        if (isset($data['avatar'])) {
            $storage = Storage::uploadFile(['file' => $data['avatar'], 'type' => 'image']);
            $data['avatarSID'] = $storage->SID;
            unset($data['avatar']);
        }
        $manager = Manager::create($data);
        $storage?->used($manager, true);

        return back()->with('success', st('Operation done successfully'));
    }

    /**
     * @param Manager $manager
     * @return Factory|View|Application|\Illuminate\View\View
     */
    public function edit(Manager $manager)
    {
        if ($manager->avatarSID) {
            $manager->load('storage');
            $manager->avatarSID = $manager->avatarSID . '.' . $manager->storage->extension ?? '';
        }

        return view('subsystem::admin.manager.create-edit', [
            'manager' => $manager,
            'statuses' => ManagerStatus::valuesTranslate(),
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

        return redirect()->route('admin.manager.list')->with('success', st('Operation done successfully'));
    }

    /**
     * @param Manager $manager
     * @return RedirectResponse
     */
    public function delete(Manager $manager)
    {
        if ($manager->status == ManagerStatus::Deleted->value) {
            return redirect()->route('admin.manager.list')->withErrors(st('Manager already deleted'));
        }
        $manager->status = ManagerStatus::Deleted->value;
        $manager->save();

        return redirect()->route('admin.manager.list')->with('success', st('Operation done successfully'));
    }
}
