/**
 *
 * Toggle floating help
 *
 */


jQuery(document).ready(function($) {
  // Set variables.
  var floatingHelp = $('.js-floating-help-open');
  var closeButton = $('.js-floating-help-close');

  // Open floating help.
  floatingHelp.click(function() {
    if (!floatingHelp.hasClass('is-open')) {
      floatingHelp.addClass('is-open');
    }
  });

  // Close floating help.
  closeButton.click(function() {
    floatingHelp.removeClass('is-open');
    return false;
  })
});
