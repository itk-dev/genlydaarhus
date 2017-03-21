/**
 * @file
 * Javascript to load OSM and plot activities.
 */

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
 * Load template used by popup's for markers.
 */
function loadPopupTemplate() {
  var template = Twig.twig({
    id: 'popup',
    href: drupalSettings.genlyd_maps.path + drupalSettings.genlyd_maps.template,
    async: false
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
 * Add feature event clicks with popup.
 *
 * @param {ol.Map} map
 *   The OpenLayers map object.
 */
function addPopups(map) {
  // Get the popup element from the DOM and add it to the map as an overlay.
  var element = document.getElementById('popup');
  var popup = new ol.Overlay({
    element: element,
    positioning: 'top-center'
  });
  map.addOverlay(popup);

  // Change mouse cursor when over a marker.
  var target =  document.getElementById(map.getTarget());
  map.on('pointermove', function(e) {
    var pixel = map.getEventPixel(e.originalEvent);
    var hit = map.hasFeatureAtPixel(pixel);
    if (hit) {
      target.style.cursor = 'pointer';
    }
    else {
      target.style.cursor = '';
    }
  });

  // Display popup on click.
  map.on('click', function(evt) {
    // Loop over the features at the current event point.
    map.forEachFeatureAtPixel(evt.pixel, function(feature, layer) {

      // Move popup to the right position.
      var coordinates = evt.coordinate;
      popup.setPosition(coordinates);

      // Get properties and render them with twig into popup content.
      var properties = feature.getProperties();
      element.innerHTML = Twig.twig({ ref: 'popup' }).render(properties);

      // Get hold of the close button and when clicked hide it.
      var close = document.getElementsByClassName('js-close-button');
      if (close.length) {
        close[0].addEventListener('click', function() {
          element.innerHTML = '';
        });
      }

      // This is used to move the popup/map into view if this partly outside the
      // current view area.
      var bs_element = document.getElementsByClassName('popup')[0];
      var clicked_pixel = evt.pixel;
      var mapSize = map.getSize();

      var offset_height = 10;
      var offset_width = 10;

      // Get popup height.
      var popup_height = Math.max(
          bs_element.scrollHeight,
          bs_element.offsetHeight,
          bs_element.clientHeight
        ) + offset_height;

      // Get popup width.
      var popup_width = Math.max(
          bs_element.scrollWidth,
          bs_element.offsetWidth,
          bs_element.clientWidth
        ) + offset_width;

      // Calculate if the popup is outside the view area.
      var height_left = mapSize[1] - clicked_pixel[1] - popup_height;
      var width_left = mapSize[0] - clicked_pixel[0] - popup_width;

      // Get current view and map center.
      var view = map.getView();
      var center_px = map.getPixelFromCoordinate(view.getCenter());

      // Check if we are outside map view.
      if (height_left < 0 || width_left < 0) {
        if (height_left < 0) {
          center_px[1] -= height_left;
        }
        if (width_left < 0) {
          center_px[0] -= width_left;
        }

        view.animate({
          center: map.getCoordinateFromPixel(center_px),
          duration: 300
        });
      }


    });
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
loadPopupTemplate();
addOSMMap(map);
addActivities(map);
addPopups(map);