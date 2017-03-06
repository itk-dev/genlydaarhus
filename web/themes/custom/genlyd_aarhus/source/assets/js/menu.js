jQuery(document).ready(function($) {
  var header = $('.js-header');
  var navigation = $('.js-navigation');
  var hamburgerMenu = $('.js-hamburger-menu');
  var hamburgerToggle = $('.js-hamburger-menu--toggle');
  var heroHeight = $('.js-hero').height();

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