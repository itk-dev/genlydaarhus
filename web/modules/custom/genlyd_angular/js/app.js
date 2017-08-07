/**
 * @file
 * Contains the App grundsalg.
 */

/**
 * Setup the app.
 */
angular.module('genlyd', ['angular-cache', 'bw.paging']).config(
  function sceProvider($sceProvider) {
    'use strict';

    // Completely disable SCE. We need to allow html to be added from Drupal.
    $sceProvider.enabled(false);
  }
);
