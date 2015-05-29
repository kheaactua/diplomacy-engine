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
use DiplomacyOrm\State;
use DiplomacyOrm\StateQuery;
use DiplomacyOrm\Unit;
use Propel\Runtime\ActiveQuery\Criteria;

$opts=getopt('Gg:Mm:Tt:Ss:e:h', array('help'));
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
			$str .= $game;
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
			$str .= sprintf("%0.3d: %s\n", $o->getPrimaryKey(), $o->getName());
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
			->filterByPrimaryKey($opts['e'])
		->_or()
			->filterByName($opts['e'] . '%', Criteria::LIKE)
		->findOne();
	if (!is_object($empire)) die("Invalid empire");

	$states = StateQuery::create()
		->filterByTurn($turn)
		->filterByOccupier($empire)
		->find();

	$str = '';
	if (count($states)) {
		$str .= "$empire units in $turn:\n";
		$str .= str_pad('Empire', 12) .	str_pad('Unit', 7) .           str_pad('Territory', 30) ."\n";
		$str .= str_pad('', 11, '-') .' '. str_pad('', 6, '-') . ' ' . str_pad('', 29, '-')	."\n";
		// $str .= str_pad('Empire', 12) .	str_pad('Unit', 7) .	 str_pad('Prev Territory', 30) . str_pad('Territory', 30) ."\n";
		// $str .= str_pad('', 11, '-') .' '. str_pad('', 6, '-') .' '. str_pad('', 29, '-') . ' '	. str_pad('', 29, '-')	."\n";
		foreach ($states as $state) {
			$str .= str_pad($state->getOccupier(), 12);
			$str .= str_pad(new Unit($state->getUnitType()), 6);

			if (is_object($state)) {
				$currentTerritory = $state->getTerritory()->__toString();
			} else {
				// Retreated..
				$currentTerritory = '<stateless>';
			}

			/*
			if (is_object($unit->getLastState())) {
				$lastTerritory = $unit->getLastState()->getTerritory()->__toString();
			} else {
				// Retreated..
				$lastTerritory = '';
			}
			$str .= str_pad($lastTerritory, 31) . str_pad($currentTerritory, 30) . "\n";
			*/
			$str .= str_pad($currentTerritory, 30) . "\n";

		}
		$str .= "\n";
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
	$turns = $match->getTurnsRelatedByMatchId();
	$str = '';
	if (count($turns)) {
		foreach ($turns as $turn) {
			$str .= sprintf("%0.3d: %s\n", $turn->getPrimaryKey(), $turn);
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
	$str = '';
	$states = StateQuery::create()
		->useTurnQuery()
			->filterByMatch($match)
		->endUse()
		->filterByTurn($match->getCurrentTurn())
		->find();
	if (count($states)) {
		foreach ($states as $o) {
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
				->filterByPrimaryKey($opts['s'])
			->_or()
				->filterByName($opts['s'], Criteria::LIKE)
			->find();
	}
	$states = StateQuery::create()
		->filterByTerritory($territories, Criteria::IN)
		->filterByTurn($match->getCurrentTurn())
		->find();
	$str = '';
	if (count($states)) {
		foreach ($states as $o) {
			$str .= $o . "\n";
		}
	} else {
		$str .= "$match has no territories\n";
	}
	print $str;
	exit(0);
}


if (array_key_exists('m', $opts)) {
	if (array_key_exists('m', $opts)) {
		$match = MatchQuery::create()->findPk($opts['m']);
		if (!is_object($match)) die("Invalid match id");
	}
	print $match;
	exit(0);
}


if (array_key_exists('t', $opts)) {
	if (array_key_exists('t', $opts)) {
		$turn = TurnQuery::create()->findPk($opts['t']);
		if (!is_object($turn)) die("Invalid turn id");
	}

	$states = StateQuery::create()
		->filterByTurn($turn)
		->filterByOccupierId(null, Criteria::ISNOTNULL)
		->orderByOccupierId()
		->orderByTerritoryId()
		->find();
	$str = '';
	if (count($states)) {
		$str .= "Units in $turn:\n";
		$str .= str_pad('Empire', 12) .	str_pad('Unit', 13) .	 str_pad('Territory', 30) ."\n";
		$str .= str_pad('', 11, '-') .' '. str_pad('', 11, '-') . ' '	. str_pad('', 29, '-')	."\n";
		foreach ($states as $state) {
			$str .= str_pad($state->getOccupier(), 12);
			$type = new Unit($state->getUnitType());
			$str .= str_pad($type, 13);

			if (is_object($state)) {
				$currentTerritory = $state->getTerritory()->__toString();
			} else {
				// Retreated..
				$currentTerritory = '<stateless>';
			}
			$str .= str_pad($currentTerritory, 30) . "\n";
		}
		$str .= "\n";
	} else {
		$str .= "$empire has no units in $turn\n";
	}
	print $str;
	exit(0);
}

print "Parameters not matched.\n";

// vim: ts=4 sts=0 sw=4 noexpandtab :
