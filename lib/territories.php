<?php

namespace DiplomacyEngine\Territories;

interface iTerritory {
	public function __toString();
}

// going to be replaced with a propel class
class Territory implements iTerritory {
	public $name;
	public function __construct($name) {
		$this->name = $name;
	}
	public function __toString() {
		return $this->name;
	}
}

// vim: ts=3 sw=3 noet :
