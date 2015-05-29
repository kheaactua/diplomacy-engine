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

$opts=getopt('Gg:Mm:Tt:Sh', array('help'));
if (array_key_exists('h', $opts) || array_key_exists('help', $opts)) {
	print <<<TEND

Read info from the database

Usage:
	-G
	Show all games

	-g game_id
		Show game game_id

	-M
		Show all matches

	-m match_id
		Show game game_id

	-m match_id -T
		Show match turns

	-m match_id -e empire_id
		Show information about an empire's current state

	-t turn_id -e empire_id
		Show information about an empire's state at a given turn

	-m match_id -S
		Show the territories in a match

	-m match_id -s <territory>
		Filter the territories by a name or an ID

	-h --help  Print this message

TEND;

	exit(0);
}

if (array_key_exists('G', $opts)) {
	$games = GameQuery::create()->find();
	$str = '';
	if (count($games)) {
		$str .= "Available games:\n";
		$str .= str_repeat('-', 25) . "\n";
		foreach ($games as $game) {
			print $game;
		}
	} else {
		$str .= "The system has no games\n";
	}
	print $str;
	exit(0);
}

if (array_key_exists('M', $opts)) {
	$objs = MatchQuery::create()->find();
	$str = '';
	if (count($objs)) {
		$str .= "Available Matches:\n";
		$str .= str_repeat('-', 25) . "\n";
		foreach ($objs as $o) {
			$str .= $o->getName();
		}
	} else {
		$str .= "The system has no matches\n";
	}
	print $str;
	exit(0);
}

if (array_key_exists('e', $opts) &&
	(array_key_exists('t', $opts) || array_key_exists('m', $opts))
) {

	if (array_key_exists('m', $opts)) {
		$match = MatchQuery::create()->findPk($opts['m']);
		if (!is_object($match)) die("Invalid match id");
		$turn = $match->getCurrentTurn();
	}

	if (array_key_exists('t', $opts)) {
		$turn = TurnQuery::create()->findPk($opts['t']);
		if (!is_object($turn)) die("Invalid turn id");
	}

	$empire = EmpireQuery::create()
			->filterByPk($opts['e'])
		->_or()
			->filterByName($opts['e'] . '%', Criteria::LIKE)
		->findOne();
	if (!is_object($empire)) die("Invalid empire");

	$units = UnitQuery::create()
		->filterByTurn($turn)
		->filterByEmpire($empire)
		->find();
	$str = '';
	if (count($units)) {
		$str .= "$empire unit's in $turn:\n";
		$str .= str_pad('Empire', 12) .	str_pad('Unit', 7) .	 str_pad('Prev Territory', 30) . str_pad('Territory', 30) ."\n";
		$str .= str_pad('', 11, '-') .' '. str_pad('', 6, '-') .' '. str_pad('', 29, '-') . ' '	. str_pad('', 29, '-')	."\n";
		foreach ($units as $unit) {
			$str .= str_pad($unit->getState()->getOccupier(), 12);
			$str .= str_pad($unit->getUnitType(), 6);

			if (is_object($unit->getState())) {
				$currentTerritory = $unit->getState()->getTerritory()->__toString();
			} else {
				// Retreated..
				$currentTerritory = '<stateless>';
			}

			if (is_object($unit->getLastState())) {
				$lastTerritory = $unit->getLastState()->getTerritory()->__toString();
			} else {
				// Retreated..
				$lastTerritory = '';
			}
			$str .= str_pad($lastTerritory, 31) . str_pad($currentTerritory, 30) . "\n";
		}
		$str .= $str;
	} else {
		$str .= "$empire has no units in $turn\n";
	}
	print $str;
	exit(0);
}

if (array_key_exists('m', $opts) && array_key_exists('T', $opts)) {
	if (array_key_exists('m', $opts)) {
		$match = MatchQuery::create()->findPk($opts['m']);
		if (!is_object($match)) die("Invalid match id");
		$turn = $match->getCurrentTurn();
	}
	$turns = $match->getTurns();
	if (count($turns)) {
		foreach ($turns as $turn) {
			$str .= "$turn\n";
		}
	} else {
		$str .= "$match has no turns\n";
	}
	print $str;
	exit(0);
}


if (array_key_exists('m', $opts) && array_key_exists('S', $opts)) {
	if (array_key_exists('m', $opts)) {
		$match = MatchQuery::create()->findPk($opts['m']);
		if (!is_object($match)) die("Invalid match id");
	}
	$states = StateQuery::create()
		->useTurnQuery()
			->filterByMatch($match)
		->endUse()
		->filterByTurn($match->getCurrentTurn())
		->find();
	if (count($states)) {
		foreach ($state as $o) {
			$str .= $o . "\n";
		}
	} else {
		$str .= "$match has no territories\n";
	}
	print $str;
	exit(0);
}

if (array_key_exists('m', $opts) && array_key_exists('s', $opts)) {
	if (array_key_exists('m', $opts)) {
		$match = MatchQuery::create()->findPk($opts['m']);
		if (!is_object($match)) die("Invalid match id");
	}
	if (array_key_exists('s', $opts)) {
		$territories = TerritoryTemplateQuery::create()
				->findPk($opts['s'])
			->_or()
				->filterByName($opts['s'], Criteria::LIKE)
			->find();
	}
	$states = StateQuery::create()
		->filterByTerritor($territories, Criteria::IN)
		->filterByTurn($match->getCurrentTurn())
		->find();
	if (count($states)) {
		foreach ($state as $o) {
			$str .= $o . "\n";
		}
	} else {
		$str .= "$match has no territories\n";
	}
	print $str;
	exit(0);
}
// vim: ts=4 sts=0 sw=4 noexpandtab :