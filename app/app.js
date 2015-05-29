(function(){
	var app = angular.module('diplomacy', []);

	//derrived from http://scotch.io/tutorials/javascript/angular-routing-using-ui-router
	app.config(['$httpProvider', '$locationProvider', '$sceDelegateProvider',
		function($http, $locationProvider, $sceDelegateProvider) {

			// Here to prevent Angular from sending an OPTIONS method
			// http://stackoverflow.com/questions/12111936/angularjs-performs-an-options-http-request-for-a-cross-origin-resource
			$sceDelegateProvider.resourceUrlWhitelist([
				'self',
				'http://diplomacy.asilika.com:9494/**',
				'http://diplomacy2.asilika.com:9494/**',
				'http://diplomacy3.asilika.com/**',
			]);

			// Configure $http to use cors so we're not locked to this domain
			// Good answer on WHERE this code should go: http://stackoverflow.com/questions/18877715/http-auth-headers-in-angularjs
			// domain (even though in production, it's not unlikely that
			$http.defaults.headers.common.Accept = 'application/json';
			$http.defaults.useXDomain = true;


			// http://victorblog.com/2012/12/20/make-angularjs-http-service-behave-like-jquery-ajax/
			// Use x-www-form-urlencoded Content-Type
			$http.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';

			/**
			  * The workhorse; converts an object to x-www-form-urlencoded serialization.
			  * @param {Object} obj
			  * @return {String}
			  */
			var param = function(obj) {
				var query = '', name, value, fullSubName, subName, subValue, innerObj, i;

				for(name in obj) {
					value = obj[name];

					if(value instanceof Array) {
						for(i=0; i<value.length; ++i) {
							subValue = value[i];
							fullSubName = name + '[' + i + ']';
							innerObj = {};
							innerObj[fullSubName] = subValue;
							query += param(innerObj) + '&';
						}
					}
					else if(value instanceof Object) {
						for(subName in value) {
							subValue = value[subName];
							fullSubName = name + '[' + subName + ']';
							innerObj = {};
							innerObj[fullSubName] = subValue;
							query += param(innerObj) + '&';
						}
					}
					else if(value !== undefined && value !== null) {
						query += encodeURIComponent(name) + '=' + encodeURIComponent(value) + '&';
					}
				}

				return query.length ? query.substr(0, query.length - 1) : query;
			};

			// Override $http service's default transformRequest
			$http.defaults.transformRequest = [function(data) {
				return angular.isObject(data) && String(data) !== '[object File]' ? param(data) : data;
			}];
		}
	])

	app.controller('MatchesController', ['$http', '$scope', '$window', 'TerritoryController', 'Territory', 'OrderController', 'config', function($http, $scope, $window, TerritoryController, territory, OrderController, config){

		territoryController = TerritoryController.getTerritoryController();
		orderController = OrderController.getOrderController();

		$scope.territories = [];

		$scope.action = 'hold';

		$http.get( config.restUrl +'/matches').success(function(response){
			$scope.matches = response.data.data;
		});

		$scope.setMatch = function(matchId){
			$scope.selectedMatch=$scope.matches[matchId];

			//Get empires for this match
			$http.get( config.restUrl + '/matches/'+matchId).success(function(response){
				$scope.empires = response.data.data;
			});
		}

		$scope.setEmpire = function(matchId, empireId){
			$scope.selectedEmpire=$scope.empires[empireId];

			//Get territories for this match
			$http.get( config.restUrl + '/matches/'+matchId+'/empires/'+empireId+'/territories?include_neighbours=1').success(function(response){
				angular.forEach(response.data.data, function (val, idx) {
					var t = territoryController.push(val.territory);
					$scope.territories.push(t);
				});
				angular.forEach(response.data.data, function (val, idx) {
					var t = territoryController.get(val.territory.territoryId);
					t.setUnit(val.unit);
					t.loadNeighbours(val.neighbours);
				});
			});
		}

		/**
		 * Iterates through all the orders in the form, and submits them one-by-one
		 */
		$scope.sendAllOrders = function() {
			angular.forEach($scope.orders, function(val) {
				sendOrder($scope.matchId, $scope.empireId, val);
			});
		}

		sendOrder = function(matchId, empireId, order, orderText){
			var req = config.restUrl + '/matches/'+matchId+'/empires/'+empireId+'/orders?order_str='+escape(ordersText);

			$http.get(req).success(function(response){
				console.debug(response);
			});
		}


		/**
		 * These functions create order objects that are displayed in the directive.
		 * Like this you can validate them, easily get the collection to send of,
		 * whatever.
		 */

		$scope.orders=[];
		$scope.writeMoveOrder = function(home, destId) {
			var dest   = territoryController.get(parseInt(destId));

			orderController.push({
				matchId:   $scope.matchId,
				empireId:  $scope.empireId,
				orderId:   home.territoryId,
				command: 'MOVE "' + home.name + '" "' + dest.name + '"',
				valid:     false,
				orderParams: {
					sourceId: home.territoryId,
					destId:   dest.territoryId,
				},
			});
			$scope.orders = orderController.getOrders();
		}
		$scope.writeSupportOrder = function(home, sourceId, destId) {
			var source = territoryController.get(parseInt(sourceId));
			var dest   = territoryController.get(parseInt(destId));

			orderController.push({
				matchId:   $scope.matchId,
				empireId:  $scope.empireId,
				orderId:   home.territoryId,
				command: 'SUPPORT "'
					+ home.name + '" "'
					+ source.name + '" "'
					+ dest.name + '"',
				valid: false,
				orderParams: {
					sourceId:         home.territoryId,
					allyTerritoryId:  source.territoryId,
					destId:           dest.territoryId,
				},
			});
			$scope.orders = orderController.getOrders();
		}
		$scope.writeConvoyOrder = function(home, sourceId, destId) {
			var source = territoryController.get(parseInt(sourceId));
			var dest   = territoryController.get(parseInt(destId));

			orderController.push({
				matchId:   $scope.matchId,
				empireId:  $scope.empireId,
				orderId:   home.territoryId,
				command: 'CONVOY "'
					+ home.name + '" "'
					+ source.name + '" "'
					+ dest.name + '"',
				valid: false,
				orderParams: {
					sourceId:  source.territoryId,
					destId:    dest.territoryId,
				},
			});
			$scope.orders = orderController.getOrders();
		}

		$scope.deleteOrder = function(orderId) {
			orderController.deleteOrder(orderId);
			$scope.orders = orderController.getOrders();

		}
		$scope.validateCallback = function(order) {
			console.log('Order \''+ order.command +'\' validated');
		}
	}]);

	app.directive("neighbourTerritories", function(){
		return {
		  restrict: 'E',
		  templateUrl: 'neighbour-territories.html'
		};
	});


	/**
	 * Using this technique instead of angular.constant() because here I can actually
	 * run some code, to allow me for instance to detect whether we're on a dev site
	 * or not.  Copied from Mayofest
	**/
	app.factory('config', ['$window', function($window) {
		var baseUrl = 'diplomacy.asilika.com:9494';

		var conf = {
			server: '//'+baseUrl+'',
			restUrl: '//'+baseUrl+'/api/rest',
			rpcUrl:  '//'+baseUrl+'/api/rpc',
		};

		if (conf.debug) console.log('Using base_url='+baseUrl);
		return conf;
	}]);

})();

// vim: noet sts=0 sw=4 ts=4 :
