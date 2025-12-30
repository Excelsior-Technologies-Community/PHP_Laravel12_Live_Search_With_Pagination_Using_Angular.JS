/*!
 * angular-utils-pagination
 * Version: 0.11.1
 * Author: Michael Bromley
 * License: MIT
 */
(function () {
  'use strict';

  angular.module('angularUtils.directives.dirPagination', [])
    .directive('dirPaginate', ['$parse', function ($parse) {
      return {
        restrict: 'A',
        priority: 1000,
        terminal: true,
        compile: function (element, attrs) {
          var expression = attrs.dirPaginate;
          var match = expression.match(/^\s*(.+)\s+in\s+(.*?)\s*(?:\|\s*itemsPerPage\s*:\s*(\d+))?\s*$/);

          if (!match) {
            throw new Error("Expected expression in form of '_item_ in _collection_ | itemsPerPage:_num_' but got '" +
              expression + "'.");
          }

          var itemString = match[1];
          var collectionString = match[2];
          var itemsPerPage = parseInt(match[3] || '10', 10);

          return function ($scope, $element, $attrs) {
            var collectionGetter = $parse(collectionString);
            var collection = collectionGetter($scope);

            var currentPage = 1;
            var begin = (currentPage - 1) * itemsPerPage;
            var end = begin + itemsPerPage;

            $scope.$watchCollection(collectionString, function (newVal, oldVal) {
              if (!newVal) return;
              var paginatedCollection = newVal.slice(begin, end);
              $element.empty();
              angular.forEach(paginatedCollection, function (item) {
                var clone = $element.clone();
                $scope[itemString] = item;
                $element.after(clone);
              });
            });
          };
        }
      };
    }])
    .directive('dirPaginationControls', function () {
      return {
        restrict: 'E',
        template: '<div class="pagination-container"><ng-transclude></ng-transclude></div>',
        transclude: true
      };
    });

})();
