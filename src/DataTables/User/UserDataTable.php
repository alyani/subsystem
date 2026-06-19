<?php

namespace Alyani\Subsystem\DataTables\User;

use Alyani\Subsystem\DataTables\DataTable;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;

class UserDataTable extends DataTable
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
                if (request()->filled('nickname')) {
                    $query->where('nickname', 'like', "%" . request('nickname') . "%");
                }
                if (request()->filled('email')) {
                    $query->where('email', 'like', "%" . request('email') . "%");
                }
                if (request()->filled('mobile')) {
                    $query->where('mobile', 'like', "%" . request('mobile') . "%");
                }
                if (request()->filled('status')) {
                    $query->where('status', request('status'));
                }
                return $query;
            })
            ->addColumn('name', function ($model) {
                return $this->optional($model->name);
            })
            ->addColumn('family', function ($model) {
                return $this->optional($model->family);
            })
            ->editColumn('mobile', function ($model) {
                return preg_replace('/^\+98/', '0', $model->mobile);
            })
            ->editColumn('created_at', function ($model) {
                return $this->parseDate($model->created_at);
            })
            ->editColumn('status', function ($model) {
                return $model->status->getTranslate() ;
            })
            ->addColumn('show', function ($model) {
                return $this->actionShow(route('admin.user.show', $model));
            })
            ->addColumn('edit', function ($model) {
                return $this->actionEdit(route('admin.user.edit', $model));
            })
            ->rawColumns(['show', 'edit'])
            ->setTotalRecords($query->count())
            ->addIndexColumn()
            ->orderColumn('id', ':column $1')
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(User $model): QueryBuilder
    {
        return $model->newQuery()
            ->orderBy('id', 'desc')
            ->select(
                'id',
                'name',
                'family',
                'mobile',
                'created_at',
                'status',
            );
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('DT_RowIndex')->title('#')->orderable(false),
            Column::make('name')->title(st('Name'))->orderable(false),
            Column::make('family')->title(st('Family'))->orderable(false),
            Column::make('mobile')->title(st('Mobile'))->orderable(false),
            Column::make('created_at')->title(st('Created'))->orderable(false),
            Column::make('status')->title(st('Status'))->orderable(false),
            Column::make('show')->title(st('Show'))->orderable(false),
            Column::make('edit')->title(st('Edit'))->orderable(false),
        ];
    }
}
