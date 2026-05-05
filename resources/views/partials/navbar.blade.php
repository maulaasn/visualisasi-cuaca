<nav class="bg-white border-b border-slate-300 sticky top-0 z-[3000]">
    <div class="flex px-5 md:px-10 h-[70px]">

        <div class="flex items-center mr-auto md:mr-10 h-full">
            <div class="border-[1.5px] border-blue-600 rounded-full px-4 py-2 font-bold text-sm">
                <span class="text-blue-600">GIS</span> <span class="text-slate-800">Cuaca Jatim</span>
            </div>
        </div>

        <button class="self-center block md:hidden bg-transparent border-none text-slate-700 cursor-pointer p-1 ml-auto"
            id="mobileMenuBtn" aria-label="Menu">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="3" y1="12" x2="21" y2="12"></line>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
        </button>

        <div class="hidden [&.show]:flex md:flex absolute top-[70px] left-0 right-0 bg-white md:bg-transparent flex-col h-auto shadow-md md:static md:flex-row md:h-full md:shadow-none md:-mb-[1px]"
            id="navMenu">

            <a href="{{ route('map.index') }}" class="flex items-center gap-2 px-5 py-4 md:py-0 font-medium text-[15px] md:h-full transition-colors duration-200 border-l-[3px] md:border-l-0 md:border-b-[3px]
               {{ request()->routeIs('map.index') ? 'text-blue-600 border-blue-600' : 'text-slate-500 border-transparent hover:text-blue-600' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 20l-5.447-2.724A2 2 0 013 15.489V5.236a2 2 0 012.894-1.789l5.106 2.553 6-3 5.447 2.724A2 2 0 0121 7.511v10.253a2 2 0 01-2.894 1.789l-5.106-2.553-6 3z"></path>
                    <path d="M9 5v15"></path>
                    <path d="M15 4v15"></path>
                </svg>
                Peta Visualisasi
            </a>

            <a href="{{ route('warning.index') }}" class="flex items-center gap-2 px-5 py-4 md:py-0 font-medium text-[15px] md:h-full transition-colors duration-200 border-l-[3px] md:border-l-0 md:border-b-[3px]
               {{ request()->routeIs('warning.*') ? 'text-blue-600 border-blue-600' : 'text-slate-500 border-transparent hover:text-blue-600' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                Peringatan Dini
            </a>

            <a href="{{ route('news.index') }}" class="flex items-center gap-2 px-5 py-4 md:py-0 font-medium text-[15px] md:h-full transition-colors duration-200 border-l-[3px] md:border-l-0 md:border-b-[3px]
                {{ request()->routeIs('news.index') || request()->routeIs('news.show') ? 'text-blue-600 border-blue-600' : 'text-slate-500 border-transparent hover:text-blue-600' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 22h16a2 2 0 002-2V4a2 2 0 00-2-2H8a2 2 0 00-2 2v16l-2 2z"></path>
                    <path d="M14 2v4a2 2 0 002 2h4"></path>
                </svg>
                Berita
            </a>
        </div>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const mobileBtn = document.getElementById('mobileMenuBtn');
        const navMenu = document.getElementById('navMenu');
        if (mobileBtn && navMenu) {
            mobileBtn.addEventListener('click', function (e) {
                e.preventDefault();
                navMenu.classList.toggle('show');
            });
        }
    });
</script>