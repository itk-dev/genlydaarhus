/**
 * @file
 * Contains javascript to setup time pickers for field_time_start and field_time_end.
 */

(function ($) {
  $(document).ready(function () {
    var config = {
      timeFormat: 'HH:mm',
      interval: 30,
      defaultTime: '8',
      startTime: '8:00',
      dynamic: false,
      dropdown: true,
      scrollbar: true
    };

    $('input.js-field-time-start').timepicker(config);
    $('input.js-field-time-end').timepicker(config);
  });
})(jQuery);
