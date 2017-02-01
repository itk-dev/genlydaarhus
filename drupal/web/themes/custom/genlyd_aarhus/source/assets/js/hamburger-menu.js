/**
 *
 * Toggle hamburgermenu
 *
 */

jQuery(document).ready(function ($) {
  "use strict";

  // Set body variable
  var hamburger_body = $('body');

  // Create overlay element.
  hamburger_body.prepend('<div class="hamburger-menu--overlay js-hamburger-menu-toggle"></div>');

  // Set remaining variables.
  var hamburger_html = $('html');
  var hamburger_button = $('.hamburger-menu--toggle');
  var hamburger_menu = $('.hamburger-menu');
  var hamburger_overlay = $('.hamburger-menu--overlay');

  $('.js-hamburger-menu-toggle').click(function() {
    if (hamburger_button.hasClass("is-open")) {
      // Button animation 'back to hamburger'.
      hamburger_button.removeClass("is-open");

      // Closes hamburger menu.
      hamburger_menu.removeClass("is-open");

      // Hides overlay.
      hamburger_overlay.removeClass('is-visible');

      // Unlocks html and body element.
      hamburger_html.removeClass('is-locked-by-hamburger-menu');
      hamburger_body.removeClass('is-locked-by-hamburger-menu');
    }
    else
    {
      // Hamburger button animation to 'x'.
      hamburger_button.addClass("is-open");

      // Open hamburger menu.
      hamburger_menu.addClass("is-open");

      // Shows overlay.
      hamburger_overlay.addClass('is-visible');

      // Lock html and body elements.
      hamburger_html.addClass('is-locked-by-hamburger-menu');
      hamburger_body.addClass('is-locked-by-hamburger-menu');
    }
  });
});