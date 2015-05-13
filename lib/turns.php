<?php

namespace DiplomacyEngine\Turns;

use DiplomacyEngine\Match\iMatch as Match;
use DiplomacyEngine\Empires\iEmpire as Empire;
use DiplomacyEngine\Orders\iOrder as Order;
use DiplomacyEngine\Orders;

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
			  //$o->fail('Invalid');
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
	 * Most territories will not be acted upon.  This function filters our
	 * list of territories by which ones will be acted upon.
	 *
	 * return array(iTerritory)
	 */
	protected function getActiveTerritories() {
		$ret = array();
		foreach ($this->orders as $o) {
			$ts = $o->territories();
			$ret = array_merge($ret, $ts);
		}
		return $ret;
	}

	/**
	 * Build an array of territories and which orders are acting upon them
	 * @return array(tid=>array('territory' => iTerritory, 'orders' => array(Orders), ..)
	 */
	protected function getTerritoryOrderMap($include_failed = true) {
		$ret = array();

		// Could make these loops more efficient, but doing it in the
		// "easiest to read" way.
		// First filter the territories down
		$ters = $this->getActiveTerritories();

		foreach ($ters as &$t) {
			$ret[$t->getId()] = array('territory' => $t, 'orders' => array(), 'tally' => new PlayerMap($t->getOccupier()));

			foreach ($this->orders as &$o) {
				$affected = $o->territories();
				foreach ($affected as $t2) {
					if ($t2 == $t) {
						if (!$include_failed && $o->failed()) continue;
						$ret[$t->getId()]['orders'][] = $o;
					}
				}
			}
		}
		return $ret;
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
		// Limit our iterations to territories in play
		$ters = $this->getTerritoryOrderMap(false); // false to skip failed orders
		foreach ($ters as $t_id=>&$map) {
			$t     = $map['territory'];
			$tally = $map['tally'];

			foreach ($map['orders'] as &$o) {
				if ($o->failed()) continue;
				if ($o instanceof Orders\Move) {
					if ($o->dest == $t) {
print "Order $o\n";
						$tally->inc($o->getEmpire());
					}
				} else {
					trigger_error("Not sure how to perform [". get_class($o). "] $o");
				}
			}
		}

		foreach ($ters as $t_id=>&$map) {
			// Find the winner of each territory
			$winner = $map['tally']->findWinner()->winner();

			if (is_null($winner)) {
				continue;
			}

			// Loop through the orders again, fail all orders
			// who supported a loser, or lost a battle

			foreach ($map['orders'] as &$o) {
				if ($o->failed()) continue;
				if ($o instanceof Orders\Move) {
					if ($o->dest == $t && $o->getEmpire() != $winner) {
						$o->fail("Lost battle for $t to $winner");
					}
				} elseif ($o instanceof Orders\Support) {
					if ($o->dest == $t && $o->supporting() != $winner) {
						$o->fail("Supported ". $o->supporting() . " in failed campaign against $t that $winner won");
					}
				} else {
					trigger_error("Not sure how to perform [". get_class($o) . "]$o");
				}
			}
		}

		// Debug
		print "\n";
		print "Resolutions before retreats:\n";
		foreach ($ters as $t_id=>&$map) {
			print "{$map['territory']}, tally:\n";
			print $map['tally'];
			print "\n";
		}


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

/**
 * Little helper class to add up the 'winners' on a territory during order
 * resolution.  Implementing stuff here, instead of a bunch of array code
 * in the resolution function
 */
class PlayerMap {
	protected $map;
	protected $winner;
	public function __construct(Empire $default = null) {
		$this->map = array();

		if (!is_null($default)) {
print "Adding default point to $default\n";
			$this->inc($default); // add starting 'defenders' point
			$this->winner = $default; // Set as default winner
		} else {
			$this->winner = null;
		}
	}
	public function inc(Empire $empire) {
		if (!array_key_exists($empire->getId(), $this->map))
			$this->map[$empire->getId()] = array('tally' => 0, 'empire' => $empire);

//print "Incrementing $empire\n";
		$this->map[$empire->getId()]['tally']++;
	}
	public function findWinner() {
		$c = -1;
		foreach ($this->map as $empire_id=>$arr) {
			if ($arr['tally'] > $c) {
print "{$arr['empire']}={$arr['tally']} > $c\n";
				$c = $arr['tally'];
				$this->winner = $arr['empire'];
			}
		}
		return $this;
	}
	public function winner() {
		return $this->winner;
	}
	public function __toString() {
		$str = '';
		foreach ($this->map as $arr) {
			$str .= str_pad($arr['empire'], 12) . ' ' . sprintf('%0.2d', $arr['tally']) . ($arr['empire'] == $this->winner ? ' (winner)':'') . "\n";
		}
		return $str;
	}
}

// vim: ts=3 sw=3 noet :
