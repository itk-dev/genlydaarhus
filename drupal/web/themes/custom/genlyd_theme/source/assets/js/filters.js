jQuery(document).ready(function($) {
  var button = $('.action-filter');
  var filters = $('.filters');

  button.click(function(){
    filters.toggle();
  });
});