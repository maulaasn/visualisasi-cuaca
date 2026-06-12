document.addEventListener('DOMContentLoaded', function () {
    if (document.getElementById('full-map')) {
        initMap();
    }

    function initMap() {
        let map = L.map('full-map').setView([-7.75, 112.75], 8); 

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        if (typeof warningData !== 'undefined' && warningData && warningData.areas) {
            let bounds = L.latLngBounds();
            let hasBounds = false;

            let rawEvent = warningData.event || 'Cuaca Ekstrem'; 
            let badgeIcon = '⚠️';
            let badgeText = rawEvent;

            if (rawEvent.toLowerCase().includes('petir') || rawEvent.toLowerCase().includes('kilat')) {
                badgeIcon = '⛈️';
                badgeText = 'Hujan Petir';
            } else if (rawEvent.toLowerCase().includes('hujan lebat')) {
                badgeIcon = '🌧️';
                badgeText = 'Hujan Lebat';
            } else if (rawEvent.toLowerCase().includes('angin')) {
                badgeIcon = '💨';
                badgeText = 'Angin Kencang';
            }

            warningData.areas.forEach(function(area) {
                var polygon = L.polygon(area.coordinates, {
                    color: '#0a192f',    
                    fillColor: '#172554', 
                    fillOpacity: 0.5,
                    weight: 2
                }).addTo(map);

                polygon.on('click', function(e) {
                    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${e.latlng.lat}&lon=${e.latlng.lng}&zoom=14&addressdetails=1`)
                        .then(response => response.json())
                        .then(data => {
                            if (data) {
                                let address = data.address || {};
                                
                                let kabupaten = address.state_district || address.city || address.county || '';
                                
                                let kecamatan = address.village || address.suburb || address.city_district || address.town || address.municipality || '';

                                if (!kecamatan && data.display_name) {
                                    kecamatan = data.display_name.split(',')[0];
                                }

                                kecamatan = kecamatan.replace(/Kecamatan |Desa |Kelurahan |Dusun |Kampung /gi, '').trim();
                                kabupaten = kabupaten.replace(/Kabupaten |Kota /gi, '').trim();

                                if (kecamatan === kabupaten) {
                                    kecamatan = 'Area ' + kabupaten;
                                }

                                let finalContent = `
                                    <div style="font-family: sans-serif; text-align: center; min-width: 150px;">
                                        <h4 style="margin: 0 0 2px 0; color: #1e293b; font-size: 13px; font-weight: 700;">${kecamatan || 'Wilayah Terdampak'}</h4>
                                        <p style="margin: 0 0 6px 0; font-size: 11px; color: #64748b;">${kabupaten}</p>
                                        <span style="font-size: 10px; padding: 2px 6px; border-radius: 4px; background: #ebf5fb; border: 1px solid #a9cce3; color: #1A5276; font-weight: 600;">${badgeIcon} ${badgeText}</span>
                                    </div>
                                `;
                                
                                L.popup().setLatLng(e.latlng).setContent(finalContent).openOn(map);
                            } else {
                                L.popup().setLatLng(e.latlng).setContent('<div style="text-align: center;"><span style="font-size:12px;color:#ef4444;">Detail wilayah tidak ditemukan</span></div>').openOn(map);
                            }
                        })
                        .catch(err => {
                            L.popup().setLatLng(e.latlng).setContent('<div style="text-align: center;"><span style="font-size:12px;color:#ef4444;">Gagal memuat wilayah (koneksi terputus)</span></div>').openOn(map);
                        });
                });

                polygon.on('mouseover', function () { this.setStyle({ fillOpacity: 0.8 }); });
                polygon.on('mouseout', function () { this.setStyle({ fillOpacity: 0.5 }); });

                bounds.extend(polygon.getBounds());
                hasBounds = true;
            });

            if (hasBounds) {
                map.fitBounds(bounds, { padding: [30, 30] });
            }
        }
    }
});