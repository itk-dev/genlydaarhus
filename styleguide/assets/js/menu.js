jQuery(document).ready(function($) {
  var header = $('.header');
  var navigation = $('.navigation');
  var hamburgerMenu = $('.hamburger-menu');
  var hamburgerToggle = $('.hamburger-menu--toggle');
  var heroHeight = $('.hero').height();

  $(window).on('scroll', function() {
    var scroll = $(this).scrollTop();
    
    if(scroll >= heroHeight-200) {      
      if(header.hasClass('is-transparent')) {
        header.removeClass('is-transparent');
        navigation.removeClass('is-transparent');
        hamburgerMenu.removeClass('is-transparent');
        hamburgerToggle.removeClass('is-transparent');
      }

    } else {
      if(!header.hasClass('is-transparent')) {
        header.addClass('is-transparent');
        navigation.addClass('is-transparent');
        hamburgerMenu.addClass('is-transparent');
        hamburgerToggle.addClass('is-transparent');
      }
    }
  });
});