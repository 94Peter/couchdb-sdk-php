<?php
use PHPUnit\Framework\TestCase;

class ModelDocTest extends TestCase
{
    private $database = null;
    protected function setUp()
    {
        $doc = new sample\document\Order();
        $this->database = new nosqldb\Database($doc);

        if ($this->database->isExist() === false) {
            $this->database->create();
        }

        $di = \Phalcon\Di::getDefault();
        $connection = $di->getShared('db');

        $model = new sample\model\Order();
        $tableName = $model->getSource();
        $isOrderExists = $connection->tableExists($tableName);

        if ($isOrderExists === true) {
            $connection->dropTable($tableName);
        }

        $connection->createTable($tableName, null, $model->tableDefinition());
    }

    protected function tearDown()
    {
         $this->database->delete();
    }

    public function testSaveUpdateDelete()
    {
        $order = $this->save();

        $this->update($order->id, $order->rev);
    }

    private function save()
    {
        $order = new sample\model\Order();
        $order->customerName = 'customerName';
        $order->isPay = 1;
        $order->amount = 100;
        $order->discount = 10;
        $order->orderNo = 'O1234';
        $order->save();

        $id = $order->id;
        $this->assertNotNull($id);

        $orderDoc = sample\document\Order::findById($id);
        $this->assertSame($id, $orderDoc->id);
        return $order;
    }

    private function update($id, $rev)
    {
        $order = sample\model\Order::findFirstById($id);
        $order->isPay = 0;
        $order->save();

        $orderDoc = sample\document\Order::findById($id);
        $this->assertNotSame($rev, $orderDoc->rev);
        $this->assertFalse($orderDoc->isPay);
    }

    private function delete($id)
    {
        $order = sample\model\Order::findFirstById($id);
        $order->delete();
    }
}
