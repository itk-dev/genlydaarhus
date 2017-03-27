/**
 * @file
 * Supplies functionality for filters button.
 */

jQuery(document).ready(function($) {
  // Find button and filters in DOM.
  var button = $('.js-filters-toggle');
  var filters = $('.js-filters');

  // If a category filter is active in the url, open the filters.
  var showFilters = window.location.href.split('field_categories_target_id').length > 1;

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
});