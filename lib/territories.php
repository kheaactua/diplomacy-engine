<?php

namespace DiplomacyEngine\Territories;
use DiplomacyEngine\Empires\Empire as Empire;

/** Terrotory type */
define('TERR_LAND', 1);
define('TERR_WATER', 2);

interface iTerritory {
	public function __toString();

	public function getId();

	/** @return bool Check if $this is land */
	public function isLand();

	// /** @return bool Check if $this is coast line */
	// public function isCoast();

	/** @return bool Check if $this is water */
	public function isWater();

	/** @return int Return the territory type (IS_LAND, IS_COAST, IS_WATER) */
	public function type();
	public function setType($type);

	public function addNeighbour(iTerritory $neighbour);
	public function addNeighbours(array $neighbours);


	/** @return bool Has Supply center */
	public function hasSupplyCenter();
	public function setSupplyCenter($hasSupply);

	public function isNeighbour(iTerritory $neighbour);
	public function getNeighbours();

	public function setOccupier(Empire $occupier, $unit);

	/** Short name, used as array key, and other non-user stuff */
	//public function shortName();

	//public static function createTerritories(array $map);
}

// going to be replaced with a propel class
class Territory implements iTerritory {
	public $name;
	protected $type;
	protected $neighbours;
	protected $is_supply;
	protected $id;

	protected $occupier;
	protected $unit;

	public function __construct($id, $name, $type = TERR_LAND) {
		$this->id = $id;
		$this->name = $name;
		$this->setType($type);
		$this->is_supply = false;
		$this->neighbours = array();

		$this->occupier = null;
		$this->unit = UNIT_ARMY;

	}
	public function __toString() {
		return $this->name;
	}
	public function getId() { return $this->id; }
	public function getName() { return $this->name; }

	public function getOccupier() { return $this->occupier; }
	public function getUnitType() { return $this->unit; }

	/** @return bool Check if $this is land */
	public function isLand() { return $this->type == TERR_LAND; }

	/** @return bool Check if $this is water */
	public function isWater() { return $this->type == TERR_WATER; }

	/** @return int Return the territory type (IS_LAND, IS_COAST, IS_WATER) */
	public function type() { return $this->type; }
	public function setType($type) {
		if ($type == TERR_LAND || $type == TERR_WATER) {
			$this->type = $type;
		} elseif (strtolower($type) == 'land') {
			$this->type = TERR_LAND;
		} elseif (strtolower($type) == 'water') {
			$this->type = TERR_WATER;
		} else {
			trigger_error("Attempted to set invalid territory type: $type");
		}
	}

	public function addNeighbour(iTerritory $neighbour) {
		if (!array_key_exists($neighbour->getId(), $this->neighbours)) {
			$this->neighbours[$neighbour->getId()] = $neighbour;

			// The check above should prevent this from being an infinite loop
			$neighbour->addNeighbour($this);
		}
	}
	public function addNeighbours(array $neighbours) {
		trigger_error('Not implemented');
	}
	public function isNeighbour(iTerritory $neighbour) {
		return array_key_exists($neighbour->getId(), $this->neighbours);
	}
	public function getNeighbours() { return $this->neighbours; }


	/** @return bool Has Supply center */
	public function hasSupplyCenter() { return $this->is_supply; }
	public function setSupplyCenter($hasSupply) {
		$this->is_supply = $hasSupply;
	}

	public function setOccupier(Empire $occupier, $unit) {
			$this->occupier  = $occupier;
			$this->unit = $unit;
	}

	public static function loadTerritories(array $empires, array $objs) {
		$ts = array();
		foreach ($objs as $obj) {
			$t = new Territory($obj->id, $obj->name, $obj->type);
			$t->setSupplyCenter($obj->has_supply);

			if (array_key_exists($obj->empire_start, $empires)) {
				$t->setOccupier($empires[$obj->empire_start], $obj->starting_forces);
			};

			$ts[$t->getId()] = $t;
		}

		// Second pass, set up neighbours
		foreach ($objs as $obj) {
			$t = $ts[$obj->id];
			foreach ($obj->neighbours as $nid) {
				$n = $ts[$nid];
				$t->addNeighbour($n);
			}
		}
		return $ts;
	}
	public function findTerritoryByName($territories, $name) {
		foreach ($territories as $t) {
			if ($t->getName() == $name) {
				return $t;
			}
		}
	}
}

// vim: ts=3 sw=3 noet :
