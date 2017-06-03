<?php
namespace nosqldb\response\parser;

class DocumentParser
{
    private $document = null;

    public function __construct($document)
    {
        $this->document = $document;
    }

    public function createParser($response)
    {
        $map = [
                '201' => function ($document, $bodyObj) {
                    $document->id = $bodyObj->id;
                    $document->rev = $bodyObj->rev;
                },
              ];
        $this->parserResponse($response, $map);
        // 201 Created – Document created and stored on disk
        // 202 Accepted – Document data accepted, but not yet stored on disk
        // 400 Bad Request – Invalid request body or parameters
        // 401 Unauthorized – Write privileges required
        // 404 Not Found – Specified database or document ID doesn’t exists
        // 409 Conflict – Document with the specified ID already exists or
        //                specified revision is not latest for target docume
    }

    public function deleteParser($response)
    {
        $map = [
                '200' => function ($document, $bodyObj) {
                    $document->deleted = true;
                },
              ];
        $this->parserResponse($response, $map);
        // 200 OK – Document successfully removed
        // 202 Accepted – Request was accepted, but changes are not yet stored on disk
        // 400 Bad Request – Invalid request body or parameters
        // 401 Unauthorized – Write privileges required
        // 404 Not Found – Specified database or document ID doesn’t exists
        // 409 Conflict – Specified revision is not the latest for target document
    }

    private function parserResponse($response, $statusMap)
    {
        $responseClassName = get_class($response);
        if ('lib\curl\CurlResponse' !== get_class($response)) {
            throw new \Exception('response must be class "CurlResponse", actual is ' . $responseClassName);
        }
        $document = $this->document;
        $statusCode = $response->getHeader('Status-Code');
        $bodyObj = $this->getBodyObj($response);
        if (isset($statusMap[$statusCode]) === false) {
            $reason = (isset($bodyObj->reason)) ? $bodyObj->reason : "statusCode handler {$statusCode} not set.";
            throw new \Exception($bodyObj->reason);
        }
        $fun = $statusMap[$statusCode];
        $fun($document, $bodyObj);
    }


    public function findParser($response)
    {
        $map = [
                '200' => function ($document, $bodyObj) {
                    $changeAry = ['_id' => 'id', '_rev' => 'rev'];
                    foreach ($changeAry as $key => $value) {
                        $bodyObj->$value = $bodyObj->$key;
                        unset($bodyObj->$key);
                    }
                    $document->mappingByJson($bodyObj);
                },
              ];
        $this->parserResponse($response, $map);
        if ('lib\curl\CurlResponse' !== get_class($response)) {
            throw new \Exception('response must be class "CurlResponse"');
        }
        // 200 OK – Request completed successfully
        // 304 Not Modified – Document wasn’t modified since specified revision
        // 400 Bad Request – The format of the request or revision was invalid
        // 401 Unauthorized – Read privilege required
        // 404 Not Found – Document not found
    }

    private function getBodyObj($response)
    {
        $body = $response->getBody();
        return json_decode($body);
    }
}
