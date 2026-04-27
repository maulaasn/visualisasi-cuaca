@extends('admin.layouts.sidebar')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@section('content')
<div class="p-8 max-w-full">
    <div class="mb-6">
        <h1 class="text-xl font-bold text-slate-800 mb-1">Informasi Peringatan Dini</h1>
        <p class="text-sm text-slate-500">Pembaruan terakhir: {{ $checkedAt }}</p>
    </div>

    @if($warning)
        <div class="bg-orange-50 border border-orange-200 rounded-xl overflow-hidden shadow-sm flex flex-col transition-all duration-300">
            <div class="p-6 flex justify-between items-start cursor-pointer" id="warningToggle">
                <div class="flex gap-4 items-start">
                    <svg class="shrink-0 w-8 h-8 text-orange-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                        <line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                    <div>
                        <h2 class="text-base font-bold text-slate-800 mb-2">{{ $warning['title'] }}</h2>
                        <p class="text-sm text-slate-600 mb-3 leading-relaxed">Berpotensi terjadi hujan dengan intensitas sedang hingga lebat yang dapat disertai kilat/petir dan angin kencang.</p>
                        <div class="inline-block bg-orange-600 text-white px-4 py-1.5 rounded-full text-[11px] font-semibold">
                            Berlaku: {{ $warning['effective_wib'] }} - {{ $warning['expires_wib'] }}
                        </div>
                    </div>
                </div>
                <button class="text-blue-600 font-semibold text-sm whitespace-nowrap" id="toggleBtnText">Selengkapnya &rarr;</button>
            </div>

            <div class="hidden [&.show]:block px-6 pb-6 border-t border-dashed border-orange-200 mt-2 pt-6" id="warningDetails">
                <div id="mini-map" class="w-full h-[330px] rounded-lg border border-orange-200 z-10"></div>
                <div class="mt-4 pt-4 border-t border-dashed border-orange-200 text-right text-xs text-slate-400">
                    Sumber: Prakirawan BMKG - Jawa Timur
                </div>
            </div>
        </div>
    @else
        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-10 text-center text-emerald-800">
            <div class="flex justify-center mb-4">
                <svg class="w-12 h-12 text-emerald-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
            </div>
            <h2 class="text-xl font-bold mb-2">Kondisi Aman</h2>
            <p class="text-slate-600">Saat ini tidak ada peringatan dini cuaca ekstrem untuk wilayah Jawa Timur.</p>
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