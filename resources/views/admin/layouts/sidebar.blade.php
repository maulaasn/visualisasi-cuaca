<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Admin')</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    @stack('styles')
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-slate-50 flex h-screen overflow-hidden relative">

    <div class="w-[280px] bg-[#1b2579] text-white flex flex-col shrink-0 shadow-xl z-20">
        
        <div class="pt-8 pb-6 flex flex-col items-center border-b border-white/10 mx-5">
            <div class="bg-[#c2cae6] px-5 py-2.5 rounded-full flex items-center gap-1.5 shadow-sm">
                <span class="text-[#134295] font-black text-[20px] tracking-tight">GIS</span>
                <span class="text-slate-800 font-bold text-[15px]">Cuaca Jatim</span>
            </div>
            <div class="text-[12px] text-blue-100 mt-4 font-medium tracking-wide">Monitor Data Spasial & Cuaca</div>
        </div>
        
        <nav class="flex-1 px-5 py-5 space-y-3 overflow-y-auto">
            
            <a href="{{ route('admin.dashboard') }}" 
               class="flex items-center gap-4 p-3.5 rounded-xl transition-all duration-200 border-[1.5px]
               {{ request()->routeIs('admin.dashboard') 
                  ? 'bg-[#253294] border-[#6ba4ff] shadow-md' 
                  : 'bg-[#202a85] border-transparent hover:bg-[#253294]/80' }}">
                <div class="text-white shrink-0">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                        <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                </div>
                <div>
                    <div class="font-bold text-[13px] mb-0.5">Peta Explorer</div>
                    <div class="text-[11px] text-blue-200 tracking-wide">Jelajahi wilayah dengan detail</div>
                </div>
            </a>

            <a href="{{ route('admin.warning') }}" 
               class="flex items-center gap-4 p-3.5 rounded-xl transition-all duration-200 border-[1.5px]
               {{ request()->routeIs('admin.warning') 
                  ? 'bg-[#253294] border-[#6ba4ff] shadow-md' 
                  : 'bg-[#202a85] border-transparent hover:bg-[#253294]/80' }}">
                <div class="text-white shrink-0">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                </div>
                <div>
                    <div class="font-bold text-[13px] mb-0.5">Peringatan Dini Cuaca</div>
                    <div class="text-[11px] text-blue-200 tracking-wide">Pantau cuaca ekstrem</div>
                </div>
            </a>

            <a href="{{ route('admin.news.index') }}" 
               class="flex items-center gap-4 p-3.5 rounded-xl transition-all duration-200 border-[1.5px]
               {{ request()->routeIs('admin.news.*') 
                  ? 'bg-[#253294] border-[#6ba4ff] shadow-md' 
                  : 'bg-[#202a85] border-transparent hover:bg-[#253294]/80' }}">
                <div class="text-white shrink-0">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                </div>
                <div>
                    <div class="font-bold text-[13px] mb-0.5">Berita Terbaru</div>
                    <div class="text-[11px] text-blue-200 tracking-wide">Update informasi terdepan</div>
                </div>
            </a>

        </nav>

        <div class="p-5 border-t border-white/10 flex items-center relative bg-transparent z-50">
            <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shrink-0 shadow-sm">
                <span class="text-slate-800 font-bold text-[14px]">AD</span>
            </div>
            
            <div class="ml-3 flex-1 overflow-hidden">
                <div class="text-[13px] font-bold text-white truncate mb-0.5">{{ Auth::user()->name ?? 'Admin' }}</div>
                <div class="text-[11px] text-slate-300 truncate">{{ Auth::user()->email ?? 'admin@gmail.com' }}</div>
            </div>
            
            <button id="userMenuBtn" type="button" class="p-1.5 text-slate-300 hover:text-white hover:bg-white/10 rounded-lg transition-colors cursor-pointer">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="1.5"></circle>
                    <circle cx="12" cy="5" r="1.5"></circle>
                    <circle cx="12" cy="19" r="1.5"></circle>
                </svg>
            </button>

            <div id="userDropdown" class="hidden absolute bottom-5 left-[265px] w-36 bg-[#202a85] border border-[#253294] rounded-lg shadow-[0_10px_40px_rgba(0,0,0,0.2)] py-1.5 z-[100]">
                <button type="button" onclick="showLogoutModal()" class="w-full text-left px-3.5 py-2 text-[13px] font-semibold text-blue-100 hover:bg-[#253294] hover:text-white flex items-center gap-2.5 transition-colors cursor-pointer">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                    Log out
                </button>
            </div>
        </div>
    </div>

    <div class="flex-1 overflow-y-auto relative h-screen w-full">
        @yield('content')
    </div>

    <div id="logoutModal" class="hidden fixed inset-0 z-[9999] bg-slate-900/40 flex items-center justify-center p-4 opacity-0 transition-opacity duration-300">
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6 transform scale-95 transition-transform duration-300 relative" id="logoutModalContent">
            
            <button type="button" onclick="hideLogoutModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-700 hover:bg-slate-100 p-1.5 rounded-full transition-colors cursor-pointer outline-none">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>

            <div class="flex flex-col items-center text-center">
                <div class="w-14 h-14 rounded-full bg-red-100 flex items-center justify-center mb-5">
                    <svg class="w-7 h-7 text-red-600 ml-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                </div>
                
                <p class="text-[14px] text-slate-500 mb-8 px-2">Apakah anda yakin ingin keluar dari halaman admin?</p>
                
                <div class="flex gap-3 w-full">
                    <button type="button" onclick="hideLogoutModal()" class="flex-1 px-4 py-2.5 bg-slate-50 hover:bg-slate-100 text-slate-700 text-sm font-bold rounded-lg transition-colors border border-slate-300 shadow-sm cursor-pointer">
                        Batal
                    </button>
                    <form action="{{ route('admin.logout') }}" method="POST" class="flex-1 m-0">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-lg transition-colors shadow-sm shadow-red-200 border border-red-700 cursor-pointer">
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="toast-container" class="fixed top-6 left-1/2 -translate-x-1/2 z-[9999] flex flex-col items-center gap-3 pointer-events-none w-full px-4"></div>

    @stack('scripts')
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btn = document.getElementById('userMenuBtn');
            const dropdown = document.getElementById('userDropdown');
            
            if(btn && dropdown) {
                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    dropdown.classList.toggle('hidden');
                });
                
                document.addEventListener('click', function(e) {
                    if (!dropdown.contains(e.target) && !btn.contains(e.target)) {
                        dropdown.classList.add('hidden');
                    }
                });
            }

            @if(session('success'))
                showSonnerToast("{!! session('success') !!}", 'success');
            @endif

            @if(session('error'))
                showSonnerToast("{!! session('error') !!}", 'error');
            @endif
        });

        const modal = document.getElementById('logoutModal');
        const modalContent = document.getElementById('logoutModalContent');

        function showLogoutModal() {
            document.getElementById('userDropdown').classList.add('hidden');
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modal.classList.add('opacity-100');
                modalContent.classList.remove('scale-95');
                modalContent.classList.add('scale-100');
            }, 10);
        }

        function hideLogoutModal() {
            modal.classList.remove('opacity-100');
            modal.classList.add('opacity-0');
            modalContent.classList.remove('scale-100');
            modalContent.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        function showSonnerToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            
            toast.className = `transform transition-all duration-400 -translate-y-10 opacity-0 flex items-center gap-3 px-4 py-3.5 bg-white border border-slate-100 rounded-xl shadow-[0_8px_30px_rgb(0,0,0,0.08)] min-w-[300px] max-w-sm w-fit`;
            
            let icon = '';
            if (type === 'success') {
                icon = `<div class="flex items-center justify-center w-6 h-6 bg-green-500 rounded-full shrink-0 shadow-sm shadow-green-200">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>`;
            } else if (type === 'error') {
                icon = `<div class="flex items-center justify-center w-6 h-6 bg-red-500 rounded-full shrink-0 shadow-sm shadow-red-200">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>`;
            }

            toast.innerHTML = `
                ${icon}
                <p class="text-[14px] font-semibold text-slate-700 flex-1">${message}</p>
            `;

            container.appendChild(toast);

            requestAnimationFrame(() => {
                setTimeout(() => {
                    toast.classList.remove('-translate-y-10', 'opacity-0');
                }, 10);
            });

            setTimeout(() => {
                if(container.contains(toast)) {
                    toast.remove();
                }
            }, 3000);
        }
    </script>
</body>
</html>