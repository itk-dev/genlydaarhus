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
     * @param {string} text
     *   The text to search for.
     * @param {object} facets
     *   The facets to use.
     * @param {int} page
     *   The current page to display.
     *
     * @return {object}
     *   Promise to fulfill.
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

    /**
     * Search the backend for map information.
     *
     * @param {string} text
     *   The text to search for.
     * @param {object} facets
     *   The facets to use.
     *
     * @return {object}
     *   Promise to fulfill.
     */
    this.searchMap = function searchMap(text, facets) {
      var deferred = $q.defer();

      $http.post(config.map.endpoint, {
        keys: text,
        fields: config.search_fields,
        limit: 9999,
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