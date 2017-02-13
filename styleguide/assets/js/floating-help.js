/**
 *
 * Toggle floating help
 *
 */


jQuery(document).ready(function($) {
  // Set variables.
  var floatingHelp = $('.floating-help');
  var closeButton = $('.action-help');

  // Open floating help.
  floatingHelp.click(function() {
    if (!floatingHelp.hasClass('is-open')) {
      floatingHelp.addClass('is-open');
    }
  });

  // Close floating help.
  closeButton.click(function() {
    floatingHelp.removeClass('is-open');
    event.stopPropagation();
  })
});
