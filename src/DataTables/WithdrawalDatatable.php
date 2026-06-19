<?php

namespace Alyani\Subsystem\DataTables;

use Alyani\Subsystem\Enums\WithdrawalStatus;
use Alyani\Subsystem\Models\Withdrawal;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;

class WithdrawalDataTable extends DataTable
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
                if (request()->filled('nickname')) {
                    $query->whereHas('user', function ($q) {
                        $q->where('nickname', 'like', '%' . request('nickname') . '%');
                    });
                }
                if (request()->filled('withdrawal_gateway_id')) {
                    $query->where('withdrawal_gateway_id', request('withdrawal_gateway_id'));
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
            ->editColumn('withdrawal_gateway', function ($model) {
                if (!empty($model->withdrawalGateway)) {
                    $withdrawalGateway = $model->withdrawalGateway->title[config('app.locale')] ?? current($model->withdrawalGateway->title);
                }
                return $withdrawalGateway ?? '---';
            })
            ->editColumn('amount', function ($model) {
                return number_format(exchange($model->amount, $model->currency, $model->currency->display()));
            })
            ->editColumn('status', function ($model) {
                return $model->status->getTranslate();
            })
            ->editColumn('created_at', function ($model) {
                $date = $model->created_at ? $this->parseDate($model->created_at, 'Y-m-d H:i:s') : '---';
                return  "<div class='left-to-right'>$date</div>";
            })
            ->editColumn('description', function ($model) {
                $desciption = $model->gateway_data['description'] ?? '';
                return $desciption ?: '---';
            })
            ->rawColumns(['user', 'description', 'created_at'])
            ->setTotalRecords($query->count())
            ->addIndexColumn()
            ->orderColumn('id', ':column $1')
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Withdrawal $model): QueryBuilder
    {
        return $model->newQuery()
            ->orderBy('id', 'desc')
            ->with(['manager', 'withdrawalGateway', 'user:id,nickname'])
            ->select(
                'id',
                'user_id',
                'manager_id',
                'withdrawal_gateway_id',
                'amount',
                'currency',
                'status',
                'created_at',
                'gateway_data',
            )
            ->where('status', WithdrawalStatus::Verified);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        $columns = [
            Column::make('DT_RowIndex')->title('#')->orderable(false),
            Column::make('user')->title(st('User nickname'))->orderable(false),
            Column::make('withdrawal_gateway')->title(st('Withdrawal gateway'))->orderable(false),
            Column::make('amount')->title(st('Amount') . ' (' . st('IRT') . ')')->orderable(false),
            Column::make('description')->title(st('Description'))->orderable(false),
            Column::make('status')->title(st('Status'))->orderable(false),
            Column::make('manager')->title(st('Manager'))->orderable(false),
            Column::make('created_at')->title(st('Created at'))->orderable(false),
        ];
        return $columns;
    }
}
