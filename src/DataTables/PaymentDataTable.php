<?php

namespace Alyani\Subsystem\DataTables;

use Alyani\Subsystem\Enums\PaymentStatus;
use Alyani\Subsystem\Models\Payment;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;

class PaymentDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->filter(function ($query) {
                if (request()->filled('id')) {
                    $query->where('id', request('id'));
                }
                if (request()->filled('user_id')) {
                    $query->where('user_id', request('user_id'));
                }
                if (request()->filled('gateway_reference')) {
                    $query->where('gateway_reference', request('gateway_reference'));
                }
                if (request()->filled('status')) {
                    $query->where('status', request('status'));
                } else if (!request()->has('status')) {
                    $query->where('status', PaymentStatus::Verified);
                }
                if (request()->filled('nickname')) {
                    $query->whereHas('user', function ($q) {
                        $q->where('nickname', 'like', '%' . request('nickname') . '%');
                    });
                }
                if (request()->filled('payment_gateway_id')) {
                    $query->where('payment_gateway_id', request('payment_gateway_id'));
                }
                return $query;
            })
            ->editColumn('user', function ($model) {
                if (!empty($model->user)) {
                    $username = $model->user->nickname ?: '---';
                    $user = $this->link($username ?: 'user#' . $model->user->id, route('admin.user.show', $model->user));
                }
                return $user ?? '---';
            })
            ->editColumn('manager', function ($model) {
                if (!empty($model->manager)) {
                    $manager = $model->manager->name . ' ' . $model->manager->family;
                }
                return $manager ?? '---';
            })
            ->editColumn('payment_gateway', function ($model) {
                if (!empty($model->paymentGateway)) {
                    $paymentGateway = $model->paymentGateway->title[config('app.locale')] ?? current($model->paymentGateway->title);
                }
                return $paymentGateway ?? '---';
            })
            ->editColumn('amount', function ($model) {
                return number_format(exchange($model->amount, $model->currency, $model->currency->display()));
            })
            ->editColumn('status', function ($model) {
                return $model->status->getTranslate();
            })
            ->editColumn('gateway_reference', function ($model) {
                return $this->optional($model->gateway_reference);
            })
            ->editColumn('payment_date', function ($model) {
                $date = $model->payment_date ? $this->parseDate($model->payment_date, 'Y-m-d H:i:s') : '---';
                return  "<div class='left-to-right'>$date</div>";
            })
            ->editColumn('description', function ($model) {
                if (!is_null($model->invoiceable_type)) {
                    $desciption = $model->invoiceable_type::getPayableTranslate();
                    $route = $model->invoiceable_type::getPayableDetailAdminRoute($model->invoiceable_id);
                    if ($route) {
                        $desciption = $this->link($desciption, $route);
                    }
                } else {
                    $desciption = $model->gateway_data['description'] ?? '';
                }
                return $desciption ?: '---';
            })
            // ->addColumn('show', function ($model) {
            //     return $this->actionShow(route('admin.payment.show', $model->id));
            // })
            ->rawColumns(['show', 'user', 'payment_gateway', 'description', 'payment_date'])
            ->setTotalRecords($query->count())
            ->addIndexColumn()
            ->orderColumn('id', ':column $1')
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Payment $model): QueryBuilder
    {
        return $model->newQuery()
            ->orderBy('id', 'desc')
            ->with(['manager', 'paymentGateway', 'user:id,nickname'])
            ->select(
                'id',
                'user_id',
                'manager_id',
                'payment_gateway_id',
                'amount',
                'currency',
                'status',
                'gateway_reference',
                'payment_date',
                'gateway_data',
                'invoiceable_type',
                'invoiceable_id',
            );
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        $columns = [
            Column::make('DT_RowIndex')->title('#')->orderable(false),
            Column::make('user')->title(st('User nickname'))->orderable(false),
            Column::make('payment_gateway')->title(st('Payment gateway'))->orderable(false),
            Column::make('amount')->title(st('Amount') . ' (' . st('IRT') . ')')->orderable(false),
            Column::make('description')->title(st('Description'))->orderable(false),
            Column::make('status')->title(st('Status'))->orderable(false),
            Column::make('gateway_reference')->title(st('Gateway reference'))->orderable(false),
            Column::make('manager')->title(st('Manager'))->orderable(false),
            Column::make('payment_date')->title(st('Paid at'))->orderable(false),
            // Column::make('show')->title(st('Show'))->orderable(false),
        ];
        return $columns;
    }
}
