<?php

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
 * this will return a response to the incoming GET request
 */

$app->run();
