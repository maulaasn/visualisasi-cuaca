document.addEventListener('DOMContentLoaded', function () {

    const map = L.map('map', { zoomControl: false }).setView([-7.5360639, 112.2384017], 8);
    L.control.zoom({ position: 'topleft' }).addTo(map);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    const redIcon = new L.Icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41]
    });

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
    let currentlyHovered = null;
    let marker = null;
    let allPolygons = [];

    function formatWIBDate(dateString) {
        if (!dateString) return '';
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
        const parts = dateString.split(' ');
        if (parts.length !== 2) return dateString;
        const dateParts = parts[0].split('-');
        const timeParts = parts[1].split(':');
        const year = dateParts[0];
        const month = months[parseInt(dateParts[1], 10) - 1];
        const day = parseInt(dateParts[2], 10);
        const hour = timeParts[0];
        const minute = timeParts[1];
        return `${day} ${month} ${year} (${hour}:${minute}) WIB`;
    }

    function translateWindDirection(dir) {
        if (!dir) return '-';
        const directions = {
            'N': 'Utara', 'NNE': 'Utara Timur Laut', 'NE': 'Timur Laut', 'ENE': 'Timur Timur Laut',
            'E': 'Timur', 'ESE': 'Timur Tenggara', 'SE': 'Tenggara', 'SSE': 'Selatan Tenggara',
            'S': 'Selatan', 'SSW': 'Selatan Barat Daya', 'SW': 'Barat Daya', 'WSW': 'Barat Barat Daya',
            'W': 'Barat', 'WNW': 'Barat Barat Laut', 'NW': 'Barat Laut', 'NNW': 'Utara Barat Laut'
        };
        const cleanDir = String(dir).trim().toUpperCase();
        return directions[cleanDir] || dir;
    }

    function getWeatherColor(code) {
        const colors = {
            '0': '#F1C40F', '1': '#F4D03F', '2': '#F4D03F', '3': '#F9E79F',
            '4': '#D4AC0D', '5': '#2C3E50', '10': '#7F8C8D', '45': '#7F8C8D',
            '60': '#85C1E9', '61': '#5DADE2', '63': '#3498DB', '97': '#283747',
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
        if (weatherDataMap[name3]) return weatherDataMap[name3];
        for (const wKey of weatherKeys) {
            const normalizedWKey = normalizeKey(wKey);
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
                <p class="item-label" style="margin-top:0; margin-bottom:2px;">${kabupaten}</p>
                <h5 class="popup-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                    ${kecamatan}
                </h5>
                <div class="badge-row">
                    <span class="weather-badge" style="background:#e2e8f0;color:#64748b;text-shadow:none;">Data Tidak Tersedia</span>
                </div>
            </div>
            `;
        }

        const badgeBg = getWeatherColor(weather.weather_code);
        const formattedUpdate = formatWIBDate(weather.last_update);
        const translatedWind = translateWindDirection(weather.wind_dir);
        let forecastHtml = '';

        if (weather.forecasts && weather.forecasts.length > 0) {
            forecastHtml = `
            <div class="forecast-section">
                <div class="forecast-title">
                    <svg width="14" height="12" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    Prakiraan Cuaca
                </div>
                <div class="forecast-list">
                    ${weather.forecasts.map(f => `
                        <div class="forecast-item">
                            <span class="forecast-time">${formatWIBDate(f.datetime)}</span>
                            <span class="forecast-desc">${f.weather_desc}</span>
                        </div>
                    `).join('')}
                </div>
            </div>
            `;
        }

        return `
        <div class="popup-header">
            <p class="item-label" style="margin-top:0; margin-bottom:5px; color:#64748b; font-size:11px; font-weight:600;">
                ${kabupaten}
            </p>
            <h5 class="popup-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                ${kecamatan}
            </h5>
            <div class="badge-row">
                <span class="weather-badge" style="background:${badgeBg}; color:#ffffff;">${weather.weather_desc}</span>
                <span class="update-time">Update: ${formattedUpdate}</span>
            </div>
        </div>
        <div class="popup-grid">
            <div class="grid-item">
                <div class="grid-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2"><path d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z"></path></svg></div>
                <div class="grid-data">
                    <span class="item-label">Suhu</span>
                    <span class="item-value">${weather.temp}°C</span>
                </div>
            </div>
            <div class="grid-item">
                <div class="grid-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2"><path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"></path></svg></div>
                <div class="grid-data">
                    <span class="item-label">Kelembapan</span>
                    <span class="item-value">${weather.humidity}%</span>
                </div>
            </div>
            <div class="grid-item">
                <div class="grid-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2"><path d="M9.59 4.59A2 2 0 1 1 11 8H2m10.59 11.41A2 2 0 1 0 14 16H2m15.73-8.27A2.5 2.5 0 1 1 19.5 12H2"></path></svg></div>
                <div class="grid-data">
                    <span class="item-label">Angin</span>
                    <span class="item-value">${weather.wind_speed} km/j</span>
                </div>
            </div>
            <div class="grid-item">
                <div class="grid-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#8b5cf6" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"></polygon></svg></div>
                <div class="grid-data">
                    <span class="item-label">Arah Angin</span>
                    <span class="item-value">${translatedWind}</span>
                </div>
            </div>
            <div class="grid-item" style="grid-column:span 2;">
                <div class="grid-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></div>
                <div class="grid-data">
                    <span class="item-label">Jarak Pandang</span>
                    <span class="item-value">${weather.visibility}</span>
                </div>
            </div>
        </div>
        ${forecastHtml}
        `;
    }

    function styleLayer(layer, weather) {
        if (weather) {
            layer.setStyle({
                fillColor: getWeatherColor(weather.weather_code),
                weight: 1,
                opacity: 1,
                color: 'white',
                dashArray: '3',
                fillOpacity: 0.65,
            });
        } else {
            layer.setStyle({
                fillColor: '#e2e8f0',
                weight: 1,
                opacity: 1,
                color: 'white',
                dashArray: '3',
                fillOpacity: 0.3,
            });
        }
    }

    function resetLayerStyle(layer) {
        const weather = findWeather(layer.feature.properties.NAME_3);
        styleLayer(layer, weather);
    }

    function forceResetAllLayers() {
        map.eachLayer(function (layer) {
            if (layer.feature && layer.feature.properties && layer.feature.properties.NAME_3) {
                resetLayerStyle(layer);
            }
        });
    }

    function onEachFeature(feature, layer) {
        const weather = findWeather(feature.properties.NAME_3);
        const desc = weather ? weather.weather_desc : 'Tanpa Data';

        allPolygons.push({ layer: layer, desc: desc });
        styleLayer(layer, weather);

        layer.on({
            mouseover: function (e) {
                if (currentlyHovered && currentlyHovered !== activeLayer && currentlyHovered !== e.target) {
                    resetLayerStyle(currentlyHovered);
                }
                currentlyHovered = e.target;
                if (activeLayer !== e.target) {
                    e.target.setStyle({ weight: 3, color: '#666', dashArray: '', fillOpacity: 0.85 });
                    e.target.bringToFront();
                }
            },
            mouseout: function (e) {
                if (activeLayer !== e.target) {
                    resetLayerStyle(e.target);
                }
                if (currentlyHovered === e.target) {
                    currentlyHovered = null;
                }
            },
            click: function (e) {
                forceResetAllLayers();
                activeLayer = e.target;
                activeLayer.setStyle({ weight: 3, color: '#2563eb', fillOpacity: 0.85 });
                activeLayer.bringToFront();
                if (marker) map.removeLayer(marker);
                marker = L.marker(e.latlng, { icon: redIcon }).addTo(map);

                const isMobile = window.innerWidth <= 768;
                const popupWidth = isMobile ? 280 : 380;

                L.popup({
                    className: 'custom-popup',
                    minWidth: popupWidth,
                    maxWidth: popupWidth,
                    offset: [0, -20],
                })
                    .setLatLng(e.latlng)
                    .setContent(generatePopupContent(feature, weather))
                    .openOn(map);
            },
        });
    }

    map.on('popupclose', function () {
        forceResetAllLayers();
        activeLayer = null;
        currentlyHovered = null;
        if (marker) {
            map.removeLayer(marker);
            marker = null;
        }
    });

    function applyFilter() {
        const checkboxes = document.querySelectorAll('.filter-checkbox:checked');
        const activeFilters = Array.from(checkboxes).map(cb => cb.value);

        allPolygons.forEach(item => {
            let isVisible = true;

            if (item.desc !== 'Tanpa Data') {
                let currentDesc = item.desc;
                if (currentDesc === 'Kabut' || currentDesc === 'Asap') {
                    currentDesc = 'Kabut/Asap';
                }

                if (!activeFilters.includes(currentDesc)) {
                    isVisible = false;
                }
            }

            if (isVisible) {
                if (!map.hasLayer(item.layer)) {
                    map.addLayer(item.layer);
                }
            } else {
                if (map.hasLayer(item.layer)) {
                    map.removeLayer(item.layer);
                }
            }
        });
    }

    const filterCheckboxes = document.querySelectorAll('.filter-checkbox');
    filterCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', applyFilter);
    });

    async function initMap() {
        try {
            const weatherResponse = await fetch('/api/weather');
            const weatherJson = await weatherResponse.json();

            if (weatherJson.status === 'success') {
                weatherDataMap = weatherJson.data;
                weatherKeys = Object.keys(weatherDataMap);
            }

            for (const url of geoJsonUrls) {
                try {
                    const geoResponse = await fetch(url);
                    if (!geoResponse.ok) {
                        console.error(`Gagal memuat: ${url} (Status: ${geoResponse.status})`);
                        continue;
                    }
                    const geoData = await geoResponse.json();
                    L.geoJSON(geoData, { onEachFeature: onEachFeature }).addTo(map);
                } catch (err) {
                    console.error(`Error parsing di: ${url}`, err);
                }
            }

            const loadingScreen = document.getElementById('loading-screen');
            if (loadingScreen) {
                loadingScreen.style.display = 'none';
            }

            applyFilter();

        } catch (error) {
            const loadingScreen = document.getElementById('loading-screen');
            if (loadingScreen) {
                loadingScreen.innerHTML = '<p style="color:#ef4444; font-weight:600;">Gagal memuat data. Coba refresh halaman.</p>';
            }
        }
    }

    initMap();
});