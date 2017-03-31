/**
 * @file
 * Supplies functionality for filters button.
 */

(function ($, Drupal) {
  Drupal.behaviors.toggleFilterBehavior = {
    attach: function (context, settings) {
      // Find button and filters in DOM.
      var button = $('.js-filters-toggle');
      var filters = $('.js-filters');

      /**
       * Gets the first query parameter with parameterName.
       *
       * @param {string} parameterName The parameter to get.
       * @returns {string|null} The parameter or null.
       */
      var getQueryParameter = function getQueryParameter(parameterName) {
        var query = window.location.search.substring(1);
        var vars = query.split('&');
        for (var i = 0; i < vars.length; i++) {
          var pair = vars[i].split('=');
          if (decodeURIComponent(pair[0]) === parameterName) {
            return decodeURIComponent(pair[1]);
          }
        }
        return null;
      };

      // If a category filter is active in the url, open the filters.
      var showFilters =
        getQueryParameter('title') ||
        window.location.href.split(/field_categories_target_id.+=.+/ig).length > 1 ||
        getQueryParameter('field_zipcode_value');

      // Get button config.
      var buttonConfig;
      if (drupalSettings) {
        buttonConfig = drupalSettings.itk_activity.filterButton;
      }
      else {
        buttonConfig = {
          textShow: 'Show filters',
          textHide: 'Hide filters'
        };
      }

      /**
       * Show/hide filters and change text for show/hide filters button.
       */
      var setFilters = function setFilters() {
        if (showFilters) {
          button.text(buttonConfig.textHide);
          filters.show();
        }
        else {
          button.text(buttonConfig.textShow);
          filters.hide();
        }
      };
      setFilters();

      // Show and hide filters on click
      button.click(function click(event) {
        event.preventDefault();
        event.stopPropagation();

        // Toggle filter variable.
        showFilters = !showFilters;

        setFilters();
      });
    }
  };
})(jQuery, Drupal);
