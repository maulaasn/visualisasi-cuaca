@extends('layouts.main')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@section('content')
    <div class="warning-map-container p-6 md:p-[30px_40px] m-0 w-full box-border">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h1 class="page-title text-base md:text-[20px] font-bold text-slate-800 m-0 mb-1">Peta Persebaran Cuaca
                    Ekstrem</h1>
                <p class="last-update text-xs md:text-sm text-slate-500 m-0 mb-2">Pembaruan terakhir: {{ $checkedAt }}</p>
            </div>
            <a href="{{ route('warning.index') }}"
                class="text-sm text-slate-600 hover:text-orange-600 font-medium transition-colors no-underline flex items-center gap-1">
                &larr; Kembali ke Peringatan Dini
            </a>
        </div>

        <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 md:p-6 w-full flex flex-col h-[70vh] min-h-[500px]">

            <div id="full-map" class="w-full flex-grow rounded-lg border border-orange-200 z-10 bg-gray-100"></div>

            <div
                class="warning-footer mt-4 pt-4 border-t border-dashed border-orange-200 text-right text-[11px] md:text-xs text-slate-400">
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