<?php

namespace DiplomacyOrm;

use DiplomacyOrm\Base\Unit as BaseUnit;
use DiplomacyOrm\Map\UnitTableMap;

/**
 * Skeleton subclass for representing a row from the 'unit' table.
 *
 * Lagragian perspective of the unit.  Keep track of every army/fleet in the game, and they associated states.  Also save the last state - this was the main driver for this, as it allows us to better limit moves.  Having this will likely expand future capabilities as well.
 *
 * This started as a tiny non ORM class for working with unit types. But
 *
 * That said, the only real advantage it has over a string now is type
 * checking, and easily importing data from the spreadsheet in.
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class Unit extends BaseUnit {

	public function __construct($type = null) {
		$this->setUnitType($type);
		parent::__construct();
	}

	public function setUnitType($v) {
		$official_type = self::convert($v);
		if ($official_type === false) {
			throw new InvalidUnitException('Must specify unit type, "'. $type .'" provided.');
		}
		parent::setUnitType($official_type);
	}

	public function __toString() {
		switch ($this->getUnitType()) {
			case UnitTableMap::COL_UNIT_TYPE_FLEET:
				return "Fleet";
			case UnitTableMap::COL_UNIT_TYPE_ARMY:
				return "Army";
			case UnitTableMap::COL_UNIT_TYPE_VACANT:
				return "Vacant";
			case UnitTableMap::COL_UNIT_TYPE_NONE:
				return "n/a";
			default:
				return "n/a";
		}
	}

	/**
	 * Function designed to take broad inputs and output an official unit type
	*/
	public static function convert($type) {
		if (strtolower($type) == 'f' || trim(strtolower($type)) == 'fleet' || $type == UnitTableMap::COL_UNIT_TYPE_FLEET)
			return UnitTableMap::COL_UNIT_TYPE_FLEET;
		elseif (strtolower($type) == 'a' || trim(strtolower($type)) == 'army' || $type == UnitTableMap::COL_UNIT_TYPE_ARMY)
			return UnitTableMap::COL_UNIT_TYPE_ARMY;
		elseif (strtolower($type) == 'v' || trim(strtolower($type)) == 'vacant' || $type == UnitTableMap::COL_UNIT_TYPE_VACANT)
			return UnitTableMap::COL_UNIT_TYPE_VACANT;
		elseif (is_null($type) || $type == UnitTableMap::COL_UNIT_TYPE_NONE)
			return UnitTableMap::COL_UNIT_TYPE_NONE;
		else
			return false;
	}

	// /**
	//  * Used to give the value to the propel enum
	//  */
	// public function enum() {
	// 	return $this->type;
	// }

	/** No longer sure what this is really used for */
	// public function isValid() {
	// 	return $this->type != UnitTableMap::COL_UNIT_TYPE_NONE;
	// }
}

// vim: ts=3 sw=3 noet :