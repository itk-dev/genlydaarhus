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
        map: Drupal.t('Show map'),
        list: Drupal.t('Show list')
      };

      // Hide it.
      viewTab.hide();
      mapTab.hide();
      locationBtn.hide();

      // Initialization of hide/show filters.
      var filterDisplayState = true;

      function initialization() {
        // Detected which view (map or list).
        var viewmode = readHashValue('viewmode');
        viewmode = viewmode.length ? viewmode[0] : 'list';
        if (viewmode === 'list') {
          switchBtn.text(switchBtnTexts.map);
          viewTab.show();
        }
        else {
          switchBtn.text(switchBtnTexts.list);
          mapTab.show();
          // Setting display property to block, because IE 11 sets it to display inline.
          locationBtn.css('display', 'block');
        }

        // Set filters.
        var categories = readHashValue('field_categories_target_id');
        for (var i in categories) {
          $('[id^=edit-field-categories-target-id-' + categories[i] + ']')[0].checked = true;
        }

        var title = readHashValue('title');
        if (title.length) {
          $('[id^=edit-title]').val(title[0]);
        }

        var zipcode = readHashValue('field_zipcode_value');
        if (zipcode.length) {
          $('[id^=edit-field-zipcode-value]').val(zipcode[0]);
        }

        // Execute filters.
        if (viewsActivityFirstLoad) {
          // We don't known when drupal ajax is ready, so wait 200, throw a
          // "Hail Mary" and click.
          setTimeout(function(){ searchBtn.click(); }, 200);
        }

        // Ensures that the maps has all activities loaded.
        if (viewsActivityFirstLoad) {
          updateMap();
        }

        // Set first load to false.
        viewsActivityFirstLoad = false;
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
        var hash = window.location.hash;

        // Ensures that the & is not missing in front of URL if users have
        // removed it in links
        hash += '&' + key + "=" + value + '&';

        // Remove extra & if they exists in the hash.
        window.location.hash = hash.replace(/&{2,}/gi, '&');
      }

      /**
       * Replace value in the URL hash.
       *
       * @param {string} key
       *   Key for the value to add.
       * @param {string} value
       *   The value to add to the hash.
       */
      function replaceHashValue(key, value) {
        var hash = window.location.hash.substr(1);
        var regex = new RegExp(key + '=\\w+', 'gi');
        hash = hash.replace(regex, '');

        // Remove extra & if they exists in the hash.
        hash = hash.replace(/&{2,}/, '&');
        window.location.hash = hash;

        addHashValue(key, value);
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

        // Remove extra & if they exists in the hash.
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
      function toggleFilters() {
        if (filterDisplayState) {
          filterBtn.text(filterBtnTexts.hide);
          filters.show();
        }
        else {
          filterBtn.text(filterBtnTexts.show);
          filters.hide();
        }
        filterDisplayState = !filterDisplayState;
      }

      /**
       * Handle changes to filters and update hash based on this.
       */
      filters.off();
      filters.change(function (event) {
        var target = $(event.target);
        var regex = new RegExp('\\[\\d+\\]');
        var key = target.attr('name').replace(regex, '');

        // Handle checkboxes.
        if (target.is(':checked')) {
          addHashValue(key, target.val());
        }
        else {
          removeHashValue(key, target.val());
        }

        // Handle input fields.
        if (target.attr('type') === 'text') {
          replaceHashValue(key, target.val());
        }
      });

      /**
       * Show/hide the filters.
       */
      filterBtn.off();
      filterBtn.click(function click(event) {
        event.preventDefault();
        event.stopPropagation();
        toggleFilters();
      });

      /**
       * Switch view mode.
       */
      function switchView() {
        var currentView = readHashValue('viewmode');
        currentView = currentView.length ? currentView[0] : 'list';

        switch (currentView) {
          case 'list':
            viewTab.hide();
            mapTab.show();
            locationBtn.css('display', 'block');
            replaceHashValue('viewmode', 'map');
            switchBtn.text(switchBtnTexts.list);
            break;

          case 'map':
            viewTab.show();
            mapTab.hide();
            locationBtn.hide();
            switchBtn.text(switchBtnTexts.map);
            replaceHashValue('viewmode', 'list');
            break;
        }
      }

      /**
       * Show/hide map/view.
       *
       * The off here is needed as this gets attached more that once on views
       * ajax updates.
       */
      switchBtn.off();
      switchBtn.click(function click(event) {
        event.preventDefault();
        event.stopPropagation();
        switchView();
      });

      /**
       * Update map with filters.
       *
       * The functions are global available from genlyd_maps.js.
       */
      function updateMap() {
        // Ensure map popups are closed.
        var element = document.getElementById('popup');
        element.innerHTML = '';

        var filters = {
          'field_categories': readHashValue('field_categories_target_id'),
          'title': readHashValue('title'),
          'field_zipcode': readHashValue('field_zipcode_value')
        };
        genlydMapsAddActivities(genlydMapsObject, filters);
      }

      /**
       * Update the map on search.
       *
       * The off here is needed as this gets attached more that once on views
       * ajax updates.
       */
      searchBtn.off('click', updateMap);
      searchBtn.on('click', updateMap);

      /**
       * Used to change location (map center) based on users current location.
       *
       * @param event
       *   Click event.
       */
      function myLocation(event) {
        event.preventDefault();
        event.stopPropagation();

        /**
         * Successful location acquired update map center.
         *
         * @param position
         */
        function success(position) {
          var latitude  = position.coords.latitude;
          var longitude = position.coords.longitude;

          genlydMapsObject.getView().setCenter(ol.proj.transform([longitude, latitude], 'EPSG:4326', 'EPSG:3857'));
        }

        /**
         * Unsuccessful in acquiring user location.
         *
         * @param err
         */
        function error(err) {
           alert('Unable to get position.');
        }

        if ("geolocation" in navigator) {
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
       * Attached click event listener to "My location" button.
       */
      locationBtn.off('click', myLocation);
      locationBtn.on('click', myLocation);
    }
  }
})(jQuery, Drupal);
