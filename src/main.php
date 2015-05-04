#!/usr/bin/env php
<?php
namespace DiplomacyEngine;
use DiplomacyEngine\Players\Player as Player;

require_once( __DIR__ . '/../config/config.php');


$player = new Player('Matt');
//$canada = new Territories\Territory('Canada');
//$usa    = new Territories\Territory('USA');

//$move = new Orders\Move($player, $canada, $usa);
//print "Created '$move'\n";


// Create Terriroties
$t_objs = json_decode(file_get_contents('territories-real.json'), false);
$territories = Territories\Territory::createTerritories($t_objs);

$texas   = Territories\Territory::findTerritoryByName($territories, 'Texas');
$sequoia = Territories\Territory::findTerritoryByName($territories, 'Sequoia');
$ohio    = Territories\Territory::findTerritoryByName($territories, 'Ohio');

print "Texas-Sequoia? " . ($texas->isNeighbour($sequoia) ? 'PASS':'FAIL') . "\n";
print "Sequoia-Texas? " . ($sequoia->isNeighbour($texas) ? 'PASS':'FAIL') . "\n";
print "Texas-Ohio? "    . ($texas->isNeighbour($ohio) ? 'FAIL':'PASS') . "\n";


// vim: ts=3 sw=3 noet :
