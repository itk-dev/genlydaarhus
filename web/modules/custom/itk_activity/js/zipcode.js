/**
 * @file
 * Contains javascript to get field_area from field_zipcode using dawa.
 */

(function ($) {
  $(document).ready(function () {
    var zipcodeField = $('.js-field-zipcode');
    var areaField = $('.js-field-area');

    /**
     * Update area field with data from dawa.
     */
    var updateArea = function updateArea() {
      var zipcode = zipcodeField.val();

      if (zipcode.length === 4) {
        $.getJSON("http://dawa.aws.dk/postnumre/" + zipcode,
          function (data) {
            areaField.val(data.navn);
          }
        );
      }
    };

    // Register the change listener.
    zipcodeField.change(updateArea);
  });
})(jQuery);
