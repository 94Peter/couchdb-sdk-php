<?
$ch = curl_init();
$options = array(CURLOPT_URL => 'http://192.168.99.100:5984/',
                 CURLOPT_HEADER => false
                );

curl_setopt_array($ch, $options);

// grab URL and pass it to the browser
var_dump(curl_exec($ch));

// close cURL resource, and free up system resources
curl_close($ch);
