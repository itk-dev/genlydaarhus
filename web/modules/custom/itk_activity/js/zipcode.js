/**
 * @file
 * Contains javascript to get field_area from field_zipcode using dawa.
 */

(function ($) {
  $(document).ready(function () {
    /**
     * Update area field with data from dawa.
     */
    var updateArea = function updateArea() {
      var zipcode = $('.js-field-zipcode').val();

      if (zipcode.length === 4) {
        $.getJSON("http://dawa.aws.dk/postnumre/" + zipcode,
          function (data) {
            $('.js-field-area').val(data.navn);
          }
        );
      }
    };

    // Register the change listener.
    $('.js-field-zipcode').change(updateArea);
  });
})(jQuery);
