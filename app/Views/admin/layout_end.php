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
  try {
    if (sessionStorage.getItem(KEY)) return;
  } catch (e) {
    return;
  }
  if (!navigator.geolocation) return;
  navigator.geolocation.getCurrentPosition(function (position) {
    var lat = position.coords.latitude;
    var lon = position.coords.longitude;
    var url = 'https://api.bigdatacloud.net/data/reverse-geocode-client?latitude='
      + encodeURIComponent(lat) + '&longitude=' + encodeURIComponent(lon)
      + '&localityLanguage=ar';
    fetch(url)
      .then(function (response) { return response.json(); })
      .then(function (data) {
        return fetch('/api/visitor-geo', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          credentials: 'same-origin',
          body: JSON.stringify({
            country: data.countryName || '',
            country_code: data.countryCode || '',
            region: data.principalSubdivision || '',
            city: data.city || data.locality || ''
          })
        });
      })
      .catch(function () {})
      .then(markDone);
  }, function () { markDone(); }, { timeout: 6000, maximumAge: 600000 });
})();
</script>
</body>
</html>
