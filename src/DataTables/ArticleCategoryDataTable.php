<?php

namespace Alyani\Subsystem\DataTables;

use Alyani\Subsystem\Models\ArticleCategory;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Column;

class ArticleCategoryDataTable extends DataTable
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
                if (request()->filled('slug')) {
                    $query->where('slug', 'like', "%" . request('slug') . "%");
                }
                if (request()->filled('title')) {
                    $query->where('title', 'like', "%" . request('title') . "%");
                }
                if (request()->filled('status')) {
                    $query->where('status', request('status'));
                }
                return $query;
            })
            ->editColumn('photoSID', function ($model) {
                return $this->getImage($model->photoSID);
            })
            ->editColumn('status', function ($model) {
                return $model->status->getTranslate();
            })
            ->addColumn('articles', function ($model) {
                return $this->articlesAction($model);
            })
            ->addColumn('edit', function ($model) {
                return $this->actionEdit(route('admin.articleCategory.edit', $model->id));
            })
            ->addColumn('delete', function ($model) {
                return $this->actionDelete(route('admin.articleCategory.delete', $model->id));
            })
            ->rawColumns(['photoSID', 'articles', 'edit', 'delete'])
            ->setTotalRecords($query->count())
            ->addIndexColumn()
            ->orderColumn('id', ':column $1')
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(ArticleCategory $model): QueryBuilder
    {
        return $model->newQuery()
            ->orderBy('sort_order', 'desc')
            ->select(
                'id',
                'title',
                'slug',
                'status',
                'photoSID',
            )
            ->withCount('articles as articlesCount');
    }

    public function articlesAction($model): string
    {
        return $model->articlesCount ? '<div>
            <a class="btn btn-sm btn-info" href="' . route('admin.article.list') . '?article_category_id=' . $model->id . '" ' . '>
                <i class="far fa-newspaper"></i>&nbsp ' . st('menu.Articles') . ' - ' . $model->articlesCount . '</a>
        </div>' : '<div>
            <button class="btn btn-sm btn-info"' . ' disabled' . '>
                <i class="far fa-newspaper"></i>&nbsp ' . st('No article') . '</button>
        </div>';
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('DT_RowIndex')->title('#')->orderable(false),
            Column::make('photoSID')->title(st('Icon'))->orderable(false),
            Column::make('title')->title(st('Title'))->orderable(false),
            Column::make('slug')->title(st('Slug'))->orderable(false),
            Column::make('status')->title(st('Status'))->orderable(false),
            Column::make('articles')->title(st('menu.Articles'))->orderable(false),
            Column::make('edit')->title(st('Edit'))->orderable(false),
            Column::make('delete')->title(st('Delete'))->orderable(false),
        ];
    }
}
