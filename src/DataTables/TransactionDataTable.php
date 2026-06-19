<?php

namespace Alyani\Subsystem\DataTables;

use Alyani\Subsystem\Models\Transaction;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;

class TransactionDataTable extends DataTable
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
                if (request()->filled('user_id')) {
                    $query->where('user_id', request('user_id'));
                } else if (request()->filled('nickname')) {
                    $query->whereHas('user', function ($q) {
                        $q->where('nickname', 'like', '%' . request('nickname') . '%');
                    });
                }

                if (request()->filled('transactionable_type')) {
                    $query->where('transactionable_type', request('transactionable_type'));
                }

                if (request()->filled('transactionable_id')) {
                    $query->where('transactionable_id', request('transactionable_id'));
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
            ->editColumn('base_amount', function ($model) {
                return number_format(exchange($model->base_amount, $model->currency, $model->currency->display()));
            })
            ->editColumn('VAT_amount', function ($model) {
                return number_format(exchange($model->VAT_amount, $model->currency, $model->currency->display()));
            })
            ->editColumn('amount', function ($model) {
                return number_format(exchange($model->amount, $model->currency, $model->currency->display()));
            })
            ->editColumn('type', function ($model) {
                return $model->type->getTranslate();
            })
            ->editColumn('created_at', function ($model) {
                $date = $model->created_at ? $this->parseDate($model->created_at, 'Y-m-d H:i:s') : '---';
                return  "<div class='left-to-right'>$date</div>";
            })
            ->editColumn('description', function ($model) {
                $desciption = $model->transactionable_type::getPayableTranslate();
                $route = $model->transactionable_type::getPayableDetailAdminRoute($model->transactionable_id);
                if ($route) {
                    $desciption = $this->link($desciption, $route);
                }
                if ($model->description) {
                    $desciption .= " ($model->description) ";
                }
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
    public function query(Transaction $model): QueryBuilder
    {
        return $model->newQuery()
            ->orderBy('id', 'desc')
            ->with(['user:id,nickname']);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        $columns = [
            Column::make('DT_RowIndex')->title('#')->orderable(false),
            Column::make('user')->title(st('User nickname'))->orderable(false),
            Column::make('base_amount')->title(st('Base amount') . ' (' . st('IRT') . ')')->orderable(false),
            Column::make('VAT_amount')->title(st('VAT amount') . ' (' . st('IRT') . ')')->orderable(false),
            Column::make('amount')->title(st('Amount') . ' (' . st('IRT') . ')')->orderable(false),
            Column::make('description')->title(st('Description'))->orderable(false),
            Column::make('type')->title(st('Type'))->orderable(false),
            Column::make('created_at')->title(st('Created at'))->orderable(false),
        ];
        return $columns;
    }
}
