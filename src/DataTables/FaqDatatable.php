<?php

namespace Alyani\Subsystem\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Alyani\Subsystem\Models\Faq;
use Alyani\Subsystem\Models\FaqCategory;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;

class FaqDatatable extends DataTable
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
                if (request('language')) {
                    $query->where('language', request('language'));
                }
                if (request('category_id')) {
                    $query->where('category_id', request('category_id'));
                } else {
                    $notMorphedCategories = FaqCategory::query()
                        ->whereNull(['morphable_type', 'morphable_id'])
                        ->pluck('id')
                        ->toArray();

                    $query->whereIn('category_id', $notMorphedCategories)
                        ->orWhere('category_id', null);
                }
                return $query;
            })
            ->addColumn('edit', function ($model) {
                return $this->actionEdit(
                    route(
                        'admin.faq.edit',
                        ['faq' => $model->id, 'category_id' => request('category_id') ?? null]
                    )
                );
            })
            ->editColumn('created', function ($model) {
                return $this->parseDate($model->created);
            })
            ->addColumn('category', function ($model) {
                return $this->optional($model->category?->title);
            })
            ->addColumn('delete', function ($model) {
                return $this->actionDelete(route('admin.faq.delete', $model->id));
            })
            ->editColumn('language', function ($model) {
                return st($model->language);
            })
            ->rawColumns(['edit', 'articles', 'archive', 'delete'])
            ->setTotalRecords($query->count())
            ->addIndexColumn()
            ->orderColumn('id', ':column $1')
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Faq $model): QueryBuilder
    {
        return $model->newQuery()
            ->orderBy('id', 'desc')
            ->with('category');
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        $columns = [
            Column::make('DT_RowIndex')->title('#')->orderable(false),
            Column::make('question')->title(st('Question'))->orderable(false),
            Column::make('category')->title(st('Category'))->orderable(false),
            Column::make('language')->title(st('language'))->orderable(false),
            Column::make('sort_order')->title(st('Sort Order'))->orderable(false),
            Column::make('created')->title(st('created'))->orderable(false),
            Column::make('edit')->title(st('Edit'))->orderable(false),
            Column::make('delete')->title(st('Delete'))->orderable(false),
        ];

        if (request('category_id')) {
            unset($columns[2]);
        }

        return $columns;
    }

}
