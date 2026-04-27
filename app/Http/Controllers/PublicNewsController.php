<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;

class PublicNewsController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->query('category', 'Semua');
        
        $query = News::latest();
        
        if ($category !== 'Semua') {
            $query->where('urgency_level', $category);
        }
        
        $news = $query->get();
        
        return view('pages.news.index', compact('news', 'category'));
    }

    public function show($slug)
    {
        $news = News::where('slug', $slug)->firstOrFail();
        
        return view('pages.news.show', compact('news'));
    }
}