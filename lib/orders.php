<?php

namespace DiplomacyEngine;
use DiplomacyEngine\Unit;
use DiplomacyEngine\iEmpire as Empire;

/**
 * Easy way to see if this order has a
 * destination or not.
 */
interface MultiTerritory  {}
interface SingleTerritory  {}

// abstract class Order implements iOrder {
//
// 	/**
// 	 * Format of the order, uses replacable strings
// 	 *
// 	 * %cmd% %source% %dest%
// 	 */
// 	public $format;
//
// 	/** The command assoiated with this order */
// 	public $cmd = "";
//
// 	/**
// 	 * The source territory
// 	 */
// 	public $source;
//
// 	/**
// 	 * The destination territory
// 	 */
// 	public $dest;
//
// 	/**
// 	 * The empire initiating the order
// 	 */
// 	public $empire;
//
// 	/**
// 	 * Unit/Force type (fleet or army)
// 	 */
// 	protected $unit;
//
// 	/**
// 	 * Whether this order has failed
// 	 */
// 	protected $failed;
//
// 	/**
// 	 * Transcript, mostly for fail messages */
// 	protected $transcript;
//
// 	public function __construct(
// 		Unit $unit,
// 		Empire $empire,
// 		Territory $source,
// 		Territory $dest
// 	) {
// 		$this->unit= $unit;
// 		$this->empire = $empire;
// 		$this->source = $source;
// 		$this->dest   = $dest;
// 		$this->transcript = array();
// 	}
//
// 	public function failed() {
// 		return $this->failed;
// 	}
//
// 	public function getTranscript() {
// 		return $this->transcript;
// 	}
//
// 	public function __toString() {
// 		return "n/a";
// 	}
//
// 	/** Serialize the order into a string using a format */
// 	protected function generateOrder($keys, $vals) {
// 		array_walk($keys, function(&$e) { $e = "/%$e%/"; });
// 		$str = preg_replace($keys, $vals, $this->format);
// 		return $str;
// 	}
//
// 	/** Marks the order as failed */
// 	public function fail($reason='') {
// 		if (strlen($reason))
// 			$this->transcript[] = $reason;
// 		$this->failed = true;
// 	}
//
// 	/**
// 	 * Function to return the empire being supported by this order.
// 	 * This will typically be the empire, except for in support orders.
// 	 */
// 	public function supporting() {
// 		return $this->empire;
// 	}
// 	public function getEmpire() {
// 		return $this->empire;
// 	}
//
// 	public function validate() {
// 		if ($this->failed()) return false;
//
// 		// Does the empire own the source territory
// // TODO make exception for CONVOYS
// 		if ($this->source->getOccupier() != $this->empire) {
// 			$this->fail("$this->empire does not occupy $this->source");
// 		}
// 	}
//
// 	/**
// 	 * Return a list of territories that are involved
// 	 * in this order.
// 	 **/
// 	public function territories() {
// 		return array($this->source, $this->dest);
// 	}
// }
//
// class Move extends Order {
// 	public $format = "%empire% %cmd% %unit% %source%-%dest%";
//
// 	/** The command assoiated with this order */
// 	public $cmd = "MOVE";
//
// 	public function __toString() {
// 		$str = $this->generateOrder(
// 			array('empire', 'unit', 'cmd', 'source', 'dest'),
// 			array($this->empire, $this->unit->__toString(), $this->cmd, $this->source, $this->dest)
// 		);
//
// 		return $str;
// 	}
// }
//
//
// class Support extends Order {
// 	public $format = "%empire% %cmd% %aly% %source%-%dest%";
//
// 	/** The command assoiated with this order */
// 	public $cmd = "SUPPORT";
//
// 	public function __construct(
// 		Unit $unit,
// 		Empire $empire,
// 		Empire $aly,
// 		Territory $source,
// 		Territory $dest
// 	) {
// 		parent::__construct($unit, $empire, $source, $dest);
// 		$this->aly = $aly;
//
// 	}
// 	public function supporting() {
// 		return $this->aly;
// 	}
//
// 	public function __toString() {
// 		$str = $this->generateOrder(
// 			array('empire', 'aly', 'unit', 'cmd', 'source', 'dest'),
// 			array($this->empire, $this->aly, $this->unit->__toString(), $this->cmd, $this->source, $this->dest)
// 		);
//
// 		return $str;
// 	}
//
// }

// vim: ts=3 sw=3 noet :
