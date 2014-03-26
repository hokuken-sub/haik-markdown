'use strict';

angular.module('haikMarkdownDemoApp')
  .controller('EditorCtrl', ['$scope', '$rootScope', 'Editor',
    function ($scope, $rootScope, Editor) {
    var styles = [
          {"href": "bower_components/bootstrap/dist/css/bootstrap.css"},
          {"href": "bower_components/bootstrap/dist/css/bootstrap.css"},
          {"href": "bower_components/kube/css/kube.css"},
          {"href": "bower_components/pure/pure-min.css"}
    ];
    
    $scope.Editor = Editor;

    $scope.Editor.body = '' +
                          '<a class="btn btn-blue">Blue</a>\n'+
                          '<a class="pure-button pure-button-primary" href="#">A Primary Button</a>\n' +
                          '<a class="btn btn-success" href="#">eeee</a>\n';


    $scope.editorVisible = true;
    $scope.active = 0;
    $scope.fullscreen = false;

    $rootScope.styles = [styles[$scope.active]];

    
    $scope.isActive = function(matchIdx) {
      return $scope.active == matchIdx;
    };
    
    $scope.switchScreen = function(full) {
      $scope.fullscreen = full;
    }

    $scope.preview = function (type) {

      if ($scope.Editor.isEditor(type)) {
        $scope.active = $scope.Editor.viewIndexOf(type);
        $scope.editorVisible = true;
        $rootScope.styles = [styles[$scope.active]];
        return;
      }

      var data = new FormData();
      data.append("body", $scope.Editor.body);
      data.append("type", type);

      $.ajax({
        data: data,
        type: "POST",
        url: "convert.php",
        cache: false,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function(response) {
            console.log('success');

            $scope.$apply(function(){
              $scope.Editor.html = response.html;
              $scope.active = $scope.Editor.viewIndexOf(type);
              $scope.editorVisible = false;
              $rootScope.styles = [styles[$scope.active]];
            });

        },
        error: function(response){
            console.log('error');
        }
      });

    }

    $scope.insert = function(name){
      var target = angular.element("textarea");
      var str = $scope.Editor.snippetText(name)

      target.focus();

      if(navigator.userAgent.match(/MSIE/)) {
        var r = document.selection.createRange();
        r.text = str;
        r.select();
      } 
      else {
        var s = target.val();
        var p = target.get(0).selectionStart;
        var np = p + str.length;
        target.val(s.substr(0, p) + str + s.substr(p));
        target.get(0).setSelectionRange(np, np);
      }
    }

  }]);
