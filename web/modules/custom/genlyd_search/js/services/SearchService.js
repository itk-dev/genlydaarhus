/**
 * @file
 * Service to access search backend.
 */

angular.module('genlyd').service('SearchService', ['$http', '$q',
  function ($http, $q) {
    'use strict';

    var config = drupalSettings.genlyd_search;

    /**
     * Search the backend.
     *
     * @param text
     *   The text to search for.
     * @param facets
     *   The facets to use.
     * @param page
     *   The current page to display.
     */
    this.search = function search(text, facets, page) {
      var deferred = $q.defer();

      $http.post(config.endpoint, {
        keys: text,
        fields: config.search_fields,
        limit: config.search_limit,
        page: page,
        sort: config.search_sort,
        index: config.index,
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