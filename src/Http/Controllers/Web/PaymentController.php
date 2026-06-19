<?php

namespace Alyani\Subsystem\Http\Controllers\Web;

use Alyani\Subsystem\DataTables\PaymentDatatable;
use Alyani\Subsystem\Enums\PaymentStatus;
use Alyani\Subsystem\Models\PaymentGateway;

class PaymentController extends Controller
{
    /**
     * @param PaymentDatatable $dataTable
     * @return mixed
     */
    public function list(PaymentDataTable $dataTable)
    {
        return $dataTable->render('subsystem::admin.payment.list', [
            'statuses' => PaymentStatus::valuesTranslate(),
            'paymentGateways' => PaymentGateway::getForItemPicker(),
        ]);
    }
}
