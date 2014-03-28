'use strict';

angular.module('haikMarkdownDemoApp')
  .controller('EditorCtrl', ['$scope', '$rootScope', '$location', 'Editor',
    function ($scope, $rootScope, $location, Editor) {

    angular.extend($scope, {
      "isActive": function(matchIdx) {
          return $scope.active == matchIdx;
      },
      "backEdit": function() {
          $scope.active = $scope.Editor.viewIndexOf('Editor');
          $scope.editorVisible = true;
          $rootScope.styles = [$scope.Editor.styles[$scope.active]];
          $scope.fullscreen = $scope.zen;
          $location.path("editor");
      },
      "setZen": function(full) {
          $scope.fullscreen = full;
          $scope.zen = full;
          if (full) {
            angular.element('#haikmde_full_textarea').click();
          } else {
            angular.element('#haikmde_textarea').click();
          }
      },
      "setExnote": function() {
        angular.element('#haikmde_textarea')
          .exnote({css:{height:"300px", fontSize:"14px"}});
        angular.element('#haikmde_full_textarea')
          .exnote({css:{height:"100%", fontSize:"16px"}});
      }
    });
    
    $scope.Editor = Editor;

    if ($scope.Editor.body.length === 0)
    {
      $scope.Editor.body = '' +
            '<button class="btn btn-success" type="button">Blue</button>\n'+
            '<a class="btn btn-blue">Blue</a>\n'+
            '<a class="pure-button pure-button-primary" href="#">A Primary Button</a>\n' +
            '<a class="btn btn-success" href="#">eeee</a>\n';
    }

    setTimeout(function(){$scope.setExnote();}, 5)


    $scope.editorVisible = true;
    $scope.active = 0;
    $scope.fullscreen = false;

    $scope.zen = $rootScope.zen || false;
    $scope.setZen($scope.zen);
    


    $scope.preview = function (type) {

      if ($scope.Editor.isEditor(type)) {
        $scope.backEdit();
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
              $rootScope.zen = $scope.zen;
              $location.path("preview/"+type);
            });
        },
        error: function(response){
            console.log('error');
        }
      });
    }

    $scope.insert = function(name) {
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
