<?php

namespace DiplomacyEngine\Empires;

define('UNIT_ARMY', 1);
define('UNIT_FLEET', 2);

interface iEmpire {
	public function __toString();

	/** Empire ID **/
	public function getId();
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

	public function getId() {
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
