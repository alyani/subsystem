<?php

namespace Alyani\Subsystem\DataTables;

use Alyani\Subsystem\Models\Article;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;

class ArticleDataTable extends DataTable
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
                if (request()->filled('slug')) {
                    $query->where('slug', 'like', "%" . request('slug') . "%");
                }
                if (request()->filled('title')) {
                    $query->where('title', 'like', "%" . request('title') . "%");
                }
                if (request()->filled('withTrashed')) {
                    $query->withTrashed();
                }
                if (request()->filled('article_category_id')) {
                    $query->whereHas('categories', function ($q) {
                        $q->where('article_category_id', request('article_category_id'));
                    });
                }
                return $query;
            })
            ->editColumn('manager', function ($model) {
                if (!empty($model->manager)) {
                    $manager = $model->manager->name . ' ' . $model->manager->family;
                }
                return $manager ?? '---';
            })
            ->editColumn('created', function ($model) {
                return $this->parseDate($model->created_at);
            })
            ->editColumn('updated', function ($model) {
                return $this->parseDate($model->updated_at);
            })
            // ->addColumn('show', function ($model) {
            //     return $this->actionShow(route('admin.article.show', $model->id));
            // })
            ->addColumn('edit', function ($model) {
                return $this->actionEdit(route('admin.article.edit', $model->id));
            })
            ->addColumn('delete', function ($model) {
                return $this->actionDelete(route('admin.article.delete', $model->id));
            })
            ->rawColumns(['show', 'edit', 'delete'])
            ->setTotalRecords($query->count())
            ->addIndexColumn()
            ->orderColumn('id', ':column $1')
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Article $model): QueryBuilder
    {
        return $model->newQuery()
            ->orderBy('id', 'desc')
            ->with('manager')
            ->select(
                'id',
                'title',
                'slug',
                'manager_id',
                'created_at',
                'updated_at',
            );
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        $columns = [
            Column::make('DT_RowIndex')->title('#')->orderable(false),
            Column::make('title')->title(st('Title'))->orderable(false),
            Column::make('slug')->title(st('Slug'))->orderable(false),
            Column::make('manager')->title(st('Manager'))->orderable(false),
            Column::make('created')->title(st('Created at'))->orderable(false),
            Column::make('updated')->title(st('Updated at'))->orderable(false),
            // Column::make('show')->title(st('Show'))->orderable(false),
        ];
        if (!request()->filled('withTrashed')) {
            $columns[] = Column::make('edit')->title(st('Edit'))->orderable(false);
            $columns[] = Column::make('delete')->title(st('Delete'))->orderable(false);
        }
        return $columns;
    }
}
