/*
	By Osvaldas Valutis, www.osvaldas.info
	Available for use under the MIT License

  https://tympanus.net/codrops/2015/09/15/styling-customizing-file-inputs-smart-way/

  NB! Modified.
*/

jQuery(document).ready(function ($) {
  "use strict";

  $('.js-image-upload').each(function () {
    var $input = $(this),
      $label = $input.next('label'),
      infoText = $('#js-upload-info'),
      labelVal = $label.html(),
      fileChosenText = Drupal.t('You have chosen the file:'),
      chooseAnotherText = Drupal.t('Choose another');

    $input.on('change', function (e) {
      var fileName = '';

      if (e.target.value) {
        fileName = e.target.value.split('\\').pop();
      }

      if (fileName) {
        infoText.html(fileChosenText + ' ' + fileName);
        $label.find('span').html(chooseAnotherText);
        $label.removeClass("is-cta");
        $label.addClass('is-secondary-light');
      }
      else {
        $label.html(labelVal);
      }
    });

    // Firefox bug fix.
    $input
    .on('focus', function () {
      $input.addClass('has-focus');
    })
    .on('blur', function () {
      $input.removeClass('has-focus');
    });
  });

});
