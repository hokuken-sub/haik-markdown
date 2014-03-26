'use strict';

angular.module('haikMarkdownDemoApp')
  .controller('PreviewCtrl', ['$scope', '$rootScope', function ($scope, $rootScope) {
/*
      $rootScope.styles = [
      ];
*/
    $scope.html = $rootScope.htmlData

}]);
