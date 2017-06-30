/**
 * @file
 * Contains the Search Controller.
 */

angular.module('genlyd').controller('SearchController', ['$scope', 'SearchService',
  function ($scope, SearchService) {
    'use strict';

    var config = drupalSettings.genlyd_search;

    // Set default variable values in the scope.
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

    /**
     * Toggle search filter button and text.
     */
    $scope.filterLabel = 'Skjule filter';
    $scope.showFilters = false;
    $scope.toggleFilters = function toggleFilters() {
      $scope.showFilters = !$scope.showFilters;

      if ($scope.showFilters) {
        $scope.filterLabel = 'Skjule filter';
      }
      else {
        $scope.filterLabel = 'Vis filter';
      }
    };

    /**
     * Perform search.
     */
    $scope.search = function search() {
      $scope.searching = true;

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

    // Start by sending an empty search query to display all items.
    $scope.search();
  }
]);
