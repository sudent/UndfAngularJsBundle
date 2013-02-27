angular.module('uFileUpload', []).directive('uFileUpload', function () {
    'use strict';
    return {
        restrict: 'E',
        transclude: false,
        controller: function ($scope, $element, $attrs) {
            $scope.showImage = function (input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function (e) {
                        $scope.$apply(function () {
                            $scope[$attrs.uFileName] = e.timeStamp;
                            $scope[$attrs.uFileSrc] = e.target.result;
                        });
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            };
            $scope.uploadFile = function () {
                $element.find('[type=file]').click();
            };
            $scope.removePic = function () {
                $scope[$attrs.uFileSrc] = $scope[$attrs.uFileDefaultSrc];
            };
        },
        compile: function (element, attrs, transclude) {
            element.find('[type=file]').attr('onchange', 'angular.element(this).scope().showImage(this)');
        }

    };

});


