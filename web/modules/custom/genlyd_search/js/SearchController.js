/**
 * @file
 * Contains the Search Controller.
 */

angular.module('genlyd').controller('SearchController', ['$scope', 'SearchService',
  function ($scope, SearchService) {
    'use strict';


    $scope.filterLabel = 'Show filters';
    $scope.showFilters = false;
    $scope.toggleFilters = function toggleFilters() {
      $scope.showFilters = !$scope.showFilters;

      if ($scope.showFilters) {
        $scope.filterLabel = 'Hide filters';
      }
      else {
        $scope.filterLabel = 'Show filters';
      }
    };

    $scope.search = function search() {
      console.log($scope.searchText);
      console.log($scope.searchZipcode);
      console.log($scope.searchFacets);
    };
  }
]);
