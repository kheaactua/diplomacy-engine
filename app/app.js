(function(){
	var app = angular.module('diplomacy', []);
	
	app.controller('GamesController', ['$http', function($http){
   
		this.games = "Hello, these are the games";
		var gamesCtrl = this;
		
		$http.get('http://diplomacy.asilika.com:9494/api/rest/games').success(function(data){
		  gamesCtrl.games = data;
		});
	}]);
  
})();