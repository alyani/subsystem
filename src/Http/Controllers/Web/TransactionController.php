<?php

namespace Alyani\Subsystem\Http\Controllers\Web;

use Alyani\Subsystem\DataTables\TransactionDataTable;
use App\Models\User;

class TransactionController extends Controller
{
    /**
     * @param TransactionDataTable $dataTable
     * @return mixed
     */
    public function list(TransactionDataTable $dataTable)
    {
        if (request()->filled('user_id')) {
            $user = User::find(request('user_id'));
        }
        return $dataTable->render('subsystem::admin.transaction.list', [
            'user' => $user ?? null,
        ]);
    }
}
