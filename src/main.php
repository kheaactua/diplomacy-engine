#!/usr/bin/env php
<?php
namespace DiplomacyEngine;
use DiplomacyEngine\Players\Player as Player;
use DiplomacyEngine\Territories\Territory as Territory;
use DiplomacyEngine\Orders\Order as Order;
use DiplomacyEngine\Game\Game as Game;

require_once( __DIR__ . '/../config/config.php');


$player = new Player('Matt');
//$canada = new Territory('Canada');
//$usa    = new Territory('USA');

//$move = new Move($player, $canada, $usa);
//print "Created '$move'\n";


// Create Terriroties
$t_objs = json_decode(file_get_contents('territories-real.json'), false);
$territories = Territories\Territory::createTerritories($t_objs);

$texas   = Territory::findTerritoryByName($territories, 'Texas');
$sequoia = Territory::findTerritoryByName($territories, 'Sequoia');
$ohio    = Territory::findTerritoryByName($territories, 'Ohio');

print "Texas-Sequoia? " . ($texas->isNeighbour($sequoia) ? 'PASS':'FAIL') . "\n";
print "Sequoia-Texas? " . ($sequoia->isNeighbour($texas) ? 'PASS':'FAIL') . "\n";
print "Texas-Ohio? "    . ($texas->isNeighbour($ohio) ? 'FAIL':'PASS') . "\n";


$game = new Game("The past", 1812);

// vim: ts=3 sw=3 noet :
