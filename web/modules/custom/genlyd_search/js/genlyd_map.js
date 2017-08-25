/**
 * @file
 * Javascript to load OSM and plot activities.
 */

// Global variables need to get it to work.
var genlydMapsActivityLayer = null;

/**
 * Initialize the OpenLayers Map.
 *
 * @return {ol.Map}
 *   The OpenLayers map object.
 */
function genlydMapInitOpenlayersMap() {
  'use strict';

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
      maxZoom: 17,
      projection: 'EPSG:3857'
    })
  });
}

/**
 * Load template used by popup's for markers.
 */
function genlydMapLoadPopupTemplate() {
  'use strict';

  Twig.twig({
    id: 'popup',
    href: drupalSettings.genlyd_search.map.template,
    async: false
  });
}

/**
 * Display information about the map.
 *
 * Used when debugging and changes in configuration.
 *
 * @param {ol.Map} map
 *   The OpenLayers map object.
 */
function genlydMasDebugInfo(map) {
  'use strict';

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
function genlydMapsAddPopups(map) {
  'use strict';

  // Get the popup element from the DOM and add it to the map as an overlay.
  var element = document.getElementById('popup');
  var popup = new ol.Overlay({
    element: element,
    positioning: 'top-center'
  });
  map.addOverlay(popup);

  // Change mouse cursor when over a marker.
  var target = document.getElementById(map.getTarget());
  map.on('pointermove', function(e) {
    var pixel = map.getEventPixel(e.originalEvent);
    if (map.hasFeatureAtPixel(pixel)) {
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
        close[0].addEventListener('click', function () {
          element.innerHTML = '';
        });
      }

      // This is used to move the popup/map into view if this partly outside the
      // current view area.
      var bs_element = document.getElementsByClassName('popup')[0];
      var clicked_pixel = evt.pixel;
      var map_size = map.getSize();

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
      var remaining_height = map_size[1] - clicked_pixel[1] - popup_height;
      var remaining_width = map_size[0] - clicked_pixel[0] - popup_width;

      // Get current view and map center.
      var view = map.getView();
      var center_px = map.getPixelFromCoordinate(view.getCenter());

      // Check if we are outside map view.
      if (remaining_height < 0 || remaining_width < 0) {
        if (remaining_height < 0) {
          center_px[1] -= remaining_height;
        }
        if (remaining_width < 0) {
          center_px[0] -= remaining_width;
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
 * @param {array} points
 *   The points to plot.
 */
function genlydMapsAddActivities(points) {
  'use strict';

  var map = genlydMapsObject;

  if (genlydMapsActivityLayer) {
    map.removeLayer(genlydMapsActivityLayer);
  }

  var format = new ol.format.GeoJSON({
    defaultDataProjection: 'EPSG:4326'
  });

  var dataSource = new ol.source.Vector({
    features: format.readFeatures(points, {
      dataProjection: 'EPSG:4326',
      featureProjection: 'EPSG:3857'
    })
  });

  // Find the marker to use or fallback to default.
  var markerUrl = drupalSettings.genlyd_search.map.marker;

  genlydMapsActivityLayer = new ol.layer.Vector({
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
  map.addLayer(genlydMapsActivityLayer);
  map.getView().fit(dataSource.getExtent(), map.getSize());
}

/**
 * OpenStreetMap layer to use in test.
 *
 * @param {ol.Map} map
 *   The OpenLayers map object.
 */
function genlydMapsAddOSMMap(map) {
  'use strict';

  map.addLayer(new ol.layer.Tile({
    source: new ol.source.OSM()
  }));
}

/**
 * Used to change location (map center) based on users current location.
 */
function genlydMapsMyLocation() {
  /**
   * Successful location acquired update map center.
   *
   * @param {object} position
   *   The position browser object.
   */
  function success(position) {
    var latitude = position.coords.latitude;
    var longitude = position.coords.longitude;

    genlydMapsObject.getView().setCenter(ol.proj.transform([longitude, latitude], 'EPSG:4326', 'EPSG:3857'));
  }

  /**
   * Unsuccessful in acquiring user location.
   *
   * @param {error} err
   *   Error object.
   */
  function error(err) {
    alert('Unable to get position.');
  }

  if ('geolocation' in navigator) {
    navigator.geolocation.getCurrentPosition(success, error, {
      enableHighAccuracy: true,
      timeout: 10000,
      maximumAge: 600000
    });
  }
  else {
    alert('Position not supported by browser');
  }
}

/**
 * Update the map to fit the viewed items.
 */
function updateMapDisplay() {
  if (genlydMapsActivityLayer) {
    genlydMapsObject.getView().fit(genlydMapsActivityLayer.getSource().getExtent(), genlydMapsObject.getSize());
  }
}

var genlydMapsObject = genlydMapInitOpenlayersMap();

// Line below used to debug the map, when configured it.
//genlydMasDebugInfo(genlydMapsObject);

// Add behaviour and map layers.
genlydMapLoadPopupTemplate();
genlydMapsAddOSMMap(genlydMapsObject);
genlydMapsAddPopups(genlydMapsObject);
