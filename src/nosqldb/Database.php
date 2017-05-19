<?php
namespace nosqldb;

class Database
{
  public function createDB($db)
  {
     $scheme = $this->getBasicUrlScheme();
     $scheme['path'] = $db;
     $url = $this->build_url($scheme);
     var_dump($url); exit();
     $curl = new Curl();
     $response = $curl->put($url);
  }
}
