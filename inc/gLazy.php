<?php

/**
 * @author T5UN4M1
 * @copyright 2015
 */ 


ob_start();
error_reporting(E_ALL);
session_start();

if(empty($_SESSION['userid'])){ // if user isn't connected
    header("Location:index.php?a=accueil");
}

define('PHPEX','.php');


define('INCLUDE_PATH',ROOT_PATH . 'inc/');
define('PAGES_PATH',ROOT_PATH . 'gamepages/');
define('CLASS_PATH',ROOT_PATH . 'class/');

 $TBI = array(
    "CLASS" => array("Player","Planet","Location","Building","Ship","Research","Defense"),
    "INCLUDE" => array("sql","gameconfig","function","var","plasmafire","fleetUpdate")
      
    
 );
 
 for($i=0;$i<count($TBI["CLASS"]);++$i){
    $TBI['CLASS'][$i] .= ".class";
 }
 
 foreach($TBI as $type=>$includes){
    for($glazi=0,$m=count($includes);$glazi<$m;++$glazi){
        include_once constant($type . "_PATH") . $includes[$glazi] . PHPEX;
        //echo "included (".$glazi."):" . constant($type . "_PATH") . $includes[$glazi] . PHPEX . "<br />";
    }
 }
 if(!defined("skip")){
    $planet = new Planet($_SESSION['planetid']);
    $planet->lazy($_SERVER['REQUEST_TIME']);
}


?> 