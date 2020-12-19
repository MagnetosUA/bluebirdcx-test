<?php
/**
 * The main front controller file
 */
require_once __DIR__ . "/../vendor/autoload.php";

use App\Firebase;
use App\Config\Config;

const EDIT = 'edit';
const GET  = 'get';

$requestUri = trim($_SERVER['REQUEST_URI'], '/');

$firebase = new Firebase(Config::PROJECT_ID, Config::KEY_FILE);

if ($requestUri == EDIT) {
    $firebase->set('test_sushytsky');
} elseif ($requestUri == GET) {
    $firebase->get('test_sushytsky', 'first');
} else {
    echo 'Undefined resource!';
}
