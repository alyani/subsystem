<?php

namespace Alyani\Subsystem\DataTables\Manager;

use Alyani\Subsystem\Enums\ManagerStatus;
use Alyani\Subsystem\DataTables\DataTable;
use Alyani\Subsystem\Models\Manager;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;

class ManagerDataTable extends DataTable
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
                if (request()->filled('mobile')) {
                    return $query->where('mobile', 'like', "%" . request('mobile') . "%");
                }
            })
            ->editColumn('avatarSID', function ($model) {
                return $this->getImage($model->avatarSID);
            })
            ->addColumn('name', function ($model) {
                return $this->optional($model->name);
            })
            ->addColumn('family', function ($model) {
                return $this->optional($model->family);
            })
            ->editColumn('status', function ($model) {
                return ManagerStatus::valuesTranslate()[$model->status] ?? '-';
            })
            ->editColumn('mobile', function ($model) {
                return preg_replace('/^\+98/', '0', $model->mobile);
            })
            ->addColumn('email', function ($model) {
                return $this->optional($model->email);
            })
            ->addColumn('edit', function ($model) {
                if ($model->status != ManagerStatus::Deleted->value) {
                    return $this->actionEdit(route('admin.manager.edit', $model));
                }
                return '';
            })
            ->addColumn('delete', function ($model) {
                if ($model->status != ManagerStatus::Deleted->value) {
                    return $this->actionDelete(route('admin.manager.delete', $model));
                }
                return '';
            })
            ->rawColumns(['edit', 'delete', 'avatarSID'])
            ->setTotalRecords($query->count())
            ->addIndexColumn()
            ->orderColumns(['id'], ':column $1')
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Manager $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('DT_RowIndex')->title('#')->orderable(false),
            Column::make('avatarSID')->title(st('Avatar'))->orderable(false),
            Column::make('name')->title(st('Name'))->orderable(false),
            Column::make('family')->title(st('Family'))->orderable(false),
            Column::make('mobile')->title(st('Mobile'))->orderable(false),
            Column::make('email')->title(st('Email'))->orderable(false),
            Column::make('status')->title(st('Status'))->orderable(false),
            Column::make('edit')->title(st('Edit'))->orderable(false),
            Column::make('delete')->title(st('Delete'))->orderable(false),
        ];
    }
}
