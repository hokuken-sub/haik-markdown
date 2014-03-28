'use strict';

/* Services */

var editorServices = angular.module('editorServices', ['ngResource']);

editorServices.factory('Editor', ['$resource', function($resource) {
    
    var body = '';
    var views = [
          {"view":"Editor",     "href": ""},
          {"view":"bootstrap",  "href": "bower_components/bootstrap/dist/css/bootstrap.css"},
          {"view":"kube",       "href": "bower_components/kube/css/kube.css"},
          {"view":"pure",       "href": "bower_components/pure/pure-min.css"}
        ];
    var html = '';
    var toolsData = $resource('editors/tools.json', {}, {
      query: {method:'GET', isArray:true}
    });

    return {
      body: body,
      html: html,
      tools: toolsData.query(),
      isEditor: function(type) {
        return (type === 'Editor');
      },
      views: (function(){
                return views.map(function(v){ return v.view; });
      })(),

      styles:(function(){
        return views.map(function(v){ return {"href": v.href}; });
      })(),

      viewIndexOf: function(type) {
        return this.views.indexOf(type);
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
