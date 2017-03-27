/**
 * @file
 * Switch content base on tab selection.
 */

(function ($, Drupal) {
  Drupal.behaviors.genlydMapsTabsBehavior = {
    attach: function (context, settings) {
      var mapTab = $('.js-map-tab-map');
      var viewTab = $('.js-map-tab-view');
      var locationBtn = $('.js-maps-my-location');
      var btn = $('.js-maps-switch');

      mapTab.hide();
      locationBtn.hide();

      btn.click(function (event) {
        event.preventDefault();
        event.stopPropagation();

        if (!mapTab.is(':visible')) {
          btn.text( Drupal.t('Show table'));
          viewTab.hide();
          mapTab.show();
          locationBtn.show();
        }
        else {
          btn.text(Drupal.t('Show map'));
          viewTab.show();
          mapTab.hide();
          locationBtn.hide();
        }
      });
    }
  }
})(jQuery, Drupal);