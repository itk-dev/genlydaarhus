/**
 * @file
 * Contains the Search Controller.
 */

angular.module('genlyd').controller('SearchController', ['$scope', 'SearchService',
  function ($scope, SearchService) {
    'use strict';

    var config = drupalSettings.genlyd_search;

    $scope.searchZipcode = '';
    $scope.searchText = '';
    $scope.searchFacets = {};
    $scope.results = {};


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

    $scope.search = function search() {
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

      SearchService.search($scope.searchText, facets, 0).then(function (results) {
        $scope.results = results;
        console.log(results);
      });
    };

    $scope.search();
  }
]);
