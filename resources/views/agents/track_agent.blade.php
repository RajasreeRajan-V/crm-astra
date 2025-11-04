@extends('layouts.app')

@section('title', 'Agent Tracking Dashboard')

@section('content')
<br>
<h2>Agent Tracking Dashboard</h2>

<table id="agentTable" class="table table-bordered table-striped mt-3">
  <thead class="table-dark">
    <tr>
      <th>ID</th>
      <th>Agent Name</th>
      <th>Phone</th>
      <th>Last Updated</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody></tbody>
</table>

<!-- Map Modal -->
<div id="mapModal" class="modal">
  <div class="modal-content">
    <h3 id="mapTitle"></h3>
    <p id="lastUpdated" style="font-weight:bold; color:blue;"></p>
    <div id="map" style="height:400px; width:100%; border-radius:10px;"></div>
    <br>
    <button onclick="closeModal()" style="background:#dc3545;color:white;">Close</button>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
let map, marker, liveInterval = null;

// Format date & time
function formatDate(dateString) {
  const date = new Date(dateString);
  const day = String(date.getDate()).padStart(2, '0');
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const year = date.getFullYear();
  let hours = date.getHours();
  const minutes = String(date.getMinutes()).padStart(2, '0');
  const ampm = hours >= 12 ? 'PM' : 'AM';
  hours = hours % 12 || 12;
  return `${day}/${month}/${year} ${hours}:${minutes} ${ampm}`;
}

// Fetch latest agent location data
async function fetchLatest() {
  try {
    const resp = await fetch('/admin/agents/locations/latest', {
      headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    });

    if (!resp.ok) throw new Error('Failed to fetch locations');
    const data = await resp.json();
    populateTable(data);
  } catch (err) {
    console.error('Fetch error:', err);
  }
}

// Fill table
function populateTable(data) {
  const tbody = document.querySelector('#agentTable tbody');
  tbody.innerHTML = '';

  data.forEach((row, index) => {
    const formattedTime = row.location_time ? formatDate(row.location_time) : '-';
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${index + 1}</td>
      <td>${row.agent_name ?? 'Unknown Agent'}</td>
      <td>${row.agent_phone ?? '-'}</td>
      <td>${formattedTime}</td>
      <td>
        <button 
          class="btn btn-primary btn-sm"
          onclick="openLiveMap('${row.agent_name}', ${row.latitude}, ${row.longitude}, ${row.agent_id})">
          Live Track
        </button>
      </td>
    `;
    tbody.appendChild(tr);
  });
}

// Open the map modal
function openLiveMap(name, lat, lng, agentId) {
  const modal = document.getElementById('mapModal');
  const title = document.getElementById('mapTitle');
  const lastUpdated = document.getElementById('lastUpdated');
  title.textContent = 'Live Tracking: ' + name;
  modal.style.display = 'block';

  // Initialize or reset map
  if (!map) {
    map = L.map('map', {
      zoomControl: true,
      attributionControl: true
    }).setView([lat, lng], 15);

    L.tileLayer('https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png', {
      maxZoom: 20,
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    marker = L.marker([lat, lng]).addTo(map);
  } else {
    map.setView([lat, lng], 15);
    marker.setLatLng([lat, lng]);
  }

  // Immediately fetch the latest info for this agent
  updateAgentLocation(agentId, name, true);

  // Start live updates every 5 seconds
  if (liveInterval) clearInterval(liveInterval);
  liveInterval = setInterval(() => {
    updateAgentLocation(agentId, name, false);
  }, 5000);
}

// Update marker position and live time
async function updateAgentLocation(agentId, name, isInitial = false) {
  try {
    const resp = await fetch(`/admin/agents/location/${agentId}`, {
      headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    });
    if (!resp.ok) return;
    const data = await resp.json();

    if (data && data.latitude && data.longitude) {
      const lastUpdatedTime = data.location_time ? formatDate(data.location_time) : 'Unknown';

      marker.setLatLng([data.latitude, data.longitude])
        .bindPopup(`
          <b>${name}</b><br>
          <b>Located Date & Time:</b> ${lastUpdatedTime}<br>
        `)
        .openPopup();

      map.setView([data.latitude, data.longitude], 15);

      // Status display logic
      const statusText = data.tracking_status ? data.tracking_status.toUpperCase() : 'UNKNOWN';
      const statusColor = data.tracking_status === 'active' ? 'green' : 'red';

      document.getElementById('lastUpdated').innerHTML = `
        <span style="color:blue;">Located Date & Time:</span> ${lastUpdatedTime} <br>
        <span style="color:${statusColor}; font-weight:bold;">Status: ${statusText}</span>
      `;
    }
  } catch (err) {
    console.error('Live update error:', err);
  }
}


// Close map modal
function closeModal() {
  document.getElementById('mapModal').style.display = 'none';
  if (liveInterval) clearInterval(liveInterval);
  liveInterval = null;
}

// Auto-refresh agent list every 10 seconds
fetchLatest();
setInterval(fetchLatest, 10000);
</script>
@endpush
