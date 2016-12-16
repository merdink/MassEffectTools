<?php
// Path to directory with library
define('MASSEFFECT_PATH', dirname(__FILE__).'/');

// pcsav or xbsav extension
$save = new MassEffect_Save('./example.pcsav');

// access properties (these all are the same) - most objects in this library can be accessed like that
$version = $save->version;
$version = $save->version();
$version = $save['version'];
$version = $save->getVersion();
$version = $save->getProperty('version');

// player object
$player = $save->player->getPlayer();

$player->name;
$player->level; // and many other props...

// plot table - get IDs from other save editors
$flag = $save->plotTable->bool(1111);
$save->plotTable->bool(1111, FALSE);

// again, extension determines format
$save->save('./example.xbsav');