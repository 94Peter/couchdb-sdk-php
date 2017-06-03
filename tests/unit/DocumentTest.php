<?php
use PHPUnit\Framework\TestCase;

class DocumentTest extends TestCase
{
    private $database = null;
    protected function setUp()
    {
         $doc = new sample\Order();
         $this->database = new nosqldb\Database($doc);
         $this->database->create();
    }

    protected function tearDown()
    {
        $this->database->delete();
    }

    public function testCreate()
    {
        $document = new sample\Order();

        $document->orderNo = 'O1112';
        $document->amount = 3;
        $document->dd = 'bb';

        $this->assertFalse(isset($document->id));

        $document->create();

        $this->assertTrue(isset($document->id));
    }
}
