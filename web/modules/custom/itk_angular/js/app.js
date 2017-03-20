/**
 * @file
 * Contains ITKAngular app definition.
 */

/**
 * Setup the app.
 */
angular.module('ITKAngular', ['ngRoute', 'ngAnimate', 'angular-cache']).config(function($sceProvider) {
  // Completely disable SCE. We need to allow html to be added from Drupal.
  $sceProvider.enabled(false);
});
