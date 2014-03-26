'use strict';

angular.module('haikMarkdownDemoApp')
  .controller('MainCtrl', ['$scope', '$rootScope', function ($scope, $rootScope) {
      $rootScope.styles = [
          {"href": "bower_components/bootstrap/dist/css/bootstrap.css"}
      ];
  }]);
