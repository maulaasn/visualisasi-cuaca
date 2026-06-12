@extends('admin.layouts.sidebar')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@section('content')
    <div class="warning-map-container pt-6 px-6 pb-10 md:pt-[30px] md:px-[40px] md:pb-[40px] m-0 w-full box-border flex flex-col h-screen">
        
        <div class="mb-2 flex items-center justify-between shrink-0">
            <div>
                <h1 class="page-title text-xl font-bold text-slate-800 m-0 mb-1">Peta Persebaran Cuaca Ekstrem</h1>
                <p class="last-update text-sm text-slate-500 m-0">Pembaruan terakhir: {{ \Carbon\Carbon::parse(str_replace(' WIB', '', $checkedAt))->translatedFormat('d F Y, H:i') }} WIB</p>
            </div>
            
            <a href="{{ route('admin.warning') }}"
                class="order-1 md:order-2 text-[12px] md:text-sm text-orange-600 hover:text-orange-600 font-medium transition-colors no-underline flex items-center gap-1.5 self-start">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5 md:w-4 md:h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                <span>Kembali ke Peringatan Dini</span>
            </a>
        </div>

        <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 md:p-6 w-full flex flex-col flex-1 min-h-0">
        
            <div id="full-map" class="w-full flex-1 min-h-0 h-full rounded-lg border border-orange-200 z-10 bg-gray-100"></div>

            <div class="warning-footer mt-4 pt-4 border-t border-dashed border-orange-200 text-right text-[11px] md:text-xs text-slate-400 shrink-0">
                Sumber: Prakirawan BMKG - Jawa Timur
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const warningData = @json($warning);
    </script>
    <script src="{{ asset('js/warning.js') }}"></script>
@endpush