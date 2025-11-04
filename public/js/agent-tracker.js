document.addEventListener("DOMContentLoaded", () => {
  const trackLink = document.getElementById('trackLink');
  if (!trackLink) return;

  let tracking = false;
  let watchId = null;
  let sendIntervalId = null;
  let lastPosition = null;

  // --- Send location data or status update to server ---
  function sendPosition(position = null, status = 'active') {
    const token = document.querySelector('meta[name="csrf-token"]').content;

    const payload = {
      tracking_status: status,
      location_time: new Date().toISOString(), // agent device time
    };

    if (position) {
      payload.latitude = position.coords.latitude;
      payload.longitude = position.coords.longitude;
      payload.accuracy = position.coords.accuracy;
    }

    fetch('/agent/location/', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': token
      },
      body: JSON.stringify(payload)
    })
    .then(response => response.json())
    .then(data => console.log(`📡 Status '${status}' sent:`, data))
    .catch(error => console.error('❌ Failed to send location/status:', error));
  }

  // --- Start tracking ---
  function startTracking() {
    if (!navigator.geolocation) {
      alert('Geolocation is not supported by your browser.');
      return;
    }

    console.log('🟢 Starting tracking...');
    sendPosition(null, 'active'); // ✅ Immediately mark as active

    watchId = navigator.geolocation.watchPosition((pos) => {
      lastPosition = pos;
      sendPosition(pos, 'active'); // realtime send
    }, (err) => {
      console.error('Geolocation error', err);
    }, {
      enableHighAccuracy: true,
      maximumAge: 0,
      timeout: 10000
    });

    // Send position every 15 seconds if available
    sendIntervalId = setInterval(() => {
      if (lastPosition) sendPosition(lastPosition, 'active');
    }, 15000);

    // Send one immediate position if available
    navigator.geolocation.getCurrentPosition((pos) => {
      lastPosition = pos;
      sendPosition(pos, 'active');
    });

    tracking = true;
    localStorage.setItem('trackingActive', 'true');
    updateButtonUI(true);
  }

  // --- Stop tracking ---
  function stopTracking() {
    console.log('🔴 Stopping tracking...');

    if (watchId !== null) {
      navigator.geolocation.clearWatch(watchId);
      watchId = null;
    }
    if (sendIntervalId !== null) {
      clearInterval(sendIntervalId);
      sendIntervalId = null;
    }

    sendPosition(null, 'inactive'); // ✅ Send inactive immediately

    tracking = false;
    localStorage.removeItem('trackingActive');
    updateButtonUI(false);
  }

  // --- Update UI Button ---
  function updateButtonUI(isTracking) {
    if (isTracking) {
      trackLink.textContent = 'Stop Tracking';
      trackLink.classList.remove('btn-primary');
      trackLink.classList.add('btn-danger');
    } else {
      trackLink.textContent = 'Start Tracking';
      trackLink.classList.remove('btn-danger');
      trackLink.classList.add('btn-primary');
    }
  }

  // --- Restore state on reload ---
  if (localStorage.getItem('trackingActive') === 'true') {
    startTracking();
  } else {
    updateButtonUI(false);
  }

  // --- Handle click toggle ---
  trackLink.addEventListener('click', (e) => {
    e.preventDefault();
    if (tracking) stopTracking();
    else startTracking();
  });
});
