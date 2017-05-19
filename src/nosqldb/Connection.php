<?php
namespace nosqldb;

use lib\curl\curl;

class Connection
{
    private $host = null;
    private $port = null;

    public function __construct($host, $port)
    {
        $this->host = $host;
        $this->port = $port;
    }

    public function put($path, $args = [], $body = null, $headers = [])
    {
        return $this->send('put', $path, $args, $body, $headers);
    }

    public function get($path, $args = [], $body = null, $headers = [])
    {
        return $this->send('get', $path, $args, $body, $headers);
    }

    public function delete($path, $args = [], $body = null, $headers = [])
    {
        return $this->send('delete', $path, $args, $body, $headers);
    }

    private function getBasicUrlScheme()
    {
        return [
                'scheme' => 'http',
                'host'   => $this->host,
                'port'   => $this->port,
               ];
    }

    private function send($method, $path, $args, $body, $headers)
    {
        $url = $this->getUrl($path, $args);
        $curl = new Curl();
        $curl->headers = $headers;
        return $curl->$method($url, $body);
    }

    private function getUrl($path, $args = [])
    {
        $scheme = $this->getBasicUrlScheme();
        $scheme['path'] = $path;
        return $this->buildUrl($scheme);
    }

    private function buildUrl(array $elements)
    {
        $e = $elements;
        return
            (isset($e['host']) ? (
                (isset($e['scheme']) ? "$e[scheme]://" : '//') .
                (isset($e['user']) ? $e['user'] .
                (isset($e['pass']) ? ":$e[pass]" : '') . '@' : '') .
                $e['host'] .
                (isset($e['port']) ? ":$e[port]" : '')
            ) : '') .
            (isset($e['path']) ? $e['path'] : '/') .
            (isset($e['query']) ? '?' . (is_array($e['query']) ?
                  http_build_query($e['query'], '', '&') : $e['query']) : '') .
            (isset($e['fragment']) ? "#$e[fragment]" : '')
        ;
    }
}
