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
    let currentView = 'cuaca';

    const btnSuhu = document.getElementById('btn-suhu');
    const btnCuaca = document.getElementById('btn-cuaca');
    const filterSuhu = document.getElementById('filter-suhu');
    const filterCuaca = document.getElementById('filter-cuaca');
    const legendSuhu = document.getElementById('legend-suhu');
    const legendCuaca = document.getElementById('legend-cuaca');
    const btnLapisan = document.getElementById('btn-lapisan');
    const layerPopup = document.getElementById('layer-popup');

    const btnToggleCuaca = document.getElementById('btn-toggle-filter-cuaca');
    const panelCuaca = document.getElementById('panel-filter-cuaca');
    const btnToggleSuhu = document.getElementById('btn-toggle-filter-suhu');
    const panelSuhu = document.getElementById('panel-filter-suhu');

    if (btnSuhu) {
        btnSuhu.addEventListener('click', () => {
            setView('suhu');
            if (layerPopup) layerPopup.classList.add('hidden');
        });
    }

    if (btnCuaca) {
        btnCuaca.addEventListener('click', () => {
            setView('cuaca');
            if (layerPopup) layerPopup.classList.add('hidden');
        });
    }

    if (btnLapisan && layerPopup) {
        btnLapisan.addEventListener('click', (e) => {
            e.stopPropagation();
            layerPopup.classList.toggle('hidden');
            if (layerPopup.style.display === 'none') {
                layerPopup.style.display = '';
            }

            if (window.innerWidth < 768) {
                if (panelCuaca) panelCuaca.classList.add('hidden');
                if (panelSuhu) panelSuhu.classList.add('hidden');
            }
        });
    }

    if (btnToggleCuaca && panelCuaca) {
        btnToggleCuaca.addEventListener('click', (e) => {
            e.stopPropagation();
            panelCuaca.classList.toggle('hidden');

            if (layerPopup && !layerPopup.classList.contains('hidden')) {
                layerPopup.classList.add('hidden');
            }
        });
    }

    if (btnToggleSuhu && panelSuhu) {
        btnToggleSuhu.addEventListener('click', (e) => {
            e.stopPropagation();
            panelSuhu.classList.toggle('hidden');

            if (layerPopup && !layerPopup.classList.contains('hidden')) {
                layerPopup.classList.add('hidden');
            }
        });
    }

    document.addEventListener('click', (e) => {
        if (layerPopup && btnLapisan && !btnLapisan.contains(e.target) && !layerPopup.contains(e.target)) {
            layerPopup.classList.add('hidden');
            layerPopup.style.display = ''; 
        }

        if (window.innerWidth < 768) {
            if (filterCuaca && panelCuaca && !filterCuaca.contains(e.target)) {
                panelCuaca.classList.add('hidden');
            }
            if (filterSuhu && panelSuhu && !filterSuhu.contains(e.target)) {
                panelSuhu.classList.add('hidden');
            }
        }
    });

    function setView(type) {
        currentView = type;
        const isSuhu = type === 'suhu';
        
        const iconSuhu = document.getElementById('icon-suhu');
        const iconCuaca = document.getElementById('icon-cuaca');
        const bgSuhu = document.getElementById('bg-suhu');
        const bgCuaca = document.getElementById('bg-cuaca');

        const activeContainer = 'w-[26px] h-[26px] md:w-[38px] md:h-[38px] rounded-[6px] md:rounded-md md:border-2 border border-[#1a73e8] transition-all relative overflow-hidden flex items-center justify-center text-[#1a73e8] shadow-sm bg-white shrink-0';
        const inactiveContainer = 'w-[26px] h-[26px] md:w-[38px] md:h-[38px] rounded-[6px] md:rounded-md md:border-2 border border-transparent transition-all relative overflow-hidden flex items-center justify-center text-slate-500 shadow-sm bg-slate-50 group-hover:bg-white shrink-0';

        if (isSuhu) {
            if (iconSuhu) iconSuhu.className = activeContainer;
            if (iconCuaca) iconCuaca.className = inactiveContainer;
            
            if (bgSuhu) bgSuhu.className = 'absolute inset-0 bg-gradient-to-br from-orange-100 via-white to-blue-100 opacity-100 transition-opacity duration-300';
            if (bgCuaca) bgCuaca.className = 'absolute inset-0 bg-gradient-to-br from-slate-200 via-white to-sky-100 opacity-30 group-hover:opacity-60 transition-opacity duration-300';
        } else {
            if (iconCuaca) iconCuaca.className = activeContainer;
            if (iconSuhu) iconSuhu.className = inactiveContainer;
            
            if (bgSuhu) bgSuhu.className = 'absolute inset-0 bg-gradient-to-br from-orange-100 via-white to-blue-100 opacity-30 group-hover:opacity-60 transition-opacity duration-300';
            if (bgCuaca) bgCuaca.className = 'absolute inset-0 bg-gradient-to-br from-slate-200 via-white to-sky-100 opacity-100 transition-opacity duration-300';
        }

        if (filterSuhu) filterSuhu.classList.toggle('hidden', !isSuhu);
        if (legendSuhu) legendSuhu.classList.toggle('hidden', !isSuhu);
        if (filterCuaca) filterCuaca.classList.toggle('hidden', isSuhu);
        if (legendCuaca) legendCuaca.classList.toggle('hidden', isSuhu);

        map.closePopup();
        forceResetAllLayers();
        applyFilter();
    }

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

    function getStandardizedDesc(code, originalDesc) {
        const cleanCode = String(parseInt(code, 10));
        if (['0', '100'].includes(cleanCode)) return 'Cerah';
        if (['1', '2', '101', '102'].includes(cleanCode)) return 'Cerah Berawan';
        return originalDesc;
    }

    function getWeatherColor(code) {
        const cleanCode = String(parseInt(code, 10));
        const colors = {
            '0': '#FCD34D',
            '1': '#FDE68A',
            '2': '#FDE68A',
            '3': '#CBD5E1',
            '4': '#475569',
            '5': '#2C3E50',
            '10': '#7F8C8D',
            '45': '#7F8C8D',
            '60': '#22C55E',
            '61': '#22C55E',
            '63': '#EAB308',
            '65': '#F97316',
            '95': '#EF4444',
            '97': '#A855F7',
            '100': '#FCD34D', 
            '101': '#FDE68A', 
            '102': '#FDE68A', 
            '103': '#CBD5E1', 
            '104': '#475569'  
        };
        return colors[cleanCode] || '#E2E8F0';
    }

    function getTemperatureColor(temp) {
        if (!temp) return '#e2e8f0';
        if (temp < 22) return '#3b82f6';
        if (temp >= 22 && temp < 26) return '#60a5fa';
        if (temp >= 26 && temp < 30) return '#facc15';
        if (temp >= 30 && temp <= 34) return '#f97316';
        return '#ef4444';
    }

    function getTemperatureCategory(temp) {
        if (!temp) return 'Tanpa Data';
        if (temp < 22) return 'Dingin';
        if (temp >= 22 && temp < 26) return 'Sejuk';
        if (temp >= 26 && temp < 30) return 'Normal';
        if (temp >= 30 && temp <= 34) return 'Panas';
        return 'Sangat Panas';
    }

    function getContrastColor(hexColor) {
        hexColor = hexColor.replace(/^#/, '');
        const r = parseInt(hexColor.substr(0, 2), 16);
        const g = parseInt(hexColor.substr(2, 2), 16);
        const b = parseInt(hexColor.substr(4, 2), 16);
        const luminance = (0.2126 * r + 0.7152 * g + 0.0722 * b);
        return (luminance > 160) ? '#1e293b' : '#FFFFFF';
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
                    <span class="weather-badge" style="background:#e2e8f0; color:#64748b; text-shadow:none; border:none;">Data Tidak Tersedia</span>
                </div>
            </div>
            `;
        }

        const formattedUpdate = formatWIBDate(weather.last_update);
        const translatedWind = translateWindDirection(weather.wind_dir);
        let forecastHtml = '';

        if (weather.forecasts && weather.forecasts.length > 0) {
            const dailyForecasts = weather.forecasts;
            const forecastTitle = currentView === 'suhu' ? 'Prakiraan Suhu' : 'Prakiraan Cuaca';

            forecastHtml = `
            <style>
                .vertical-scroll-forecast {
                    -ms-overflow-style: none;  
                    scrollbar-width: none;  
                }
                .vertical-scroll-forecast::-webkit-scrollbar { 
                    display: none; 
                }
            </style>
            <div class="forecast-section">
                <div class="forecast-title" style="margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                    <svg width="14" height="12" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    ${forecastTitle}
                </div>
                <div class="vertical-scroll-forecast" style="display: flex; flex-direction: column; max-height: 115px; overflow-y: auto;">
                    ${dailyForecasts.map((f, index) => {
                const isLast = index === dailyForecasts.length - 1;
                const borderBottom = isLast ? 'none' : '1px dashed #e2e8f0';
                
                let tempVal = f.temp !== undefined ? f.temp : (f.t !== undefined ? f.t : f.tempC);
                const infoKanan = currentView === 'suhu' ? getTemperatureCategory(tempVal) : f.weather_desc;

                return `
                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 11px; color: #334155; padding: 6px 0; border-bottom: ${borderBottom}; width: 100%;">
                            <span style="color: #475569;">${formatWIBDate(f.datetime)}</span>
                            <span style="text-align: right; font-weight: 500;">${infoKanan}</span>
                        </div>
                        `;
            }).join('')}
                </div>
            </div>
            `;
        }

        let badgeBg, badgeText;
        let grid1Icon, grid1Label, grid1Value;

        if (currentView === 'suhu') {
            badgeBg = getTemperatureColor(weather.temp);
            badgeText = `${weather.temp}°C (${getTemperatureCategory(weather.temp)})`;

            const c = String(parseInt(weather.weather_code, 10));
            if(c === '0' || c === '100') grid1Icon = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#F1C40F" stroke-width="2"><circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42 1.42"/></svg>`;
            else if(c === '1' || c === '2' || c === '101' || c === '102') grid1Icon = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#F1C40F" stroke-width="2"><path d="M12 2v2M4.93 4.93l1.41 1.41M2 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41M22 12h-2M17.66 17.66l1.41 1.41"/><path d="M17.5 19H9a7 7 0 1 1 6.71-9h1.79a4.5 4.5 0 1 1 0 9Z" stroke="#94a3b8"/></svg>`;
            else grid1Icon = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><path d="M17.5 19H9a7 7 0 1 1 6.71-9h1.79a4.5 4.5 0 1 1 0 9Z"/></svg>`;
            
            grid1Label = 'Status Cuaca';
            grid1Value = getStandardizedDesc(weather.weather_code, weather.weather_desc);
        } else {
            badgeBg = getWeatherColor(weather.weather_code);
            badgeText = getStandardizedDesc(weather.weather_code, weather.weather_desc);

            grid1Icon = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z"></path><path d="M12 7v5"></path></svg>`;
            
            grid1Label = 'Suhu';
            grid1Value = `${weather.temp}°C`;
        }

        const contrastColor = getContrastColor(badgeBg);

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
                <span class="weather-badge" style="background:${badgeBg}; color:${contrastColor}; text-shadow:none; border:none; border:1px solid rgba(0,0,0,0.1)">${badgeText}</span>
                <span class="update-time">Update: ${formattedUpdate}</span>
            </div>
        </div>
        <div class="popup-grid">
            <div class="grid-item">
                <div class="grid-icon">${grid1Icon}</div>
                <div class="grid-data">
                    <span class="item-label">${grid1Label}</span>
                    <span class="item-value">${grid1Value}</span>
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
            let fillColor = currentView === 'cuaca' ? getWeatherColor(weather.weather_code) : getTemperatureColor(weather.temp);

            layer.setStyle({
                fillColor: fillColor,
                weight: 1,
                color: 'white',
                dashArray: '',
                opacity: 1,
                fillOpacity: 0.85,
            });
        } else {
            layer.setStyle({
                fillColor: '#E2E8F0',
                weight: 1,
                opacity: 1,
                color: 'white',
                dashArray: '3',
                fillOpacity: 0.4,
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
        const desc = weather ? getStandardizedDesc(weather.weather_code, weather.weather_desc) : 'Tanpa Data';
        const tempCat = weather ? getTemperatureCategory(weather.temp) : 'Tanpa Data';

        allPolygons.push({ layer: layer, desc: desc, tempCat: tempCat });
        styleLayer(layer, weather);

        layer.on({
            mouseover: function (e) {
                if (currentlyHovered && currentlyHovered !== activeLayer && currentlyHovered !== e.target) {
                    resetLayerStyle(currentlyHovered);
                }
                currentlyHovered = e.target;
                if (activeLayer !== e.target) {
                    e.target.setStyle({ weight: 3, color: '#666', dashArray: '', fillOpacity: 0.95 });
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
                activeLayer.setStyle({ weight: 3, color: '#2563eb', fillOpacity: 0.95 });
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
        let activeFilters = [];

        if (currentView === 'suhu') {
            const checkboxes = document.querySelectorAll('.filter-checkbox-suhu:checked');
            activeFilters = Array.from(checkboxes).map(cb => cb.value);
        } else {
            const checkboxes = document.querySelectorAll('.filter-checkbox-cuaca:checked');
            activeFilters = Array.from(checkboxes).map(cb => cb.value);
        }

        allPolygons.forEach(item => {
            let isVisible = true;

            if (item.desc !== 'Tanpa Data') {
                if (currentView === 'suhu') {
                    if (!activeFilters.includes(item.tempCat)) {
                        isVisible = false;
                    }
                } else {
                    let currentDesc = item.desc;
                    if (!activeFilters.includes(currentDesc)) {
                        isVisible = false;
                    }
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

    const filterCheckboxesSuhu = document.querySelectorAll('.filter-checkbox-suhu');
    const filterCheckboxesCuaca = document.querySelectorAll('.filter-checkbox-cuaca');

    filterCheckboxesSuhu.forEach(checkbox => {
        checkbox.addEventListener('change', applyFilter);
    });
    filterCheckboxesCuaca.forEach(checkbox => {
        checkbox.addEventListener('change', applyFilter);
    });

    setView('cuaca');

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
                        continue;
                    }
                    const geoData = await geoResponse.json();
                    L.geoJSON(geoData, { onEachFeature: onEachFeature }).addTo(map);
                } catch (err) {
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