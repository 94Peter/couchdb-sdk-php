<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Phalcon\Di\FactoryDefault;

define('DB_HOST', '192.168.99.100');
define('DB_PORT', '5984');

$di = new FactoryDefault();
$di->setShared(
    'couchDbConnection',
    function () {
        return new nosqldb\Connection(DB_HOST, DB_PORT);
    }
);
