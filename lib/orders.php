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
	 * The empire initiating the order
	 */
	public $empire;

	/**
	 * Unit/Force type (fleet or army)
	 */
	public $unit;

	/**
	 * Whether this order has failed
	 */
	protected $failed;

	/**
	 * Transcript, mostly for fail messages */
	protected $transcript;

	public function __construct(
		$unit= UNIT_ARMY,
		\DiplomacyEngine\Empires\Empire $empire,
		\DiplomacyEngine\Territories\Territory $source,
		\DiplomacyEngine\Territories\Territory $dest
	) {
		$this->unit= $unit;
		$this->empire = $empire;
		$this->source = $source;
		$this->dest   = $dest;
		$this->transcript = array();
	}

	public function failed() {
		return $this->failed;
	}

	public function getTranscript() {
		return $this->transcript;
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
	 * Function to return the empire being supported by this order.
	 * This will typically be the empire, except for in support orders.
	 */
	public function supporting() {
		return $this->empire;
	}
	public function getEmpire() {
		return $empire;
	}

	public function validate() {
		if ($this->failed()) return false;

		// Does the empire own the source territory
		if ($this->source->getOccupier() != $this->empire) {
			$this->fail("$this->empire does not occupy $this->source");
		}
	}

}

class Move extends Order {
	public $format = "%empire% %cmd% %unit% %source%-%dest%";

	/** The command assoiated with this order */
	public $cmd = "MOVE";

	public function __toString() {
		$str = $this->generateOrder(
			array('empire', 'unit', 'cmd', 'source', 'dest'),
			array($this->empire, $this->unit==UNIT_ARMY?'ARMY':'FLEET', $this->cmd, $this->source, $this->dest)
		);

		return $str;
	}

}

// vim: ts=3 sw=3 noet :
