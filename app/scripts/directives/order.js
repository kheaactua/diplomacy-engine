'use strict';

angular.module('diplomacy')
	.directive('order', ['$compile', 'config', function ($compile, config) {
	return {
		scope: {
			order: '=',
			territoryId: '=',
			deleteOrder: '&',
			validateCallback: '&',
		},
		replace: true,
		restrict: 'E',
		templateUrl: 'views/order.html',
		link: function (scope, element, attrs) {
			//element.html($compile(template)(scope));
		}
	};
}]);
// vim: noet sts=0 sw=4 ts=4 :
