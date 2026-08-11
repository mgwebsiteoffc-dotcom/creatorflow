<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $leads = Lead::query()
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->when($request->get('q'), fn ($q, $term) => $q->where('email', 'like', "%{$term}%")->orWhere('name', 'like', "%{$term}%"))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        $counts = [
            'all'       => Lead::count(),
            'new'       => Lead::where('status', 'new')->count(),
            'contacted' => Lead::where('status', 'contacted')->count(),
            'qualified' => Lead::where('status', 'qualified')->count(),
            'won'       => Lead::where('status', 'won')->count(),
            'lost'      => Lead::where('status', 'lost')->count(),
        ];

        return view('admin.leads.index', compact('leads', 'status', 'counts'));
    }

    public function update(Lead $lead, Request $request)
    {
        $data = $request->validate([
            'status' => ['nullable', 'in:new,contacted,qualified,won,lost'],
            'note'   => ['nullable', 'string', 'max:2000'],
        ]);
        $lead->update($data);
        return back()->with('status', 'Lead updated.');
    }
}
