@extends('layouts.main')

@section('content')
    <div class="bg-white w-full pt-6 md:pt-8 pb-16">
        <div class="max-w-[1600px] mx-auto px-6 sm:px-6 lg:px-10">

            <div class="mb-4">
                <a href="{{ route('news.index') }}"
                    class="inline-flex items-center gap-1.5 text-[13px] md:text-sm font-semibold text-blue-600 hover:text-blue-800 transition-colors group">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 transform group-hover:-translate-x-1 transition-transform" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                    Kembali ke halaman awal
                </a>
            </div>

            <h1 class="text-sm md:text-xl lg:text-[17px] font-extrabold text-slate-900 leading-snug mb-4">
                {{ $news->title }}
            </h1>

            <div class="flex flex-wrap items-center gap-5 mb-6 border-b border-slate-100 pb-4">

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

            <div class="mb-2">
                @if($news->image)
                    <div
                        class="w-full sm:max-w-md md:w-[400px] lg:w-[500px] aspect-[16/10] overflow-hidden rounded-lg border border-slate-200 shadow-sm">
                        <img src="{{ asset('storage/' . $news->image) }}" alt="{{ $news->title }}"
                            class="w-full h-full object-cover object-center">
                    </div>
                @else
                    <div
                        class="w-full sm:max-w-md md:w-[400px] lg:w-[500px] aspect-[16/10] bg-slate-50 flex items-center justify-center rounded-lg border border-dashed border-slate-300">
                        <span class="text-slate-400 font-medium text-sm">Foto tidak tersedia</span>
                    </div>
                @endif
            </div>

            <article class="prose prose-slate prose-lg max-w-none">
                <div class="text-slate-700 leading-relaxed text-[12px] md:text-[15px] space-y-4 whitespace-pre-line">
                    {{ $news->content }}
                </div>
            </article>

        </div>
    </div>
@endsection