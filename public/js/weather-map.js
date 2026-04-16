document.addEventListener('DOMContentLoaded', function () {
    // Fungsi Menu Hamburger Mobile
    const mobileBtn = document.querySelector('.mobile-menu-btn');
    const navMenu = document.getElementById('navMenu');

    if (mobileBtn && navMenu) {
        mobileBtn.addEventListener('click', function () {
            navMenu.classList.toggle('show');
        });
    }

    // Inisialisasi Peta
    const map = L.map('map', {
        zoomControl: false
    }).setView([-7.5360639, 112.2384017], 8);

    L.control.zoom({
        position: 'topleft'
    }).addTo(map);

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
    let activeLayer = null;
    let marker = null;

    function getWeatherColor(code) {
        const colors = {
            '0': '#F1C40F',
            '1': '#F4D03F',
            '2': '#F4D03F',
            '3': '#F9E79F',
            '4': '#D4AC0D',
            '5': '#2C3E50',
            '10': '#A6ACAF',
            '45': '#7F8C8D',
            '60': '#85C1E9',
            '61': '#5DADE2',
            '63': '#3498DB',
            '97': '#283747'
        };
        return colors[code] || '#FFFFFF';
    }

    function normalizeString(str) {
        if (!str) return '';
        return str.toLowerCase().replace(/kecamatan |kec\.|kab\.|kota /g, '').trim();
    }

    function generatePopupContent(feature, weather) {
        const kecamatan = feature.properties.NAME_3 || 'Kecamatan Tidak Diketahui';
        const kabupaten = feature.properties.NAME_2 || 'Kabupaten Tidak Diketahui';

        if (!weather) {
            return `
            <div class="popup-header">
                <p class="item-label" style="margin-bottom: 2px;">${kabupaten}</p>
                <h5 class="popup-title" style="margin-top: 0;">${kecamatan}</h5>
                <span class="weather-badge" style="background:#e2e8f0; color:#64748b;">Data Tidak Tersedia</span>
            </div>
        `;
        }

        return `
        <div class="popup-header">
            <p class="item-label" style="margin-bottom: 2px; color: #64748b; font-size: 11px; font-weight: 600; uppercase;">
                ${kabupaten}
            </p>
            <h5 class="popup-title" style="margin-top: 0; margin-bottom: 8px;">
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
            <div class="grid-item" style="grid-column: span 2;">
                <span class="item-label">Jarak Pandang</span>
                <span class="item-value">${weather.visibility} km</span>
            </div>
        </div>
    `;
    }

    function onEachFeature(feature, layer) {
        const areaName = normalizeString(feature.properties.NAME_3);
        const weather = weatherDataMap[areaName];

        if (weather) {
            layer.setStyle({
                fillColor: getWeatherColor(weather.weather_code),
                weight: 1,
                opacity: 1,
                color: 'white',
                dashArray: '3',
                fillOpacity: 0.6
            });
        } else {
            layer.setStyle({
                fillColor: '#e2e8f0',
                weight: 1,
                opacity: 1,
                color: 'white',
                dashArray: '3',
                fillOpacity: 0.3
            });
        }

        layer.on({
            mouseover: function (e) {
                const layer = e.target;
                layer.setStyle({
                    weight: 3,
                    color: '#666',
                    dashArray: '',
                    fillOpacity: 0.8
                });
                layer.bringToFront();
            },
            mouseout: function (e) {
                if (activeLayer !== e.target) {
                    map.eachLayer((l) => {
                        if (l.feature && l.feature === e.target.feature) {
                            const isWeatherExist = weatherDataMap[normalizeString(l.feature.properties.NAME_3)];
                            l.setStyle({
                                weight: 1,
                                color: 'white',
                                dashArray: '3',
                                fillOpacity: isWeatherExist ? 0.6 : 0.3
                            });
                        }
                    });
                }
            },
            click: function (e) {
                if (activeLayer) {
                    map.eachLayer((l) => {
                        if (l.feature && l.feature === activeLayer.feature) {
                            const isWeatherExist = weatherDataMap[normalizeString(l.feature.properties.NAME_3)];
                            l.setStyle({
                                weight: 1,
                                color: 'white',
                                dashArray: '3',
                                fillOpacity: isWeatherExist ? 0.6 : 0.3
                            });
                        }
                    });
                }

                activeLayer = e.target;
                activeLayer.setStyle({
                    weight: 3,
                    color: '#2563eb',
                    fillOpacity: 0.8
                });

                if (marker) {
                    map.removeLayer(marker);
                }

                marker = L.marker(e.latlng).addTo(map);

                const isMobile = window.innerWidth <= 768;
                const popupWidth = isMobile ? 250 : 320; // 250px untuk HP, 320px untuk PC

                const popup = L.popup({
                    className: 'custom-popup',
                    minWidth: popupWidth,
                    maxWidth: popupWidth,
                    offset: [0, -20]
                })
                    .setLatLng(e.latlng)
                    .setContent(generatePopupContent(feature, weather))
                    .openOn(map);
            }
        });
    }

    async function initMap() {
        try {
            const weatherResponse = await fetch('/api/weather');
            const weatherJson = await weatherResponse.json();

            if (weatherJson.status === 'success') {
                weatherDataMap = weatherJson.data;
            }

            const loadPromises = geoJsonUrls.map(async (url) => {
                try {
                    const geoResponse = await fetch(url);
                    const geoData = await geoResponse.json();
                    L.geoJSON(geoData, {
                        onEachFeature: onEachFeature
                    }).addTo(map);
                } catch (err) {
                    console.error(url);
                }
            });

            await Promise.all(loadPromises);

            document.getElementById('loading-overlay').style.display = 'none';

        } catch (error) {
            document.getElementById('loading-overlay').innerHTML = '<p>Gagal memuat data.</p>';
        }
    }

    initMap();
});