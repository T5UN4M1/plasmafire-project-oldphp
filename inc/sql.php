<?php

/**
 * @author T5UN4M1
 * @copyright 2015
 */
 
$db = array(
    "host" => 'localhost',
    "name" => 'plasmafireproject',
    "username" => 'root',
    "password" => '',
    "prefix" => 'pf_'
);


$GLOBALS['pdo'] = new PDO('mysql:host=' . $db["host"] . ';dbname=' . $db["name"] . ';charset=utf8', $db["username"], $db["password"],array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

function table($t){
    global $db;
    return $db['prefix'] . $t;    
}
?>