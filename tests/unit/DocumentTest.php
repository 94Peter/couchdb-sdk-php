<?php
use PHPUnit\Framework\TestCase;

class DocumentTest extends TestCase
{
  private function getConnect()
  {
    return new nosqldb\Connection(DB_HOST, DB_PORT);
  }
    public function testCreate()
    {
        $document = new sample\Order();
        $document->setConnection($this->getConnect());

        $document->orderNo = 'O1112';
        $document->amount = 3;
        $document->dd = 'bb';

        $this->assertFalse(isset($document->id));

        $document->create();

        $this->assertTrue(isset($document->id));
    }
}
