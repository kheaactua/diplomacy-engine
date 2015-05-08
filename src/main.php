#!/usr/bin/env php
<?php
namespace DiplomacyEngine;
use DiplomacyEngine\Players\Player as Player;
use DiplomacyEngine\Territories\Territory as Territory;
use DiplomacyEngine\Orders\Order as Order;
use DiplomacyEngine\Orders\Move as Move;
use DiplomacyEngine\Game\Game as Game;

require_once( __DIR__ . '/../config/config.php');


//$canada = new Territory('Canada');
//$usa    = new Territory('USA');

//$move = new Move($player, $canada, $usa);
//print "Created '$move'\n";


// Create Terriroties
//$t_objs = json_decode(file_get_contents('territories-real.json'), false);

// Create players
$p_objs = json_decode(file_get_contents('empires-test.json'), false);
$players = Player::loadPlayers($p_objs);

$t_objs = json_decode(file_get_contents('territories-test.json'), false);
$territories = Territory::loadTerritories($players, $t_objs);

// $texas   = Territory::findTerritoryByName($territories, 'Texas');
// $sequoia = Territory::findTerritoryByName($territories, 'Sequoia');
// $ohio    = Territory::findTerritoryByName($territories, 'Ohio');

// print "Texas-Sequoia? " . ($texas->isNeighbour($sequoia) ? 'PASS':'FAIL') . "\n";
// print "Sequoia-Texas? " . ($sequoia->isNeighbour($texas) ? 'PASS':'FAIL') . "\n";
// print "Texas-Ohio? "    . ($texas->isNeighbour($ohio) ? 'FAIL':'PASS') . "\n";

// Players
$red = $players['RED'];
$blue = $players['BLU'];

// Territories
$t_a = Territory::findTerritoryByName($territories, 'A');
$t_b = Territory::findTerritoryByName($territories, 'B');
$t_c = Territory::findTerritoryByName($territories, 'C');
$t_d = Territory::findTerritoryByName($territories, 'D');

$game = new Game("The past", 1812);
$game->setPlayers($players);
$game->setTerritories($territories);
$game->start();
$turn = $game->getCurrentTurn();

$turn->addOrder(new Move(UNIT_ARMY, $red, $t_a, $t_b));
$turn->resolveAttacks();

// vim: ts=3 sw=3 noet :
