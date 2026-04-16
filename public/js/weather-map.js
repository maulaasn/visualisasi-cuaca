document.addEventListener('DOMContentLoaded', function () {
    const mobileBtn = document.querySelector('.mobile-menu-btn');
    const navMenu = document.getElementById('navMenu');

    if (mobileBtn && navMenu) {
        mobileBtn.addEventListener('click', function () {
            navMenu.classList.toggle('show');
        });
    }

    const map = L.map('map', { zoomControl: false }).setView([-7.5360639, 112.2384017], 8);

    L.control.zoom({ position: 'topleft' }).addTo(map);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    const daftarKabupaten = [
        'Bangkalan', 'Banyuwangi', 'Batu', 'Blitar', 'Bojonegoro',
        'Bondowoso', 'Gresik', 'Jember', 'Jombang', 'Kediri',
        'Kota Blitar', 'Kota Kediri', 'Kota Madiun', 'Kota Malang',
        'Kota Mojokerto', 'Kota Pasuruan', 'Kota Probolinggo',
        'Lamongan', 'Lumajang', 'Madiun', 'Magetan', 'Malang',
        'Mojokerto', 'Nganjuk', 'Ngawi', 'Pacitan', 'Pamekasan',
        'Pasuruan', 'Ponorogo', 'Probolinggo', 'Sampang', 'Sidoarjo',
        'Situbondo', 'Sumenep', 'Surabaya', 'Trenggalek', 'Tuban',
        'Tulungagung'
    ];

    const geoJsonUrls = daftarKabupaten.map(nama => `/assets/geojson/${nama}.geojson`);

    let weatherDataMap = {};
    let weatherKeys = [];
    let activeLayer = null;
    let marker = null;

    function getWeatherColor(code) {
        const colors = {
            '0':  '#F1C40F',
            '1':  '#F4D03F',
            '2':  '#F4D03F',
            '3':  '#F9E79F',
            '4':  '#D4AC0D',
            '5':  '#2C3E50',
            '10': '#A6ACAF',
            '45': '#7F8C8D',
            '60': '#85C1E9',
            '61': '#5DADE2',
            '63': '#3498DB',
            '97': '#283747',
        };
        return colors[String(code)] || '#FFFFFF';
    }

    function normalizeKey(str) {
        if (!str) return '';
        let s = str.toLowerCase().trim();
        s = s.replace(/\b(kecamatan|kec\.|kab\.|kota|kepulauan|kep\.)\s*/g, '');
        s = s.replace(/[^a-z0-9\s]/g, '');
        s = s.replace(/\s+/g, ' ').trim();
        return s;
    }

    function findWeather(name3) {
        const key = normalizeKey(name3);

        if (weatherDataMap[key]) return weatherDataMap[key];

        for (const wKey of weatherKeys) {
            if (wKey === key) return weatherDataMap[wKey];
            if (wKey.includes(key) || key.includes(wKey)) return weatherDataMap[wKey];
        }

        return null;
    }

    function generatePopupContent(feature, weather) {
        const kecamatan = feature.properties.NAME_3 || 'Kecamatan Tidak Diketahui';
        const kabupaten = feature.properties.NAME_2 || 'Kabupaten Tidak Diketahui';

        if (!weather) {
            return `
                <div class="popup-header">
                    <p class="item-label" style="margin-bottom:2px;">${kabupaten}</p>
                    <h5 class="popup-title" style="margin-top:0;">${kecamatan}</h5>
                    <span class="weather-badge" style="background:#e2e8f0;color:#64748b;">Data Tidak Tersedia</span>
                </div>
            `;
        }

        return `
            <div class="popup-header">
                <p class="item-label" style="margin-bottom:2px;color:#64748b;font-size:11px;font-weight:600;">
                    ${kabupaten}
                </p>
                <h5 class="popup-title" style="margin-top:0;margin-bottom:8px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"></path>
                        <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                    ${kecamatan}
                </h5>
                <span class="weather-badge">${weather.weather_desc}</span>
                <span class="update-time">Update: ${weather.last_update}</span>
            </div>
            <div class="popup-grid">
                <div class="grid-item">
                    <span class="item-label">Suhu</span>
                    <span class="item-value">${weather.temp}°C</span>
                </div>
                <div class="grid-item">
                    <span class="item-label">Kelembapan</span>
                    <span class="item-value">${weather.humidity}%</span>
                </div>
                <div class="grid-item">
                    <span class="item-label">Kec. Angin</span>
                    <span class="item-value">${weather.wind_speed} km/j</span>
                </div>
                <div class="grid-item">
                    <span class="item-label">Arah Angin</span>
                    <span class="item-value">${weather.wind_dir}</span>
                </div>
                <div class="grid-item" style="grid-column:span 2;">
                    <span class="item-label">Jarak Pandang</span>
                    <span class="item-value">${weather.visibility}</span>
                </div>
            </div>
        `;
    }

    function styleLayer(layer, weather) {
        if (weather) {
            layer.setStyle({
                fillColor:   getWeatherColor(weather.weather_code),
                weight:      1,
                opacity:     1,
                color:       'white',
                dashArray:   '3',
                fillOpacity: 0.65,
            });
        } else {
            layer.setStyle({
                fillColor:   '#e2e8f0',
                weight:      1,
                opacity:     1,
                color:       'white',
                dashArray:   '3',
                fillOpacity: 0.3,
            });
        }
    }

    function resetLayerStyle(layer) {
        const weather = findWeather(layer.feature.properties.NAME_3);
        styleLayer(layer, weather);
    }

    function onEachFeature(feature, layer) {
        const weather = findWeather(feature.properties.NAME_3);
        styleLayer(layer, weather);

        layer.on({
            mouseover: function (e) {
                e.target.setStyle({ weight: 3, color: '#666', dashArray: '', fillOpacity: 0.85 });
                e.target.bringToFront();
            },
            mouseout: function (e) {
                if (activeLayer !== e.target) {
                    resetLayerStyle(e.target);
                }
            },
            click: function (e) {
                if (activeLayer) resetLayerStyle(activeLayer);

                activeLayer = e.target;
                activeLayer.setStyle({ weight: 3, color: '#2563eb', fillOpacity: 0.85 });

                if (marker) map.removeLayer(marker);
                marker = L.marker(e.latlng).addTo(map);

                const isMobile = window.innerWidth <= 768;
                const popupWidth = isMobile ? 250 : 320;

                L.popup({
                    className: 'custom-popup',
                    minWidth:  popupWidth,
                    maxWidth:  popupWidth,
                    offset:    [0, -20],
                })
                    .setLatLng(e.latlng)
                    .setContent(generatePopupContent(feature, weather))
                    .openOn(map);
            },
        });
    }

    async function initMap() {
        try {
            const weatherResponse = await fetch('/api/weather');
            const weatherJson = await weatherResponse.json();

            if (weatherJson.status === 'success') {
                weatherDataMap = weatherJson.data;
                weatherKeys = Object.keys(weatherDataMap);
            } else {
                console.warn('Gagal memuat data cuaca:', weatherJson.message);
            }

            const loadPromises = geoJsonUrls.map(async (url) => {
                try {
                    const geoResponse = await fetch(url);
                    if (!geoResponse.ok) return;
                    const geoData = await geoResponse.json();
                    L.geoJSON(geoData, { onEachFeature }).addTo(map);
                } catch (err) {
                    console.warn('GeoJSON gagal:', url, err.message);
                }
            });

            await Promise.all(loadPromises);

            document.getElementById('loading-overlay').style.display = 'none';
        } catch (error) {
            console.error('initMap error:', error);
            document.getElementById('loading-overlay').innerHTML =
                '<p style="color:#ef4444;">Gagal memuat data. Coba refresh halaman.</p>';
        }
    }

    initMap();
});