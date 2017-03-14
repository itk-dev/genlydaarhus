/**
 * @file
 * Contains javascript to get field_area from field_zipcode using dawa.
 */

(function ($) {
  $(document).ready(function () {
    window.fbAsyncInit = function() {
      FB.init({
        appId      : drupalSettings.fbShare.appId,
        xfbml      : true,
        version    : drupalSettings.fbShare.apiVersion
      });
      FB.AppEvents.logPageView();
    };

    (function(d, s, id){
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) {return;}
      js = d.createElement(s); js.id = id;
      js.src = "//connect.facebook.net/en_US/sdk.js";
      fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));

    $('.js-facebook-share-button').click(function () {
      FB.ui({
        method: 'share',
        href: drupalSettings.fbShare.url
      }, function(response){});
    });
  });
})(jQuery);
