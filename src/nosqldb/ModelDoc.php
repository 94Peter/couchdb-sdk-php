<?php
namespace nosqldb;

abstract class ModelDoc extends \Phalcon\Mvc\Model
{
    protected $id;

    protected $rev;

    protected $settingAry = [];

    private $document = null;

    public function __set($property, $value)
    {
        if (isset($this->document) === false) {
            $this->settingAry[$property] = $value;
        } else {
            $this->document->$property = $value;
        }

        if (property_exists($this, $property)) {
            $this->$property = $value;
        }
        return $this;
    }

    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }

    abstract protected function documentToModel();

    public function beforeValidationOnCreate()
    {
        $document = $this->getDocument();
        $document->create();
        $this->id = $this->getDocValue('id');
        $this->rev = $this->getDocValue('rev');
        $this->documentToModel();
    }

    public function beforeValidationOnUpdate()
    {
        $document = $this->getDocument();
        $document->update();
        $this->id = $this->getDocValue('id');
        $this->rev = $this->getDocValue('rev');
        $this->documentToModel();
    }

    public function getDocValue($key)
    {
        $document = $this->getDocument();
        return $document->$key;
    }

    public function getDocument()
    {
        if (isset($this->document) === true) {
            return $this->document;
        }

        if ((isset($this->id) === true) && (isset($this->rev) === true)) {
            $className = get_class($this);
            $documentName = str_replace('model', 'document', $className);
            $reflectionClass = new \ReflectionClass($documentName);
            $this->document = $reflectionClass->getMethod('findById')->invoke(new $documentName(), $this->id);
            $this->applySetting($this->document);
        } else {
            $className = get_class($this);
            $documentName = str_replace('model', 'document', $className);
            $this->document = new $documentName();
            $this->applySetting($this->document);
        }
        return $this->document;
    }

    private function applySetting($document)
    {
        foreach ($this->settingAry as $key => $value) {
            $document->$key = $value;
        }
        $this->settingAry = [];
        return $document;
    }

}
