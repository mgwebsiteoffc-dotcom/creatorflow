<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CaseStudy;
use App\Models\PlatformSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CaseStudyController extends Controller
{
    public function index(Request $request)
    {
        $studies = CaseStudy::query()
            ->when($request->get('q'), fn ($q, $t) => $q->where('brand_name', 'like', "%{$t}%")->orWhere('headline', 'like', "%{$t}%"))
            ->latest()->paginate(25)->withQueryString();

        return view('admin.case-studies.index', compact('studies'));
    }

    public function create() { return view('admin.case-studies.edit', ['study' => new CaseStudy()]); }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = $data['slug'] ?: Str::slug(($data['brand_name'] ?? 'case').'-'.($data['headline'] ?? Str::random(6)));
        $data['uuid'] = (string) Str::uuid();
        $data['metrics'] = $this->parseMetrics($request);

        $study = CaseStudy::create($data);
        return redirect()->route('admin.case-studies.edit', $study)->with('status', 'Case study created.');
    }

    public function edit(CaseStudy $case_study) { return view('admin.case-studies.edit', ['study' => $case_study]); }

    public function update(Request $request, CaseStudy $case_study)
    {
        $data = $this->validated($request);
        $data['metrics'] = $this->parseMetrics($request);
        $case_study->update($data);
        return back()->with('status', 'Case study saved.');
    }

    public function destroy(CaseStudy $case_study)
    {
        $case_study->delete();
        return redirect()->route('admin.case-studies.index')->with('status', 'Case study deleted.');
    }

    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'slug'             => ['nullable', 'string', 'max:190', 'regex:/^[a-z0-9\-]+$/'],
            'brand_name'       => ['required', 'string', 'max:120'],
            'brand_logo_path'  => ['nullable', 'string', 'max:255'],
            'cover_image_path' => ['nullable', 'string', 'max:255'],
            'hero_video_url'   => ['nullable', 'url', 'max:255'],
            'industry'         => ['nullable', 'string', 'max:80'],
            'city'             => ['nullable', 'string', 'max:80'],
            'campaign_type'    => ['nullable', 'in:barter,paid,ugc,hybrid,seeding,giveaway'],
            'headline'         => ['required', 'string', 'max:190'],
            'subheadline'      => ['nullable', 'string', 'max:500'],
            'summary'          => ['nullable', 'string'],
            'challenge'        => ['nullable', 'string'],
            'solution'         => ['nullable', 'string'],
            'results'          => ['nullable', 'string'],
            'quote'            => ['nullable', 'string', 'max:800'],
            'quote_author'     => ['nullable', 'string', 'max:190'],
            'quote_role'       => ['nullable', 'string', 'max:190'],
            'featured'         => ['nullable', 'boolean'],
            'position'         => ['nullable', 'integer'],
            'published_at'     => ['nullable', 'date'],
        ]);
        $data['featured'] = (bool) ($data['featured'] ?? false);
        return $data;
    }

    protected function parseMetrics(Request $request): array
    {
        $labels = (array) $request->input('metric_label', []);
        $values = (array) $request->input('metric_value', []);
        $tones  = (array) $request->input('metric_tone',  []);
        $out = [];
        foreach ($labels as $i => $label) {
            $label = trim((string) $label);
            $value = trim((string) ($values[$i] ?? ''));
            if ($label === '' || $value === '') continue;
            $out[] = ['label' => $label, 'value' => $value, 'tone' => $tones[$i] ?? 'violet'];
        }
        return $out;
    }
}
