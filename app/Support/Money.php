<?php

namespace App\Support;

use App\Models\Workspace;
use Illuminate\Support\Facades\Auth;

/**
 * One place to format money for the whole app. Every view/component should
 * call Money::fmt($cents) instead of hard-coding a symbol.
 *
 * Rules:
 *   • Prefer the current workspace's currency
 *   • Fall back to the authed user's first workspace currency
 *   • Fall back to INR (Indian rupee)
 *
 * INR uses Indian grouping: ₹1,84,200.00
 */
class Money
{
    /** Format a paise/cents amount. */
    public static function fmt(int|float|null $cents, ?string $currency = null, int $decimals = 2): string
    {
        $currency = strtoupper($currency ?: static::currentCurrency() ?: 'INR');
        $symbol   = static::symbol($currency);
        $amount   = ((float) $cents) / 100;
        $formatted = $currency === 'INR'
            ? number_format($amount, $decimals, '.', ',')          // Indian grouping
            : number_format($amount, $decimals);

        // Indian grouping needs a special formatter for millions+ (1,84,200 not 184,200)
        if ($currency === 'INR' && abs($amount) >= 100000) {
            $formatted = static::indianGrouping($amount, $decimals);
        }

        return $symbol.$formatted;
    }

    /** Format cents without any decimals — good for chart labels. */
    public static function fmtCompact(int|float|null $cents, ?string $currency = null): string
    {
        return static::fmt($cents, $currency, 0);
    }

    /** Return just the symbol for a currency code. */
    public static function symbol(string $code): string
    {
        return match (strtoupper($code)) {
            'INR' => '₹',
            'USD', 'CAD', 'AUD', 'SGD', 'NZD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'AED' => 'د.إ ',
            'JPY' => '¥',
            default => $code.' ',
        };
    }

    /** Detect the current workspace / auth user currency. */
    public static function currentCurrency(): string
    {
        // Prefer view-provided current workspace (shared by TenantContext)
        try {
            $current = view()->shared('currentWorkspace');
            if ($current instanceof Workspace && ! empty($current->currency)) {
                return strtoupper($current->currency);
            }
        } catch (\Throwable) {}

        $user = Auth::user();
        if ($user) {
            try {
                $ws = $user->workspaces?->first();
                if ($ws && ! empty($ws->currency)) return strtoupper($ws->currency);
            } catch (\Throwable) {}
        }

        return 'INR';
    }

    /**
     * Format a number with Indian grouping (1,84,200.00) — used automatically
     * inside fmt() when INR is the currency and the number is >= 1L.
     */
    protected static function indianGrouping(float $amount, int $decimals = 2): string
    {
        $negative = $amount < 0;
        $amount   = abs($amount);
        $parts    = explode('.', number_format($amount, $decimals, '.', ''));
        $integer  = $parts[0];
        $fraction = $parts[1] ?? '';

        $last3   = substr($integer, -3);
        $rest    = substr($integer, 0, -3);
        if ($rest !== '') {
            $rest = preg_replace('/(\d)(?=(\d\d)+$)/', '$1,', $rest);
            $out  = $rest.','.$last3;
        } else {
            $out = $last3;
        }

        if ($decimals > 0) $out .= '.'.$fraction;
        return ($negative ? '-' : '').$out;
    }
}
