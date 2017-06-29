/**
 * @file
 * Service to access cookies.
 */

angular.module('genlyd').service('SearchService', ['$http', '$q',
  function ($http, $q) {
    'use strict';

    var config = drupalSettings.genlyd_search;

    console.log(config);

  }]
);