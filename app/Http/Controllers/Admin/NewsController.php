<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::latest()->get();
        return view('admin.news.index', compact('news'));
    }

    public function create()
    {
        return view('admin.news.create');
    }

    public function show($id)
    {
        $news = News::findOrFail($id);
        return view('admin.news.show', compact('news'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'urgency_level' => 'required|string',
            'news_date' => 'required|date',
            'content' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->title . '-' . time());

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('news-images', 'public');
        }

        News::create($data);

        return redirect()->route('admin.news.index')->with('success', 'Data berita berhasil ditambahkan');
    }

    public function edit($id)
    {
        $news = News::findOrFail($id);
        return view('admin.news.edit', compact('news'));
    }

    public function update(Request $request, $id)
    {
        $news = News::findOrFail($id);
        $request->validate([
            'title' => 'required|string|max:255',
            'urgency_level' => 'required|string',
            'news_date' => 'required|date',
            'content' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $data = $request->all();
        if ($request->title !== $news->title) {
            $data['slug'] = Str::slug($request->title . '-' . time());
        }

        if ($request->hasFile('image')) {
            if ($news->image && Storage::disk('public')->exists($news->image)) {
                Storage::disk('public')->delete($news->image);
            }
            $data['image'] = $request->file('image')->store('news-images', 'public');
        }

        $news->update($data);

        return redirect()->route('admin.news.index')->with('success', 'Data berita berhasil diperbarui');
    }

    public function destroy($id)
    {
        $news = News::findOrFail($id);
        if ($news->image && Storage::disk('public')->exists($news->image)) {
            Storage::disk('public')->delete($news->image);
        }
        $news->delete();

        return redirect()->route('admin.news.index')->with('success', 'Data berita berhasil dihapus');
    }
}