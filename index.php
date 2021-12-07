<?php

define('SOAP',microtime(1));

require_once 'Autoload.php';
require '../routes.php';
$configuration = require '../config.php';

$soap = new Obsidian\Application($configuration);
$soap->execute();