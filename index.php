<?php

define('SOAP',microtime(1));

use Obsidian\Application;

require_once 'Autoload.php';
require '../routes.php';
$configuration = require '../config.php';

$soap = new Application($configuration);
$soap->execute();