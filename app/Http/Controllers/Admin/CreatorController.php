<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Creator;
use App\Models\CreatorImport;
use App\Models\CreatorNiche;
use App\Models\CreatorSocialAccount;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreatorController extends Controller
{
    public function index(Request $request)
    {
        $creators = Creator::query()
            ->when($request->get('q'), fn ($q, $term) => $q->where('display_name', 'like', "%{$term}%")->orWhere('email', 'like', "%{$term}%"))
            ->when($request->get('status'), fn ($q, $s) => $q->where('status', $s))
            ->withCount(['assignments'])
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.creators.index', compact('creators'));
    }

    public function suspend(Creator $creator, Request $request)
    {
        $creator->update([
            'status'            => 'suspended',
            'suspension_reason' => $request->input('reason'),
            'suspended_at'      => now(),
        ]);

        return back()->with('status', "{$creator->display_name} has been banned.");
    }

    public function reinstate(Creator $creator)
    {
        $creator->update([
            'status'            => 'active',
            'suspension_reason' => null,
            'suspended_at'      => null,
        ]);

        return back()->with('status', "{$creator->display_name} has been reinstated.");
    }

    public function importForm()
    {
        return view('admin.creators.import');
    }

    public function importStore(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');
        $header = null;
        $rows = 0; $imported = 0; $failed = 0; $errors = [];

        while (($row = fgetcsv($handle)) !== false) {
            if (! $header) {
                $header = array_map(fn ($h) => strtolower(trim($h)), $row);
                continue;
            }
            $rows++;
            $data = @array_combine($header, $row) ?: [];
            try {
                $email = $data['email'] ?? null;
                $name  = $data['name'] ?? $data['display_name'] ?? null;
                if (! $name) throw new \RuntimeException('Missing name');

                $user = $email
                    ? User::firstOrCreate(
                        ['email' => $email],
                        [
                            'uuid' => (string) Str::uuid(),
                            'name' => $name,
                            'password' => Hash::make(Str::random(24)),
                        ],
                    )
                    : null;

                $creator = Creator::create([
                    'uuid'         => (string) Str::uuid(),
                    'user_id'      => $user?->id,
                    'display_name' => $name,
                    'slug'         => $this->uniqueSlug($name),
                    'bio'          => $data['bio'] ?? null,
                    'email'        => $email,
                    'country'      => strtoupper((string) ($data['country'] ?? '')) ?: null,
                    'city'         => $data['city'] ?? null,
                    'niches'       => array_filter(array_map('trim', explode(',', $data['niches'] ?? ''))),
                    'status'       => 'active',
                    'open_to_work' => true,
                    'accepts_barter' => filter_var($data['accepts_barter'] ?? true, FILTER_VALIDATE_BOOLEAN),
                    'accepts_paid'   => filter_var($data['accepts_paid']   ?? true, FILTER_VALIDATE_BOOLEAN),
                    'rate_ugc_cents' => isset($data['rate_ugc_cents']) ? (int) $data['rate_ugc_cents'] : null,
                    'follower_count_total' => (int) ($data['followers'] ?? 0),
                    'engagement_rate' => (float) ($data['engagement_rate'] ?? 0),
                    'performance_score' => 60,
                    'fraud_risk' => 5,
                ]);

                foreach ($creator->niches ?? [] as $niche) {
                    CreatorNiche::create(['creator_id' => $creator->id, 'niche' => $niche]);
                }

                foreach (['instagram','tiktok','youtube'] as $platform) {
                    $key = "{$platform}_handle";
                    if (! empty($data[$key])) {
                        CreatorSocialAccount::create([
                            'creator_id'     => $creator->id,
                            'platform'       => $platform,
                            'handle'         => ltrim($data[$key], '@'),
                            'follower_count' => (int) ($data['followers'] ?? 0),
                            'engagement_rate' => (float) ($data['engagement_rate'] ?? 0),
                        ]);
                    }
                }

                $imported++;
            } catch (\Throwable $e) {
                $failed++;
                $errors[] = "Row {$rows}: ".$e->getMessage();
            }
        }
        fclose($handle);

        if (\App\Support\SchemaCheck::has('creator_imports')) {
            CreatorImport::create([
                'user_id'       => $request->user()->id,
                'source'        => 'csv',
                'filename'      => $file->getClientOriginalName(),
                'total_rows'    => $rows,
                'imported_rows' => $imported,
                'failed_rows'   => $failed,
                'errors'        => array_slice($errors, 0, 100),
                'status'        => $failed > 0 ? 'partial' : 'completed',
            ]);
        }

        return redirect()->route('admin.creators.index')
            ->with('status', "Imported {$imported} creator(s). {$failed} failed.");
    }

    protected function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'creator';
        $slug = $base; $i = 1;
        while (Creator::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }
        return $slug;
    }
}
