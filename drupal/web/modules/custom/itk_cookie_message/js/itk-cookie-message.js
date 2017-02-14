/**
 * @file
 * Enables the display of the EU cookie dialog.
 */
(function($) {
  "use strict";
  
  var settings = drupalSettings.itk_cookie_message;
  var cookieName = settings.cookie_name;

  // Inline function to get the current value of the cookie.
  var cookieValue = function() {
    var regex = new RegExp('(?:^|; )' + encodeURIComponent(cookieName) + '=([^;]*)');
    var result = regex.exec(document.cookie);
    return result ? (result[1]) : null;
  }();

  var el = $('#js-cookieterms');
	if (!cookieValue) {
    // Display cookie dialog.
    el.show();

    // Handle "Acceptance" of cookie usage.
    $('#js-cookieterms--agree').on('click', function() {
      var expiryDate = new Date(new Date().getTime() + settings.cookie_lifetime * 1000);
			document.cookie = cookieName+'=true; path=/; expires='+expiryDate.toGMTString();

      // Hide the dialog.
      el.slideUp('fast', function(){
        el.empty().remove();
			});
		});
	}
  else {
    el.remove();
  }
}(jQuery));
