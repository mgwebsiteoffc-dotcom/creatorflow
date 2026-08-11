<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'    => ['required', 'string', 'max:190'],
            'email'   => ['required', 'email', 'max:190'],
            'company' => ['nullable', 'string', 'max:190'],
            'reason'  => ['nullable', 'in:demo,pricing,partnership,other'],
            'message' => ['nullable', 'string', 'max:5000'],
        ]);

        Lead::create($data + [
            'source'       => $request->input('source', 'contact_form'),
            'utm_source'   => $request->input('utm_source'),
            'utm_campaign' => $request->input('utm_campaign'),
            'utm_medium'   => $request->input('utm_medium'),
            'ip'           => $request->ip(),
            'user_agent'   => substr((string) $request->userAgent(), 0, 500),
            'status'       => 'new',
        ]);

        return back()->with('status', "Thanks! We'll be in touch within one business day.");
    }
}
