/**
 * @file
 * Contains javascript to setup time pickers for field_time_start and field_time_end.
 */

(function ($) {
  $(document).ready(function () {
    var timepickerConfig = {
      timeFormat: 'HH:mm',
      interval: 30,
      startTime: '07:00',
      dynamic: false,
      dropdown: true,
      scrollbar: true
    };

    $('input.js-timepicker-field').each(function (index, value) {
      var c = JSON.parse(JSON.stringify(timepickerConfig));
      c.defaultTime = value['value'] ? value['value'] : '8:00';

      $(this).timepicker(c);
    });
  });
})(jQuery);
