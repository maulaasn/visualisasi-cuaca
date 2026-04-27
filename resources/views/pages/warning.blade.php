@extends('layouts.main')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@section('content')
<div class="warning-container p-6 md:p-[30px_40px] m-0 w-full box-border">
    <div class="warning-header mb-6">
        <h1 class="page-title text-base md:text-[20px] font-bold text-slate-800 m-0 mb-2">Informasi Peringatan Dini</h1>
        <p class="last-update text-xs md:text-sm text-slate-500 m-0">Pembaruan terakhir: {{ $checkedAt }}</p>
    </div>

    @if($warning)
    <div class="warning-card bg-orange-50 border border-orange-200 rounded-lg overflow-hidden w-full transition-all duration-300 max-h-[calc(100vh-130px)] h-auto flex flex-col">
        <div class="warning-card-header p-4 md:p-6 flex flex-col md:flex-row justify-between md:items-start gap-3 md:gap-0 cursor-pointer" id="warningToggle">     
            <div class="warning-title-wrapper flex gap-4 items-start">
                <svg class="shrink-0 w-6 h-7 md:w-7 md:h-7" viewBox="0 0 24 24" fill="none" stroke="#ea580c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                    <line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
                <div class="warning-title-text">
                    <h2 class="text-sm md:text-base font-bold text-slate-800 m-0 mb-1.5 md:mb-2">{{ $warning['title'] }}</h2>
                    <p class="warning-summary text-xs md:text-sm text-slate-600 m-0 mb-2.5 md:mb-3 leading-relaxed">Berpotensi terjadi hujan dengan intensitas sedang hingga lebat yang dapat disertai kilat/petir dan angin kencang.</p>
                    <div class="warning-time-badge inline-block bg-orange-600 text-white px-3 md:px-4 py-1 rounded-full text-[11px] md:text-xs font-semibold">
                        Berlaku: {{ $warning['effective_wib'] }} - {{ $warning['expires_wib'] }}
                    </div>
                </div>
            </div>

            <button class="toggle-btn bg-transparent border-none text-blue-600 font-semibold text-[13px] md:text-sm cursor-pointer whitespace-nowrap p-0 self-start ml-10 md:ml-0 md:self-auto" id="toggleBtnText">Selengkapnya &rarr;</button>
        </div>

        <div class="warning-details hidden [&.show]:block px-4 md:px-6 pb-4 border-t border-dashed border-orange-200 mt-0 md:mt-2.5 pt-4 md:pt-6 overflow-y-auto flex-none" id="warningDetails">
            <div class="map-content w-full">
                <div id="mini-map" class="w-full h-[190px] md:h-[270px] rounded-lg border border-orange-200 z-10"></div>
            </div>
            
            <div class="warning-footer mt-4 pt-4 border-t border-dashed border-orange-200 text-right text-[11px] md:text-xs text-slate-400">
                Sumber: Prakirawan BMKG - Jawa Timur
            </div>
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

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const warningData = @json($warning);
</script>
<script src="{{ asset('js/warning.js') }}"></script>
@endpush