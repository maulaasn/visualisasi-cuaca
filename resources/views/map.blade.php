<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualisasi Spasial Cuaca di Jawa Timur</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/weather-map.css') }}">
</head>
<body>

    <nav class="navbar">
        <div class="nav-brand">
            <div class="brand-badge">
                <span class="text-blue">GIS</span> <span class="text-dark">Cuaca Jatim</span>
            </div>
        </div>

        <button class="mobile-menu-btn" aria-label="Menu">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
        </button>

        <div class="nav-menu" id="navMenu">
            <a href="#" class="nav-link active">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 20l-5.447-2.724A2 2 0 013 15.489V5.236a2 2 0 012.894-1.789l5.106 2.553 6-3 5.447 2.724A2 2 0 0121 7.511v10.253a2 2 0 01-2.894 1.789l-5.106-2.553-6 3z"></path><path d="M9 5v15"></path><path d="M15 4v15"></path></svg>
                Peta Visualisasi
            </a>
            <a href="#" class="nav-link">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                Peringatan Dini
            </a>
            <a href="#" class="nav-link">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 22h16a2 2 0 002-2V4a2 2 0 00-2-2H8a2 2 0 00-2 2v16l-2 2z"></path><path d="M14 2v4a2 2 0 002 2h4"></path></svg>
                Berita
            </a>
        </div>
    </nav>

    <div class="map-container">
        <div id="loading-overlay" class="loading-overlay">
            <div class="spinner"></div>
            <p>Memuat Data Spasial & Cuaca...</p>
        </div>
        <div id="map"></div>
        
        <div class="legend-box">
            <div class="legend-item"><span class="color-box" style="background: #F1C40F;"></span> Cerah</div>
            <div class="legend-item"><span class="color-box" style="background: #F4D03F;"></span> Cerah Berawan</div>
            <div class="legend-item"><span class="color-box" style="background: #F9E79F;"></span> Berawan</div>
            <div class="legend-item"><span class="color-box" style="background: #D4AC0D;"></span> Berawan Tebal</div>
            <div class="legend-item"><span class="color-box" style="background: #2C3E50;"></span> Udara Kabur</div>
            <div class="legend-item"><span class="color-box" style="background: #A6ACAF;"></span> Asap</div>
            <div class="legend-item"><span class="color-box" style="background: #7F8C8D;"></span> Kabut</div>
            <div class="legend-item"><span class="color-box" style="background: #85C1E9;"></span> Hujan Ringan</div>
            <div class="legend-item"><span class="color-box" style="background: #5DADE2;"></span> Hujan Sedang</div>
            <div class="legend-item"><span class="color-box" style="background: #3498DB;"></span> Hujan Lebat</div>
            <div class="legend-item"><span class="color-box" style="background: #283747;"></span> Hujan Petir</div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="{{ asset('js/weather-map.js') }}"></script>
</body>
</html>