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
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
// Global variables for map state
let map = null;
let marker = null;
let activeAgentId = null; // Tracks the ID of the agent currently displayed on the map

// Store the latest data for all agents
let latestAgentData = [];

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

// Fetch latest agent data
async function fetchLatest() {
  const resp = await fetch('/admin/agents/locations/latest', {
    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
  });
  if (!resp.ok) return;
  const data = await resp.json();
  // Update the global data array
  latestAgentData = data; 
  populateTable(data);
  // **LIVE TRACKING UPDATE:** Check if the active agent needs a map update
  if (activeAgentId !== null) {
    updateLiveMap();
  }
}

// Populate table without showing lat/lng
function populateTable(data) {
  const tbody = document.querySelector('#agentTable tbody');
  tbody.innerHTML = '';
  data.forEach((row) => {
    const formattedTime = formatDate(row.location_time);
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${row.agent_id}</td>
      <td>${row.agent_name ?? 'Unknown Agent'}</td>
      <td>${row.agent_phone ?? '-'}</td>
      <td>${formattedTime}</td>
      <td>
        <button 
          class="btn btn-primary btn-sm"
          onclick="viewMap(${row.agent_id}, '${row.agent_name}', ${row.latitude}, ${row.longitude})">
          View Map
        </button>
      </td>
    `;
    tbody.appendChild(tr);
  });
}

// Open map modal and initialize/update the map
function viewMap(id, name, lat, lng) {
  const modal = document.getElementById('mapModal');
  const title = document.getElementById('mapTitle');
  title.textContent = 'Tracking: ' + name;
  modal.style.display = 'block';
  activeAgentId = id; // Set the active agent ID

  // Using a timeout to ensure the map container is visible and has dimensions
  setTimeout(() => {
    // 1. Initialize Map (only on first call)
    if (!map) {
      map = L.map('map').setView([lat, lng], 14);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { 
        maxZoom: 19,
        attribution: '© OpenStreetMap contributors'
      }).addTo(map);
      marker = L.marker([lat, lng]).addTo(map).bindPopup(name).openPopup();
    } 
    // 2. Update Map for the specific agent
    else {
      map.invalidateSize(); // Fixes map rendering issues when hidden then shown
      map.setView([lat, lng], map.getZoom() > 14 ? map.getZoom() : 14);
      marker.setLatLng([lat, lng]).bindPopup(name).openPopup();
    }
  }, 100); // Shorter timeout for better responsiveness
}

// Function to update the map with the latest position of the active agent
function updateLiveMap() {
  if (document.getElementById('mapModal').style.display !== 'block' || activeAgentId === null) {
    return; // Only update if modal is open and an agent is active
  }

  const agent = latestAgentData.find(a => a.agent_id === activeAgentId);

  if (agent && map && marker) {
    const newLat = agent.latitude;
    const newLng = agent.longitude;
    const currentLatLng = marker.getLatLng();

    // Check if the position has actually changed to avoid unnecessary updates
    if (currentLatLng.lat !== newLat || currentLatLng.lng !== newLng) {
      marker.setLatLng([newLat, newLng]).setPopupContent(agent.agent_name + '<br>Updated: ' + formatDate(agent.location_time)).openPopup();
      // Optionally move the map view to the new location
      map.panTo([newLat, newLng]);
      console.log(`Map updated for Agent ${activeAgentId}`);
    }
  }
}

// Close modal
function closeModal() {
  document.getElementById('mapModal').style.display = 'none';
  activeAgentId = null; // Clear the active agent when the modal closes
}

// Auto-refresh every 10 seconds
fetchLatest();
setInterval(fetchLatest, 10000); // 10-second interval for fetching data and updating the map
</script>
<style>
  #mapModal {
    /* Basic styling for the modal, assuming you're using a simple CSS modal */
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.4);
  }
  .modal-content {
    background-color: #fefefe;
    margin: 15% auto; /* 15% from the top and centered */
    padding: 20px;
    border: 1px solid #888;
    width: 80%; /* Could be more or less, depending on screen size */
  }
  #map {
    height: 400px; /* Essential: must set a height for the map container */
    width: 100%;
  }
</style>
@endpush