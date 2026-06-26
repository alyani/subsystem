<?php

namespace Alyani\Subsystem\DataTables;

use Alyani\Subsystem\Enums\PaymentStatus;
use Alyani\Subsystem\Models\Manager;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;

class RoleDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('managers', function ($model) {
                return $this->managersAction($model);
            })
            ->addColumn('edit', function ($model) {
                
                return $model->name == 'Super Admin' ?
                    '' :
                    $this->actionEdit(route('admin.role.edit', $model->id));
            })
            ->addColumn('delete', function ($model) {
                return $model->name == 'Super Admin' ?
                    '' :
                    $this->actionDelete(route('admin.role.delete', $model->id));
            })
            ->rawColumns(['edit', 'managers', 'delete'])
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
        return $model->newQuery()
            ->select('*')
            ->selectSub(function ($query) {
                $query->from(config('permission.table_names.model_has_roles'))
                    ->selectRaw('count(*)')
                    ->whereColumn('role_id', 'roles.id')
                    ->where('model_type', Manager::class);
            }, 'managers_count');
    }

    public function managersAction($model): string
    {
        return $model->managers_count ? '<div>
            <a class="btn btn-sm btn-info" href="' . route('admin.manager.list') . '?role_id=' . $model->id . '" ' . '>
                <i class="far fa-diamonds-4"></i>&nbsp ' . st('menu.Managers') . ' - ' . $model->managers_count . '</a>
        </div>' : '<div>
            <button class="btn btn-sm btn-info"' . ' disabled' . '>
                <i class="far fa-diamonds-4"></i>&nbsp ' . st('No manager') . '</button>
        </div>';
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('DT_RowIndex')->title('#')->orderable(false),
            Column::make('name')->title(st('Title'))->orderable(false),
            Column::make('managers')->title(st('menu.Managers'))->orderable(false),
            Column::make('edit')->title(st('Edit'))->orderable(false),
            Column::make('delete')->title(st('Delete'))->orderable(false),
        ];
    }
}
