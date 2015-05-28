(function(){
	var app = angular.module('diplomacy', []);

	var server = 'http://diplomacy3.asilika.com';

	app.controller('GamesController', ['$http', function($http){

		this.games = "Hello, these are the games";
		var gamesCtrl = this;

		$http.get( server + '/api/rest/games').success(function(response){
		  gamesCtrl.games = response.data.data;
		});
	}]);

	app.controller('MatchesController', ['$http', '$scope', '$window', function($http, $scope, $window){

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
			this.selectedEmpire=$scope.empires[empireId];

			//Get territories for this match
			$http.get( server + '/api/rest/matches/'+matchId+'/empires/'+empireId+'/territories?include_neighbours=1').success(function(response){
				$scope.territories = response.data.data;
			});
		}

		$scope.sendOrders = function(matchId, empireId, ordersText){
			var req = server + '/api/rest/matches/'+matchId+'/empires/'+empireId+'/orders?order_str='+escape(ordersText);
			console.debug(req);

			$http.get(req).success(function(response){
				console.debug(response);
			});
		}
	}]);

	app.directive("neighbourTerritories", function(){
		return {
		  restrict: 'E',
		  templateUrl: 'neighbour-territories.html'
		};

	});

})();
