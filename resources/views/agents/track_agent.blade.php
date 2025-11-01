@extends('layouts.app')

@section('title', 'Agent Tracking Dashboard')

@section('content')
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
    <div id="map"></div>
    <br>
    <button onclick="closeModal()" style="background:#dc3545;color:white;">Close</button>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
// Format date to DD/MM/YYYY hh:mm AM/PM
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

let map, marker;

// Fetch latest agent data
async function fetchLatest() {
  const resp = await fetch('/admin/agents/locations/latest', {
    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
  });
  if (!resp.ok) return;
  const data = await resp.json();
  populateTable(data);
}

// Populate table without showing lat/lng
function populateTable(data) {
  const tbody = document.querySelector('#agentTable tbody');
  tbody.innerHTML = '';
  data.forEach((row, index) => {
    const formattedTime = formatDate(row.location_time);
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${index + 1}</td>
      <td>${row.agent_name ?? 'Unknown Agent'}</td>
      <td>${row.agent_phone ?? '-'}</td>
      <td>${formattedTime}</td>
      <td>
        <button 
          class="btn btn-primary btn-sm"
          onclick="viewMap('${row.agent_name}', ${row.latitude}, ${row.longitude})">
          View Map
        </button>
      </td>
    `;
    tbody.appendChild(tr);
  });
}

// Open map modal
function viewMap(name, lat, lng) {
  const modal = document.getElementById('mapModal');
  const title = document.getElementById('mapTitle');
  title.textContent = 'Tracking: ' + name;
  modal.style.display = 'block';
  setTimeout(() => {
    if (!map) {
      map = L.map('map').setView([lat, lng], 14);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);
      marker = L.marker([lat, lng]).addTo(map).bindPopup(name).openPopup();
    } else {
      map.setView([lat, lng], 14);
      marker.setLatLng([lat, lng]).bindPopup(name).openPopup();
    }
  }, 300);
}

// Close modal
function closeModal() {
  document.getElementById('mapModal').style.display = 'none';
}

// Auto-refresh every 10 seconds
fetchLatest();
setInterval(fetchLatest, 10000);
</script>
@endpush
