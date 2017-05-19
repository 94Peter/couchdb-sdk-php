<?php
include_once __DIR__ . "/../vendor/autoload.php";

use Phalcon\Di\FactoryDefault;

define('DB_HOST', '192.168.99.100');
define('DB_PORT', '5984');

// Create a DI
$di = new FactoryDefault();
$di->setShared(
    'couchDbConnection',
    function () {
        return new nosqldb\Connection(DB_HOST, DB_PORT);
    }
);

$doc = new sample\Order();
$doc->orderNo = 'O1112';
$doc->amount = 3;
$doc->dd = 'bb';

//$doc->create();

// --------------------------------------
//$result = nosqldb\util\Purge::updateRevsLimit('order', 100, $connection);
//var_dump($result);
$connection = $di->getShared('couchDbConnection');
$result = nosqldb\util\Purge::getRevsLimit('order', $connection);

// 修改order 15個版本

$count = 15;



// for ($i=1; $i<$count; $i++) {
//   $doc = sample\Order::findById('59069ed94e3ec');
//   $doc->amount = $i + 10;
//   $doc->update();
// }

//var_dump($doc);

$doc = sample\Order::findById('590c9310b7a08');
var_dump($doc);
//$doc->delete();
