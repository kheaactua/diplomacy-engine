<?php

namespace DiplomacyEngine\Turns;

use DiplomacyEngine\Game\iGame as Game;
use DiplomacyEngine\Players\iPlayer as Player;
use DiplomacyEngine\Orders\iOrder as Order;

interface iTurn {
	/** Add a player */
	public function addOrder(Order $order);

	/** Resolves all the orders in this turn, and determine
	 * who needs to retreat */
	public function resolveAttacks();

	/** Resolves all the retreat orders */
	function resolveRetreats();

	/** Carry out the succesful orders */
	function carryOutOrders();

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

	public function addOrder(Order $order) {
		$this->orders[] = $order;
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
		//$old = $this->game->getTerritories();
		//$this->territories=array();
		foreach ($this->orders as &$o) {
			if (!$o->isValid()) {
			  $o->fail('Invalid');
			}
		}
	}

	/** Iterates through the list of orders, and removes any order
	 * who's source territory is the attack destination of another
	 * player. */
	protected function removeOrdersFromAttackedTerritories() {
		foreach ($this->orders as &$order) {
			if ($order->failed()) continue;
			foreach ($this->orders as $ref) {
				if ($ref->failed()) continue;
				if ($order == $ref) continue;

				if (
					$order->source == $ref>dest
					&& $order->supporting() != $ref->supporting
				) {
					$order->fail("Source territory (". $order->source .") is being acted on by ". $ref->getPlayer() . " in '". $ref . "'");
				}
			}
		}
	}

	/**
	 * Build a list of territories from the Game and place it in $this->territories
	 * with the structure
	 * $territories[territory_id] = array(player1_id => count, player2_id => count, ...)
	 *
	 * Iterate through the orders, and increment the 'count' in the structure above every
	 * time a player attacks a teritory or is supported.
	 *
	 * Then, iterate again through the orders, and mark every order that supports the
	 * winner as "success", and all others as "failed"
	 **/
	protected function resolveOrders() {

	}

	function resolveRetreats() {

	}


	function carryOutOrders() {

	}

}

// vim: ts=3 sw=3 noet :
