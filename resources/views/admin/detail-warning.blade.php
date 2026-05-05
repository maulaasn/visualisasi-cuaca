@extends('admin.layouts.sidebar')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@section('content')
    <div class="warning-map-container pt-6 px-6 pb-10 md:pt-[30px] md:px-[40px] md:pb-[40px] m-0 w-full box-border flex flex-col h-screen">
        
        <div class="mb-2 flex items-center justify-between shrink-0">
            <div>
                <h1 class="page-title text-xl font-bold text-slate-800 m-0 mb-1">Peta Persebaran Cuaca Ekstrem</h1>
                <p class="last-update text-sm text-slate-500 m-0 mb-5">Pembaruan terakhir: {{ $checkedAt }}</p>
            </div>
            
            <a href="{{ route('admin.warning') }}" class="text-sm text-slate-600 hover:text-orange-600 font-medium transition-colors no-underline flex items-center gap-1">
                &larr; Kembali ke Peringatan Dini
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