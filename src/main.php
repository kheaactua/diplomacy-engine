#!/usr/bin/env php
<?php
namespace DiplomacyEngine;
use DiplomacyEngine\Empires\Empire;
use DiplomacyEngine\Empires\Unit;
use DiplomacyEngine\Territories\Territory;
use DiplomacyEngine\Orders\Order as Order;
use DiplomacyEngine\Orders\Move as Move;
use DiplomacyEngine\Orders\Support as Support;
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
$green = $empires['GRN'];

// Territories
$t_a = Territory::findTerritoryByName($territories, 'A');
$t_b = Territory::findTerritoryByName($territories, 'B');
$t_c = Territory::findTerritoryByName($territories, 'C');
$t_d = Territory::findTerritoryByName($territories, 'D');
$t_e = Territory::findTerritoryByName($territories, 'E');

$match = new Match("The Past", 1812);
$match->setEmpires($empires);
$match->setTerritories($territories);
$match->start();
$turn = $match->getCurrentTurn();
print "\n" . $match ."\n";

$case = 2;
switch ($case) {
	case 1;
		// Test move conflict
		$turn->addOrder(new Move(new Unit('Army'), $red, $t_a, $t_b));
		$turn->addOrder(new Move(new Unit('Army'), $red, $t_a, $t_c));
		$turn->addOrder(new Move(new Unit('Army'), $blue, $t_a, $t_b));
		$turn->addOrder(new Move(new Unit('Army'), $green, $t_e, $t_d));
		break;
	case 2;
		// Test support
		$turn->addOrder(new Move(new Unit('Army'), $red, $t_a, $t_b));
		$turn->addOrder(new Move(new Unit('Army'), $blue, $t_a, $t_b));
		$turn->addOrder(new Support(new Unit('Army'), $green, $red, $t_e, $t_b));
		break;
}


$turn->resolveAttacks();

// Show which orders have failed, etc
$turn->printOrders();

print "\n" . $match ."\n";

// vim: ts=3 sw=3 noet :
