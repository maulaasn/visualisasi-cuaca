@extends('layouts.main')

@section('content')
<div class="warning-container p-6 md:p-[30px_40px] m-0 w-full box-border">
    <div class="warning-header mb-6">
        <h1 class="page-title text-base md:text-[20px] font-bold text-slate-800 m-0 mb-2">Informasi Peringatan Dini</h1>
        <p class="last-update text-xs md:text-sm text-slate-500 m-0">Pembaruan terakhir: {{ $checkedAt }}</p>
    </div>

    @if($warning)
    @php
        $eventLower = strtolower($warning['event'] ?? '');
        $iconType = 'warning'; 

        if (str_contains($eventLower, 'petir') || str_contains($eventLower, 'kilat')) {
            $iconType = 'petir';
        } elseif (str_contains($eventLower, 'hujan lebat')) {
            $iconType = 'hujan';
        } elseif (str_contains($eventLower, 'angin')) {
            $iconType = 'angin';
        }
    @endphp

    <div class="warning-card bg-orange-50 border border-orange-200 rounded-lg overflow-hidden w-full h-auto flex flex-col">
        <div class="warning-card-header p-4 md:p-6 flex flex-col md:flex-row justify-between md:items-start gap-4 md:gap-8">     
            <div class="warning-title-wrapper flex gap-4 items-start flex-1">
                
                @if($iconType === 'petir')
                <svg class="shrink-0 w-6 h-7 md:w-8 md:h-8 md:mt-1.5" viewBox="0 0 24 24" fill="none" stroke="#ea580c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 16.9A5 5 0 0 0 18 7h-1.26a8 8 0 1 0-11.62 9"></path>
                    <polyline points="13 11 9 17 15 17 11 23"></polyline>
                </svg>
                @elseif($iconType === 'hujan')
                <svg class="shrink-0 w-6 h-7 md:w-8 md:h-8 md:mt-1.5" viewBox="0 0 24 24" fill="none" stroke="#ea580c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 16.2A5 5 0 0 0 18 7h-1.26A8 8 0 1 0 4 15.25"></path>
                    <path d="M16 14v6"></path>
                    <path d="M8 14v6"></path>
                    <path d="M12 16v6"></path>
                </svg>
                @elseif($iconType === 'angin')
                <svg class="shrink-0 w-6 h-7 md:w-8 md:h-8 md:mt-1.5" viewBox="0 0 24 24" fill="none" stroke="#ea580c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12.8 19.6A2 2 0 1 0 14 16H2"></path>
                    <path d="M17.5 8a2.5 2.5 0 1 1 2 4H2"></path>
                    <path d="M9.8 4.4A2 2 0 1 1 11 8H2"></path>
                </svg>
                @else
                <svg class="shrink-0 w-6 h-7 md:w-8 md:h-8 md:mt-1.5" viewBox="0 0 24 24" fill="none" stroke="#ea580c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                    <line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
                @endif
                
                <div class="warning-title-text w-full md:pr-6">
                    <p class="text-orange-600 font-bold text-xs md:text-sm m-0 mb-1 capitalize tracking-wide">
                        {{ strtolower($warning['event']) }}
                    </p>

                    <h2 class="text-sm md:text-base font-bold text-slate-800 m-0 mb-1.5 md:mb-3">{{ $warning['title'] }}</h2>
                    <p class="warning-summary text-justify text-xs md:text-sm text-slate-600 m-0 mb-2.5 md:mb-5 leading-relaxed">
                        {{ $warning['description'] }}
                    </p>
                    <div class="warning-time-badge inline-block bg-orange-600 text-white px-3 md:px-4 py-1 md:py-2 rounded-full text-[11px] md:text-xs font-semibold mb-2 md:mb-4">
                        Berlaku: {{ $warning['effective_wib'] }} - {{ $warning['expires_wib'] }}
                    </div>
                </div>
            </div>

            <a href="{{ route('warning.detail') }}" class="bg-transparent hover:text-orange-700 text-orange-600 font-bold text-[13px] md:text-sm py-2 transition-colors whitespace-nowrap text-left md:text-right self-start md:self-auto no-underline">
                Lihat Peta Persebaran &rarr;
            </a>
        </div>
    </div>
    @else
    <div class="empty-state-card bg-emerald-50 border border-emerald-200 rounded-lg p-[40px_20px] md:p-[60px_40px] text-center text-emerald-800 w-full box-border">
        <div class="empty-state-icon flex justify-center mb-4">
            <svg class="w-10 h-10 md:w-12 md:h-12" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
        </div>
        <h2 class="empty-state-title text-sm md:text-[20px] font-bold m-0 mb-2">Kondisi Aman</h2>
        <p class="empty-state-desc text-xs md:text-base m-0">Saat ini tidak ada peringatan dini cuaca ekstrem untuk wilayah Jawa Timur.</p>
    </div>
    @endif
</div>
@endsection