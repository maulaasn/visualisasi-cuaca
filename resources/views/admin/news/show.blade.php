@extends('admin.layouts.sidebar')

@section('content')
    <div class="p-8 max-w-full mx-auto">
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('admin.news.index') }}" class="text-slate-500 hover:text-slate-800 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-lg font-bold text-slate-800">Detail Berita</h1>
                <p class="text-slate-500 text-sm">Tinjauan lengkap informasi berita cuaca</p>
            </div>
        </div>

        <div class="max-w-full">

            <h1 class="text-base md:text-lg font-bold text-slate-800 leading-tight mb-4">
                {{ $news->title }}
            </h1>

            <div class="flex items-center gap-4 mb-8">
                <div class="flex items-center gap-1.5 text-slate-500 text-[12px] md:text-sm font-normal">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1.5">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke-linecap="round"
                            stroke-linejoin="round"></rect>
                        <line x1="16" y1="2" x2="16" y2="6" stroke-linecap="round" stroke-linejoin="round"></line>
                        <line x1="8" y1="2" x2="8" y2="6" stroke-linecap="round" stroke-linejoin="round"></line>
                        <line x1="3" y1="10" x2="21" y2="10" stroke-linecap="round" stroke-linejoin="round"></line>
                    </svg>
                    {{ \Carbon\Carbon::parse($news->news_date)->translatedFormat('l, j F Y') }}
                </div>

                <div class="flex items-center gap-1.5 text-slate-500 text-[12px] md:text-sm font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    {{ $news->urgency_level }}
                </div>
            </div>

            @if($news->image)
                <img src="{{ asset('storage/' . $news->image) }}" alt="Cover Berita"
                    class="w-full max-w-sm h-64 md:h-60 object-cover rounded-xl mb-2 shadow-sm">
            @else
                <div
                    class="w-full max-w-sm h-64 md:h-60 object-cover rounded-xl mb-2 shadow-sm bg-slate-100 flex items-center justify-center border-slate-200">
                    <span class="text-slate-400 font-medium">Tidak ada foto utama</span>
                </div>
            @endif

            <div class="text-slate-700 leading-relaxed space-y-4 text-[15px] whitespace-pre-line max-w-full">
                {{ $news->content }}
            </div>

        </div>
    </div>
@endsection