<?php

namespace DiplomacyEngine\Turns;

use DiplomacyEngine\Players\iGame as Game;
use DiplomacyEngine\Players\iPlayer as Player;
use DiplomacyEngine\Players\iOrder as Order;

interface iTurn {
	/** Add a player */
	public function addOrder(Order $order);

	/** Resolves all the orders in this turn, and determine
	 * who needs to retreat */
	public function resolveAttacks();

	/** Resolves all the retreat orders */
	public function resolveRetreats();

	/** Carry out the succesful orders */
	public funtion carryOutOrders();

}

class Turn implements iTurn {

	protected $game;
	protected $turn;
	protected $orders;

	// /** Array representing the territories (but not Territory objects) used
	//  * to help resolve the fate of the Territory objects. */
	// protected $territories;

	/**
	 * Constructs a new turn.
	 *
	 * @param iGame The game being played
	 * @param int $turn The number of the turn.  Even represents spring, odd represents fall
	 **/
	public function __construct(Game $game, $turn) {
		$this->game = $game;
		$this->orders = array();
		$this->turn = $turn;
	}

	public function resolveAttacks() {
		$this->validateOrders();
		$this->removeOrdersFromAttackedTerritories();
		$this->resolveOrders();
		$this->carryOutOrders();
	}

	/** While orders should be validated before even being assigned
	 * to a turn, run this just in case.  This will call order->Validate
	 * which will ensure that all the parameters in an order make sense */
	protected function validateOrders() {

	}

	/** Iterates through the list of orders, and removes any order
	 * who's source territory is the attack destination of another
	 * player. */
	protected function removeOrdersFromAttackedTerritories() {

	}

	/**
	 * Build a list of territories from the Game and place it in $this->territories
	 * with the structure
	 * $territories[territory_id] = array(player1_id => count, player2_id => count, ...)
	 *
	 * Iterate through the orders, and increment the 'count' in the structure above every
	 * time a player attacks a teritory or is supported.
	 *
	 * Then, loop again to determine who won each territory based on the largest 'count',
// Wrong! well, sort of.  Need to determine the winning ORDERs
// User $order->fail to mark failed orders.
	 * and record this as $territories[territory_id]['winner'] = player_id, and 
	 * $territories[territory_id]['losers'] = array(loser_player_ids);
	 **/
	protected function resolveOrders() {

	}

	protected function carryOutOrders() {

	}

}

// vim: ts=3 sw=3 noet :
