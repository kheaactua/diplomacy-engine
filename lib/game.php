<?php

namespace DiplomacyEngine\Game;

use DiplomacyEngine\Players\iPlayer as Player;

interface iGame {
	/** Add a player */
	public function addPlayer(Player $player);

	/** Start the game */
	public function start();

	/** Advance to the next turn */
	public function next();

	public function __toString();
}

class Game implements iGame {
	protected $name;
	protected $year;
	protected $time;

	protected $players;
	
	protected $seasons;
	/**
	 * New game.
	 * @param $name Name of the game/map
	 * @param $year Stating year, i.e. 1846
	 * @param $season Starting season (0=spring, 1=fall)
	 **/
	public function __construct($name, $year, $season=0) {
		$this->name = $name;
		$this->year = $year;
		$this->time = $season ? 1 : 0;

		$this->players = array();

		$this->seasons = array('Spring', 'Fall');
	}

	public function addPlayer(Player $player) {
		$this->players[] = $player;
	}

	public function next() {
		$this->time++;
	}
	public function start() {

	}

	public function __toString() {
		$str = "$this->name ";
		$str .= $this->seasons[$this->time % 2] . " ";
		$str .= $this->year + floor($this->time/2);
	}

}

// vim: ts=3 sw=3 noet :
