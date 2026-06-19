<?php

namespace Alyani\Subsystem\Http\Controllers\Web;

use Alyani\Subsystem\DataTables\FaqDataTable;
use Alyani\Subsystem\Enums\Language;
use Alyani\Subsystem\Http\Requests\Admin\Faq\CreateRequest;
use Alyani\Subsystem\Http\Requests\Admin\Faq\UpdateRequest;
use Alyani\Subsystem\Models\Faq;
use Alyani\Subsystem\Models\FaqCategory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class FaqController extends Controller
{
    /**
     * @param FaqDataTable $dataTable
     * @return mixed
     */
    public function list(FaqDataTable $datatable)
    {
        $relatedTo = request('relatedTo');
        $relatedID = request('relatedID');

        if ($relatedTo && $relatedID) {
            $className = ucfirst($relatedTo);

            $namespaces = [
                'App\\Models\\',
                'Alyani\\Subsystem\\Models\\',
            ];

            $modelPath = null;
            foreach ($namespaces as $namespace) {
                $candidate = $namespace . $className;
                if (class_exists($candidate)) {
                    $modelPath = $candidate;
                    break;
                }
            }

            if (!$modelPath) {
                return back()->withErrors(st('Class not found', ['class' => $relatedTo]));
            }

            $modelInstance = new $modelPath();
            $relatedObject = $modelInstance::query()
                ->findOrFail($relatedID);

            $faqCategory = FaqCategory::query()
                ->where('morphable_type', $modelPath)
                ->where('morphable_id', $relatedID)
                ->first();

            if (!$faqCategory) {
                $faqCategory = FaqCategory::query()
                    ->create([
                        'slug' => $className . '_' . $relatedID . (isset($relatedObject->language) ? ('_' . $relatedObject->language) : ''),
                        'morphable_type' => $modelPath,
                        'morphable_id' => $relatedID,
                        'language' => $relatedObject->language ?? Language::Fa->value,
                    ]);
            }

            request()->merge(['category_id' => $faqCategory->id]);
        }

        $language = Language::valuesTranslate();
        return $datatable->render('subsystem::admin.faq.list', compact('language'));
    }

    /**
     * @return View
     */
    public function create()
    {
        $faqCategories = FaqCategory::query()
            ->whereNull(['morphable_type', 'morphable_id'])
            ->pluck('title', 'id');
        $language = Language::valuesTranslate();
        return view('subsystem::admin.faq.create', compact('faqCategories', 'language'));
    }

    /**
     * @param CreateRequest $request
     * @return RedirectResponse
     */
    public function store(CreateRequest $request)
    {
        $data = $request->validated();

        if (isset($data['faqCategoryID'])) {
            $faqCategory = FaqCategory::query()
                ->findOrFail($data['faqCategoryID']);

            $data['language'] = $faqCategory->language;
            $data['category_id'] = $data['faqCategoryID'];
        }

        Faq::create($data);

        return back()->with('success', st('Operation done successfully'));
    }

    /**
     * @param Faq $faq
     * @return View
     */
    public function edit(Faq $faq)
    {
        $faqCategories = FaqCategory::query()
            ->whereNull(['morphable_type', 'morphable_id'])
            ->pluck('title', 'id');
        $language = Language::valuesTranslate();
        return view('subsystem::admin.faq.edit', compact('faq', 'faqCategories', 'language'));
    }

    /**
     * @param Faq $faq
     * @param UpdateRequest $request
     * @return RedirectResponse
     */
    public function update(Faq $faq, UpdateRequest $request)
    {
        $data = $request->validated();
        if (empty($data['language']) && isset($data['faqCategoryID'])) {
            $faqCategory = FaqCategory::query()
                ->findOrFail($data['faqCategoryID']);

            $data['language'] = $faqCategory->language;
        }
        $faq->update($data);
        return back()->with('success', st('Operation done successfully'));
    }

    /**
     * @param Faq $faq
     * @return RedirectResponse
     */
    public function delete(Faq $faq)
    {
        $faq->delete();
        return back()->with('success', st('Operation done successfully'));
    }
}
