<?php


ini_set('display_startup_errors',1);
ini_set('display_errors',1);
error_reporting(-1);

/**
 * Load config
 */

require __DIR__ . '/../app/config/config.php';


/**
 * Load classes
 */

require __DIR__ . '/../bootstrap/autoload.php';

/**
 * Load the application
 */

$app = require_once __DIR__ .  '/../bootstrap/start.php';

/**
 * Run the applciation
 *
 * this will return the response to the incoming request
 */

$app->run();
