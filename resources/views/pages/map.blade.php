@extends('layouts.main')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush 

@section('content')
    <div class="map-container flex-1 p-4 md:p-[30px_40px] relative h-[calc(100vh-70px)]">
        <div id="loading-screen" class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-[2000] flex justify-center items-center bg-white/95 px-5 py-3 rounded-full shadow-lg border border-slate-100 transition-opacity duration-500">
            <svg class="animate-spin text-blue-600 w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="ml-2 font-semibold text-slate-700 text-xs md:text-sm">Memuat Peta...</span>
        </div>

        <div class="absolute top-[24px] md:top-[45px] right-[24px] md:right-[52px] z-[1500] flex flex-col items-end">
            
            <div id="layer-popup" class="hidden absolute md:static right-[48px] md:right-auto top-0 md:top-auto md:mb-3 bg-white rounded-lg shadow-[0_4px_15px_rgba(0,0,0,0.15)] md:shadow-[0_4px_20px_rgba(0,0,0,0.15)] p-1.5 md:p-3 w-max min-w-[100px] md:w-auto flex flex-col md:flex-row gap-0.5 md:gap-2 border border-slate-100 transition-all origin-top-right md:origin-bottom-right z-20">
                
                <button id="btn-suhu" class="w-full md:w-auto flex md:flex-col items-center gap-2 md:gap-1.5 py-1 px-1.5 md:p-0 rounded-md transition focus:outline-none group hover:bg-slate-50">
                    <div id="icon-suhu" class="w-[26px] h-[26px] md:w-[52px] md:h-[52px] rounded-[6px] md:rounded-lg md:border-2 border border-[#1a73e8] transition-all relative overflow-hidden flex items-center justify-center text-[#1a73e8] shadow-sm bg-white shrink-0">
                        <div id="bg-suhu" class="absolute inset-0 bg-gradient-to-br from-orange-100 via-white to-blue-100 opacity-100 transition-opacity duration-300"></div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 md:w-6 md:h-6 relative z-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z"></path>
                            <path d="M12 7v5"></path>
                        </svg>
                    </div>
                    <span class="text-[12px] md:text-[11px] font-semibold md:font-medium text-slate-700 md:text-center whitespace-nowrap">Suhu</span>
                </button>

                <button id="btn-cuaca" class="w-full md:w-auto flex md:flex-col items-center gap-2 md:gap-1.5 py-1 px-1.5 md:p-0 rounded-md transition focus:outline-none group hover:bg-slate-50">
                    <div id="icon-cuaca" class="w-[26px] h-[26px] md:w-[52px] md:h-[52px] rounded-[6px] md:rounded-lg md:border-2 border border-transparent transition-all relative overflow-hidden flex items-center justify-center text-slate-500 shadow-sm bg-slate-50 group-hover:bg-white shrink-0">
                        <div id="bg-cuaca" class="absolute inset-0 bg-gradient-to-br from-slate-200 via-white to-sky-100 opacity-30 group-hover:opacity-60 transition-opacity duration-300"></div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 md:w-6 md:h-6 relative z-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17.5 19H9a7 7 0 1 1 6.71-9h1.79a4.5 4.5 0 1 1 0 9Z"></path>
                        </svg>
                    </div>
                    <span class="text-[12px] md:text-[11px] font-semibold md:font-medium text-slate-700 md:text-center whitespace-nowrap">Cuaca</span>
                </button>
            </div>

            <button id="btn-lapisan" class="w-[42px] h-[42px] md:w-[60px] md:h-[60px] rounded-lg border md:border-[3px] border-slate-300 md:border-white shadow-[0_2px_8px_rgba(0,0,0,0.15)] md:shadow-[0_4px_12px_rgba(0,0,0,0.2)] overflow-hidden relative group bg-white focus:outline-none flex items-center justify-center transition-all duration-300 hover:bg-slate-50 md:hover:bg-white z-10">
                <div class="md:hidden flex items-center justify-center text-slate-700 w-full h-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-[22px] h-[22px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                        <polyline points="2 12 12 17 22 12"></polyline>
                        <polyline points="2 17 12 22 22 17"></polyline>
                    </svg>
                </div>
                <div class="hidden md:block absolute inset-0 w-full h-full">
                    <img src="https://mt1.google.com/vt/lyrs=s&x=0&y=0&z=0" alt="Satelit" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-300" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                    <div class="absolute bottom-1 left-0 right-0 flex flex-col items-center justify-center text-white pb-0.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mb-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                            <polyline points="2 12 12 17 22 12"></polyline>
                            <polyline points="2 17 12 22 22 17"></polyline>
                        </svg>
                        <span class="text-[10px] font-bold drop-shadow-md tracking-wide">Lapisan</span>
                    </div>
                </div>
            </button>
        </div>

        <div id="map" class="w-full h-full rounded-xl shadow-[0_4px_6px_-1px_rgba(0,0,0,0.1)] z-10"></div>

        <div id="filter-cuaca" class="hidden absolute top-[72px] right-[24px] md:top-auto md:right-auto md:bottom-[45px] md:left-[52px] z-[1000] w-[42px] h-[42px] md:w-auto md:h-auto">
            
            <button type="button" id="btn-toggle-filter-cuaca" class="md:hidden w-full h-full bg-white rounded-lg border border-slate-300 shadow-[0_2px_8px_rgba(0,0,0,0.15)] flex items-center justify-center text-slate-700 transition focus:outline-none hover:bg-slate-50 relative z-20">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                </svg>
            </button>

            <div id="panel-filter-cuaca" class="hidden md:block absolute right-[48px] md:right-auto md:static top-0 md:top-auto bg-white/95 p-3 md:p-[15px] rounded-lg shadow-[0_4px_15px_rgba(0,0,0,0.15)] md:min-w-[160px] border border-slate-100 md:border-none origin-top-right md:origin-bottom-left z-10 w-max max-w-[calc(100vw-80px)] md:w-auto md:max-w-none">
                <div class="filter-header text-xs md:text-[13px] font-bold text-slate-800 mb-2 md:mb-2.5 pb-1 md:pb-2 border-b border-slate-200">Filter Cuaca</div>
                <div class="filter-list flex flex-col gap-2 max-h-[40vh] md:max-h-none md:overflow-visible overflow-y-auto pr-1">
                    <label class="filter-item flex items-center gap-2 text-xs text-slate-700 cursor-pointer select-none whitespace-nowrap">
                        <input type="checkbox" class="filter-checkbox-cuaca accent-blue-600 w-3.5 h-3.5 cursor-pointer m-0 flex-shrink-0" value="Cerah" checked> 
                        <span class="w-[14px] h-[14px] flex items-center justify-center md:hidden flex-shrink-0"><svg viewBox="0 0 24 24" fill="none" stroke="#F1C40F" stroke-width="2"><circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42 1.42"/></svg></span>
                        <span>Cerah</span>
                    </label>
                    <label class="filter-item flex items-center gap-2 text-xs text-slate-700 cursor-pointer select-none whitespace-nowrap">
                        <input type="checkbox" class="filter-checkbox-cuaca accent-blue-600 w-3.5 h-3.5 cursor-pointer m-0 flex-shrink-0" value="Cerah Berawan" checked> 
                        <span class="w-[14px] h-[14px] flex items-center justify-center md:hidden flex-shrink-0"><svg viewBox="0 0 24 24" fill="none" stroke="#F1C40F" stroke-width="2"><path d="M12 2v2M4.93 4.93l1.41 1.41M2 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41M22 12h-2M17.66 17.66l1.41 1.41"/><path d="M17.5 19H9a7 7 0 1 1 6.71-9h1.79a4.5 4.5 0 1 1 0 9Z" stroke="#94a3b8"/></svg></span>
                        <span>Cerah Berawan</span>
                    </label>
                    <label class="filter-item flex items-center gap-2 text-xs text-slate-700 cursor-pointer select-none whitespace-nowrap">
                        <input type="checkbox" class="filter-checkbox-cuaca accent-blue-600 w-3.5 h-3.5 cursor-pointer m-0 flex-shrink-0" value="Berawan" checked> 
                        <span class="w-[14px] h-[14px] flex items-center justify-center md:hidden flex-shrink-0"><svg viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><path d="M17.5 19H9a7 7 0 1 1 6.71-9h1.79a4.5 4.5 0 1 1 0 9Z"/></svg></span>
                        <span>Berawan</span>
                    </label>
                    <label class="filter-item flex items-center gap-2 text-xs text-slate-700 cursor-pointer select-none whitespace-nowrap">
                        <input type="checkbox" class="filter-checkbox-cuaca accent-blue-600 w-3.5 h-3.5 cursor-pointer m-0 flex-shrink-0" value="Berawan Tebal" checked> 
                        <span class="w-[14px] h-[14px] rounded-[3px] border border-black/10 md:hidden flex-shrink-0" style="background: #475569;"></span>
                        <span>Berawan Tebal</span>
                    </label>
                    <label class="filter-item flex items-center gap-2 text-xs text-slate-700 cursor-pointer select-none whitespace-nowrap">
                        <input type="checkbox" class="filter-checkbox-cuaca accent-blue-600 w-3.5 h-3.5 cursor-pointer m-0 flex-shrink-0" value="Udara Kabur" checked> 
                        <span class="w-[14px] h-[14px] rounded-[3px] border border-black/10 md:hidden flex-shrink-0" style="background: #2C3E50;"></span>
                        <span>Udara Kabur</span>
                    </label>
                    <label class="filter-item flex items-center gap-2 text-xs text-slate-700 cursor-pointer select-none whitespace-nowrap">
                        <input type="checkbox" class="filter-checkbox-cuaca accent-blue-600 w-3.5 h-3.5 cursor-pointer m-0 flex-shrink-0" value="Kabut/Asap" checked> 
                        <span class="w-[14px] h-[14px] rounded-[3px] border border-black/10 md:hidden flex-shrink-0" style="background: #7F8C8D;"></span>
                        <span>Kabut/Asap</span>
                    </label>
                    <label class="filter-item flex items-center gap-2 text-xs text-slate-700 cursor-pointer select-none whitespace-nowrap">
                        <input type="checkbox" class="filter-checkbox-cuaca accent-blue-600 w-3.5 h-3.5 cursor-pointer m-0 flex-shrink-0" value="Hujan Ringan" checked> 
                        <span class="w-[14px] h-[14px] rounded-[3px] border border-black/10 md:hidden flex-shrink-0" style="background: #22C55E;"></span>
                        <span>Hujan Ringan</span>
                    </label>
                    <label class="filter-item flex items-center gap-2 text-xs text-slate-700 cursor-pointer select-none whitespace-nowrap">
                        <input type="checkbox" class="filter-checkbox-cuaca accent-blue-600 w-3.5 h-3.5 cursor-pointer m-0 flex-shrink-0" value="Hujan Sedang" checked> 
                        <span class="w-[14px] h-[14px] rounded-[3px] border border-black/10 md:hidden flex-shrink-0" style="background: #EAB308;"></span>
                        <span>Hujan Sedang</span>
                    </label>
                    <label class="filter-item flex items-center gap-2 text-xs text-slate-700 cursor-pointer select-none whitespace-nowrap">
                        <input type="checkbox" class="filter-checkbox-cuaca accent-blue-600 w-3.5 h-3.5 cursor-pointer m-0 flex-shrink-0" value="Hujan Lebat" checked> 
                        <span class="w-[14px] h-[14px] rounded-[3px] border border-black/10 md:hidden flex-shrink-0" style="background: #F97316;"></span>
                        <span>Hujan Lebat</span>
                    </label>
                    <label class="filter-item flex items-center gap-2 text-xs text-slate-700 cursor-pointer select-none whitespace-nowrap">
                        <input type="checkbox" class="filter-checkbox-cuaca accent-blue-600 w-3.5 h-3.5 cursor-pointer m-0 flex-shrink-0" value="Hujan Petir" checked> 
                        <span class="w-[14px] h-[14px] rounded-[3px] border border-black/10 md:hidden flex-shrink-0" style="background: #EF4444;"></span>
                        <span>Hujan Petir</span>
                    </label>
                    <label class="filter-item flex items-center gap-2 text-xs text-slate-700 cursor-pointer select-none whitespace-nowrap">
                        <input type="checkbox" class="filter-checkbox-cuaca accent-blue-600 w-3.5 h-3.5 cursor-pointer m-0 flex-shrink-0" value="Hujan Petir Hebat" checked> 
                        <span class="w-[14px] h-[14px] rounded-[3px] border border-black/10 md:hidden flex-shrink-0" style="background: #A855F7;"></span>
                        <span>Hujan Petir Hebat</span>
                    </label>
                </div>
            </div>
        </div>

        <div id="filter-suhu" class="absolute top-[72px] right-[24px] md:top-auto md:right-auto md:bottom-[45px] md:left-[52px] z-[1000] w-[42px] h-[42px] md:w-auto md:h-auto">
            
            <button type="button" id="btn-toggle-filter-suhu" class="md:hidden w-full h-full bg-white rounded-lg border border-slate-300 shadow-[0_2px_8px_rgba(0,0,0,0.15)] flex items-center justify-center text-slate-700 transition focus:outline-none hover:bg-slate-50 relative z-20">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                </svg>
            </button>

            <div id="panel-filter-suhu" class="hidden md:block absolute right-[48px] md:right-auto md:static top-0 md:top-auto bg-white/95 p-3 md:p-[15px] rounded-lg shadow-[0_4px_15px_rgba(0,0,0,0.15)] md:min-w-[160px] border border-slate-100 md:border-none origin-top-right md:origin-bottom-left z-10 w-max max-w-[calc(100vw-80px)] md:w-auto md:max-w-none">
                <div class="filter-header text-xs md:text-[13px] font-bold text-slate-800 mb-2 md:mb-2.5 pb-1 md:pb-2 border-b border-slate-200">Filter Suhu</div>
                <div class="filter-list flex flex-col gap-2 max-h-[40vh] md:max-h-[250px] overflow-y-auto pr-1">
                    <label class="flex items-center gap-2 text-xs text-slate-700 cursor-pointer whitespace-nowrap">
                        <input type="checkbox" class="filter-checkbox-suhu accent-blue-600 w-3.5 h-3.5 m-0 flex-shrink-0" value="Sangat Panas" checked> 
                        <span class="w-[14px] h-[14px] rounded-[3px] border border-black/10 md:hidden flex-shrink-0" style="background: #ef4444;"></span>
                        <span>Sangat Panas <span class="md:hidden text-slate-500">(> 34°C)</span></span>
                    </label>
                    <label class="flex items-center gap-2 text-xs text-slate-700 cursor-pointer whitespace-nowrap">
                        <input type="checkbox" class="filter-checkbox-suhu accent-blue-600 w-3.5 h-3.5 m-0 flex-shrink-0" value="Panas" checked> 
                        <span class="w-[14px] h-[14px] rounded-[3px] border border-black/10 md:hidden flex-shrink-0" style="background: #f97316;"></span>
                        <span>Panas <span class="md:hidden text-slate-500">(30°C - 34°C)</span></span>
                    </label>
                    <label class="flex items-center gap-2 text-xs text-slate-700 cursor-pointer whitespace-nowrap">
                        <input type="checkbox" class="filter-checkbox-suhu accent-blue-600 w-3.5 h-3.5 m-0 flex-shrink-0" value="Normal" checked> 
                        <span class="w-[14px] h-[14px] rounded-[3px] border border-black/10 md:hidden flex-shrink-0" style="background: #facc15;"></span>
                        <span>Normal <span class="md:hidden text-slate-500">(26°C - 29°C)</span></span>
                    </label>
                    <label class="flex items-center gap-2 text-xs text-slate-700 cursor-pointer whitespace-nowrap">
                        <input type="checkbox" class="filter-checkbox-suhu accent-blue-600 w-3.5 h-3.5 m-0 flex-shrink-0" value="Sejuk" checked> 
                        <span class="w-[14px] h-[14px] rounded-[3px] border border-black/10 md:hidden flex-shrink-0" style="background: #60a5fa;"></span>
                        <span>Sejuk <span class="md:hidden text-slate-500">(22°C - 25°C)</span></span>
                    </label>
                    <label class="flex items-center gap-2 text-xs text-slate-700 cursor-pointer whitespace-nowrap">
                        <input type="checkbox" class="filter-checkbox-suhu accent-blue-600 w-3.5 h-3.5 m-0 flex-shrink-0" value="Dingin" checked> 
                        <span class="w-[14px] h-[14px] rounded-[3px] border border-black/10 md:hidden flex-shrink-0" style="background: #3b82f6;"></span>
                        <span>Dingin <span class="md:hidden text-slate-500">(< 22°C)</span></span>
                    </label>
                </div>
            </div>
        </div>

        <div class="hidden md:block">
            <div id="legend-cuaca" class="legend-box hidden absolute bottom-[58px] right-[52px] bg-white/95 p-[15px_20px] rounded-lg shadow-[0_4px_15px_rgba(0,0,0,0.1)] text-xs text-slate-700 z-[1000] pointer-events-none origin-bottom-right">
                <div class="legend-item flex items-center mb-1.5 last:mb-0"><span class="w-[18px] h-[18px] mr-2.5 flex items-center justify-center"><svg viewBox="0 0 24 24" fill="none" stroke="#F1C40F" stroke-width="2"><circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42 1.42"/></svg></span> Cerah</div>
                <div class="legend-item flex items-center mb-1.5 last:mb-0"><span class="w-[18px] h-[18px] mr-2.5 flex items-center justify-center"><svg viewBox="0 0 24 24" fill="none" stroke="#F1C40F" stroke-width="2"><path d="M12 2v2M4.93 4.93l1.41 1.41M2 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41M22 12h-2M17.66 17.66l1.41 1.41"/><path d="M17.5 19H9a7 7 0 1 1 6.71-9h1.79a4.5 4.5 0 1 1 0 9Z" stroke="#94a3b8"/></svg></span> Cerah Berawan</div>
                <div class="legend-item flex items-center mb-1.5 last:mb-0"><span class="w-[18px] h-[18px] mr-2.5 flex items-center justify-center"><svg viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><path d="M17.5 19H9a7 7 0 1 1 6.71-9h1.79a4.5 4.5 0 1 1 0 9Z"/></svg></span> Berawan</div>
                <div class="legend-item flex items-center mb-1.5 last:mb-0"><span class="color-box w-[18px] h-[18px] mr-2.5 rounded-[3px] border border-black/10" style="background: #475569;"></span> Berawan Tebal</div>
                <div class="legend-item flex items-center mb-1.5 last:mb-0"><span class="color-box w-[18px] h-[18px] mr-2.5 rounded-[3px] border border-black/10" style="background: #2C3E50;"></span> Udara Kabur</div>
                <div class="legend-item flex items-center mb-1.5 last:mb-0"><span class="color-box w-[18px] h-[18px] mr-2.5 rounded-[3px] border border-black/10" style="background: #7F8C8D;"></span> Kabut/Asap</div>
                <hr class="my-2 border-slate-200">
                <div class="legend-item flex items-center mb-1.5 last:mb-0"><span class="color-box w-[18px] h-[18px] mr-2.5 rounded-[3px] border border-black/10" style="background: #22C55E;"></span> Hujan Ringan</div>
                <div class="legend-item flex items-center mb-1.5 last:mb-0"><span class="color-box w-[18px] h-[18px] mr-2.5 rounded-[3px] border border-black/10" style="background: #EAB308;"></span> Hujan Sedang</div>
                <div class="legend-item flex items-center mb-1.5 last:mb-0"><span class="color-box w-[18px] h-[18px] mr-2.5 rounded-[3px] border border-black/10" style="background: #F97316;"></span> Hujan Lebat</div>
                <div class="legend-item flex items-center mb-1.5 last:mb-0"><span class="color-box w-[18px] h-[18px] mr-2.5 rounded-[3px] border border-black/10" style="background: #EF4444;"></span> Hujan Petir</div>
                <div class="legend-item flex items-center mb-1.5 last:mb-0"><span class="color-box w-[18px] h-[18px] mr-2.5 rounded-[3px] border border-black/10" style="background: #A855F7;"></span> Hujan Petir Hebat</div>
            </div>

            <div id="legend-suhu" class="legend-box absolute bottom-[58px] right-[52px] bg-white/95 p-[15px_20px] rounded-lg shadow-[0_4px_15px_rgba(0,0,0,0.1)] text-xs text-slate-700 z-[1000] pointer-events-none origin-bottom-right">
                <div class="flex items-center mb-1.5"><span class="w-[18px] h-[18px] mr-2.5 rounded-[3px] border border-black/10" style="background: #ef4444;"></span> Sangat Panas (> 34°C)</div>
                <div class="flex items-center mb-1.5"><span class="w-[18px] h-[18px] mr-2.5 rounded-[3px] border border-black/10" style="background: #f97316;"></span> Panas (30°C - 34°C)</div>
                <div class="flex items-center mb-1.5"><span class="w-[18px] h-[18px] mr-2.5 rounded-[3px] border border-black/10" style="background: #facc15;"></span> Normal (26°C - 29°C)</div>
                <div class="flex items-center mb-1.5"><span class="w-[18px] h-[18px] mr-2.5 rounded-[3px] border border-black/10" style="background: #60a5fa;"></span> Sejuk (22°C - 25°C)</div>
                <div class="flex items-center"><span class="w-[18px] h-[18px] mr-2.5 rounded-[3px] border border-black/10" style="background: #3b82f6;"></span> Dingin (< 22°C)</div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="{{ asset('js/weather.js') }}"></script>
@endpush