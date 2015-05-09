<?php

namespace DiplomacyEngine\Turns;

use DiplomacyEngine\Match\iMatch as Match;
use DiplomacyEngine\Empires\iEmpire as Empire;
use DiplomacyEngine\Orders\iOrder as Order;

interface iTurn {
	/** Add a empire */
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

	protected $match;
	protected $turn;
	protected $orders;

	// /** Array representing the territories (but not Territory objects) used
	//  * to help resolve the fate of the Territory objects. */
	// protected $territories;

	/**
	 * Constructs a new turn.
	 *
	 * @param iMatch The match being played
	 * @param int $turn The number of the turn.  Even represents spring, odd represents fall
	 **/
	public function __construct(Match $match, $turn) {
		$this->match = $match;
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
		//$old = $this->match>getTerritories();
		//$this->territories=array();
		foreach ($this->orders as &$o) {
			if (!$o->validate()) {
			  $o->fail('Invalid');
			}
		}
	}

	/** Iterates through the list of orders, and removes any order
	 * who's source territory is the attack destination of another
	 * empire. */
	protected function removeOrdersFromAttackedTerritories() {
		foreach ($this->orders as &$order) {
			if ($order->failed()) continue;
			foreach ($this->orders as $ref) {
				if ($ref->failed()) continue;
				if ($order == $ref) continue;

				if (
					$order->source == $ref->dest
					&& $order->supporting() != $ref->supporting
				) {
					$order->fail("Source territory (". $order->source .") is being acted on by ". $ref->getEmpire() . " in '". $ref . "'");
				}
			}
		}
	}

	/**
	 * Build a list of territories from the Match and place it in $this->territories
	 * with the structure
	 * $territories[territory_id] = array(empire1_id => count, empire2_id => count, ...)
	 *
	 * Iterate through the orders, and increment the 'count' in the structure above every
	 * time a empire attacks a teritory or is supported.
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

	public function printOrders() {
		$str = "Orders:\n";
		foreach ($this->orders as $o) {
			$str .= str_pad($o, 40) . ($o->failed()?'FAIL':'PASS') . "\n";
			if ($o->failed()) {
				$transcript = $o->getTranscript();
				foreach ($transcript as $t) {
					$str .= " - $t\n";
				}
			}
		}
		print $str."\n";
	}

}

// vim: ts=3 sw=3 noet :
