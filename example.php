<?php
// Including API. Please respect folder location!
require '../php-binance-api/vendor/autoload.php';

// Loading API Key & Secret
$keyfile = fopen("keysecret/key", "r") or die("Unable to open file!");
$secretfile = fopen("keysecret/secret", "r") or die("Unable to open file!");
$key = fread($keyfile, filesize("keysecret/key"));
$secret = fread($secretfile, filesize("keysecret/secret"));
fclose($keyfile);
fclose($secretfile);

// Creating an API object
$api = new Binance\API($key, $secret);


// An easy api usage example
$ticker = $api->prices();
$keys = array_keys($ticker);

foreach($keys as $key) {
    echo $key."\t\t".$ticker[$key].PHP_EOL;
}


?>
