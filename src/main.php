#!/usr/bin/env php
<?php
//use DiplomacyEngine\Empires\Empire;
//use DiplomacyEngine\Territories\Territory;
//use DiplomacyEngine\Match\Match as Match;
// use DiplomacyEngine\Orders\Order as Order;
// use DiplomacyEngine\Orders\Move as Move;
// use DiplomacyEngine\Orders\Support as Support;

use DiplomacyOrm\Empire;
use DiplomacyOrm\EmpireQuery;
use DiplomacyEngine\Empires\Unit;
use DiplomacyOrm\TerritoryTemplate;
use DiplomacyOrm\TerritoryTemplateQuery;
use DiplomacyOrm\Match;
use DiplomacyOrm\Game;
use DiplomacyOrm\GameQuery;

use DiplomacyOrm\Move;
use DiplomacyOrm\Support;

require_once( __DIR__ . '/../config/config.php');

// Clear the DB first
use Propel\Runtime\Propel;
$con = Propel::getWriteConnection(DiplomacyOrm\Map\GameTableMap::DATABASE_NAME);
$sql = "DELETE FROM game";
$stmt = $con->prepare($sql);
$stmt->execute();


// Create or use
$game = null;
$games = GameQuery::create()->findByName('test');
if (count($games) == 1) {
	$game = $games[0];
} elseif (!count($games)) {
	$game_name = 'test';
	$p_objs = json_decode(file_get_contents($config->host->data . "/$game_name/empires.json"), false);
	$t_objs = json_decode(file_get_contents($config->host->data . "/$game_name/territories.json"), false);


	$game = Game::create($game_name, 1861, 'spring');
	$game->loadEmpires($p_objs);
	$game->loadTerritories($t_objs);

	$game->save();
} else {
	trigger_error("A duplicate got created!");
}


// $texas   = Territory::findTerritoryByName($territories, 'Texas');
// $sequoia = Territory::findTerritoryByName($territories, 'Sequoia');
// $ohio    = Territory::findTerritoryByName($territories, 'Ohio');

// print "Texas-Sequoia? " . ($texas->isNeighbour($sequoia) ? 'PASS':'FAIL') . "\n";
// print "Sequoia-Texas? " . ($sequoia->isNeighbour($texas) ? 'PASS':'FAIL') . "\n";
// print "Texas-Ohio? "    . ($texas->isNeighbour($ohio) ? 'FAIL':'PASS') . "\n";

// Empires
$red   = EmpireQuery::create()->findPk('RED');
$blue  = EmpireQuery::create()->findPk('BLU');
$green = EmpireQuery::create()->findPk('GRN');

// Territories
$t_a = TerritoryTemplateQuery::create()->findByName('A');
$t_b = TerritoryTemplateQuery::create()->findByName('B');
$t_c = TerritoryTemplateQuery::create()->findByName('C');
$t_d = TerritoryTemplateQuery::create()->findByName('D');
$t_e = TerritoryTemplateQuery::create()->findByName('E');

$match = new Match("The Past", 1812);
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
