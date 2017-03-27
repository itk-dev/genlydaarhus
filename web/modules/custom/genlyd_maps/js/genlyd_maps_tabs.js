/**
 * @file
 * Switch content base on tab selection.
 */

(function ($, Drupal) {
  Drupal.behaviors.genlydMapsTabsBehavior = {
    attach: function (context, settings) {
      var mapTab = $('.js-map-tab-map');
      var viewTab = $('.js-map-tab-view');
      var btn = $('.js-maps-switch');
      mapTab.hide();

      btn.click(function (event) {
        event.preventDefault();
        event.stopPropagation();

        console.log(mapTab.is(':visible'));

        if (!mapTab.is(':visible')) {
          btn.text( Drupal.t('Show table'));
          viewTab.hide();
          mapTab.show();
        }
        else {
          btn.text(Drupal.t('Show map'));
          viewTab.show();
          mapTab.hide();
        }
      });
    }
  }
})(jQuery, Drupal);