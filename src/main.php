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
use DiplomacyEngine\Unit;
use DiplomacyOrm\TerritoryTemplate;
use DiplomacyOrm\TerritoryTemplateQuery;
use DiplomacyOrm\Map\TerritoryTemplateTableMap;
use DiplomacyOrm\Match;
use DiplomacyOrm\State;
use DiplomacyOrm\StateQuery;
use DiplomacyOrm\Game;
use DiplomacyOrm\GameQuery;

use DiplomacyOrm\Move;
use DiplomacyOrm\Support;

use Propel\Runtime\ActiveQuery\Criteria;

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
//$config->system->db->useDebug(true);
$red   = EmpireQuery::create()->filterByGame($game)->filterByAbbr('RED')->findOne();
$blue  = EmpireQuery::create()->filterByGame($game)->filterByAbbr('BLU')->findOne();
$green = EmpireQuery::create()->filterByGame($game)->filterByAbbr('GRN')->findOne();


$match = Match::create($game, "Matt Test");
$turn = $match->getCurrentTurn();
print "\n" . $match ."\n";

// Territories
// Crate the $t_<territory> magic variables.
$t_names=array('A', 'B', 'C', 'D', 'E');
foreach ($t_names as $n) {
	$c = new Criteria; $c->add(TerritoryTemplateTableMap::COL_NAME, $n);
	$tt = $game->getGameTerritories($c);
	$varname  = "t_".strtolower($n);
	$$varname = StateQuery::create()->filterByTerritory($tt)->findOne();
}

$case = 2;
switch ($case) {
	case 1;
		// Test move conflict
		$turn->addOrder(Move::createNS($red,    new Unit('Army'),  $t_a,  $t_b));
		$turn->addOrder(Move::createNS($red,    new Unit('Army'),  $t_a,  $t_c));
		$turn->addOrder(Move::createNS($blue,   new Unit('Army'),  $t_a,  $t_b));
		$turn->addOrder(Move::createNS($green,  new Unit('Army'),  $t_e,  $t_d));
		break;
	case 2;
		// Test support
		$turn->addOrder(Move::createNS($red,       new Unit('Army'),  $t_a,              $t_b));
		$turn->addOrder(Move::createNS($blue,      new Unit('Army'),  $t_a,              $t_b));
		$turn->addOrder(Support::createNS($green,  $red,              new Unit('Army'),  $t_e,    $t_b));
		break;
}
$turn->save();

$turn->resolveAttacks();

// Show which orders have failed, etc
$turn->printOrders();

print "\n" . $match ."\n";

// vim: ts=3 sw=3 noet :
