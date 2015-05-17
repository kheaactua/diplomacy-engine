<?php

namespace DiplomacyEngine\Empires;
use DiplomacyOrm\Map\StateTableMap;

// Deprecated
define('UNIT_ARMY', StateTableMap::COL_UNIT_ARMY);
define('UNIT_FLEET', StateTableMap::COL_UNIT_FLEET);
define('UNIT_NONE', StateTableMap::COL_UNIT_NONE);

/**
 * Tiny class for unit type
 */
class Unit {
	protected $type;
	public function __construct($type = null) {
		if (strtolower($type) == 'a' || trim(strtolower($type)) == 'army' || $type == UNIT_ARMY || $type == StateTableMap::COL_UNIT_ARMY)
			$this->type = StateTableMap::COL_UNIT_ARMY;
		elseif (strtolower($type) == 'f' || trim(strtolower($type)) == 'fleet' || $type == UNIT_FLEET || $type == StateTableMap::COL_UNIT_FLEET)
			$this->type = StateTableMap::COL_UNIT_FLEET;
		elseif (is_null($type) || $type == StateTableMap::COL_UNIT_NONE)
			$this->type = StateTableMap::COL_UNIT_NONE;
		else
			trigger_error('Must specify unit type, "'. $type .'" provided.');
	}
	public function __toString() {
		switch ($this->type) {
			case StateTableMap::COL_UNIT_ARMY:
				return "Army";
			case StateTableMap::COL_UNIT_FLEET:
				return "Fleet";
			default:
				return "n/a";
		}
	}

	/**
	 * Used to give the value to the propel enum
	 */
	public function enum() {
		return $this->type;
		// switch ($this->type) {
		// 	case UNIT_ARMY:
		// 		return StateTableMap::COL_UNIT_ARMY;
		// 	case UNIT_FLEET:
		// 		return StateTableMap::COL_UNIT_FLEET;
		// 	default:
		// 		return StateTableMap::COL_UNIT_NONE;
		// }
	}
}

interface iEmpire {
	/** Serialize to string **/
	public function __toString();

	/** Empire ID **/
	public function getEmpireId();
}

// going to be replaced with a propel class
class Empire implements iEmpire {

	protected $id;
	protected $name_official;
	protected $name_long;
	protected $name_short;

	public function __construct($id, $name_official, $name_long, $name_short) {
		$this->id = $id;
		$this->name_official = $name_official;
		$this->name_long     = $name_long;
		$this->name_short    = $name_short;
	}
	public function __toString() {
		return $this->id;
	}

	public function getEmpireId() {
		return $this->id;
	}

	public static function loadEmpires(array $objs) {
		$empires = array();
		foreach ($objs as $obj) {
			$t = new Empire($obj->id, $obj->name_official, $obj->name_long, $obj->name_short);
			$empires[$t->getId()] = $t;
		}
		return $empires;
	}
}

// vim: ts=3 sw=3 noet :
