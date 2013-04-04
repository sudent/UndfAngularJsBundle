'use strict'

angular.module('uFancybox', []).directive('uFancybox', function () {
    return {
        restrict: 'A',
        link: function (scope, element, attrs) {
            $(element).fancybox(attrs.uFancybox);
        }
    };
});