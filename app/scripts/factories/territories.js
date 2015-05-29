'use strict';

angular.module('diplomacy')
.factory('TerritoryController', ['$q', 'Territory', 'config', function(d$q, Territory, config) {


	function TerritoryController() {
		this.territories = [];
	}

	/**
	 * Push the Territory onto the TerritoryController.territories array, or
	 * overwrite it if it exists already.
	 * @param Territory Territory
	**/
	TerritoryController.prototype.push = function(territory) {
		if (!(territory instanceof Territory)) {
			territory = new Territory(territory, this);
		}
		var eIdx=-1;
		angular.forEach(this.territories, function(val, idx) {
			if (val.territoryId === territory.territoryId) {
				eIdx=idx;
				return;
			}
		});
		if (eIdx>0) {
			// Replace
			this.territories[eIdx] = territory;
		} else {
			// Add
			this.territories.push(territory);
		}
		// console.debug("Added neighbour " + territory.territoryId);
		return territory;
	};

	TerritoryController.prototype.get = function(territoryId) {
		var eIdx=-1;
		angular.forEach(this.territories, function(val, idx) {
			if (val.territoryId === territoryId) {
				eIdx=idx;
				return;
			}
		});
		if (eIdx>=0) {
			return this.territories[eIdx];
		} else {
			return null;
		}
	}

	var territoryController = new TerritoryController;

	return {
		getTerritoryController: function() { return territoryController; }
	};

}]);

angular.module('diplomacy')
.factory('Territory', [function() {

	function Territory(data, controller) {
		this.controller = controller;
		if (angular.isDefined(data))
			this.populate(data);
	}
	Territory.prototype.populate = function(data) {
		this.territoryId = data.territoryId;
		this.name        = data.name;
		this.type        = data.type;
		this.isSupply    = data.isSupply?true:false;
		// this.neighbourIds = [];
		this.neighbours  = [];
		this.unit        = 'none'; // confusing with State now..
	};

	/*
	 * Given a neighbour structure, add it to the controller, and add
	 * it's ID to us.  (maybe try adding it to us directly too?)
	 */
	Territory.prototype.loadNeighbours = function (data) {
		angular.forEach(data, function(val) {
			var t = this.controller.push(val);
			// console.debug("Adding neighbour " + t.territoryId);
			this.neighbours.push(t);
			// this.neighbourIds.push(val.territoryId);
		}, this);
	};

	Territory.prototype.setUnit = function(unit_type) {
		this.unit = unit_type;
	}


	// Territory.prototype.getNeighbours = function() {
	// 	var eIdx=-1;
	// 	var neighbours = [];
	// 	angular.forEach(this.neighbourIds, function(neighbourId, idx) {
	// 		var n = this.controller.get(neighbourId);
	// 		if (n === null)
	// 			console.error('Neighbour with id = ' + neighbourId + ' is null!');
	// 		else
	// 			neighbours.push(n);
	// 	}, this);
	// 	return neighbours;
	// }

	return (Territory);
}]);

// vim: noet sts=0 sw=4 ts=4 :
