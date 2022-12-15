(function ($) {
  mapboxgl.accessToken = 'pk.eyJ1IjoiYWxleGFuZGVyLWthbGFzaG5payIsImEiOiJja2RwdXRsdWMweWpjMzNtcW50OXptZTU5In0.X9XJuAtGN3VrWRxTKZjTEg';
  var coordinates = [-74.5, 40];
  var map = new mapboxgl.Map({
    container: 'map',
    style: 'mapbox://styles/alexander-kalashnik/ckdpv80gh0axf1iq9g4bjf3ys', // stylesheet location
    center: coordinates, // starting position [lng, lat]
    zoom: 9 // starting zoom
  });
  var marker = document.createElement('div');

  marker.className = 'marker';

  new mapboxgl.Marker(marker)
    .setLngLat(coordinates)
    .addTo(map);
})(jQuery)
