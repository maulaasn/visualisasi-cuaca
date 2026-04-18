document.addEventListener('DOMContentLoaded', function () {
    const toggleHeader = document.getElementById('warningToggle');
    const warningDetails = document.getElementById('warningDetails');
    const toggleBtnText = document.getElementById('toggleBtnText');
    let mapInitialized = false;

    if (toggleHeader && warningDetails) {
        toggleHeader.addEventListener('click', function () {
            warningDetails.classList.toggle('show');
            
            if (warningDetails.classList.contains('show')) {
                toggleBtnText.innerHTML = 'Sembunyikan &#9650;';
                
                if (!mapInitialized && typeof warningData !== 'undefined' && warningData && warningData.polygons) {
                    initMiniMap(warningData.polygons);
                    mapInitialized = true;
                }
            } else {
                toggleBtnText.innerHTML = 'Selengkapnya &rarr;';
            }
        });
    }

    function initMiniMap(polygons) {
        const miniMap = L.map('mini-map', { zoomControl: true });
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(miniMap);

        const bounds = [];

        polygons.forEach(polyCoords => {
            const polygon = L.polygon(polyCoords, {
                color: '#ea580c',
                fillColor: '#f97316',
                fillOpacity: 0.4,
                weight: 2
            }).addTo(miniMap);
            
            polyCoords.forEach(coord => bounds.push(coord));
        });

        if (bounds.length > 0) {
            miniMap.fitBounds(bounds, { padding: [20, 20] });
        } else {
            miniMap.setView([-7.5360639, 112.2384017], 8);
        }
    }
});