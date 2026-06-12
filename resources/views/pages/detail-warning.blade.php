@extends('layouts.main')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@section('content')
    <div class="warning-map-container p-5 md:p-[30px_40px] m-0 w-full box-border">
        
        <div class="mb-4 flex flex-col md:flex-row md:items-center justify-between gap-3 md:gap-0">
            <a href="{{ route('warning.index') }}"
                class="order-1 md:order-2 text-[13px] md:text-sm text-orange-600 hover:text-orange-600 font-medium transition-colors no-underline flex items-center gap-1 self-start">
                &larr; Kembali ke Peringatan Dini
            </a>
            
            <div class="order-2 md:order-1">
                <h1 class="page-title text-[15px] md:text-[20px] font-bold text-slate-800 m-0 mb-1">Peta Persebaran Cuaca Ekstrem</h1>
                <p class="last-update text-xs md:text-sm text-slate-500 m-0 mb-1.5 md:mb-3">Pembaruan terakhir: {{ $checkedAt }}</p>
            </div>
        </div>

        <div class="bg-orange-50 border border-orange-200 rounded-lg p-3 md:p-6 w-full flex flex-col h-[65vh] md:h-[70vh] min-h-[400px] md:min-h-[500px]">

            <div id="full-map" class="w-full flex-grow rounded-lg border border-orange-200 z-10 bg-gray-100"></div>

            <div class="warning-footer mt-3 md:mt-4 pt-3 md:pt-4 border-t border-dashed border-orange-200 text-center text-right text-[10px] md:text-xs text-slate-400">
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