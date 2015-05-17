<?php

namespace DiplomacyEngine\Match;

use DiplomacyEngine\Empires\iEmpire as Empire;
use DiplomacyEngine\Turns\Turn as Turn;

interface iMatch {
	/** Add a empire */
	public function addEmpire(Empire $empire);

	/** Start the match */
	public function start();

	/** Advance to the next turn */
	public function next();

	/** Return the current turn **/
	public function getCurrentTurn();

	/** Setters */
	public function setEmpires(array $empires);
	public function setTerritories(array $territories);

	public function __toString();
}

class Match implements iMatch {
	// protected $name;
	// protected $year;
	// protected $time;
   //
	// protected $empires;
	// protected $currentTurn;
	// protected $turns;
   //
	// protected $seasons;
	// /**
	//  * New match.
	//  * @param $name Name of the match/map
	//  * @param $year Stating year, i.e. 1846
	//  * @param $season Starting season (0=spring, 1=fall)
	//  **/
	// public function __construct($name, $year, $season=0) {
	// 	$this->name = $name;
	// 	$this->year = $year;
	// 	$this->time = $season ? 1 : 0;
   //
	// 	$this->empires = array();
   //
	// 	$this->seasons = array('Spring', 'Fall');
	// 	$this->currentTurn=null;
	// 	$this->turns = array();
	// }
   //
	// public function addEmpire(Empire $empire) {
	// 	$this->empires[] = $empire;
	// }
   //
	// public function next() {
	// 	$this->time++;
	// 	$this->currentTurn = new Turn($this, $this->time%2);
	// 	$this->turns[] = $this->currentTurn;
	// }
	// public function start() {
	// 	$this->next();
	// }
   //
   //
	// public function getCurrentTurn() {
	// 	return $this->currentTurn;
	// }
   //
   //
	// public function setEmpires(array $empires) {
	// 	$this->empires = $empires;
	// }
	// public function setTerritories(array $territories) {
	// 	$this->territories = $territories;
	// }
	// public function getTerritories() {
	// 	return $this->territories;
	// }
   //
	// public function state() {
	// 	$state = array();
	// 	foreach ($this->territories as $t) {
	// 		$state[$t->getTerritoryId()] = array($t, $t->getOccupier());
	// 	}
	// 	return $state;
	// }
   //
	// public function __toString() {
	// 	$str = "$this->name: ";
	// 	$str .= $this->seasons[$this->time % 2] . " ";
	// 	$str .= $this->year + floor($this->time/2) . "\n";
   //
	// 	$state = $this->state();
	// 	$str .= str_pad('Territory', 30) . str_pad('Empire', 12) . str_pad('Unit', 10) . "\n";
	// 	$str .= str_pad('', 29, '-') . ' ' . str_pad('', 11, '-') . ' '. str_pad('', 10, '-') . "\n";
	// 	foreach ($state as $s) {
	// 		$str .= str_pad($s[0], 30) . str_pad($s[1], 12) . $s[0]->getUnit() . "\n";
	// 	}
	// 	return $str;
	// }
}

// vim: ts=3 sw=3 noet :
