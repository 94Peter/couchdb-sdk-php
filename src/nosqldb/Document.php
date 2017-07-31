<?php
namespace nosqldb;

/**
 * 原則：
 * 1.沒有定義的欄位儲存時會自動移除，避免錯誤的欄位被存入
 */
class Document
{
    private $connection = null;
    private $responseParser = null ;

    private function getResponseParser()
    {
        if (isset($this->responseParser) === false) {
            $this->responseParser = new response\parser\Documentparser($this);
        }
        return $this->responseParser;
    }

    public function getConnection()
    {
        if (isset($this->connection) === false) {
            $di = \Phalcon\Di::getDefault();
            $this->connection = $di->getShared('couchDbConnection');
        }
        return $this->connection;
    }

    public function getDB()
    {
        if ($this->isDocument()) {
            throw new \Exception('must extends class Document');
        }
    }

    public function genUUID()
    {
        return uniqid(get_class($this) . '_');
    }

    public function create()
    {
        if (isset($this->id)) {
            throw new \Exception('can not create Document. use function update.');
        }
        if ($this->isDocument()) {
            throw new \Exception('must extends class Document');
        }

        $uuid = $this->genUUID();
        $db = $this->getDB();

        $path = '/' . $db . '/' . $uuid;
        $connection = $this->getConnection();
        $json = $this->toJson();

        $response = $connection->put($path, [], $json);
        $parser = $this->getResponseParser();
        $parser->createParser($response);
        return $this;
    }

    public function delete()
    {
        if ($this->isDocument()) {
            throw new \Exception('must extends class Document');
        }
        $id = $this->id;
        $rev = $this->rev;
        $db = $this->getDB();

        $path = "/{$db}/{$id}";
        $connection = $this->getconnection();
        $headers = ['If-Match' => $rev];

        $response = $connection->delete($path, [], $body, $headers);
        $parser = $this->getResponseParser();
        $parser->deleteParser($response);
        return $this;
    }

    public function update()
    {
        if ($this->isDocument()) {
            throw  new \Exception('must extends class Document');
        }
        $id = $this->id;
        $rev = $this->rev;
        $db = $this->getDB();

        $path = "/{$db}/{$id}";
        $connection = $this->getconnection();
        $body = $this->toJson();
        $headers = ['If-Match' => $rev];

        $response = $connection->put($path, [], $body, $headers);
        $parser = $this->getResponseParser();
        $parser->createParser($response);
        return $this;
    }

    public static function findById($id)
    {
        $className = get_called_class();
        if ('nosqldb\Document' === $className) {
            throw new \Exception('must extends class Document');
        }

        $document = new $className();

        $db = $document->getDB();
        $path = "/{$db}/{$id}";

        $connection = $document->getConnection();
        $response = $connection->get($path);
        $parser = $document->getResponseParser();
        $parser->findParser($response);
        return $document;
    }

    public function mappingByJson($json)
    {
        $json = ('stdClass' === get_class($json)) ? json_encode($json) : $json;
        $documentAry = json_decode($json, true);
        foreach ($documentAry as $key => $value) {
            $this->$key = $value;
        }
    }

    public function toJson()
    {
        if ($this->isDocument()) {
            throw new \Exception('must extends class Document');
        }
        $data = $this->filterData();
        return json_encode($data);
    }

    private function getArgs()
    {
        $class_vars = get_object_vars($this);
        $arr = [];
        foreach ($class_vars as $name => $value) {
            $arr[$name] = $value;
        }
        return $arr;
    }

    public function filterData()
    {
        $filter =  $this->getValidationFilter();
        if ($filter === null) {
            return;
        }
        $data = $this->getArgs();
        $data = $this->validateObject($data, true, $filter);
        return $data;
    }

    // private function validateField($value, $required, $filter, $option = null)
    // {
    //     return
    // }

    private function validateObject($obj, $required, $schema)
    {
        if ($required && empty($obj) === true) {
            return false;
        }

        $newObj = [];
        foreach ($schema as $key => $setting) {
            $value = $obj[$key];
            $type  = $setting['type'];
            $required = (isset($setting['required']))? $setting['required'] : false;

            if ($type === 'objArray') {
                $schema = $setting['schema'];
                $value = $this->validateObjArray($value, $required, $schema);
                if ($required && $value === false) {
                    throw new \Exception('miss Object Array: ' . $key);
                }
            } elseif ($type === 'object') {
                $schema = $setting['schema'];
                $value = $this->validateObject($value, $required, $schema);
                if ($required && $value === false) {
                    throw new \Exception('miss object: ' . $key);
                }
            } else {
                $filter = $setting['filter'];
                $options = $setting['options'];
                $value = filter_var($value, $filter, $options);
                if ($required && $value === false && $filter !== FILTER_VALIDATE_BOOLEAN) {
                    throw new \Exception('miss column: ' . $key);
                }
                if ($required && empty($value) && $filter === FILTER_SANITIZE_ENCODED) {
                    throw new \Exception('miss column: ' . $key);
                }
            }
            $newObj[$key] = $value;
        }
        return $newObj;
    }

    private function validateObjArray($objAry, $required, $schema)
    {
        if ($required && empty($objAry) === true) {
            return false;
        }
        $newObj = [];
        foreach ($objAry as $obj) {
            $result = $this->validateObject($obj, true, $schema);
            if ($result === false) {
                throw new \Exception('object schema error: ' . PHP_EOL . json_encode($obj));
            } else {
                $newObj[] = $result;
            }
        }
        return $newObj;
    }

    private function isColumnsRequired($column)
    {
        $filter = $this->getValidationFilter();
        if ($filter === null) {
            return true;
        }

        if (isset($filter[$column]) === false) {
            return false;
        }

        $columnFilter = $filter[$column];
        return (isset($columnFilter['required']))? $columnFilter['required'] : true;
    }

    protected function getValidationFilter()
    {
        return null;
    }

    /**
     * 檢查是否為Document的class
     */
    private function isDocument()
    {
        return get_class($this) === 'nosqldb\Document';
    }
}
