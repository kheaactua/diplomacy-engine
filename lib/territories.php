<?php

namespace DiplomacyEngine\Territories;

/** Terrotory type */
define('TERR_LAND', 1);
//define('IS_COAST', 2);
define('TERR_WATER', 3);

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

	public function __construct($id, $name, $type = TERR_LAND) {
		$this->id = $id;
		$this->name = $name;
		$this->setType($type);
		$this->is_supply = false;
		$this->neighbours = array();
	}
	public function __toString() {
		return $this->name;
	}
	public function getId() { return $this->id; }
	public function getName() { return $this->name; }

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
			trigger_error('Attempted to set invalid territory type');
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


	public static function createTerritories(array $objs) {
		$ts = array();
		foreach ($objs as $obj) {
			$t = new Territory($obj->id, $obj->name, $obj->type);
			$t->setSupplyCenter($obj->has_supply);
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
