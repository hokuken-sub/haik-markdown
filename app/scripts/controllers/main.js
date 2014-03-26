'use strict';

angular.module('haikMarkdownDemoApp')
  .controller('MainCtrl', ['$scope', '$rootScope', function ($scope, $rootScope) {
      $rootScope.styles = [
          {"href": "bower_components/bootstrap/dist/css/bootstrap.css"},
          {"href": "http://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css"},
          {"href": "http://fonts.googleapis.com/css?family=Lora:400,700,400italic,700italic"},
          {"href": "http://fonts.googleapis.com/css?family=Montserrat:400,700"}
/*           {"href": "bower_components/grayscale/css/grayscale.css"} */
      ];
      
      
      $scope.scroll = function(id){
        var $anchor = angular.element(id);
        var $obj = angular.element('html, body');

        $obj.stop().animate({
            scrollTop: $anchor.offset().top
        }, 1500, 'easeInOutExpo');
      }
      
  }]);
