<?php

namespace Alyani\Subsystem\Http\Controllers\Web;

use Alyani\Subsystem\DataTables\ArticleCategoryDataTable;
use Alyani\Subsystem\Enums\ActivationStatus;
use Alyani\Subsystem\Enums\Language;
use Alyani\Subsystem\Http\Requests\Admin\ArticleCategory\CreateRequest;
use Alyani\Subsystem\Http\Requests\Admin\ArticleCategory\UpdateRequest;
use Alyani\Subsystem\Models\ArticleCategory;
use Alyani\Subsystem\Models\Storage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ArticleCategoryController extends Controller
{
    /**
     * @param ArticleCategoryDataTable $dataTable
     * @return mixed
     */
    public function list(ArticleCategoryDataTable $dataTable)
    {
        return $dataTable->render('subsystem::admin.articleCategory.list', [
            'statuses' => ActivationStatus::valuesTranslate(),
        ]);
    }

    /**
     * @return View
     */
    public function create()
    {
        $articleCategory = new ArticleCategory();
        $articleCategory->sort_order = ArticleCategory::getSortOrder();
        return view('subsystem::admin.articleCategory.create-edit', [
            'statuses' => ActivationStatus::valuesTranslate(),
            'languages' => Language::valuesTranslate(),
            'articleCategory' => $articleCategory,
        ]);
    }

    /**
     * @param CreateRequest $request
     * @return RedirectResponse
     */
    public function store(CreateRequest $request)
    {
        $data = $request->validated();
        $isFile = isset($data['photo']);

        if ($isFile) {
            $storage = Storage::uploadFile(['file' => $data['photo'], 'type' => 'image']);
            $data['photoSID'] = $storage->SID;
            unset($data['photo']);
        }

        $articleCategory = ArticleCategory::create($data);

        if ($isFile) {
            $storage->used($articleCategory, true);
        }

        return back()->with('success', st('Operation done successfully'));
    }

    /**
     * @param ArticleCategory $articleCategory
     * @return View
     */
    public function edit(ArticleCategory $articleCategory)
    {
        if ($articleCategory->photoSID) {
            $articleCategory->load('storage');
            $articleCategory->photoSID = $articleCategory->photoSID . '.' . $articleCategory->storage->extension ?? '';
        }

        return view('subsystem::admin.articleCategory.create-edit', [
            'statuses' => ActivationStatus::valuesTranslate(),
            'languages' => Language::valuesTranslate(),
            'articleCategory' => $articleCategory,
        ]);
    }

    /**
     * @param UpdateRequest $request
     * @param ArticleCategory $articleCategory
     * @return RedirectResponse
     */
    public function update(UpdateRequest $request, ArticleCategory $articleCategory)
    {
        $data = $request->validated();
        $isFile = isset($data['photo']);

        if ($isFile) {
            Storage::deleteBySID($articleCategory->photoSID);

            $storage = Storage::uploadFile(['file' => $data['photo'], 'type' => 'image']);
            $data['photoSID'] = $storage->SID;
            unset($data['photo']);
        }

        $articleCategory->update([
            'title' => $data['title'],
            'slug' => $data['slug'],
            'description' => $data['description'],
            'sort_order' => $data['sort_order'],
            'meta_title' => $data['meta_title'],
            'meta_description' => $data['meta_description'],
            'meta_keyword' => $data['meta_keyword'],
            'photoSID' => $data['photoSID'] ?? $articleCategory->photoSID,
            'language' => $data['language'],
            'status' => $data['status'],
        ]);
        $articleCategory->save();

        if ($isFile) {
            $storage->used($articleCategory, true);
        }

        return redirect()->route('admin.articleCategory.list')->with('success', st('Operation done successfully'));
    }

    public function delete(ArticleCategory $articleCategory)
    {
        if ($articleCategory->articles->count()) {
            return back()->with('warning', st('The category has active articles, and can not be deleted'));
        }

        Storage::deleteBySID($articleCategory->photoSID);
        $articleCategory->delete();

        return back()->with('success', st('Operation done successfully'));
    }
}
