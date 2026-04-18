@extends('layouts.main')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@section('content')
<div class="map-container flex-1 p-3 md:p-[30px_40px] relative h-[calc(100vh-70px)]">
    
    <div id="loading-overlay" class="loading-overlay absolute inset-0 bg-white/80 z-[2000] flex flex-col items-center justify-center rounded-xl m-2.5 md:m-[30px_40px]">
        <div class="spinner animate-spin-custom w-10 h-10 border-4 border-slate-200 border-t-blue-600 rounded-full mb-4"></div>
        <p class="text-slate-700">Memuat Data Cuaca...</p>
    </div>

    <div id="map" class="w-full h-full rounded-xl shadow-[0_4px_6px_-1px_rgba(0,0,0,0.1)] z-10"></div>

    <div class="filter-box absolute bottom-[35px] md:bottom-[45px] left-5 md:left-[52px] bg-white/95 p-2.5 md:p-[15px] rounded-lg shadow-[0_4px_15px_rgba(0,0,0,0.1)] z-[1000] min-w-[130px] md:min-w-[160px] scale-[0.85] md:scale-100 origin-bottom-left">
        <div class="filter-header text-xs md:text-[13px] font-bold text-slate-800 mb-2 md:mb-2.5 pb-1 md:pb-2 border-b border-slate-200">Filter Cuaca</div>
        <div class="filter-list flex flex-col gap-2 max-h-[140px] md:max-h-[250px] overflow-y-auto">
            <label class="filter-item flex items-center gap-2 text-xs text-slate-700 cursor-pointer select-none"><input type="checkbox" class="filter-checkbox accent-blue-600 w-3.5 h-3.5 cursor-pointer m-0" value="Cerah" checked> <span>Cerah</span></label>
            <label class="filter-item flex items-center gap-2 text-xs text-slate-700 cursor-pointer select-none"><input type="checkbox" class="filter-checkbox accent-blue-600 w-3.5 h-3.5 cursor-pointer m-0" value="Cerah Berawan" checked> <span>Cerah Berawan</span></label>
            <label class="filter-item flex items-center gap-2 text-xs text-slate-700 cursor-pointer select-none"><input type="checkbox" class="filter-checkbox accent-blue-600 w-3.5 h-3.5 cursor-pointer m-0" value="Berawan" checked> <span>Berawan</span></label>
            <label class="filter-item flex items-center gap-2 text-xs text-slate-700 cursor-pointer select-none"><input type="checkbox" class="filter-checkbox accent-blue-600 w-3.5 h-3.5 cursor-pointer m-0" value="Berawan Tebal" checked> <span>Berawan Tebal</span></label>
            <label class="filter-item flex items-center gap-2 text-xs text-slate-700 cursor-pointer select-none"><input type="checkbox" class="filter-checkbox accent-blue-600 w-3.5 h-3.5 cursor-pointer m-0" value="Udara Kabur" checked> <span>Udara Kabur</span></label>
            <label class="filter-item flex items-center gap-2 text-xs text-slate-700 cursor-pointer select-none"><input type="checkbox" class="filter-checkbox accent-blue-600 w-3.5 h-3.5 cursor-pointer m-0" value="Kabut/Asap" checked> <span>Kabut/Asap</span></label>
            <label class="filter-item flex items-center gap-2 text-xs text-slate-700 cursor-pointer select-none"><input type="checkbox" class="filter-checkbox accent-blue-600 w-3.5 h-3.5 cursor-pointer m-0" value="Hujan Ringan" checked> <span>Hujan Ringan</span></label>
            <label class="filter-item flex items-center gap-2 text-xs text-slate-700 cursor-pointer select-none"><input type="checkbox" class="filter-checkbox accent-blue-600 w-3.5 h-3.5 cursor-pointer m-0" value="Hujan Sedang" checked> <span>Hujan Sedang</span></label>
            <label class="filter-item flex items-center gap-2 text-xs text-slate-700 cursor-pointer select-none"><input type="checkbox" class="filter-checkbox accent-blue-600 w-3.5 h-3.5 cursor-pointer m-0" value="Hujan Lebat" checked> <span>Hujan Lebat</span></label>
            <label class="filter-item flex items-center gap-2 text-xs text-slate-700 cursor-pointer select-none"><input type="checkbox" class="filter-checkbox accent-blue-600 w-3.5 h-3.5 cursor-pointer m-0" value="Hujan Petir" checked> <span>Hujan Petir</span></label>
        </div>
    </div>

    <div class="legend-box absolute bottom-[35px] md:bottom-[55px] right-[18px] md:right-[50px] bg-white/95 p-[15px_20px] rounded-lg shadow-[0_4px_15px_rgba(0,0,0,0.1)] z-[1000] text-xs text-slate-700 pointer-events-none scale-[0.75] md:scale-100 origin-bottom-right">
        <div class="legend-item flex items-center mb-1.5 last:mb-0"><span class="color-box w-[18px] h-[18px] mr-2.5 rounded-[3px] border border-black/10" style="background: #F1C40F;"></span> Cerah</div>
        <div class="legend-item flex items-center mb-1.5 last:mb-0"><span class="color-box w-[18px] h-[18px] mr-2.5 rounded-[3px] border border-black/10" style="background: #F4D03F;"></span> Cerah Berawan</div>
        <div class="legend-item flex items-center mb-1.5 last:mb-0"><span class="color-box w-[18px] h-[18px] mr-2.5 rounded-[3px] border border-black/10" style="background: #F9E79F;"></span> Berawan</div>
        <div class="legend-item flex items-center mb-1.5 last:mb-0"><span class="color-box w-[18px] h-[18px] mr-2.5 rounded-[3px] border border-black/10" style="background: #D4AC0D;"></span> Berawan Tebal</div>
        <div class="legend-item flex items-center mb-1.5 last:mb-0"><span class="color-box w-[18px] h-[18px] mr-2.5 rounded-[3px] border border-black/10" style="background: #2C3E50;"></span> Udara Kabur</div>
        <div class="legend-item flex items-center mb-1.5 last:mb-0"><span class="color-box w-[18px] h-[18px] mr-2.5 rounded-[3px] border border-black/10" style="background: #7F8C8D;"></span> Kabut/Asap</div>
        <div class="legend-item flex items-center mb-1.5 last:mb-0"><span class="color-box w-[18px] h-[18px] mr-2.5 rounded-[3px] border border-black/10" style="background: #85C1E9;"></span> Hujan Ringan</div>
        <div class="legend-item flex items-center mb-1.5 last:mb-0"><span class="color-box w-[18px] h-[18px] mr-2.5 rounded-[3px] border border-black/10" style="background: #5DADE2;"></span> Hujan Sedang</div>
        <div class="legend-item flex items-center mb-1.5 last:mb-0"><span class="color-box w-[18px] h-[18px] mr-2.5 rounded-[3px] border border-black/10" style="background: #3498DB;"></span> Hujan Lebat</div>
        <div class="legend-item flex items-center mb-1.5 last:mb-0"><span class="color-box w-[18px] h-[18px] mr-2.5 rounded-[3px] border border-black/10" style="background: #283747;"></span> Hujan Petir</div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="{{ asset('js/weather.js') }}"></script>
@endpush