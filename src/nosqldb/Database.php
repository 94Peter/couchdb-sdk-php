<?php
namespace nosqldb;

class Database
{
    private $doc  = null;

    public function __construct($document)
    {
        $this->setDocument($document);
    }

    public function setDocument($document)
    {
        // 判斷是否繼承Document，但類別不是Document
        if (is_subclass_of($document, '\nosqldb\Document')) {
            $this->doc = $document;
        } else {
            throw new \Exception('must extends class Document');
        }
    }

    public function create()
    {
        $doc = $this->doc;
        $db = $doc->getDB();

        // check db name is valid.
        $pattern = '/^[a-z][a-z0-9_$()+\\\\-]*$/';
        if (preg_match($pattern, $dbname) === 0) {
            throw new \Exception('DB name must math ' . $pattern . '. DB name is: ' . $db);
        }

        $path = '/' . $db;
        $connection = $doc->getConnection();
        $response = $connection->put($path);
        $statusCode = $response->getHeader('Status-Code');
        return $statusCode === '201';
    }

    public function isExist()
    {
        $doc = $this->doc;
        $db = $doc->getDB();

        $path = '/' . $db;
        $connection = $doc->getConnection();
        $response = $connection->head($path);
        $statusCode = $response->getHeader('Status-Code');
        return $statusCode === '200';
    }

    public function delete()
    {
        $doc = $this->doc;
        $db = $doc->getDB();

        $path = '/' . $db;
        $connection = $doc->getConnection();
        $response = $connection->delete($path);
        $statusCode = $response->getHeader('Status-Code');
        return $statusCode === '200';
    }
}
