/**
 * @file
 * Switch content base on tab selection.
 */

// Global variable to different between ajax reload of the view.
var viewsActivityFirstLoad = true;

(function ($, Drupal) {
  Drupal.behaviors.genlydMapsTabsBehavior = {
    attach: function (context, settings) {
      var mapTab = $('.js-map-tab-map');
      var viewTab = $('.js-map-tab-view');
      var locationBtn = $('.js-maps-my-location');
      var switchBtn = $('.js-maps-switch');
      var filterBtn = $('.js-filters-toggle');
      var filters = $('.js-filters');
      var searchBtn = $('.button', filters);
      var filterBtnTexts = {
        show: Drupal.t('Show filters'),
        hide: Drupal.t('Hide filters')
      };

      var switchBtnTexts = {
        show: Drupal.t('Show map'),
        hide: Drupal.t('Show list')
      };

      // Initialization of hide/show filters.
      var showFilters = false;

      function initialization() {
        // Detected which view (map or list).
        var viewmode = readHashValue('viewmode');
        viewmode = viewmode.length ? viewmode[0] : 'list';

        // Set filters.
        var categories = readHashValue('field_categories_target_id');
        for (var i in categories) {
          $('[id^=edit-field-categories-target-id-' + categories[i] + ']')[0].checked = true;
          showFilters = true;
        }

        var title = readHashValue('title');
        if (title.length) {
          $('[id^=edit-title]').val(title[0]);
          showFilters = true;
        }

        var zipcode = readHashValue('field_zipcode_value');
        if (zipcode.length) {
          $('[id^=edit-field-zipcode-value]').val(zipcode[0]);
          showFilters = true;
        }

        // Execute filters.
        if (showFilters && viewsActivityFirstLoad) {
          viewsActivityFirstLoad = false;
          setTimeout(function(){ searchBtn.click(); }, 200);
       }

        // Negate show-filters as the filter function toggles it.
        showFilters = !showFilters;
      }
      initialization();

      /**
       * Add new value to hash.
       *
       * @param {string} key
       *   Key for the value to add.
       * @param {string} value
       *   The value to add to the hash.
       */
      function addHashValue(key, value) {
        window.location.hash += key + "=" + value + '&';
      }

      /**
       * Remove key with value from the hash.
       *
       * @param {string} key
       *   Key for the value to remove.
       * @param {string} value
       *   The value to remove to the hash.
       */
      function removeHashValue(key, value) {
        var hash = window.location.hash.substr(1);
        var regex = new RegExp(key + '=' + value, 'gi');
        hash = hash.replace(regex, '');
        hash = hash.replace(/&{2,}/, '&');
        window.location.hash = hash;
      }

      /**
       * Read values form the URL's hash.
       *
       * @param {string} key
       *   Key for the value(s) to read.
       *
       * @returns {Array}
       *   If found the value else an empty array.
       */
      function readHashValue(key) {
        var hash = window.location.hash.substr(1);
        var regex = new RegExp(key + '=(\\w+)', 'gi');
        var values = [];

        var match = regex.exec(hash);
        while (match !== null) {
          values.push(match[1]);
          match = regex.exec(hash);
        }

        return values;
      }

      /**
       * Show/hide filters and change text for show/hide filters button.
       */
      function setFilters() {
        showFilters = !showFilters;
        if (showFilters) {
          filterBtn.text(filterBtnTexts.hide);
          filters.show();
        }
        else {
          filterBtn.text(filterBtnTexts.show);
          filters.hide();
        }
      }
      setFilters();

      /**
       * Handle changes to filters and update hash based on this.
       */
      filters.change(function (event) {
        var target = $(event.target);
        var regex = new RegExp('\\[\\d+\\]');
        var key = target.attr('name').replace(regex, '');
        if (target.is(':checked') || (target.attr('type') === 'text' && target.val() !== '')) {
          addHashValue(key, target.val());
        }
        else {
          removeHashValue(key, target.val());
        }
      });

      /**
       * Show/hide the filters.
       */
      filterBtn.click(function click(event) {
        event.preventDefault();
        event.stopPropagation();
        setFilters();
      });
    }
  }
})(jQuery, Drupal);