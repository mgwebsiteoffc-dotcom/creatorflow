<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationTemplate;
use App\Support\NotificationDispatcher;
use App\Support\SchemaCheck;
use Illuminate\Http\Request;

class NotificationTemplateController extends Controller
{
    public function index()
    {
        if (! SchemaCheck::has('notification_templates')) {
            return view('admin.notification-templates.index', [
                'templates' => collect(),
                'schemaMissing' => true,
            ]);
        }

        // Auto-seed on first visit if missing catalog entries.
        foreach (NotificationTemplate::catalog() as $key => [$audience, $label, $desc, $subject, $body, $wa]) {
            NotificationTemplate::firstOrCreate(
                ['event_key' => $key],
                compact('audience', 'label', 'description') + [
                    'description' => $desc,
                    'email_enabled' => true,
                    'email_subject' => $subject,
                    'email_body' => $body,
                    'whatsapp_enabled' => false,
                    'whatsapp_template_name' => $wa,
                    'inapp_enabled' => true,
                ]
            );
        }

        $templates = NotificationTemplate::orderBy('audience')->orderBy('event_key')->get()->groupBy('audience');
        return view('admin.notification-templates.index', compact('templates') + ['schemaMissing' => false]);
    }

    public function edit(NotificationTemplate $notification_template)
    {
        return view('admin.notification-templates.edit', ['tpl' => $notification_template]);
    }

    public function update(Request $request, NotificationTemplate $notification_template)
    {
        $data = $request->validate([
            'label'          => ['required', 'string', 'max:190'],
            'description'    => ['nullable', 'string', 'max:400'],
            'email_enabled'    => ['nullable', 'boolean'],
            'email_subject'    => ['nullable', 'string', 'max:190'],
            'email_body'       => ['nullable', 'string'],
            'whatsapp_enabled' => ['nullable', 'boolean'],
            'whatsapp_template_name' => ['nullable', 'string', 'max:120'],
            'whatsapp_body_params'   => ['nullable', 'string'], // comma-separated in the form
            'inapp_enabled'    => ['nullable', 'boolean'],
        ]);

        $data['email_enabled']    = (bool) $request->input('email_enabled');
        $data['whatsapp_enabled'] = (bool) $request->input('whatsapp_enabled');
        $data['inapp_enabled']    = (bool) $request->input('inapp_enabled');
        $data['whatsapp_body_params'] = collect(explode(',', (string) ($data['whatsapp_body_params'] ?? '')))
            ->map(fn ($v) => trim($v))->filter()->values()->all();

        $notification_template->update($data);

        return back()->with('status', 'Template saved.');
    }

    public function test(NotificationTemplate $notification_template, Request $request, NotificationDispatcher $dispatcher)
    {
        $data = $request->validate([
            'to_email' => ['nullable', 'email'],
            'to_phone' => ['nullable', 'string', 'max:20'],
        ]);

        $result = $dispatcher->fire(
            $notification_template->event_key,
            [
                'email' => $data['to_email'] ?? null,
                'phone' => $data['to_phone'] ?? null,
                'name'  => 'Test Recipient',
                'type'  => $notification_template->audience === 'creator' ? 'creator' : 'user',
                'id'    => $request->user()->id,
            ],
            [
                'creator_name'    => 'Aisha',
                'brand_name'      => 'Glow & Co.',
                'campaign_title'  => 'Monsoon glow drop',
                'product_title'   => 'SPF 50 Radiance Serum',
                'tracking_number' => 'DLV72190384710',
                'tracking_company'=> 'Delhivery',
                'amount'          => '4,800',
                'followers'       => '62,400',
                'engagement_rate' => '8.1',
                'comment'         => 'Please brighten the color grade and mention the discount code within the first 5 seconds.',
                'link'            => url('/notifications'),
                'message'         => 'Excited to have you on the launch!',
            ]
        );

        return back()->with('status', 'Test fired: '.json_encode($result));
    }
}
