/**
 * @file
 * Contains javascript to enable facebook share button.
 */

(function ($) {
  $(document).ready(function () {
    // Initialize facebook.
    window.fbAsyncInit = function () {
      FB.init({
        appId: drupalSettings.itk_activity.fbShare.appId,
        xfbml: true,
        version: drupalSettings.itk_activity.fbShare.apiVersion
      });
      FB.AppEvents.logPageView();
    };

    // Load facebook sdk.
    var js, fjs = document.getElementsByTagName('script')[0];
    if (document.getElementById('facebook-jssdk')) {
      return;
    }
    js = document.createElement('script');
    js.id = 'facebook-jssdk';
    js.src = "//connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);

    // Setup click listener for facebook button.
    $('.js-facebook-share-button').click(function () {
      FB.ui({
        method: 'share',
        href: drupalSettings.itk_activity.fbShare.url
      });
    });
  });
})(jQuery);
