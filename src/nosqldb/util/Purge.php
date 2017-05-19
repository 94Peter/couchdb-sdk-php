<?php
namespace nosqldb\util;

class Purge
{
    public static function updateRevsLimit($db, $limit, $connection = null)
    {
        $path = "/{$db}/_revs_limit";
        $response = $connection->put($path, [], $limit);
        var_dump($response);
        return $limit;
    }

    public static function getRevsLimit($db, $connection = null)
    {
        $path = "/{$db}/_revs_limit";
        $response = $connection->get($path, [], $limit);
        var_dump($response);
        return $limit;
    }
}
