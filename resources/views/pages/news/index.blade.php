@extends('layouts.main')

@section('content')
<div class="bg-gray-50/50 w-full min-h-[80vh] pb-20 pt-6 md:pt-10">
    <div class="max-w-[1600px] mx-auto px-6 sm:px-8 lg:px-12">
        
        <div class="text-center mb-8 sm:mb-10">
            <h1 class="text-base sm:text-[20px] font-extrabold text-slate-900 mb-0">Berita Terbaru</h1>
            <p class="text-slate-600 text-xs sm:text-sm mt-1 mb-5 sm:mb-6">Perkiraan cuaca serta update terbaru kondisi cuaca</p>

            <div class="flex flex-wrap justify-center gap-2 sm:gap-3">
                <a href="{{ route('news.index', ['category' => 'Semua']) }}" 
                   class="px-3 sm:px-4 py-1.5 text-xs sm:text-[13px] font-semibold rounded-md border transition-colors {{ $category == 'Semua' ? 'border-blue-200 bg-blue-50 text-blue-700' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50' }}">
                    Semua
                </a>
                
                <a href="{{ route('news.index', ['category' => 'Berita Utama']) }}" 
                   class="px-3 sm:px-4 py-1.5 text-xs sm:text-[13px] font-medium rounded-md border transition-colors {{ $category == 'Berita Utama' ? 'border-blue-200 bg-blue-50 text-blue-700' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50' }}">
                    Berita Utama
                </a>
                
                <a href="{{ route('news.index', ['category' => 'Prakiraan Cuaca']) }}" 
                   class="px-3 sm:px-4 py-1.5 text-xs sm:text-[13px] font-medium rounded-md border transition-colors {{ $category == 'Prakiraan Cuaca' ? 'border-blue-200 bg-blue-50 text-blue-700' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50' }}">
                    Prakiraan Cuaca
                </a>
                
                <a href="{{ route('news.index', ['category' => 'Peringatan Dini']) }}" 
                   class="px-3 sm:px-4 py-1.5 text-xs sm:text-[13px] font-medium rounded-md border transition-colors {{ $category == 'Peringatan Dini' ? 'border-blue-200 bg-blue-50 text-blue-700' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50' }}">
                    Peringatan Dini
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 sm:gap-6 xl:gap-8 items-start">
            
            @forelse($news as $item)
            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden flex flex-col sm:flex-row hover:shadow-md transition-shadow duration-300">
                
                <div class="w-full sm:w-[40%] shrink-0 h-48 sm:h-auto">
                    @if($item->image)
                        <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->title }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-slate-100 flex items-center justify-center border-r border-slate-100">
                            <span class="text-slate-400 text-xs">Tidak ada gambar</span>
                        </div>
                    @endif
                </div>

                <div class="p-4 sm:p-6 md:p-8 flex flex-col flex-1 justify-center">
                    
                    <div class="flex flex-wrap items-center gap-2 sm:gap-3 mb-2 sm:mb-2.5">
                        <span @class([
                            'px-2 sm:px-2.5 py-0.5 text-[10px] sm:text-[11px] font-bold rounded border',
                            'bg-orange-50 border-orange-200 text-orange-600' => $item->urgency_level == 'Peringatan Dini',
                            'bg-blue-50 border-blue-200 text-blue-600'     => $item->urgency_level == 'Prakiraan Cuaca',
                            'bg-purple-50 border-purple-200 text-purple-600' => $item->urgency_level == 'Berita Utama',
                            'bg-gray-50 border-gray-200 text-gray-600'     => !in_array($item->urgency_level, ['Peringatan Dini', 'Prakiraan Cuaca', 'Berita Utama'])
                        ])>
                            {{ $item->urgency_level }}
                        </span>
                        
                        <span class="text-[11px] sm:text-[12px] font-medium text-slate-500">
                            {{ \Carbon\Carbon::parse($item->news_date)->translatedFormat('j F Y') }}
                        </span>
                    </div>
                    
                    <h2 class="text-[15px] sm:text-[16px] font-bold text-slate-900 mb-2 sm:mb-3 leading-snug line-clamp-2">
                        <a href="{{ route('news.show', $item->slug) }}" class="hover:text-blue-800 transition-colors">
                            {{ $item->title }}
                        </a>
                    </h2>
                    
                    <p class="text-[12px] sm:text-[13px] text-slate-500 mb-4 sm:mb-5 line-clamp-2 sm:line-clamp-3 leading-relaxed">
                        {{ Str::limit(strip_tags($item->content), 130) }}
                    </p>

                    <div class="mt-auto pt-1">
                        <a href="{{ route('news.show', $item->slug) }}" class="inline-flex items-center text-[12px] sm:text-[13px] font-bold text-[#1e3a8a] hover:text-blue-800 transition-colors group">
                            Baca Selengkapnya
                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 ml-1.5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </a>
                    </div>
                </div>

            </div>
            
            @empty
            <div class="col-span-full py-16 sm:py-20 px-6 text-center bg-white rounded-xl border border-slate-200 border-dashed mx-0 sm:mx-0">
                <svg class="w-10 h-10 sm:w-12 sm:h-12 text-slate-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                </svg>
                <p class="text-slate-500 font-medium text-[14px] sm:text-[15px]">Belum ada data berita untuk kategori <span class="font-bold text-slate-700">"{{ $category }}"</span>.</p>
                <a href="{{ route('news.index') }}" class="inline-block mt-3 sm:mt-4 text-blue-600 hover:underline text-[13px] sm:text-sm font-semibold">Lihat Semua Berita</a>
            </div>
            @endforelse

        </div>

    </div>
</div>
@endsection