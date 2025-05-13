<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>OpenSenseMap Global Dashboard</title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    #mainMap { height: 500px; margin-bottom: 30px; border: 1px solid #ccc; }
    #details { margin-top: 30px; }
    .coords { font-weight: bold; margin-bottom: 10px; }
    .map-small { height: 300px; width: 100%; border: 1px solid #ccc; margin-bottom: 20px; }
    .sensor-grid {
      display: flex; flex-wrap: wrap; gap: 20px;
    }
    .sensor-card {
      background: #f9f9f9; border: 1px solid #ccc;
      border-radius: 8px; padding: 15px; width: 300px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    .sensor-card h4 { margin: 0 0 10px; }
    .countdown { font-weight: bold; font-size: 14px; color: #007bff; margin-bottom: 10px; }
  </style>
</head>
<body>

<h1>OpenSenseMap Explorer</h1>
<p>Click a marker to view sensor data</p>
<div id="mainMap"></div>

<div id="details">
  <!-- Dynamic content goes here -->
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const boxes = [
  { id: '61437e8999eb9a001b941e9e', title: 'Germany Box' },
  { id: '6126f02a04fd9f001b419450', title: 'Another Box' },
  { id: '65652ae82d58f70008baaa76', title: 'Yet Another Box' }
];

const map = L.map('mainMap').setView([20, 0], 2); // World view
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  attribution: '&copy; OpenStreetMap contributors'
}).addTo(map);

// Load markers
boxes.forEach(box => {
  fetch(`box.php?id=${box.id}`)
    .then(res => res.json())
    .then(data => {
      if (!data || data.error) return;
      const { lat, lng, boxName } = data;
      const marker = L.marker([lat, lng]).addTo(map).bindPopup(boxName);
      marker.on('click', () => showBoxData(data));
    });
});

function showBoxData(data) {
  const { boxName, lat, lng, sensors } = data;
  const container = document.getElementById('details');
  container.innerHTML = `
    <h2>${boxName}</h2>
    <div class="coords">Coordinates: Latitude ${lat.toFixed(5)}, Longitude ${lng.toFixed(5)}</div>
    <div id="smallMap" class="map-small"></div>
    <div class="sensor-grid" id="sensorCards"></div>
  `;

  const map2 = L.map('smallMap').setView([lat, lng], 15);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
  }).addTo(map2);
  L.marker([lat, lng]).addTo(map2).bindPopup(boxName).openPopup();

  const sensorGrid = document.getElementById('sensorCards');
  sensors.forEach(sensor => {
    const card = document.createElement('div');
    card.className = 'sensor-card';
    card.innerHTML = `
      <div class="countdown">Live</div>
      <h4>${sensor.title}</h4>
      <p><strong>Value:</strong> ${sensor.value} ${sensor.unit}</p>
      <canvas class="chart" data-value="${sensor.value}" data-label="${sensor.title}"></canvas>
    `;
    sensorGrid.appendChild(card);
  });

  document.querySelectorAll('.chart').forEach(canvas => {
    const value = parseFloat(canvas.dataset.value) || 0;
    const label = canvas.dataset.label;
    new Chart(canvas.getContext('2d'), {
      type: 'doughnut',
      data: {
        labels: [label, 'Remaining'],
        datasets: [{
          data: [value, 100 - value],
          backgroundColor: ['#007bff', '#e0e0e0']
        }]
      },
      options: {
        plugins: { legend: { display: false } },
        cutout: '70%'
      }
    });
  });
}
</script>

</body>
</html>
