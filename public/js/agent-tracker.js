
document.addEventListener("DOMContentLoaded", () => {
  const trackLink = document.getElementById('trackLink');
  if (!trackLink) return;

  let tracking = false;
  let watchId = null;
  let sendIntervalId = null;
  let lastPosition = null;

  //Send location data to server
  function sendPosition(position) {
    const lat = position.coords.latitude;
    const lng = position.coords.longitude;
    const accuracy = position.coords.accuracy;
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch('/agent/location', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': token,
      },
      body: JSON.stringify({
        latitude: lat,
        longitude: lng,
        accuracy: accuracy,
        location_time: new Date(position.timestamp).toISOString()
      })
    })
    .then(response => response.json())
    .then(data => console.log('Location sent:', data))
    .catch(error => console.error('Failed to send location:', error));
  }

  //Start tracking
  function startTracking() {
    if (!navigator.geolocation) {
      alert('Geolocation is not supported by your browser.');
      return;
    }

    // Watch position continuously
    watchId = navigator.geolocation.watchPosition((position) => {
      lastPosition = position;
    }, (err) => {
      console.error('Geolocation error', err);
    }, {
      enableHighAccuracy: true,
      maximumAge: 5000,
      timeout: 10000
    });

    // Send location every 15 seconds
    sendIntervalId = setInterval(() => {
      if (lastPosition) sendPosition(lastPosition);
    }, 15000);

    // Send once immediately
    navigator.geolocation.getCurrentPosition((pos) => {
      lastPosition = pos;
      sendPosition(pos);
    });

    tracking = true;
    localStorage.setItem('trackingActive', 'true'); //remember tracking
    updateButtonUI(true);
  }

  //Stop tracking
  function stopTracking() {
    if (watchId !== null) {
      navigator.geolocation.clearWatch(watchId);
      watchId = null;
    }
    if (sendIntervalId !== null) {
      clearInterval(sendIntervalId);
      sendIntervalId = null;
    }
    tracking = false;
    localStorage.removeItem('trackingActive'); //clear state
    updateButtonUI(false);
  }

  //Update button UI dynamically
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

  //Restore state on page load
  if (localStorage.getItem('trackingActive') === 'true') {
    startTracking();
  } else {
    updateButtonUI(false);
  }

  //Handle button click
  trackLink.addEventListener('click', (e) => {
    e.preventDefault();
    if (tracking) stopTracking();
    else startTracking();
  });
});

