<?php

namespace Alyani\Subsystem\Http\Controllers\Web;

use App\Models\User;
use Alyani\Subsystem\DataTables\User\UserDataTable;
use Alyani\Subsystem\Http\Requests\Admin\User\CreateRequest;
use Alyani\Subsystem\Http\Requests\Admin\User\UpdateRequest;
use Alyani\Subsystem\Enums\UserStatus;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * @param UserDataTable $datatable
     * @return mixed
     */
    public function list(UserDataTable $datatable)
    {
        return $datatable->render('subsystem::admin.user.list', [
            'statuses' => UserStatus::valuesTranslate(),
        ]);
    }

    /**
     * @return View
     */
    public function create()
    {
        return view('subsystem::admin.user.create');
    }

    /**
     * @param CreateRequest $request
     * @return RedirectResponse
     */
    public function store(CreateRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $user = new User();
        $user->forceFill($data);
        $user->save();

        return back()->with('success', st('Operation done successfully'));
    }

    /**
     * @param User $user
     */
    public function show(User $user)
    {
        return view('subsystem::admin.user.show', [
            'user' => $user,
        ]);
    }

    /**
     * @param User $user
     */
    public function edit(User $user)
    {
        return view('subsystem::admin.user.edit', [
            'statuses' => UserStatus::valuesTranslate(),
            'user' => $user,
        ]);
    }

    /**
     * @param User $user
     * @param UpdateRequest $request
     * @return RedirectResponse
     */
    public function update(User $user, UpdateRequest $request)
    {
        $data = $request->validated();

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->forceFill($data);

        if ($user->isDirty('status') && $user->status === UserStatus::Banned) {
            $user->tokens()->delete();
        }

        $user->save();

        return redirect()->route('admin.user.list')->with('success', st('Operation done successfully'));
    }

    public function updateStatus(User $user)
    {
        $status = request('status');
        if (!in_array($status, UserStatus::values())) {
            return redirect()->route('admin.user.show', $user)->with('warning', st('Illegal Operation'));
        }
        if ($status == UserStatus::Banned->value) {
            $user->tokens()->delete();
        }
        $user->status = $status;
        $user->save();

        return redirect()->route('admin.user.show', $user)->with('success', st('Operation done successfully'));
    }

    /**
     * @param User $user
     * @return RedirectResponse
     */
    public function delete(User $user)
    {
        $user->tokens()->delete();
        $user->delete();

        return redirect()->route('admin.user.list')->with('success', st('Operation done successfully'));
    }
}
