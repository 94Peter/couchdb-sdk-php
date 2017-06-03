<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Phalcon\Di\FactoryDefault;

define('DB_HOST', 'couchdb');
define('DB_PORT', '5984');

$di = new FactoryDefault();
$di->setShared(
    'couchDbConnection',
    function () {
        return new nosqldb\Connection(DB_HOST, DB_PORT);
    }
);
