<?php

namespace Alyani\Subsystem\Http\Controllers\Web;

use Alyani\Subsystem\DataTables\FaqCategoryDataTable;
use Alyani\Subsystem\Enums\Language;
use Alyani\Subsystem\Http\Requests\Admin\FaqCategory\CreateRequest;
use Alyani\Subsystem\Http\Requests\Admin\FaqCategory\UpdateRequest;
use Alyani\Subsystem\Models\FaqCategory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class FaqCategoryController extends Controller
{
    /**
     * @param FaqCategoryDataTable $dataTable
     * @return mixed
     */
    public function list(FaqCategoryDataTable $dataTable)
    {
        $language = Language::valuesTranslate();

        return $dataTable->render('subsystem::admin.faqCategory.list', compact('language'));
    }

    /**
     * @return View
     */
    public function create()
    {
        $language = Language::valuesTranslate();
        return view('subsystem::admin.faqCategory.create', compact('language'));
    }

    /**
     * @param CreateRequest $request
     * @return RedirectResponse
     */
    public function store(CreateRequest $request)
    {
        $data = $request->validated();

        FaqCategory::create($data);

        return back()->with('success', st('Operation done successfully'));
    }

    /**
     * @param FaqCategory $faqCategory
     * @return View
     */
    public function edit(FaqCategory $faqCategory)
    {
        $language = Language::valuesTranslate();
        return view('subsystem::admin.faqCategory.edit', compact('faqCategory', 'language'));
    }

    /**
     * @param FaqCategory $faqCategory
     * @param UpdateRequest $request
     * @return RedirectResponse
     */
    public function update(FaqCategory $faqCategory, UpdateRequest $request)
    {
        $data = $request->validated();

        $faqCategory->update($data);
        $faqCategory->save();

        return redirect()->route('admin.faqCategory.list')->with('success', st('Operation done successfully'));
    }

    /**
     * @param FaqCategory $faqCategory
     * @return RedirectResponse
     */
    public function archive(FaqCategory $faqCategory)
    {
        $faqCategory->archive();
        return back()->with('success', st('Operation done successfully'));
    }

    /**
     * @param FaqCategory $faqCategory
     * @return RedirectResponse
     */
    public function unarchive(FaqCategory $faqCategory)
    {
        $faqCategory->unarchive();
        return back()->with('success', st('Operation done successfully'));
    }
}
