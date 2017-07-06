/**
 * @file
 * Contains the Search Controller.
 */

angular.module('genlyd').controller('SearchController', ['$scope', '$timeout', 'SearchService',
  function ($scope, $timeout, SearchService) {
    'use strict';

    var config = drupalSettings.genlyd_search;

    // Set default variable values in the scope.
    $scope.showMap = false;
    $scope.searchZipcode = '';
    $scope.searchText = '';
    $scope.searchFacets = {};
    $scope.results = {};
    $scope.searching = true;
    $scope.pager = {
      size: config.search_limit,
      page: 1,
      no_of_results: 0
    };

    $scope.showMapLabel = Drupal.t('Show map');
    $scope.showMap = false;
    $scope.toggleMap = function toggleMap() {
      $scope.showMap = !$scope.showMap;

      if ($scope.showMap) {
        $scope.showMapLabel = Drupal.t('Show list');

        // Timeout to ensure the map is show before fit is called.
        $timeout(updateMapDisplay);
      }
      else {
        $scope.showMapLabel = Drupal.t('Show map');
      }
    };

    /**
     * Toggle search filter button and text.
     */
    $scope.filterLabel = Drupal.t('Show filter');
    $scope.showFilters = false;
    $scope.toggleFilters = function toggleFilters() {
      $scope.showFilters = !$scope.showFilters;

      if ($scope.showFilters) {
        $scope.filterLabel = Drupal.t('Hide filter');
      }
      else {
        $scope.filterLabel = Drupal.t('Show filter');
      }
    };

    /**
     * Perform search.
     */
    $scope.search = function search() {
      $scope.searching = true;

      // Close filters.
      if ($scope.showFilters) {
        $scope.toggleFilters();
      }

      var facets = {};
      if ($scope.searchZipcode !== '') {
        facets.zipcode = $scope.searchZipcode;
      }

      facets[config.search_facet_index] = [];
      for (var i in $scope.searchFacets) {
        if ($scope.searchFacets[i]) {
          facets[config.search_facet_index].push(i);
        }
      }

      SearchService.search($scope.searchText, facets, $scope.pager.page - 1).then(function (results) {
        $scope.pager.no_of_results = results.no_of_results;
        $scope.results = results;
        $scope.searching = false;
      });

      SearchService.searchMap($scope.searchText, facets).then(function (results) {
        /** @see genlyd_maps.js for map information. */
        genlydMapsAddActivities(results);
      });
    };

    /**
     * Pager page changed callback.
     *
     * @param {string} name
     *   Name of the pager.
     * @param {int} page
     *   The page that should be changed to.
     */
    $scope.changePage = function changePage(name, page) {
      $scope.pager.page = page;
      $scope.search();
    };

    /**
     * Go to the users location from the browser.
     */
    $scope.gotoMyLocation = function gotoMyLocation() {
      /** @see genlyd_maps.js for map information. */
      genlydMapsMyLocation();
    };

    // Start by sending an empty search query to display all items.
    $scope.search();
  }
]);
