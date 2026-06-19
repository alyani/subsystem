<?php

namespace Alyani\Subsystem\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Alyani\Subsystem\Models\Role;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;

class RoleDatatable extends DataTable
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
                if (request('name')) {
                    $query->where('name', 'like', '%' . request('name') . '%');
                }
                return $query;
            })
            ->addColumn('edit', function ($model) {
                $allowedToEdit = true;
                foreach (config('subsystem.defaultRoles') as $role) {
                    match (true) {
                        in_array($model->name, $role) => $allowedToEdit = false,
                        default => null,
                    };
                }
                return $allowedToEdit ? $this->actionEdit(route('admin.role.edit', $model->id)) : '-';
            })
            ->addColumn('delete', function ($model) {
                $allowedToDelete = true;
                foreach (config('subsystem.defaultRoles') as $role) {
                    match (true) {
                        in_array($model->name, $role) => $allowedToDelete = false,
                        default => null,
                    };
                }
                return $allowedToDelete ? $this->actionDelete(route('admin.role.delete', $model->id)) : '-';
            })
            ->rawColumns(['edit', 'delete'])
            ->setTotalRecords($query->count())
            ->addIndexColumn()
            ->orderColumn('id', ':column $1')
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Role $model): QueryBuilder
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
            Column::make('name')->title(st('Name'))->orderable(false),
            Column::make('description')->title(st('description'))->orderable(false),
            Column::make('edit')->title(st('Edit'))->orderable(false),
            Column::make('delete')->title(st('Delete'))->orderable(false),
        ];
    }
}
