var map = L.map("map").setView([0, 0], 2);
      L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {
        maxZoom: 18,
        attribution:
          '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
      }).addTo(map);
      L.marker([coordinates[1], coordinates[0]]).addTo(map);

      