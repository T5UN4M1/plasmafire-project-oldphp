<?php

/**
 * @author T5UN4M1
 * @copyright 2015
 */
 
 $TBI = array(
     "INCLUDE" => array("sql","gameconfig","function","var"),
     "CLASS" => array("Player","Planet","Location"),
     "PAGES" => array("header",$requestedPage,"bottom")
 );
 
 for($i=0;$i<count($TBI["CLASS"]);++$i){
    $TBI['CLASS'][$i] .= ".class";
 }
 
 
 foreach($TBI as $type=>$includes){
    for($i=0;$i<count($includes);++$i){
        include constant($type . "_PATH") . $includes[$i] . PHPEX;
        //echo "Included : " . constant($type . "_PATH") . $includes[$i] . PHPEX . '<br />';
    }
 }
 
 ?>