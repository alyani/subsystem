<?php

namespace Alyani\Subsystem\Http\Controllers\Web;

use Alyani\Subsystem\DataTables\WithdrawalDataTable;

class WithdrawalController extends Controller
{
    /**
     * @param WithdrawalDataTable $dataTable
     * @return mixed
     */
    public function list(WithdrawalDataTable $dataTable)
    {
        return $dataTable->render('subsystem::admin.withdrawal.list');
    }
}
