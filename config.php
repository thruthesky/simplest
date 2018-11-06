<?php
/**
 * @file config.php
 */
/**
 * Root folder where Simplest is installed.
 * @example
 *      C:/www/simplest/
 *      /Users/name/www/simplest/
 */
define('ROOT_DIR', dirname(__FILE__) . '/');
define('FILE_DIR', ROOT_DIR . 'files/');
define('FILE_UPLOAD_DIR', FILE_DIR . 'uploads/');
define('LOG_FILE', FILE_DIR . 'debug.log');

define('ROOT_DIR_URL', 'https://seo.sonub.com/simplest/');

define('FILE_DIR_URL', ROOT_DIR_URL . 'files/');


define('DB_PREFIX', 'sp_');



$_config = [];
