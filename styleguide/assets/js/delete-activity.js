jQuery(document).ready(function($) {

  // Find elements i DOM and define overlay variable for reuse
  var buttonDelete = $('.js-card-activity-delete');
  var buttonBack = $('.js-card-edit-overlay-back');
  var overlay;

  // Show overlay when delete button clicked
  buttonDelete.click(function(){
    overlay = $(this).parents('.card').children('.card-edit-overlay');
    overlay.css("display", "flex");
  });

  // Hide overlay when back button clicked
  buttonBack.click(function(){
    overlay = $(this).parents('.card-edit-overlay');
    overlay.css("display", "none");
  })
});