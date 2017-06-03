<?php
use PHPUnit\Framework\TestCase;

class DocumentTest extends TestCase
{
    private $database = null;
    protected function setUp()
    {
        $doc = new sample\document\Order();
        $this->database = new nosqldb\Database($doc);

        if ($this->database->isExist() === false) {
            $this->database->create();
        }
    }

    protected function tearDown()
    {
        $this->database->delete();
    }

    public function testCreateUpdateDelete()
    {
        $document = $this->create();
        $this->update($document->id, $document->rev);
        $this->delete($document->id);
    }

    private function create()
    {
        $document = new sample\document\Order();

        $document->orderNo = 'O1112';
        $document->amount = 3;
        $document->dd = 'bb';
        $document->customerName = 'peter';

        $this->assertFalse(isset($document->id));

        $document = $document->create();

        $this->assertTrue(isset($document->id));
        return $document;
    }

    private function update($id, $rev)
    {

        $order = sample\document\Order::findById($id);
        $order->amount = 100;
        $order = $order->update();
        $this->assertNotSame($rev, $order->rev);
    }

    private function delete($id)
    {
        $order = sample\document\Order::findById($id);
        $order = $order->delete();
        $this->assertTrue($order->deleted);
    }


}
