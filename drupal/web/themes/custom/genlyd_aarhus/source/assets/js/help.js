jQuery(document).ready(function($) {
  var floatingHelp = $('.floating-help');
  var closeButton = $('.action-help');

  floatingHelp.click(function(){
    if(!floatingHelp.hasClass('is-open')) {
      floatingHelp.addClass('is-open');
    }
  });

  closeButton.click(function(e){
    floatingHelp.removeClass('is-open');
    return false;
  })
});