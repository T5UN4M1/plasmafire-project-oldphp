<?php

/**
 * @author T5UN4M1
 * @copyright 2015
 */ 

 if(!defined("ROOT_PATH")){
    header("Location:game.php");
    exit();
}

function createUsername(){
    $i=0;
    $start = array("uber","giga","super","OPee","jesuistropfort","meega","ultra","imso","toogood4you","huhuhu","lenoob","leraider","darkside","winteriscoming","lepepere","imagod","toostrongforyou","golen");
    $end = array("nova","alpha","beta","theta","omicron","sigma","haxor","roxor","yeeee","yaaaa","yoooo","yamto","sakura","jecpalol","tzeureuteu","uhuhu");
	 do{
		 $username = $start[mt_rand(0,count($start)-1)] . $end[mt_rand(0,count($end)-1)] . mt_rand(1,9999);
         ++$i;
         if($i===10000){
            error(__FILE__,__LINE__,"couldnt create an username after 10000 attempts");
         }
	 }while(checkIfExist(table("user"),array("username"=>$username)));
	 return $username;
}
function createMailAdress(){
    $i=0;
	 do{
		 $mail = sha1(mt_rand(0,2000000000)); // 1-1000 are reserved
         ++$i;
         if($i===10000){
            error(__FILE__,__LINE__,"couldnt create a mail after 10000 attempts");
         }
	 }while(checkIfExist(table("user"),array("mail"=>$mail)));
	 return $mail;
}
function setConfigBaseDataRandom(){
    global $var,$list,$config;
    $config['initialPlanet'] = array();
    foreach($list['ressources'] AS $id){
        if($var[$id]['isPalpable']){
            $config['initialPlanet'][$id] = mt_rand(1000000,5000000000);
        }
    }
    foreach($list['buildings'] AS $id){
        $config['initialPlanet'][$id] = mt_rand(9,15);
    }
    foreach($list['ships'] AS $id){
        if($id < 207){
            $config['initialPlanet'][$id] = mt_rand(3000,10000);
        }else if($id<213){
            $config['initialPlanet'][$id] = mt_rand(500,1000);
        } else {
            $config['initialPlanet'][$id] = mt_rand(10,100);
        }
    }
    foreach($list['defenses'] AS $id){
        $config['initialPlanet'][$id] = mt_rand(10,400);
    }
    $config["initialPlanet"]['case'] = 500;
}
function goRandom(){ // alias
    setConfigBaseDataRandom();
}



if(isset($_GET['confirmation'])){
    $username = createUsername();
    $user = new Player($username,"default",createMailAdress());
    goRandom();
    $user->create();
    $id = $user->getId();
    foreach($list['researches'] AS $techId){
        $user->addTech($techId,mt_rand(9,15));
    }
    for($i=0,$m=mt_rand(4,10);$i<$m;++$i){
        $loc = Planet::seekSpotForNewPlayer();
        $pl = new Planet();
        goRandom();
        $pl->createNewPlanet($user->getId(),"Planete",false,$loc,mt_rand(1,50),$config["initialPlanet"]);
    }
    echo '<table class="content"><tr><td>Joueur aléatoire "'.$username.'" créé avec '.$m.' planètes + planete mere</td></tr></table>';
}





?> 
<table class="content"><tr><td><a href="?a=randomplayer&confirmation=1">Cliquez ici pour générer aléatoirement un joueur (le mot de passe est "default")</a></td></tr></table>