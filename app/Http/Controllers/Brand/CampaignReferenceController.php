<?php

namespace App\Http\Controllers\Brand;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignReference;
use App\Support\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CampaignReferenceController extends Controller
{
    public function store(Campaign $campaign, Request $request, TenantContext $tenant)
    {
        $this->authorize($campaign, $tenant);

        $data = $request->validate([
            'kind' => ['required', 'in:file,link'],
            'title' => ['nullable', 'string', 'max:190'],
            'note' => ['nullable', 'string', 'max:2000'],
            'url' => ['nullable', 'required_if:kind,link', 'url', 'max:800'],
            'files'   => ['nullable', 'array', 'max:10'],
            'files.*' => [
                'file',
                'max:51200', // 50 MB
                'mimes:jpg,jpeg,png,webp,gif,mp4,mov,pdf,zip,doc,docx,xls,xlsx,ppt,pptx,txt,csv',
            ],
        ]);

        if ($data['kind'] === 'link') {
            CampaignReference::create([
                'campaign_id' => $campaign->id,
                'uploaded_by' => $request->user()->id,
                'kind' => 'link',
                'title' => $data['title'] ?: parse_url($data['url'], PHP_URL_HOST) ?: 'Reference link',
                'url' => $data['url'],
                'note' => $data['note'] ?? null,
            ]);
        } else {
            if (! $request->hasFile('files')) {
                return back()->with('error', 'Please choose at least one file.');
            }
            foreach ($request->file('files', []) as $file) {
                if (! $file) continue;
                $path = $file->store("campaign-references/{$campaign->id}", 'public');
                CampaignReference::create([
                    'campaign_id' => $campaign->id,
                    'uploaded_by' => $request->user()->id,
                    'kind' => 'file',
                    'title' => $data['title'] ?: $file->getClientOriginalName(),
                    'disk' => 'public',
                    'path' => $path,
                    'mime' => $file->getMimeType(),
                    'size_bytes' => $file->getSize(),
                    'note' => $data['note'] ?? null,
                ]);
            }
        }

        return back()->with('status', 'Reference added — creators can see it now.');
    }

    public function destroy(Campaign $campaign, CampaignReference $reference, TenantContext $tenant)
    {
        $this->authorize($campaign, $tenant);
        abort_unless($reference->campaign_id === $campaign->id, 404);

        if ($reference->kind === 'file' && $reference->path) {
            try { Storage::disk($reference->disk ?: 'public')->delete($reference->path); } catch (\Throwable) {}
        }
        $reference->delete();

        return back()->with('status', 'Reference removed.');
    }

    protected function authorize(Campaign $campaign, TenantContext $tenant): void
    {
        abort_unless($campaign->workspace_id === $tenant->id(), 403);
    }
}
