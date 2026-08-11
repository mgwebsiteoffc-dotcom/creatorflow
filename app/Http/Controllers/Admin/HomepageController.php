<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageItem;
use App\Support\SchemaCheck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomepageController extends Controller
{
    public function index()
    {
        if (! SchemaCheck::has('homepage_items')) {
            return view('admin.homepage.index', [
                'sections' => [
                    'client_logo'  => collect(),
                    'sample_reel'  => collect(),
                    'hero_image'   => collect(),
                ],
                'schemaMissing' => true,
            ]);
        }

        $sections = [
            'client_logo'  => HomepageItem::section('client_logo')->orderBy('position')->get(),
            'sample_reel'  => HomepageItem::section('sample_reel')->orderBy('position')->get(),
            'hero_image'   => HomepageItem::section('hero_image')->orderBy('position')->get(),
        ];

        return view('admin.homepage.index', compact('sections') + ['schemaMissing' => false]);
    }

    public function store(Request $request)
    {
        abort_unless(SchemaCheck::has('homepage_items'), 400, 'Run php artisan migrate first.');

        $data = $request->validate([
            'section'     => ['required', 'in:client_logo,sample_reel,hero_image'],
            'category'    => ['nullable', 'string', 'max:60'],
            'title'       => ['nullable', 'string', 'max:190'],
            'subtitle'    => ['nullable', 'string', 'max:190'],
            'external_url'=> ['nullable', 'url', 'max:800'],
            'gradient'    => ['nullable', 'string', 'max:120'],
            'meta'        => ['nullable', 'string', 'max:190'],
            'position'    => ['nullable', 'integer'],
            'is_active'   => ['nullable', 'boolean'],
            'image'       => ['nullable', 'image', 'max:5120'],
            'poster'      => ['nullable', 'image', 'max:5120'],
            'video'       => ['nullable', 'file', 'mimetypes:video/mp4,video/quicktime,video/webm', 'max:102400'],
        ]);

        $paths = [];
        if ($request->hasFile('image'))  $paths['image_path']  = Storage::disk('public')->url($request->file('image')->store('homepage', 'public'));
        if ($request->hasFile('poster')) $paths['poster_path'] = Storage::disk('public')->url($request->file('poster')->store('homepage', 'public'));
        if ($request->hasFile('video'))  $paths['video_path']  = Storage::disk('public')->url($request->file('video')->store('homepage', 'public'));

        HomepageItem::create($data + $paths + [
            'is_active' => (bool) $request->input('is_active', true),
        ]);

        return back()->with('status', ucfirst(str_replace('_', ' ', $data['section'])).' added.');
    }

    public function update(HomepageItem $item, Request $request)
    {
        abort_unless(SchemaCheck::has('homepage_items'), 400);

        $data = $request->validate([
            'category'    => ['nullable', 'string', 'max:60'],
            'title'       => ['nullable', 'string', 'max:190'],
            'subtitle'    => ['nullable', 'string', 'max:190'],
            'external_url'=> ['nullable', 'url', 'max:800'],
            'gradient'    => ['nullable', 'string', 'max:120'],
            'meta'        => ['nullable', 'string', 'max:190'],
            'position'    => ['nullable', 'integer'],
            'is_active'   => ['nullable', 'boolean'],
            'image'       => ['nullable', 'image', 'max:5120'],
            'poster'      => ['nullable', 'image', 'max:5120'],
            'video'       => ['nullable', 'file', 'mimetypes:video/mp4,video/quicktime,video/webm', 'max:102400'],
        ]);

        if ($request->hasFile('image'))  $data['image_path']  = Storage::disk('public')->url($request->file('image')->store('homepage', 'public'));
        if ($request->hasFile('poster')) $data['poster_path'] = Storage::disk('public')->url($request->file('poster')->store('homepage', 'public'));
        if ($request->hasFile('video'))  $data['video_path']  = Storage::disk('public')->url($request->file('video')->store('homepage', 'public'));

        $data['is_active'] = (bool) $request->input('is_active', $item->is_active);

        $item->update($data);
        return back()->with('status', 'Item updated.');
    }

    public function destroy(HomepageItem $item)
    {
        $item->delete();
        return back()->with('status', 'Item removed.');
    }
}
