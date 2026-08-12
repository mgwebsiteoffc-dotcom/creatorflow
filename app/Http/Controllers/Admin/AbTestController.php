<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbExperiment;
use App\Models\PlatformSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AbTestController extends Controller
{
    public function index()
    {
        abort_unless(PlatformSetting::feature('ab_testing'), 404, 'A/B testing is disabled. Turn it on in Integrations → Feature flags.');
        $experiments = AbExperiment::latest()->paginate(25);

        // Attach per-variant impression + conversion counts.
        $stats = DB::table('ab_events')
            ->selectRaw('experiment_id, variant_key, event, count(*) as cnt')
            ->groupBy('experiment_id','variant_key','event')
            ->get()
            ->groupBy('experiment_id');

        return view('admin.ab.index', compact('experiments','stats'));
    }

    public function create() { return view('admin.ab.edit', ['exp' => new AbExperiment()]); }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['uuid'] = (string) Str::uuid();
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        AbExperiment::create($data);
        return redirect()->route('admin.ab.index')->with('status', 'Experiment created.');
    }

    public function edit(AbExperiment $ab) { return view('admin.ab.edit', ['exp' => $ab]); }

    public function update(Request $request, AbExperiment $ab)
    {
        $ab->update($this->validated($request));
        return back()->with('status', 'Experiment saved.');
    }

    public function destroy(AbExperiment $ab) { $ab->delete(); return back()->with('status', 'Experiment deleted.'); }

    protected function validated(Request $r): array
    {
        $data = $r->validate([
            'name'       => ['required', 'string', 'max:190'],
            'slug'       => ['nullable', 'string', 'max:100', 'regex:/^[a-z0-9\-]+$/'],
            'surface'    => ['required', 'in:landing,hero,pricing,cta,signup'],
            'goal_event' => ['nullable', 'string', 'max:80'],
            'status'     => ['required', 'in:draft,running,paused,concluded'],
            'variant_keys.*'    => ['string', 'max:20'],
            'variant_weights.*' => ['numeric', 'min:0', 'max:100'],
            'variant_labels.*'  => ['nullable', 'string', 'max:190'],
            'variant_copy.*'    => ['nullable', 'string'],
        ]);

        $keys = (array) $r->input('variant_keys', []);
        $weights = (array) $r->input('variant_weights', []);
        $labels = (array) $r->input('variant_labels', []);
        $copy   = (array) $r->input('variant_copy', []);
        $variants = [];
        foreach ($keys as $i => $key) {
            $key = trim((string) $key);
            if ($key === '') continue;
            $variants[] = [
                'key'    => $key,
                'weight' => (int) ($weights[$i] ?? 0),
                'label'  => $labels[$i] ?? null,
                'copy'   => $copy[$i] ?? null,
            ];
        }
        $data['variants'] = $variants ?: [['key' => 'A', 'weight' => 100]];
        if ($data['status'] === 'running') $data['started_at'] = $data['started_at'] ?? now();
        return $data;
    }
}
