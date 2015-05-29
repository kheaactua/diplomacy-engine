'use strict';

angular.module('diplomacy')
.factory('OrderController', ['$q', 'Order', function(d$q, Order) {

	function OrderController() {
		this.orders = [];
	}

	/**
	 * Push the Order onto the OrderController.orders array, or
	 * overwrite it if it exists already.
	 * @param Order Order
	**/
	OrderController.prototype.push = function(order) {
		if (!(order instanceof Order)) {
			order = new Order(order, this);
		}
		var eIdx=-1;
		angular.forEach(this.orders, function(val, idx) {
			if (val.orderId === order.orderId) {
				eIdx=idx;
				return;
			}
		});
		if (eIdx>0) {
			// Replace
			this.orders[eIdx] = order;
		} else {
			// Add
			this.orders.push(order);
		}
		// console.debug("Added neighbour " + order.orderId);
		return order;
	};

	OrderController.prototype.get = function(orderId) {
		var eIdx=-1;
		angular.forEach(this.orders, function(val, idx) {
			if (val.orderId === orderId) {
				eIdx=idx;
				return;
			}
		});
		if (eIdx>=0) {
			return this.orders[eIdx];
		} else {
			return null;
		}
	}

	OrderController.prototype.getOrders = function() {
		return this.orders;
	}

	OrderController.prototype.deleteOrder = function(orderId) {

		var eIdx = -1;
		angular.forEach(this.orders, function(val, idx) {
			if (val.orderId === orderId) {
				eIdx=idx;
				return;
			}
		});
		if (eIdx>=0) this.orders.splice(eIdx, 1);
	};

	var orderController = new OrderController;

	return {
		getOrderController: function() { return orderController; }
	};

}]);

angular.module('diplomacy')
.factory('Order', ['$http', 'config', function($http, config) {

	function Order(data, controller) {
		this.controller = controller;
		if (angular.isDefined(data))
			this.populate(data);
	}
	Order.prototype.populate = function(data) {
		this.matchId     = data.matchId;
		this.empireId    = data.empireId;
		this.orderId     = data.orderId;
		this.command     = data.command;
		this.data        = data.orderParams;
		this.valid       = data.valid;
	};

	Order.prototype.validate = function(callback) {
		var url = config.restUrl + '/matches/'+ this.matchId +'/empires/'+ this.empireId +'/orders/validate';
		var order = this; // Used to access in the http success callback
		$http.post(url, {orderStr: this.command}).success(function (response) {
			order.valid = response.data.data; // bool
			callback(order); // TODO add response
		});
	};

	return (Order);
}]);

// vim: noet sts=0 sw=4 ts=4 :
