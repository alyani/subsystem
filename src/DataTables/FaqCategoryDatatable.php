<?php

namespace Alyani\Subsystem\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Alyani\Subsystem\Models\FaqCategory;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;

class FaqCategoryDatatable extends DataTable
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
                match (request('status')) {
                    'archived' => $query->withoutGlobalScope('archive')->whereNotNull('archived'),
                    'all' => $query->withoutGlobalScope('archive'),
                    default => $query,
                };
                return $query;
            })
            ->addColumn('answer', function ($model) {
                return $this->actionEdit(route('admin.faqCategory.edit', $model->id));
            })
            ->addColumn('archive', function ($model) {
                return $this->actionArchive(
                    $model->archived,
                    route('admin.faqCategory.archive', $model->id),
                    route('admin.faqCategory.unarchive', $model->id)
                );
            })
            ->addColumn('edit', function ($model) {
                return $this->actionEdit(route('admin.faqCategory.edit', $model->id));
            })
            ->addColumn('faqs', function ($model) {
                return $this->faqsAction($model);
            })
            ->rawColumns(['edit', 'question', 'archive', 'faqs'])
            ->setTotalRecords($query->count())
            ->addIndexColumn()
            ->orderColumn('id', ':column $1')
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(FaqCategory $model): QueryBuilder
    {
        return $model->newQuery()
            ->orderBy('id', 'desc')
            ->withCount('faq as faqCount');
    }

    public function faqsAction($model)
    {
        return '<div>
            <a class="btn btn-sm btn-info" href="' . route(
                'admin.faq.list',
                ['category_id' => $model->id]
            ) . '" >' . st('Show') . ' - ' . $model->faqCount .
            '</a>';
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('DT_RowIndex')->title('#')->orderable(false),
            Column::make('title')->title(st('Title'))->orderable(false),
            Column::make('slug')->title(st('Slug'))->orderable(false),
            Column::make('sort_order')->title(st('Sort Order'))->orderable(false),
            Column::make('faqs')->title(st('menu.Faq'))->orderable(false),
            Column::make('archive')->title(st('archive'))->orderable(false),
            Column::make('edit')->title(st('Edit'))->orderable(false),
        ];
    }

}
