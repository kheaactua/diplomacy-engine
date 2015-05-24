(function(){
	var app = angular.module('diplomacy', []);
	
	app.controller('GamesController', ['$http', function($http){
   
		this.games = "Hello, these are the games";
		var gamesCtrl = this;
		
		$http.get('http://diplomacy.asilika.com:9494/api/rest/games').success(function(response){
		  gamesCtrl.games = response.data.data;
		});
	}]);
 
	app.controller('MatchesController', ['$http', function($http){
  
		this.matches = "Hello, these are the matches";
		var matchesCtrl = this;
		
		$http.get('http://diplomacy.asilika.com:9494/api/rest/matches').success(function(response){
		  matchesCtrl.matches = response.data.data;
		});
	}]);
	

	app.controller('EmpiresController', ['$http', function($http){
		this.getMatch = function getMatch(id){
			this.empires="Hello, these are the empires for match " + id;
			var matchCtrl = this;
			$http.get('http://diplomacy.asilika.com:9494/api/rest/matches/'+id).success(function(response){
				matchCtrl.empires = response.data.data;
			});
			
		}
		this.getMatch(49);
	}]);
	
	app.controller('UnitController', ['$http', function($http){
   
		this.territories = [{unitType:'army', territory: 'canada'},{unitType:'fleet', territory: 'greenland'},{unitType:'none', territory: 'cuba'}];
		
		//$http.get('http://diplomacy.asilika.com:9494/api/rest/matches').success(function(data){
		 // 
		//});
	}]);
})();