jQuery(document).ready(function($) {
  var buttonDelete = $('.js-card-activity-delete');
  var buttonBack = $('.js-card-edit-overlay-back');
  var overlay;

  buttonDelete.click(function(){
    overlay = $(this).parents('.card-activity').children('.card-edit-overlay');
    overlay.css("display", "flex");
  });

  buttonBack.click(function(){
    overlay = $(this).parents('.card-edit-overlay');
    overlay.css("display", "none");
  })
});