#!/usr/bin/env php
<?php
require_once(dirname(__FILE__) . '/../config/config.php');

use DiplomacyOrm\Game;
use DiplomacyOrm\GameQuery;
use DiplomacyOrm\Match;
use DiplomacyOrm\MatchQuery;
use DiplomacyOrm\Turn;
use DiplomacyOrm\TurnQuery;
use DiplomacyOrm\Empire;
use DiplomacyOrm\EmpireQuery;
use DiplomacyOrm\TerritoryTemplate;
use DiplomacyOrm\TerritoryTemplateQuery;
use DiplomacyOrm\Unit;
use DiplomacyOrm\UnitQuery;
use DiplomacyOrm\State;
use DiplomacyOrm\StateQuery;
use Propel\Runtime\ActiveQuery\Criteria;

$opts=getopt('Mm:Tt:Ss:e:u:h', array('help'));
if (array_key_exists('h', $opts) || array_key_exists('help', $opts)) {
	print <<<TEND

Read info from the database

Usage:
	-m match_id
		Specify match_id in order to opperate on current turn.  Cannot be used with -t

	-t turn_id
		Specify turn to operate on.  Cannot be used with -m

	-s state_id
		State id to modify, accepts territory name

	-e empire
		Specify empire

	-u unit_type
		army, fleet, vacant, retreated, none

	-h --help  Print this message


TEND;
	print "Example:\n";
	print "\t". basename(__FILE__) . " -m 1 -s A -e Red -u army\n";

	exit(0);
}

if (array_key_exists('m', $opts) && array_key_exists('t', $opts))
	die("-m and -t are mutally exclusive options\n");
if (!array_key_exists('m', $opts) && !array_key_exists('t', $opts))
	die("Must specify either a match with -m or a territory with -t\n");

if (array_key_exists('m', $opts)) {
	$match = MatchQuery::create()->findPk($opts['m']);
	if (!is_object($match)) die("Invalid match id");
	$turn = $match->getCurrentTurn();
}

if (array_key_exists('t', $opts)) {
	$turn = TurnQuery::create()->findPk($opts['t']);
	if (!is_object($turn)) die("Invalid turn id");
}
// turn should now be set

if (!array_key_exists('s', $opts)) die("Must specify state/territory with -s\n");

$state = StateQuery::create()
	->filterByTurn($turn)
		->filterByPrimaryKey($opts['s'])
	->_or()
		->useTerritoryQuery()
			->filterByName("%{$opts['s']}%", Criteria::LIKE)
		->endUse()
	->findOne();
if (!($state instanceof State)) die("Cannot match {$opts['s']} to a state.\n");
// State should now be set

if (!array_key_exists('e', $opts)) die("Must specify empire");
$empire = EmpireQuery::getByNameOrId($opts['e']);
if (!is_object($empire)) die("Could not match {$opts['e']} to an empire");
// empire should now be set

if (!array_key_exists('u', $opts)) die("Must specify unit type");
$unit_type = Unit::convert($opts['u']);
if ($unit_type === false) die("Invalid unit type {$opts['u']}");
$unit = new Unit($unit_type);
// unit should now be set

$question = sprintf("Currently %s is occupied by %s, continuing will replace its occupier with a \"%s\" from %s.\nContinue? [Y/n] ", $state->getTerritory()->getName(), $state->getOccupier(), $unit, $empire);
$resp = readline($question);
$resp = trim($resp);
if (strtolower($resp) == 'y' || $resp == '') {
	// State table:
	$state->setOccupation($empire, $unit);
	$state->save();
	printf("%s is now occupied by $empire\n", $state->getTerritory()->getName());
}

// vim: ts=4 sts=0 sw=4 noexpandtab :
