<?php

namespace App\Support;

use App\Models\PlatformSetting;
use Illuminate\Support\Facades\Http;

/**
 * Lightweight Sentry client that works without the composer package. Any
 * exception reported here is POSTed to the Sentry Ingest API as a native
 * envelope. Enable by pasting a DSN in Admin → Integrations → Monitoring.
 *
 * We deliberately avoid the official sentry/sentry-laravel package so users
 * don't have to composer require anything extra on Laragon.
 */
class SentryReporter
{
    public static function isEnabled(): bool
    {
        try {
            if (! SchemaCheck::has('platform_settings')) return false;
            return ! empty(PlatformSetting::current()->sentry_dsn);
        } catch (\Throwable) { return false; }
    }

    public static function captureException(\Throwable $e, array $tags = [], array $extra = []): ?string
    {
        if (! static::isEnabled()) return null;
        $s = PlatformSetting::current();
        [$publicKey, $host, $projectId] = static::parseDsn((string) $s->sentry_dsn);
        if (! $publicKey || ! $host || ! $projectId) return null;

        $eventId = str_replace('-', '', (string) \Illuminate\Support\Str::uuid());
        $payload = [
            'event_id'   => $eventId,
            'timestamp'  => gmdate('Y-m-d\TH:i:s\Z'),
            'level'      => 'error',
            'platform'   => 'php',
            'environment'=> $s->sentry_environment ?: 'production',
            'server_name'=> gethostname(),
            'release'    => 'creatorplex@'.(config('app.version') ?: 'main'),
            'tags'       => array_merge(['php' => PHP_VERSION, 'laravel' => app()->version()], $tags),
            'extra'      => $extra,
            'exception'  => ['values' => [[
                'type'    => class_basename($e),
                'value'   => $e->getMessage(),
                'module'  => static::classNamespace($e),
                'stacktrace' => ['frames' => static::frames($e)],
            ]]],
        ];

        try {
            Http::withHeaders([
                'Content-Type'      => 'application/json',
                'X-Sentry-Auth'     => static::authHeader($publicKey),
            ])
            ->timeout(6)
            ->post("https://{$host}/api/{$projectId}/store/", $payload);
        } catch (\Throwable) {
            // Never let telemetry itself break the app.
        }

        return $eventId;
    }

    /**
     * Register a global exception hook — call from bootstrap/app.php.
     */
    public static function register(): void
    {
        if (! static::isEnabled()) return;
        \Illuminate\Foundation\Exceptions\Handler::class; // touch autoload
        app()->terminating(function () { /* no-op */ });
    }

    /**
     * Parse https://PUBLIC@HOST/PROJECT_ID into its parts.
     */
    protected static function parseDsn(string $dsn): array
    {
        if (! preg_match('#^https?://([^@]+)@([^/]+)/(.+)$#', $dsn, $m)) return [null, null, null];
        return [$m[1], $m[2], $m[3]];
    }

    protected static function authHeader(string $publicKey): string
    {
        return 'Sentry sentry_version=7, sentry_client=creatorplex-native/1.0, '
             . 'sentry_timestamp='.time().', '
             . 'sentry_key='.$publicKey;
    }

    protected static function classNamespace(\Throwable $e): string
    {
        $parts = explode('\\', get_class($e));
        array_pop($parts);
        return implode('\\', $parts);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected static function frames(\Throwable $e): array
    {
        // Reverse so newest frame is last (Sentry convention).
        $trace = array_reverse($e->getTrace());
        $out   = [];
        foreach ($trace as $t) {
            $out[] = [
                'filename' => $t['file']     ?? '[internal]',
                'lineno'   => $t['line']     ?? 0,
                'function' => ($t['class']   ?? '').($t['type'] ?? '').($t['function'] ?? ''),
            ];
        }
        // Append the throwing frame itself
        $out[] = [
            'filename' => $e->getFile(),
            'lineno'   => $e->getLine(),
            'function' => class_basename($e),
        ];
        return $out;
    }
}
