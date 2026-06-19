<?php

namespace Alyani\Subsystem\DataTables;

use Hekmatinasser\Verta\Facades\Verta;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Services\DataTable as BaseDataTable;

abstract class DataTable extends BaseDataTable
{
    public string $dataTableID = 'datatable';
    public string $pagingType = 'simple_numbers';
    public string $styleDataTable = "<'top'>rt<'row'<'col-sm-7'ip><'col-sm-5 index-dataTables_length'l>>";
    public string $disabled = 'style="cursor: not-allowed; opacity: 0.5;" onclick="return false;"';
    public string $orderDirection = 'desc'; // asc or desc
    public int $pageLength = 50;
    public int $orderByColumn = 0;

    /**
     * Get getColumns definition.
     *
     * @return array
     */
    abstract public function getColumns(): array;

    /**
     * Optional method if you want to use the html builder.
     *
     * Example usage:
     * public function html(): HtmlBuilder
     * {
     * $html = parent::html();
     * $html->{sum method HtmlBuilder}();
     * return $html;
     * }
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId($this->dataTableID)
            ->columns($this->getColumns())
            ->minifiedAjax(
                ajaxParameters: [
                   'headers' => ['X-Requested-With' => 'XMLHttpRequest']
                ]
            )
            ->pageLength($this->pageLength)
            ->pagingType($this->pagingType)
            ->paging(true)
            ->parameters($this->parameters());
    }

    public function parameters(): array
    {
        return [
            'dom' => $this->styleDataTable,
            'paging' => true,
            'responsive' => true,
            'fixedHeader' => true,
            'processing' => true,
            'serverSide' => true,
            'lengthChange' => true,
            'searching' => false,
            'info' => true,
            'autoWidth' => false,
            'deferRender' => false,
            'scrollCollapse' => false,
            'scroller' => false,
            'language' => [
                'url' => url("vendor/subsystem/libs/datatables-" . app()->getLocale() . ".json"),
            ],
            "order" => [
                [
                    $this->orderByColumn,
                    $this->orderDirection,
                ],
            ],
        ];
    }

    public function actionEdit($url = '', $htmlAttributes = ''): string
    {
        if (empty($url)) {
            $htmlAttributes = $this->disabled;
        }

        return '<div>
            <a class="btn btn-sm btn-success" href="' . $url . '" ' . $htmlAttributes . '>
                <i class="far fa-lg fa-edit"></i>&nbsp</a>
        </div>';
    }

    public function actionArchive($isArchived, $archiveUrl = '', $unarchiveUrl = '', $htmlAttributes = ''): string
    {
        if (empty($archiveUrl)) {
            $htmlAttributes = $this->disabled;
        }

        return $isArchived ? '<div>
            <a title="' . st(
            'Unarchive'
        ) . '" class="btn btn-sm btn-warning" ' . $htmlAttributes . ' href="' .
            (!empty($unarchiveUrl) ? $unarchiveUrl : "#") . '" ' . ' onclick="return confirm(\'' . st(
                'Are you sure you want to unarchive this item?'
            ) . '\')">
            <i class="far fa-lg fa-folder-minus"></i>&nbsp</a>
        </div>' : '<div>
            <a title="' . st(
                'archive'
            ) . '" class="btn btn-sm btn-warning" ' . $htmlAttributes . ' href="' . $archiveUrl . '" ' . ' onclick="return confirm(\'' . st(
                'Are you sure you want to archive this item?'
            ) . '\')" >
            <i class="far fa-lg fa-folder-plus"></i>&nbsp</a>
        </div>';
    }

    public function actionDelete($url = '', $htmlAttributes = ''): string
    {
        if (empty($url)) {
            $htmlAttributes = $this->disabled;
        }

        return '<div>
        <a class="btn btn-sm btn-danger" href="' . $url . '" '
            . $htmlAttributes
            . ' onclick="return confirm(\'' . st('Are you sure you want to delete this item?') . '\')">
            <i class="far fa-lg fa-remove"></i>&nbsp</a></div>';
    }

    public function actionShow($url = '', $htmlAttributes = '', $icon = 'fa-eye'): string
    {
        if (empty($url)) {
            $htmlAttributes = $this->disabled;
        }

        return '<div>
            <a class="btn btn-sm btn-primary" href="' . $url . '" ' . $htmlAttributes . '>
                <i class="far fa-lg ' . $icon . '"></i>&nbsp</a>
        </div>';
    }

    public function link($title, $route, $attributes = []): string
    {
        $attributes += [
            'target' => '_blank',
            'class' => '',
            'confirm' => '',
        ];

        return "<a href='{$route}'" . "
                    target='{$attributes['target']}'
                    class='{$attributes['class']}'>{$title}</a>";
    }

    public function getImage($image, $class = 'image-datatable'): string
    {
        if (empty($image)) {
            return "<img class='" . $class . "' src='" . url('vendor/subsystem/images/placeholder.png') . "'>";
        }
        return "<img class='" . $class . "' src='" . route(
            'storage.download',
            [
                    'type' => 'thumbnail',
                    'SID' => $image,
                ],
        ) . "'>";
    }

    public function optional($data): string
    {
        return $data ?? '-';
    }

    public function parseDate($data, $format = 'Y/m/d'): string
    {
        return toJalaliDate($data, $format) ?: '-';
    }

    public function parseAmount($val)
    {
        return parseAmount($val) ?? '-';
    }

    public function parseTimeStamp($val): ?int
    {
        return $val ? Verta::parse($val)->timestamp : null;
    }
}
