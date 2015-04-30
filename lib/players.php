<?php

namespace DiplomacyEngine\Players;

interface iPlayer {
	public function __toString();
}

// going to be replaced with a propel class
class Player {
	public function __construct($name) {
		$this->name = $name;
	}
	public function __toString() {
		return $this->name;
	}
}

// vim: ts=3 sw=3 noet :
