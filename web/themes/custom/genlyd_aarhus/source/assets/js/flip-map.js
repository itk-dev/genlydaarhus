jQuery(document).ready(function($) {
  var mapTab = $('.js-map-tab-map');
  var viewTab = $('.js-map-tab-view');
  var locationBtn = $('.js-maps-my-location');
  var filterBtn = $('.js-filters-toggle');
  var filters = $('.js-all-filters');
  var btn = $('.js-maps-switch');

  mapTab.hide();
  locationBtn.hide();

  btn.click(function (event) {
    event.preventDefault();
    event.stopPropagation();

    if (!mapTab.is(':visible')) {
      btn.text('Show table');
      filterBtn.hide();
      filters.hide();
      viewTab.hide();
      mapTab.show();
      locationBtn.show();
    }
    else {
      btn.text('Show map');
      filterBtn.show();
      filters.show();
      viewTab.show();
      mapTab.hide();
      locationBtn.hide();
    }
  });
});