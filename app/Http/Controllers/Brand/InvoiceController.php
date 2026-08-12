<?php

namespace App\Http\Controllers\Brand;

use App\Http\Controllers\Controller;
use App\Models\PaymentRecord;
use App\Models\PlatformSetting;
use App\Support\SchemaCheck;
use App\Support\TenantContext;

/**
 * India-first tax invoice for any brand PaymentRecord.
 *
 * Renders a print-optimised HTML page (`window.print()` prompts the
 * user's browser to save as PDF — zero PHP-side PDF dependency and
 * the layout stays crisp on high-DPI displays).
 *
 * If your GSTIN is in the same state as the brand → CGST + SGST (9% + 9%),
 * otherwise IGST (18%). The 18% rate is India's default for
 * "Online Information & Database Access Retrieval" services (SAC 998439).
 */
class InvoiceController extends Controller
{
    public function show(PaymentRecord $payment, TenantContext $tenant)
    {
        abort_unless($payment->workspace_id === $tenant->id(), 403);
        abort_unless($payment->status === 'succeeded', 404, 'Invoice available only for successful payments.');

        $workspace = $tenant->active();
        $settings  = SchemaCheck::has('platform_settings') ? PlatformSetting::current() : null;

        // Amounts (in paise). The recorded amount is treated as TAX-INCLUSIVE:
        // grossing down keeps the invoice honest — a ₹1,000 top-up is
        // ₹847.46 taxable value + ₹152.54 GST @ 18%.
        $totalCents   = (int) $payment->amount_cents;
        $taxableCents = (int) round($totalCents * 100 / 118); // reverse-out 18%
        $taxCents     = $totalCents - $taxableCents;

        // CGST/SGST vs IGST — India rule: intra-state = split, else IGST.
        // Compare our origin state ($settings->company_state) to the brand's
        // billing address state. If either is missing we default to inter-state
        // (IGST) which is legally safer for a SaaS default.
        $ourState     = $settings?->company_state ?: null;   // e.g. 'Uttar Pradesh'
        $brandState   = $workspace->address_state ?: null;
        $isIntraState = $ourState && $brandState
            && strcasecmp(trim($ourState), trim($brandState)) === 0;

        $cgstCents = $isIntraState ? intdiv($taxCents, 2) : 0;
        $sgstCents = $isIntraState ? ($taxCents - $cgstCents) : 0;
        $igstCents = $isIntraState ? 0 : $taxCents;

        $invoiceNumber = 'CP-'.now()->format('Y').'-'.str_pad((string) $payment->id, 6, '0', STR_PAD_LEFT);

        return view('brand.invoices.show', [
            'payment'       => $payment,
            'workspace'     => $workspace,
            'settings'      => $settings,
            'invoiceNumber' => $invoiceNumber,
            'totalCents'    => $totalCents,
            'taxableCents'  => $taxableCents,
            'taxCents'      => $taxCents,
            'cgstCents'     => $cgstCents,
            'sgstCents'     => $sgstCents,
            'igstCents'     => $igstCents,
            'isIntraState'  => $isIntraState,
        ]);
    }
}
