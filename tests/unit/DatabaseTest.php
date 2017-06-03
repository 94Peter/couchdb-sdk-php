<?php
use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{

    /**
     * 測試建構子參數若不是繼承 Document的物件會出現 Exception
     * @expectedException Exception
     */
    public function testConstructor()
    {
        $doc = new nosqldb\Document();
        $database = new nosqldb\Database($doc);
    }


    public function testCreateIsExistDelete()
    {
        $doc = new sample\Order();
        $database = new nosqldb\Database($doc);

        $isExist = $database->isExist();
        $this->assertFalse($isExist);

        $createResult = $database->create();
        $this->assertTrue($createResult);

        $isExist = $database->isExist();
        $this->assertTrue($isExist);

        $deleteResult = $database->delete();
        $this->assertTrue($deleteResult);

        $isExist = $database->isExist();
        $this->assertFalse($isExist);
    }
}
