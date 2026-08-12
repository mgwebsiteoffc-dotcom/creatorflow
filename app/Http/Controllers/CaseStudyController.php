<?php

namespace App\Http\Controllers;

use App\Models\CaseStudy;
use App\Models\PlatformSetting;
use App\Support\SchemaCheck;

class CaseStudyController extends Controller
{
    public function index()
    {
        abort_unless(PlatformSetting::feature('case_study_cms'), 404);
        $studies = SchemaCheck::has('case_studies')
            ? CaseStudy::published()->orderByDesc('featured')->orderBy('position')->latest('published_at')->get()
            : collect();
        return view('marketing.case-studies.index', compact('studies'));
    }

    public function show(string $slug)
    {
        abort_unless(PlatformSetting::feature('case_study_cms'), 404);
        abort_unless(SchemaCheck::has('case_studies'), 404);
        $study = CaseStudy::published()->where('slug', $slug)->firstOrFail();
        $related = CaseStudy::published()->where('id', '!=', $study->id)
            ->when($study->industry, fn ($q) => $q->orWhere('industry', $study->industry))
            ->take(3)->get();
        return view('marketing.case-studies.show', compact('study', 'related'));
    }
}
