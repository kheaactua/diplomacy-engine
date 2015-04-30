#!/usr/bin/env php
<?php
namespace DiplomacyEngine;
use DiplomacyEngine\Players\Player as Player;

require_once( __DIR__ . '/../config/config.php');


$player = new Player('Matt');
$canada = new Territories\Territory('Canada');
$usa    = new Territories\Territory('USA');

$move = new Orders\Move($player, $canada, $usa);
print "Created '$move'\n";

// vim: ts=3 sw=3 noet :
