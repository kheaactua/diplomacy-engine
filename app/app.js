(function(){
	var app = angular.module('diplomacy', []);

	//var server = 'http://diplomacy3.asilika.com';
	var server = 'http://diplomacy.asilika.com:9494';

	app.controller('MatchesController', ['$http', '$scope', '$window', 'TerritoryController', 'Territory', function($http, $scope, $window, TerritoryController, territory){

		territoryController = TerritoryController.getTerritoryController();

		$scope.territories = [];

		$scope.action = 'hold';

		$http.get( server + '/api/rest/matches').success(function(response){
			$scope.matches = response.data.data;
		});

		$scope.setMatch = function(matchId){
			$scope.selectedMatch=$scope.matches[matchId];

			//Get empires for this match
			$http.get( server + '/api/rest/matches/'+matchId).success(function(response){
				$scope.empires = response.data.data;
			});
		}

		$scope.setEmpire = function(matchId, empireId){
			$scope.selectedEmpire=$scope.empires[empireId];

			//Get territories for this match
			$http.get( server + '/api/rest/matches/'+matchId+'/empires/'+empireId+'/territories?include_neighbours=1').success(function(response){
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
			var req = server + '/api/rest/matches/'+matchId+'/empires/'+empireId+'/orders?order_str='+escape(ordersText);

			$http.get(req).success(function(response){
				console.debug(response);
			});
		}

		$scope.orders = [];
		$scope.writeMoveOrder = function(territoryId) {
			$scope.orders.push('MOVE "' + territoryController.get(parseInt(territoryId)).name + '"');
		}
		$scope.writeSupportOrder = function(sourceId, destId) {
			$scope.orders.push('SUPPORT ' + territoryController.get(parseInt(sourceId)).name + ' "' + + territoryController.get(parseInt(sourceId)).name + '"');
		}
		$scope.writeConvoyOrder = function(sourceId, destId) {
			$scope.orders.push('CONVOY ' + territoryController.get(parseInt(sourceId)).name + ' "' + + territoryController.get(parseInt(sourceId)).name + '"');
		}
	}]);

	app.directive("neighbourTerritories", function(){
		return {
		  restrict: 'E',
		  templateUrl: 'neighbour-territories.html'
		};
	});

})();

// vim: noet sts=0 sw=4 ts=4 :
