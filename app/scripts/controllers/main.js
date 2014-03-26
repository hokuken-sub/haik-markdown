'use strict';

angular.module('haikMarkdownDemoApp')
  .controller('MainCtrl', ['$scope', '$rootScope', function ($scope, $rootScope) {
      $rootScope.styles = [
      ];
      
      
      $scope.scroll = function(id){
        var $anchor = angular.element(id);
        var $obj = angular.element('html, body');

        $obj.stop().animate({
            scrollTop: $anchor.offset().top
        }, 1500, 'easeInOutExpo');
      }
      
  }]);
