jQuery(document).ready(function($) {
  var buttonDelete = $('.action-delete');
  var buttonBack = $('.action-back');
  var overlay;

  buttonDelete.click(function(){
    overlay = $(this).parents('.card-activity').children('.card-activity--overlay');
    overlay.css("display", "flex");
  });

  buttonBack.click(function(){
    overlay = $(this).parents('.card-activity').children('.card-activity--overlay');
    overlay.css("display", "none");
  })
});