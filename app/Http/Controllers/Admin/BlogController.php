<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function index()
    {
        return view('admin.blog.index', [
            'posts' => BlogPost::latest()->paginate(20),
        ]);
    }

    public function create()  { return view('admin.blog.edit', ['post' => new BlogPost()]); }
    public function edit(BlogPost $post) { return view('admin.blog.edit', compact('post')); }

    public function store(Request $request)
    {
        $post = BlogPost::create($this->data($request, new BlogPost()) + ['author_id' => $request->user()->id]);
        return redirect()->route('admin.blog.edit', $post)->with('status', 'Post created.');
    }

    public function update(BlogPost $post, Request $request)
    {
        $post->update($this->data($request, $post));
        return back()->with('status', 'Post saved.');
    }

    public function destroy(BlogPost $post)
    {
        $post->delete();
        return redirect()->route('admin.blog.index')->with('status', 'Post deleted.');
    }

    protected function data(Request $request, BlogPost $post): array
    {
        $data = $request->validate([
            'title'            => ['required', 'string', 'max:255'],
            'slug'             => ['nullable', 'string', 'max:255'],
            'category'         => ['nullable', 'string', 'max:60'],
            'excerpt'          => ['nullable', 'string', 'max:2000'],
            'body'             => ['required', 'string'],
            'cover_gradient'   => ['nullable', 'string', 'max:120'],
            'cover'            => ['nullable', 'image', 'max:5120'],
            'meta_title'       => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:300'],
            'canonical_url'    => ['nullable', 'url', 'max:255'],
            'faq_json'         => ['nullable', 'string'], // JSON string of [{q,a}]
            'read_minutes'     => ['nullable', 'string', 'max:12'],
            'is_published'     => ['nullable', 'boolean'],
            'published_at'     => ['nullable', 'date'],
        ]);

        $data['slug']  = Str::slug($data['slug'] ?: $data['title']);
        $baseSlug = $data['slug']; $i = 1;
        while (BlogPost::where('slug', $data['slug'])->when($post->exists, fn ($q) => $q->where('id', '!=', $post->id))->exists()) {
            $data['slug'] = $baseSlug.'-'.$i++;
        }

        $data['is_published'] = (bool) $request->input('is_published');

        if ($request->hasFile('cover')) {
            $data['cover_image_path'] = Storage::disk('public')->url(
                $request->file('cover')->store('blog-covers', 'public')
            );
        }
        unset($data['cover']);

        if (isset($data['faq_json']) && $data['faq_json']) {
            $decoded = json_decode($data['faq_json'], true);
            $data['faq_json'] = is_array($decoded) ? $decoded : null;
        } else {
            $data['faq_json'] = null;
        }

        return $data;
    }
}
