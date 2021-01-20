<?php
/**
 * ImageCache2 console script
 */
define('P2_CLI_RUN', 1);

require __DIR__ . '/../init.php';
// TODO: make autoloadable
require P2EX_LIB_DIR . '/ImageCache2/Console/Application.php';

use ImageCache2\Console\Application;

error_reporting(E_ALL & ~(E_STRICT | E_DEPRECATED));
$app = new Application();
$app->run();
