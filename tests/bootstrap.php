<?php

require __DIR__ . "/../vendor/autoload.php";

$client = new \shakie\InfluxPHP\Client;
foreach ($client->getDatabases() as $db) {
    if (preg_match("/^test_/", $db->getName())) {
        $db->drop();
    }
}
