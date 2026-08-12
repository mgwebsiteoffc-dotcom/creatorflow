<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $invoiceNumber }} · Tax invoice · CreatorPlex</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --ink:#0f172a; --muted:#64748b; --line:#e2e8f0; --grad-a:#7c3aed; --grad-b:#ec4899; --grad-c:#f59e0b; }
        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; font-family: 'Inter', sans-serif; color: var(--ink); background: #f8fafc; -webkit-print-color-adjust: exact; print-color-adjust: exact; }

        .page-shell { max-width: 820px; margin: 24px auto; }

        .toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; padding: 10px 16px; background: #fff; border: 1px solid var(--line); border-radius: 12px; }
        .toolbar button, .toolbar a { display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; border-radius: 8px; border: 1px solid var(--line); background: #fff; font-size: 13px; font-weight: 600; color: var(--ink); text-decoration: none; cursor: pointer; }
        .toolbar .primary { background: var(--ink); color: #fff; border-color: var(--ink); }

        .paper { background: #fff; border: 1px solid var(--line); border-radius: 12px; overflow: hidden; }
        .head { padding: 32px; background: linear-gradient(135deg, var(--grad-a), var(--grad-b) 60%, var(--grad-c)); color: #fff; display: flex; justify-content: space-between; align-items: flex-start; gap: 24px; }
        .head .brand { display: flex; gap: 12px; align-items: center; }
        .head .brand-mark { width: 44px; height: 44px; border-radius: 12px; background: #fff; color: var(--grad-a); font-weight: 900; display: grid; place-items: center; }
        .head h1 { margin: 0; font-size: 22px; letter-spacing: -0.01em; }
        .head p  { margin: 2px 0 0; font-size: 12px; opacity: .9; }
        .head .meta { text-align: right; font-size: 12px; }
        .head .meta strong { font-size: 15px; display: block; margin-top: 2px; }

        .body { padding: 32px; }
        .cols { display: grid; grid-template-columns: 1fr 1fr; gap: 32px; margin-bottom: 24px; }
        .col h4 { margin: 0 0 6px; font-size: 10px; letter-spacing: .12em; text-transform: uppercase; color: var(--muted); font-weight: 700; }
        .col p  { margin: 2px 0; font-size: 13px; line-height: 1.5; }
        .col p.name { font-size: 15px; font-weight: 700; color: var(--ink); }

        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        thead th { text-align: left; font-size: 10px; letter-spacing: .1em; text-transform: uppercase; color: var(--muted); padding: 8px 12px; border-bottom: 1px solid var(--line); font-weight: 700; }
        thead th.num, td.num { text-align: right; }
        tbody td { padding: 12px; font-size: 13px; border-bottom: 1px solid var(--line); vertical-align: top; }
        tbody td.desc { color: var(--ink); }
        tbody td.desc small { display: block; color: var(--muted); font-size: 11px; margin-top: 2px; }
        tfoot td { padding: 8px 12px; font-size: 13px; }
        tfoot tr.total td { font-weight: 800; font-size: 16px; color: var(--ink); border-top: 2px solid var(--ink); padding-top: 12px; }

        .notice { margin-top: 24px; padding: 12px 16px; background: #f1f5f9; border-radius: 8px; font-size: 11px; line-height: 1.55; color: var(--muted); }
        .foot { padding: 16px 32px 32px; font-size: 11px; color: var(--muted); border-top: 1px solid var(--line); }
        .badge { display: inline-block; padding: 3px 8px; border-radius: 999px; font-size: 10px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; }
        .badge.paid { background: #dcfce7; color: #166534; }

        @media print {
            body { background: #fff; }
            .toolbar { display: none; }
            .page-shell { max-width: none; margin: 0; padding: 0; }
            .paper { border: 0; border-radius: 0; }
        }
    </style>
</head>
<body>

<div class="page-shell">
    <div class="toolbar">
        <a href="{{ url()->previous() }}">← Back</a>
        <button class="primary" onclick="window.print()">Download PDF (Print → Save as PDF)</button>
    </div>

    <div class="paper">
        {{-- =============== HEAD =============== --}}
        <div class="head">
            <div class="brand">
                <div class="brand-mark">CP</div>
                <div>
                    <h1>Tax Invoice</h1>
                    <p>{{ $isIntraState ? 'CGST + SGST · Intra-state supply' : 'IGST · Inter-state supply' }}</p>
                </div>
            </div>
            <div class="meta">
                Invoice #
                <strong>{{ $invoiceNumber }}</strong>
                <p style="margin-top:8px;">Issue date<br><strong style="font-size:13px;">{{ optional($payment->paid_at ?: $payment->created_at)->format('d M Y') }}</strong></p>
                <p style="margin-top:8px;"><span class="badge paid">Paid</span></p>
            </div>
        </div>

        {{-- =============== PARTIES =============== --}}
        <div class="body">
            <div class="cols">
                {{-- Seller / From --}}
                <div class="col">
                    <h4>Billed by</h4>
                    <p class="name">{{ $settings?->company_legal_name ?: 'CreatorPlex Technologies Pvt. Ltd.' }}</p>
                    @if($settings?->company_address_line1) <p>{{ $settings->company_address_line1 }}</p> @endif
                    @if($settings?->company_address_line2) <p>{{ $settings->company_address_line2 }}</p> @endif
                    <p>
                        {{ collect([$settings?->company_city, $settings?->company_state, $settings?->company_postal])->filter()->join(', ') ?: 'Ghaziabad, Uttar Pradesh 201001' }}
                    </p>
                    @if($settings?->company_gstin) <p><strong>GSTIN:</strong> {{ $settings->company_gstin }}</p> @endif
                    @if($settings?->company_pan)   <p><strong>PAN:</strong> {{ $settings->company_pan }}</p> @endif
                    <p><strong>SAC code:</strong> 998439 — Online information / DB access &amp; retrieval</p>
                </div>

                {{-- Buyer / To --}}
                <div class="col">
                    <h4>Billed to</h4>
                    <p class="name">{{ $workspace->legal_name ?: $workspace->name }}</p>
                    @if($workspace->address_line1) <p>{{ $workspace->address_line1 }}</p> @endif
                    @if($workspace->address_line2) <p>{{ $workspace->address_line2 }}</p> @endif
                    <p>
                        {{ collect([$workspace->address_city, $workspace->address_state, $workspace->address_postal])->filter()->join(', ') ?: '—' }}
                    </p>
                    @if($workspace->tax_id) <p><strong>GSTIN:</strong> {{ $workspace->tax_id }}</p> @endif
                    @if($workspace->contact_email) <p><strong>Email:</strong> {{ $workspace->contact_email }}</p> @endif
                </div>
            </div>

            {{-- =============== LINE ITEMS =============== --}}
            <table>
                <thead>
                    <tr>
                        <th>Description</th>
                        <th class="num">HSN/SAC</th>
                        <th class="num">Qty</th>
                        <th class="num">Rate</th>
                        <th class="num">Amount (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="desc">
                            {{ ucfirst(str_replace('_', ' ', $payment->kind ?: 'Service')) }}
                            <small>
                                {{ $payment->description ?: 'CreatorPlex platform service' }}
                                @if($payment->reference) · Ref: {{ $payment->reference }} @endif
                            </small>
                        </td>
                        <td class="num">998439</td>
                        <td class="num">1</td>
                        <td class="num">{{ number_format($taxableCents / 100, 2, '.', ',') }}</td>
                        <td class="num">{{ number_format($taxableCents / 100, 2, '.', ',') }}</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="num" style="color:var(--muted)">Taxable value</td>
                        <td class="num">{{ number_format($taxableCents / 100, 2, '.', ',') }}</td>
                    </tr>
                    @if($isIntraState)
                        <tr>
                            <td colspan="4" class="num" style="color:var(--muted)">CGST @ 9%</td>
                            <td class="num">{{ number_format($cgstCents / 100, 2, '.', ',') }}</td>
                        </tr>
                        <tr>
                            <td colspan="4" class="num" style="color:var(--muted)">SGST @ 9%</td>
                            <td class="num">{{ number_format($sgstCents / 100, 2, '.', ',') }}</td>
                        </tr>
                    @else
                        <tr>
                            <td colspan="4" class="num" style="color:var(--muted)">IGST @ 18%</td>
                            <td class="num">{{ number_format($igstCents / 100, 2, '.', ',') }}</td>
                        </tr>
                    @endif
                    <tr class="total">
                        <td colspan="4" class="num">Total (incl. GST)</td>
                        <td class="num">₹{{ number_format($totalCents / 100, 2, '.', ',') }}</td>
                    </tr>
                </tfoot>
            </table>

            <div class="notice">
                <strong>Note:</strong> This is a computer-generated tax invoice. No signature required.
                @if($payment->method) Payment method: {{ ucfirst($payment->method) }}. @endif
                @if($payment->reference) Payment reference: {{ $payment->reference }}. @endif
                Amounts are in Indian Rupees (INR). Whether tax is CGST/SGST or IGST is determined by the state
                of the buyer relative to the seller (Uttar Pradesh).
                For any billing questions, email us at hello@creatorplex.in.
            </div>
        </div>

        <div class="foot">
            {{ $settings?->company_legal_name ?: 'CreatorPlex Technologies Pvt. Ltd.' }}
            @if($settings?->company_website) · {{ $settings->company_website }} @endif
            @if($settings?->company_email) · {{ $settings->company_email }} @endif
        </div>
    </div>
</div>

<script>
    // Trigger the browser print dialog automatically if ?print=1 (allows
    // the "Download PDF" button on the billing page to open + print in one step).
    if (new URLSearchParams(location.search).get('print') === '1') {
        window.addEventListener('load', () => setTimeout(() => window.print(), 400));
    }
</script>
</body>
</html>
