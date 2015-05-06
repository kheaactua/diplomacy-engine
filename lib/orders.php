<?php

namespace DiplomacyEngine\Orders;

interface iOrder  {
	/** Returns the string representation of the order */
	public function __toString();

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

	public function __construct(
		\DiplomacyEngine\Players\Player $player,
		\DiplomacyEngine\Territories\Territory $source,
		\DiplomacyEngine\Territories\Territory $dest
	) {
		$this->player = $player;
		$this->source = $source;
		$this->dest   = $dest;
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

}

// vim: ts=3 sw=3 noet :
