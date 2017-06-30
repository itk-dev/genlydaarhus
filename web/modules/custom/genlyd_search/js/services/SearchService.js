/**
 * @file
 * Service to access cookies.
 */

angular.module('genlyd').service('SearchService', ['$http', '$q',
  function ($http, $q) {
    'use strict';

    var config = drupalSettings.genlyd_search;

    this.search = function search(text, facets, page) {
      var deferred = $q.defer();

      $http.post(config['endpoint'], {
        keys: text,
        fields: config['search_fields'],
        limit: config['search_limit'],
        page: page,
        index: config['index'],
        facets: facets
      }).then(function success(response) {
        deferred.resolve(response.data);
      },
      function error(err) {
        deferred.reject(err);
      });

      return deferred.promise;
    };
  }]
);