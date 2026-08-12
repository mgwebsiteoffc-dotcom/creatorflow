<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model
{
    protected $fillable = [
        'event_key', 'audience', 'label', 'description',
        'email_enabled', 'email_subject', 'email_body',
        'whatsapp_enabled', 'whatsapp_template_name', 'whatsapp_body_params',
        'inapp_enabled',
    ];

    protected $casts = [
        'email_enabled'        => 'boolean',
        'whatsapp_enabled'     => 'boolean',
        'inapp_enabled'        => 'boolean',
        'whatsapp_body_params' => 'array',
    ];

    /**
     * All events the platform can fire. Used to seed defaults + render admin editor.
     *
     * key           => [audience, label, description, defaultSubject, defaultBody, defaultWaTemplate]
     */
    public static function catalog(): array
    {
        return [
            // ─── Creator-side events ───
            'creator.invited' => [
                'creator',
                'Creator invited to campaign',
                'Sent when a brand invites a creator to a campaign.',
                "You've been invited to {{brand_name}}'s campaign 🎉",
                "Hi {{creator_name}},\n\n{{brand_name}} just invited you to '{{campaign_title}}'.\n\n{{message}}\n\nAccept in the app: {{link}}\n\n– Team CreatorPlex",
                'creator_invited',
            ],
            'creator.application.received' => [
                'creator',
                'Application received',
                'Confirmation to creator when they apply to a campaign.',
                "Application received for {{campaign_title}}",
                "Hi {{creator_name}},\n\nWe received your application for '{{campaign_title}}' at {{brand_name}}.\n\n{{brand_name}} usually reviews within 48 hours. Track it here: {{link}}\n\n– Team CreatorPlex",
                'application_received',
            ],
            'creator.application.approved' => [
                'creator',
                'Application approved',
                'Sent to creator when their application is approved.',
                "🎉 You're in — {{brand_name}} approved you for {{campaign_title}}",
                "Hi {{creator_name}},\n\n{{brand_name}} approved you for '{{campaign_title}}'. Next steps + brief here: {{link}}\n\n– Team CreatorPlex",
                'application_approved',
            ],
            'creator.application.rejected' => [
                'creator',
                'Application rejected',
                'Sent to creator when a brand declines their application.',
                "Update on your application for {{campaign_title}}",
                "Hi {{creator_name}},\n\n{{brand_name}} won't move forward with your application this time — but they may reach out for future campaigns.\n\nBrowse other open campaigns: {{link}}\n\n– Team CreatorPlex",
                'application_rejected',
            ],
            'creator.order.shipped' => [
                'creator',
                'Order shipped to creator',
                'Sent when brand adds tracking to a barter order.',
                "📦 Your {{brand_name}} package is on the way",
                "Hi {{creator_name}},\n\n{{brand_name}} shipped '{{product_title}}' — tracking: {{tracking_number}} ({{tracking_company}}).\n\nView order: {{link}}\n\n– Team CreatorPlex",
                'order_shipped',
            ],
            'creator.content.approved' => [
                'creator',
                'Content approved',
                'Sent when brand approves creator submission.',
                "✅ Content approved for {{campaign_title}}",
                "Hi {{creator_name}},\n\n{{brand_name}} approved your submission. Payout is scheduled after the escrow hold window.\n\nDetails: {{link}}\n\n– Team CreatorPlex",
                'content_approved',
            ],
            'creator.content.changes_requested' => [
                'creator',
                'Change request from brand',
                'Sent when brand asks for content changes.',
                "Change request on {{campaign_title}}",
                "Hi {{creator_name}},\n\n{{brand_name}} requested changes on your submission:\n\n\"{{comment}}\"\n\nRevise here: {{link}}\n\n– Team CreatorPlex",
                'changes_requested',
            ],
            'creator.payout.released' => [
                'creator',
                'Payout released',
                'Sent when a creator payout hits their account.',
                "💰 Payout released — ₹{{amount}}",
                "Hi {{creator_name}},\n\n₹{{amount}} has been released to your account for '{{campaign_title}}'.\n\nView earnings: {{link}}\n\n– Team CreatorPlex",
                'payout_released',
            ],

            // ─── Brand-side events ───
            'brand.application.received' => [
                'brand',
                'New application (to brand)',
                'Sent to brand when a creator applies.',
                "New application from {{creator_name}} for {{campaign_title}}",
                "Hi {{brand_name}},\n\n{{creator_name}} applied to '{{campaign_title}}' — {{followers}} followers · {{engagement_rate}}% ER.\n\nReview here: {{link}}",
                'brand_new_application',
            ],
            'brand.creator.accepted' => [
                'brand',
                'Creator accepted invitation',
                'Sent to brand when a creator accepts a campaign invite.',
                "{{creator_name}} accepted your invite for {{campaign_title}}",
                "Hi {{brand_name}},\n\n{{creator_name}} accepted your invite to '{{campaign_title}}'. Order details + brief here: {{link}}",
                'brand_creator_accepted',
            ],
            'brand.content.submitted' => [
                'brand',
                'Content submitted for review',
                'Sent to brand when a creator submits UGC/content.',
                "🎬 New content from {{creator_name}} for {{campaign_title}}",
                "Hi {{brand_name}},\n\n{{creator_name}} submitted content for review.\n\nReview + approve: {{link}}",
                'brand_content_submitted',
            ],
            'brand.payment.received' => [
                'brand',
                'Payment received',
                'Sent to brand after a successful Razorpay charge.',
                "✅ Payment received — ₹{{amount}}",
                "Hi {{brand_name}},\n\nWe received your payment of ₹{{amount}}. Invoice attached in the billing section: {{link}}",
                'payment_received',
            ],
        ];
    }
}
