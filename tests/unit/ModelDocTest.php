<?php
use PHPUnit\Framework\TestCase;

class ModelDocTest extends TestCase
{
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
