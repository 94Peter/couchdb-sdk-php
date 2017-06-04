<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Phalcon\Di\FactoryDefault;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;

define('DB_HOST', 'couchdb');
define('DB_PORT', '5984');

$di = new FactoryDefault();
$di->setShared(
    'couchDbConnection',
    function () {
        return new nosqldb\Connection(DB_HOST, DB_PORT);
    }
);

$di->setShared(
    "db",
    function () {
        return new DbAdapter(
            [
                "host"     => "db",
                "username" => "root",
                "password" => "pass",
                "dbname"   => "CouchDB_SDK_TEST",
            ]
        );
    }
);
