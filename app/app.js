(function(){
	var app = angular.module('diplomacy', []);
	
	var server = 'http://diplomacy2.asilika.com:9494';
	
	app.controller('GamesController', ['$http', function($http){
   
		this.games = "Hello, these are the games";
		var gamesCtrl = this;
		
		$http.get( server + '/api/rest/games').success(function(response){
		  gamesCtrl.games = response.data.data;
		});
	}]);
 
	app.controller('MatchesController', ['$http', '$scope', '$window', function($http, $scope, $window){
  
		$scope.action = 'hold';
  
		this.matches = "Hello, these are the matches";
		var matchesCtrl = this;
		
		$http.get( server + '/api/rest/matches').success(function(response){
		  matchesCtrl.matches = response.data.data;
		});
		
		this.setMatch = function(matchId){
			this.selectedMatch=this.matches[matchId];
			
			//Get empires for this match
			$http.get( server + '/api/rest/matches/'+matchId).success(function(response){
				matchesCtrl.empires = response.data.data;
			});
		}
		
		this.setEmpire = function(matchId, empireId){
			this.selectedEmpire=this.empires[empireId];
			
			//Get territories for this match
			$http.get( server + '/api/rest/matches/'+matchId+'/empires/'+empireId+'/territories?include_neighbours=1').success(function(response){
				matchesCtrl.territories = response.data.data;
			});
		}
		
		this.sendOrders = function(matchId, empireId, ordersText){
			$window.alert(ordersText);
			
			$http.get( server + '/api/rest/matches/'+matchId+'/empires/'+empireId+'/orders?'+escape(ordersText)).success(function(response){
				alert(response.data.data);
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