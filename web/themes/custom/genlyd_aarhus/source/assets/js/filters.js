/**
 * @file
 * Supplies functionality for filters button.
 */

jQuery(document).ready(function ($) {
  // Find button and filters in DOM
  var button = $('.js-filters-toggle');
  var filters = $('.js-filters');

  // Show and hide filters on click
  button.click(function click(event) {
    event.preventDefault();
    event.stopPropagation();
    filters.toggle();
  });
});