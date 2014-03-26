'use strict';

angular.module('haikMarkdownDemoApp', [
  'ngCookies',
  'ngResource',
  'ngSanitize',
  'ngRoute',

  'editorServices',
  'monospaced.elastic'
])
  .config(['$routeProvider', function ($routeProvider) {
    $routeProvider
      .when('/', {
        templateUrl: 'views/main.html',
        controller: 'MainCtrl'
      })
      .when('/editor', {
        templateUrl: 'views/editor.html',
        controller: 'EditorCtrl'
      })
      .otherwise({
        redirectTo: '/'
      });
  }])
  .run(['$route', function($route) {
    $route.reload();
  }]);
