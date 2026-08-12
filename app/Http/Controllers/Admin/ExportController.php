<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Creator;
use App\Models\Payout;
use App\Models\User;
use App\Models\Workspace;
use App\Support\SchemaCheck;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * One controller for every "Download CSV" button on the admin panel.
 *
 * Streams rows via chunkById so 100K+ tables don't blow memory.
 */
class ExportController extends Controller
{
    public function users(Request $request): StreamedResponse
    {
        $columns = [
            'id', 'name', 'email', 'system_role', 'account_status',
            'email_verified_at', 'created_at', 'last_seen_at',
        ];

        return $this->stream("users-{$this->stamp()}.csv", $columns, function ($write) use ($columns) {
            User::query()->orderBy('id')->chunkById(500, function ($rows) use ($write, $columns) {
                foreach ($rows as $u) {
                    $write(array_map(fn ($c) => $this->cast($u->{$c} ?? ''), $columns));
                }
            });
        });
    }

    public function creators(Request $request): StreamedResponse
    {
        $columns = [
            'id', 'display_name', 'email', 'city', 'state', 'country',
            'tier', 'gender', 'age_range',
            'follower_count_total', 'engagement_rate',
            'accepts_barter', 'accepts_paid', 'status',
            'created_at',
        ];

        return $this->stream("creators-{$this->stamp()}.csv", $columns, function ($write) use ($columns) {
            Creator::query()->orderBy('id')->chunkById(500, function ($rows) use ($write, $columns) {
                foreach ($rows as $c) {
                    $write(array_map(fn ($col) => $this->cast($c->{$col} ?? ''), $columns));
                }
            });
        });
    }

    public function workspaces(Request $request): StreamedResponse
    {
        $columns = [
            'id', 'name', 'slug', 'plan_tier', 'currency', 'country',
            'city', 'created_at',
        ];

        return $this->stream("workspaces-{$this->stamp()}.csv", $columns, function ($write) use ($columns) {
            Workspace::query()->orderBy('id')->chunkById(500, function ($rows) use ($write, $columns) {
                foreach ($rows as $w) {
                    $write(array_map(fn ($col) => $this->cast($w->{$col} ?? ''), $columns));
                }
            });
        });
    }

    public function payouts(Request $request): StreamedResponse
    {
        if (! SchemaCheck::has('payouts')) {
            return $this->stream("payouts-{$this->stamp()}.csv", ['error'], function ($write) {
                $write(['payouts table not migrated']);
            });
        }

        $columns = [
            'id', 'workspace_id', 'creator_id', 'assignment_id',
            'amount_cents', 'net_cents', 'commission_cents', 'currency',
            'status', 'provider', 'external_id', 'external_status',
            'failure_reason', 'processed_at', 'created_at',
        ];

        return $this->stream("payouts-{$this->stamp()}.csv", $columns, function ($write) use ($columns) {
            Payout::query()->orderBy('id')->chunkById(500, function ($rows) use ($write, $columns) {
                foreach ($rows as $p) {
                    $write(array_map(fn ($col) => $this->cast($p->{$col} ?? ''), $columns));
                }
            });
        });
    }

    // -----------------------------------------------------------------

    protected function stamp(): string
    {
        return now()->format('Y-m-d-His');
    }

    protected function cast($v): string
    {
        if ($v instanceof \DateTimeInterface) return $v->format('Y-m-d H:i:s');
        if (is_bool($v)) return $v ? '1' : '0';
        if (is_array($v)) return json_encode($v);
        return (string) $v;
    }

    protected function stream(string $filename, array $columns, callable $writer): StreamedResponse
    {
        return new StreamedResponse(function () use ($columns, $writer) {
            $out = fopen('php://output', 'w');
            // UTF-8 BOM so Excel opens it cleanly.
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, $columns);

            $writer(function (array $row) use ($out) {
                fputcsv($out, $row);
            });

            fclose($out);
        }, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Cache-Control'       => 'no-store',
        ]);
    }
}
