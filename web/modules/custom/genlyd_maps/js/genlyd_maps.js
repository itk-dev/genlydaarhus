
var template = Twig.twig({
  id: "list", // id is optional, but useful for referencing the template later
  data: "{% for value in list %}{{ value }}, {% endfor %}"
});

var output = template.render({
  list: ["one", "two", "three"]
});

console.log(output);

/**
 * Initialize the OpenLayers Map.
 *
 * @returns {ol.Map}
 *   The OpenLayers map object.
 */
function initOpenlayersMap() {
  // Init the map.
  return new ol.Map({
    target: 'mapid',
    logo: false,
    interactions: ol.interaction.defaults({ mouseWheelZoom: false }),
    controls: ol.control.defaults(),
    view: new ol.View({
      center: [1136822.2651791184, 7589103.079019488],
      zoom: 12,
      minZoom: 4,
      maxZoom: 14,
      projection: 'EPSG:3857'
    })
  });
}

/**
 * Display information about the map.
 *
 * Used when debuging and changes in configuration.
 *
 * @param {ol.Map} map
 *   The OpenLayers map object.
 */
function mapDebugInfo(map) {
  map.on('moveend', function(evt) {
    var ext = map.getView().calculateExtent(map.getSize());
    console.log('Extent: ' + ext[0] + ',' + ext[1] + ',' + ext[2] + ',' + ext[3]);
    console.log('Zoom: ' + map.getView().getZoom());
    console.log('Center: ' + map.getView().getCenter());
  });
}


/**
 * Add activities.
 *
 * @param {ol.Map} map
 *   The OpenLayers map object.
 */
function addActivities(map) {
  jQuery.ajax({
    url: '/api/maps/activities.json'
  }).done(function(data) {
    var format = new ol.format.GeoJSON({
      defaultDataProjection: 'EPSG:4326'
    });

    var dataSource = new ol.source.Vector({
      features: format.readFeatures(data, {
        dataProjection: 'EPSG:4326',
        featureProjection: 'EPSG:3857'
      })
    });

    // Find the marker to use or fallback to default.
    var markerUrl = drupalSettings.genlyd_maps.path + drupalSettings.genlyd_maps.marker;
    console.log(markerUrl);

    var dataLayer = new ol.layer.Vector({
      source: dataSource,
      visible: true,
      style: new ol.style.Style({
        image: new ol.style.Icon({
          anchor: [0.5, 40],
          anchorXUnits: 'fraction',
          anchorYUnits: 'pixels',
          src: markerUrl,
          scale: 1.0
        })
      })
    });

    // Add the layer to the map.
    map.addLayer(dataLayer);
    map.getView().fit(dataSource.getExtent(), map.getSize());
  });
}

/**
 * OpenStreetMap layer to use in test.
 *
 * @param {ol.Map} map
 *   The OpenLayers map object.
 */
function addOSMMap(map) {
  map.addLayer(new ol.layer.Tile({
    source: new ol.source.OSM()
  }));
}

var map = initOpenlayersMap();
//mapDebugInfo(map);
addOSMMap(map);
addActivities(map);