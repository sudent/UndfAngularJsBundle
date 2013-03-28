'use strict';

/* Directives */


angular.module('uScope', []).
directive('uScope', function () {
    return {
        restrict : 'AC',
        scope: true
    };
});