<?php

namespace Alyani\Subsystem\Http\Controllers\Web;

use Alyani\Subsystem\DataTables\ArticleDatatable;
use Alyani\Subsystem\Enums\ActivationStatus;
use Alyani\Subsystem\Enums\Language;
use Alyani\Subsystem\Http\Requests\Admin\Article\CreateRequest;
use Alyani\Subsystem\Http\Requests\Admin\Article\UpdateRequest;
use Alyani\Subsystem\Models\Article;
use Alyani\Subsystem\Models\ArticleCategory;
use Alyani\Subsystem\Models\Storage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ArticleController extends Controller
{
    /**
     * @param ArticleDatatable $dataTable
     * @return mixed
     */
    public function list(ArticleDataTable $dataTable)
    {
        return $dataTable->render('subsystem::admin.article.list', [
            'statuses' => ActivationStatus::valuesTranslate(),
            'articleCategories' => ArticleCategory::getForItemPicker(),
        ]);
    }

    /**
     * @return View
     */
    public function create()
    {
        return view('subsystem::admin.article.create-edit', [
            'articleCategories' => ArticleCategory::getForItemPicker(),
            'languages' => Language::valuesTranslate(),
            'article' => new Article(),
        ]);
    }

    /**
     * @param CreateRequest $request
     * @return RedirectResponse
     */
    public function store(CreateRequest $request)
    {
        $data = $request->validated();

        $articleCategoryCount = ArticleCategory::whereIn('id', $data['articleCategories'])->count();
        if (count($data['articleCategories']) != $articleCategoryCount) {
            return back()
                ->withInput()
                ->withErrors([
                    'articleCategories' => st('Article category not found')
                ]);
        }

        $posterExist = isset($data['poster']);
        if ($posterExist) {
            $storage = Storage::uploadFile(['file' => $data['poster'], 'type' => 'image']);
            $data['posterSID'] = $storage->SID;
        }
        unset($data['poster']);

        $article = Article::create($data + ['manager_id' => auth()->id()]);

        if ($posterExist) {
            $storage->used($article, true);
        }
        $article->categories()->sync($data['articleCategories']);

        return back()->with('success', st('Operation done successfully'));
    }

    /**
     * @param Article $article
     * @return View
     */
    public function edit(Article $article)
    {
        $article->load('categories');
        if (!empty($article->posterSID)) {
            $article->load('storage');
            $article->posterSID = $article->posterSID . '.' . $article->storage->extension ?? '';
        }
        return view('subsystem::admin.article.create-edit', [
            'articleCategories' => ArticleCategory::getForItemPicker(),
            'languages' => Language::valuesTranslate(),
            'article' => $article,
            'currentCategories' => $article->categories->pluck('id'),
        ]);
    }

    /**
     * @param UpdateRequest $request
     * @param Article $article
     * @return RedirectResponse
     */
    public function update(UpdateRequest $request, Article $article)
    {
        $data = $request->validated();

        // Validate categories
        $articleCategoryCount = ArticleCategory::whereIn('id', $data['articleCategories'])->count();
        if (count($data['articleCategories']) != $articleCategoryCount) {
            return back()
                ->withInput()
                ->withErrors([
                    'articleCategories' => st('Article category not found')
                ]);
        }

        $posterExist = isset($data['poster']);
        if ($posterExist) {
            Storage::deleteBySID($article->posterSID);

            $storage = Storage::uploadFile(['file' => $data['poster'], 'type' => 'image']);
            $data['posterSID'] = $storage->SID;
        }

        $article->update([
            'title' => $data['title'],
            'slug' => $data['slug'],
            'introduction' => $data['introduction'],
            'content' => $data['content'],
            'reading_time' => $data['reading_time'] ?: null,
            'posterSID' => $data['posterSID'] ?? $article->posterSID,
            'meta_title' => $data['meta_title'],
            'meta_description' => $data['meta_description'],
            'meta_keyword' => $data['meta_keyword'],
            'language' => $data['language'],
        ]);

        if ($posterExist) {
            $storage->used($article, true);
        }

        $article->categories()->sync($data['articleCategories']);

        return redirect()->route('admin.article.list')->with('success', st('Operation done successfully'));
    }

    /**
     * @param Article $article
     * @return RedirectResponse
     */
    public function delete(Article $article)
    {
        Storage::deleteBySID($article->posterSID);
        $article->delete();

        return back()->with('success', st('Operation done successfully'));
    }

    /**
     * @param Article $article
     * @return View
     */
    public function show(Article $article)
    {
        $article->load(['manager', 'storage', 'categories']);
        return view('subsystem::admin.article.show', ['article' => $article]);
    }
}
