'use strict';

/* Services */

var editorServices = angular.module('editorServices', ['ngResource']);

editorServices.factory('Editor', ['$resource', function($resource) {
    
    var body = '';
    var views = [
          'Editor',
          'bootstrap',
          'kube',
          'pure'
        ];
    var html = '';
    var toolsData = $resource('editors/tools.json', {}, {
      query: {method:'GET', isArray:true}
    });

    return {
      views: views,
      body: body,
      html: html,
      tools: toolsData.query(),
      isEditor: function(type) {
        return (type === 'Editor');
      },
      viewIndexOf: function(type) {
        return views.indexOf(type);
      },
      snippetText: function(name) {
        var snippet = '';

        angular.forEach(this.tools, function (tool) {
          if (angular.equals(tool.name, name)) {
            snippet = tool.snippet;
            return false;
          }
        });

        return snippet;
      }
    }
  }
]);
