#!/usr/bin/env php
<?php
namespace DiplomacyEngine;
use DiplomacyEngine\Empires\Empire as Empire;
use DiplomacyEngine\Territories\Territory as Territory;
use DiplomacyEngine\Orders\Order as Order;
use DiplomacyEngine\Orders\Move as Move;
use DiplomacyEngine\Match\Match as Match;

require_once( __DIR__ . '/../config/config.php');


//$canada = new Territory('Canada');
//$usa    = new Territory('USA');

//$move = new Move($empire, $canada, $usa);
//print "Created '$move'\n";


// Create Terriroties
//$t_objs = json_decode(file_get_contents('territories-real.json'), false);

// Create empires
$p_objs = json_decode(file_get_contents('empires-test.json'), false);
$empires = Empire::loadEmpires($p_objs);

$t_objs = json_decode(file_get_contents('territories-test.json'), false);
$territories = Territory::loadTerritories($empires, $t_objs);

// $texas   = Territory::findTerritoryByName($territories, 'Texas');
// $sequoia = Territory::findTerritoryByName($territories, 'Sequoia');
// $ohio    = Territory::findTerritoryByName($territories, 'Ohio');

// print "Texas-Sequoia? " . ($texas->isNeighbour($sequoia) ? 'PASS':'FAIL') . "\n";
// print "Sequoia-Texas? " . ($sequoia->isNeighbour($texas) ? 'PASS':'FAIL') . "\n";
// print "Texas-Ohio? "    . ($texas->isNeighbour($ohio) ? 'FAIL':'PASS') . "\n";

// Empires
$red = $empires['RED'];
$blue = $empires['BLU'];

// Territories
$t_a = Territory::findTerritoryByName($territories, 'A');
$t_b = Territory::findTerritoryByName($territories, 'B');
$t_c = Territory::findTerritoryByName($territories, 'C');
$t_d = Territory::findTerritoryByName($territories, 'D');

$match = new Match("The Past", 1812);
$match->setEmpires($empires);
$match->setTerritories($territories);
$match->start();
$turn = $match->getCurrentTurn();
print "\n" . $match ."\n";

$turn->addOrder(new Move(UNIT_ARMY, $red, $t_a, $t_b));
$turn->addOrder(new Move(UNIT_ARMY, $blue, $t_a, $t_b));
$turn->resolveAttacks();

// Show which orders have failed, etc
$turn->printOrders();

print "\n" . $match ."\n";

// vim: ts=3 sw=3 noet :
