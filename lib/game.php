<?php

namespace DiplomacyEngine\Territories;

use DiplomacyEngine\Players\iPlayer as Player;

interface iGame {
	/** Add a player */
	public function addPlayer(Player $player);

	/** Start the game */
	public function start();

	/** Advance to the next turn */
	public function next();
}

class Game implements iGame {

}

// vim: ts=3 sw=3 noet :
