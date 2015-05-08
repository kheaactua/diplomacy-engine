<?php

namespace DiplomacyEngine\Orders;

interface iOrder  {
	/** Returns the string representation of the order */
	public function __toString();

	/** Marks the order as failed */
	public function fail($reason);
}

abstract class Order implements iOrder {

	/**
	 * Format of the order, uses replacable strings
	 *
	 * %cmd% %source% %dest%
	 */
	public $format;

	/** The command assoiated with this order */
	public $cmd = "";

	/**
	 * The source territory
	 */
	public $source;

	/**
	 * The destination territory
	 */
	public $dest;

	/**
	 * The player initiating the order
	 */
	public $player;

	/**
	 * Unit/Force type (fleet or army)
	 */
	public $unit_type;

	/**
	 * Whether this order has failed
	 */
	protected $failed;

	/**
	 * Transcript, mostly for fail messages */
	protected $transcript;

	public function __construct(
		$unit_type = UNIT_ARMY,
		\DiplomacyEngine\Players\Player $player,
		\DiplomacyEngine\Territories\Territory $source,
		\DiplomacyEngine\Territories\Territory $dest
	) {
		$this->unit_type = $unit_type;
		$this->player = $player;
		$this->source = $source;
		$this->dest   = $dest;
		$this->transcript = array();
	}

	public function failed() {
		return $this->failed;
	}

	public function __toString() {
		return "n/a";
	}

	/** Serialize the order into a string using a format */
	protected function generateOrder($keys, $vals) {
		array_walk($keys, function(&$e) { $e = "/%$e%/"; });
		$str = preg_replace($keys, $vals, $this->format);
		return $str;
	}

	/** Marks the order as failed */
	public function fail($reason) {
		$this->transcript[] = $reason;
		$this->failed = true;
	}

	/**
	 * Function to return the player being supported by this order.
	 * This will typically be the player, except for in support orders.
	 */
	public function supporting() {
		return $this->player;
	}
	public function getPlayer() {
		return $player;
	}

	abstract public function isValid();

}

class Move extends Order {
	public $format = "%cmd% %source%-%dest%";

	/** The command assoiated with this order */
	public $cmd = "MOVE";

	public function __toString() {
		$str = $this->generateOrder(
			array('cmd', 'source', 'dest'),
			array($this->cmd, $this->source, $this->dest)
		);

		return $str;
	}

	public function isValid() {
		return true;	
	}
}

// vim: ts=3 sw=3 noet :
