'use strict';

angular.module('haikMarkdownDemoApp', [
  'ngCookies',
  'ngResource',
  'ngSanitize',
  'ngRoute',

  'editorServices'
])
.config(['$routeProvider', function ($routeProvider) {
  $routeProvider
    .when('/', {
      templateUrl: 'views/main.html',
      controller: 'MainCtrl',
      css: [
        'bower_components/bootstrap/dist/css/bootstrap.css',
        'assets/styles/grayscale/css/grayscale.css',
        'styles/main.css'
      ] 
    })
    .when('/editor', {
      templateUrl: 'views/editor.html',
      controller: 'EditorCtrl',
      css: [
         'styles/editor.css'
      ]
    })
    .when('/preview/pure', {
      templateUrl: 'views/preview.html',
      controller: 'EditorCtrl',
      css: [
        'styles/editor.css',
        'bower_components/pure/pure-min.css'
      ]
    })
    .when('/preview/bootstrap', {
      templateUrl: 'views/preview.html',
      controller: 'EditorCtrl',
      css: [
        'styles/editor.css',
        'bower_components/bootstrap/dist/css/bootstrap.css'
      ]
    })
    .when('/preview/kube', {
      templateUrl: 'views/preview.html',
      controller: 'EditorCtrl',
      css: [
        'styles/editor.css',
        'bower_components/kube/css/kube.css'
      ]
    })
    .otherwise({
      redirectTo: '/'
    });
}])
.directive('head', ['$rootScope','$compile',
    function($rootScope, $compile){
        return {
            restrict: 'E',
            link: function(scope, elem){
                var html =  '<link rel="stylesheet" ng-repeat="(routeCtrl, cssUrl) in routeStyles" ng-href="{{cssUrl}}" />\n'+
                            '<link rel="stylesheet" ng-repeat="style in styles" ng-href="{{style.href}}" />';
                elem.append($compile(html)(scope));
                scope.routeStyles = {};
                
                $rootScope.$on('$routeChangeStart', function (e, next, current) {
                    if(current && current.$$route && current.$$route.css){
                        if(!Array.isArray(current.$$route.css)){
                            current.$$route.css = [current.$$route.css];
                        }
                        angular.forEach(current.$$route.css, function(sheet){
                            delete scope.routeStyles[sheet];
                        });
                    }
                    if(next && next.$$route && next.$$route.css){
                        if(!Array.isArray(next.$$route.css)){
                            next.$$route.css = [next.$$route.css];
                        }
                        angular.forEach(next.$$route.css, function(sheet){
                            scope.routeStyles[sheet] = sheet;
                        });
                    }
                });
            }
        };
    }
]);

