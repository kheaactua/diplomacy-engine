(function(){
	var app = angular.module('diplomacy', []);
	
	app.controller('GamesController', ['$http', function($http){
   
		this.games = "Hello, these are the games";
		var gamesCtrl = this;
		
		$http.get('http://diplomacy.asilika.com:9494/api/rest/games').success(function(response){
		  gamesCtrl.games = response.data.data;
		});
	}]);
	
	app.controller('EmpiresController', ['$http', function($http){
   
		this.empires = "Hello, these are the empires";
		var empiresCtrl = this;
		this.match = {};
		
		$http.get('http://diplomacy.asilika.com:9494/api/rest/matches/108').success(function(data){
		  empiresCtrl.empires = data;
		});
	}]);
 
//	app.controller('MatchesController', ['$http', function($http){
//  
//		this.matches = "Hello, these are the matches";
//		var matchesCtrl = this;
//		
//		$http.get('http://diplomacy.asilika.com:9494/api/rest/matches').success(function(data){
//		  matchesCtrl.matches = data;
//		});
//	}]);
	
	app.controller('UnitController', ['$http', function($http){
   
		this.territories = [{unitType:'army', territory: 'canada'},{unitType:'fleet', territory: 'greenland'},{unitType:'none', territory: 'cuba'}];
		
		//$http.get('http://diplomacy.asilika.com:9494/api/rest/matches').success(function(data){
		 // 
		//});
	}]);
})();