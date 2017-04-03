/**
 * @file
 * Enables the display of the EU cookie dialog.
 */

(function ($, Drupal) {
  'use strict';

  Drupal.behaviors.cookieMessageBehavior = {
    attach: function (context, settings) {
      var cookieName = drupalSettings.itk_cookie_message.cookie_name;

      /**
       * Inline function to get the current value of the cookie.
       *
       * @return boolean
       *   Is the cookie set, returns true, else false.
       */
      var cookieValue = function cookieValue() {
        var regex = new RegExp('(?:^|; )' + encodeURIComponent(cookieName) + '=([^;]*)');
        var result = regex.exec(document.cookie);

        // If cookie is set, return true.
        return result !== null;
      }();

      // Get the element.
      var el = $('.js-cookieterms');

      // If the cookie has not been set, display the dialog.
      if (!cookieValue) {
        // Display cookie dialog. The cookie dialog is hidden by css, as default.
        el.show();

        // Handle "Acceptance" of cookie usage.
        $('.js-cookieterms-agree').on('click', function () {
          var expiryDate = new Date(new Date().getTime() + drupalSettings.itk_cookie_message.cookie_lifetime * 1000);
          document.cookie = cookieName + '=true; path=/; expires=' + expiryDate.toGMTString();

          // Hide the dialog, by sliding it up.
          el.slideUp('fast', function slideUp() {
            // Remove the element from the DOM.
            el.remove();
          });
        });
      }
      else {
        // Remove the element from the DOM, since it should not be shown.
        el.remove();
      }
    }
  };
})(jQuery, Drupal);
