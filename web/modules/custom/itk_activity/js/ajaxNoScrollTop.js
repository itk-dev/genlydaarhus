(function ($, Drupal) {
  Drupal.behaviors.viewsScrollOff = {
    attach: function () {
      console.log("fisk");
      /* Views Ajax Content Load Autoscroll Feature Disabled */
      Drupal.AjaxCommands.prototype.viewsScrollTop = null;
    }
  };
})(jQuery, Drupal);
