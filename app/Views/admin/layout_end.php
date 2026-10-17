    </div>
  </div>

<script>
(function () {
  var KEY = 'geo_refined';
  var done = false;
  function markDone() {
    if (done) return;
    done = true;
    try { sessionStorage.setItem(KEY, '1'); } catch (e) {}
  }
  function send(lat, lon) {
    var url = 'https://api.bigdatacloud.net/data/reverse-geocode-client?latitude='
      + encodeURIComponent(lat) + '&longitude=' + encodeURIComponent(lon)
      + '&localityLanguage=ar';
    var meta = document.querySelector('meta[name="csrf-token"]');
    var token = meta ? (meta.getAttribute('content') || '') : '';
    return fetch(url)
      .then(function (response) { return response.json(); })
      .then(function (data) {
        return fetch('/api/visitor-geo', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token
          },
          credentials: 'same-origin',
          body: JSON.stringify({
            country: data.countryName || '',
            country_code: data.countryCode || '',
            region: data.principalSubdivision || '',
            city: data.city || data.locality || '',
            lat: lat,
            lon: lon
          })
        });
      });
  }
  function start() {
    if (!navigator.geolocation) { markDone(); return; }
    navigator.geolocation.getCurrentPosition(function (position) {
      send(position.coords.latitude, position.coords.longitude)
        .catch(function () {})
        .then(markDone);
    }, function () { markDone(); }, { timeout: 6000, maximumAge: 600000 });
  }
  try {
    if (sessionStorage.getItem(KEY)) return;
  } catch (e) {
    return;
  }
  if (!navigator.permissions || !navigator.permissions.query) return;
  navigator.permissions.query({ name: 'geolocation' }).then(function (result) {
    if (result && result.state === 'granted') {
      start();
    }
  }).catch(function () {});
})();
</script>
</body>
</html>
