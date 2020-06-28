<?php

/**
 * @author T5UN4M1
 * @copyright 2015
 */ 

if(!defined("ROOT_PATH")){
    header("Location:game.php");
    exit();
}
function ptr($res){
    if(is_array($res)){
        return array_sum($res) / 1000;
    } else {
        return $res / 1000;
    }
}
function updatePlayer($userid){
    global $pdo,$var,$list;
    // gather data
    $req = $pdo->prepare("SELECT id FROM ".table('planet')." WHERE userid=:userid");
    $req->bindValue(":userid",$userid,PDO::PARAM_INT);
    $req->execute();
    $points = 0;
    $pointsBuildings = 0;
    $pointsShips = 0;
    $pointsResearches = 0;
    $pointsDefenses = 0;

    for(;$line = $req->fetch(PDO::FETCH_ASSOC);){// foreach planet
        $pl = new Planet($line['id']);
        $pl->lazy($_SERVER['REQUEST_TIME']);
        foreach($list['buildings'] AS $id){ // pts batiments
            $building = new Building($id,$pl);
            $pointsBuildings +=  ptr($building->getTotalPrice());
        }
        foreach($list['ships'] AS $id){ // pts vsx 
            $ship = new Ship($id,$pl);
            $pointsShips += ptr($ship->getPrice($pl->getData($id)));
        }
        foreach($list['defenses'] AS $id){ // pts vsx défenses
            $ship = new Ship($id,$pl);
            $pointsDefenses += ptr($ship->getPrice($pl->getData($id)));
        }
        $pl->proceedUpdate();
    }
    foreach($list['researches'] AS $id){ // tech pts
        $tech = new Research($id,$pl);
        $pointsResearches +=  ptr($tech->getTotalPrice());
    }
    $req = $pdo->prepare("SELECT * FROM ".table("fleet")." WHERE userid=:userid");
    $req->bindValue(":userid",$userid,PDO::PARAM_INT);
    $req->execute();
    for(;$line = $req->fetch(PDO::FETCH_ASSOC);){// foreach fleet
        foreach($list['ships'] AS $id){ // pts vsx 
            $ship = new Ship($id,$pl);
            $pointsShips += ptr($ship->getPrice($pl->getData($id)));
        }
    }
    $points += $pointsBuildings   = floor($pointsBuildings);
    $points += $pointsShips       = floor($pointsShips);
    $points += $pointsResearches  = floor($pointsResearches);
    $points += $pointsDefenses    = floor($pointsDefenses);
    
    $req = $pdo->prepare("SELECT * FROM ".table("rank")." WHERE userid=:userid");
    $req->bindValue(":userid",$userid,PDO::PARAM_INT);
    $req->execute();
    $res = $req->fetch(PDO::FETCH_ASSOC);
    if(!$res){ // pas de classement
        $req = $pdo->prepare("INSERT INTO ".table("rank")."
            (userid,
            points,buildingpoints,fleetpoints,researchpoints,defensepoints,
            changepoints,changebuildingpoints,changefleetpoints,changeresearchpoints,changedefensepoints)
            VALUES
            (:userid,
            :points,:pointsbuildings,:pointsships,:pointsresearches,:pointsdefenses,
            :points2,:pointsbuildings2,:pointsships2,:pointsresearches2,:pointsdefenses2)
         ");
         $req->bindValue(":userid",$userid,PDO::PARAM_INT);
         
         $req->bindValue(":points",$points,PDO::PARAM_INT);
         $req->bindValue(":pointsbuildings",$pointsBuildings,PDO::PARAM_INT);
         $req->bindValue(":pointsships",$pointsShips,PDO::PARAM_INT);
         $req->bindValue(":pointsresearches",$pointsResearches,PDO::PARAM_INT);
         $req->bindValue(":pointsdefenses",$pointsDefenses,PDO::PARAM_INT);
         
         $req->bindValue(":points2",$points,PDO::PARAM_INT);
         $req->bindValue(":pointsbuildings2",$pointsBuildings,PDO::PARAM_INT);
         $req->bindValue(":pointsships2",$pointsShips,PDO::PARAM_INT);
         $req->bindValue(":pointsresearches2",$pointsResearches,PDO::PARAM_INT);
         $req->bindValue(":pointsdefenses2",$pointsDefenses,PDO::PARAM_INT);

         $req->execute();
         
    } else { // on a déjà un classement
        $changepoints = $points - $res['points'];
        $changepointsBuildings = $pointsBuildings - $res['buildingpoints'];
        $changepointsShips = $pointsShips - $res['fleetpoints'];
        $changepointsResearches = $pointsResearches - $res['researchpoints'];
        $changepointsDefenses = $pointsDefenses - $res['defensepoints'];
        
        $req = $pdo->prepare("UPDATE ".table("rank")." SET 
            points=:points,buildingpoints=:pointsbuildings,fleetpoints=:pointsships,researchpoints=:pointsresearches,defensepoints=:pointsdefenses,
            changepoints=:changepoints,changebuildingpoints=:changepointsbuildings,changefleetpoints=:changepointsships,changeresearchpoints=:changepointsresearches,changedefensepoints=:changepointsdefenses
            WHERE userid=:userid
        ");
        
         $req->bindValue(":userid",$userid,PDO::PARAM_INT);
         
         $req->bindValue(":points",$points,PDO::PARAM_INT);
         $req->bindValue(":pointsbuildings",$pointsBuildings,PDO::PARAM_INT);
         $req->bindValue(":pointsships",$pointsShips,PDO::PARAM_INT);
         $req->bindValue(":pointsresearches",$pointsResearches,PDO::PARAM_INT);
         $req->bindValue(":pointsdefenses",$pointsDefenses,PDO::PARAM_INT);
         
         $req->bindValue(":changepoints",$changepoints,PDO::PARAM_INT);
         $req->bindValue(":changepointsbuildings",$changepointsBuildings,PDO::PARAM_INT);
         $req->bindValue(":changepointsships",$changepointsShips,PDO::PARAM_INT);
         $req->bindValue(":changepointsresearches",$changepointsResearches,PDO::PARAM_INT);
         $req->bindValue(":changepointsdefenses",$changepointsDefenses,PDO::PARAM_INT);

         $req->execute();
    }
}
function updateAllPoints(){
    global $pdo;
    $req = $pdo->prepare("SELECT id FROM ".table("user"));
    $req->execute();
    for(;$line=$req->fetch(PDO::FETCH_ASSOC);){ // on mets tous les points a jour
        updatePlayer($line['id']);
    }
    
    // MAJ DES RANKINGS
    // maj ranking points
    foreach(array("","building","fleet","research","defense") AS $dom){
        $req = $pdo->prepare("SELECT userid,".$dom."rank FROM ".table("rank")." ORDER BY ".$dom."points DESC");
        $req->execute();
        for($i=1;$line = $req->fetch(PDO::FETCH_ASSOC);++$i){
            $rankchange = $line[$dom.'rank'] - $i;
            $REQ = $pdo->prepare("UPDATE ".table("rank") ." SET ".$dom."rank=:rank,change".$dom."rank=:changerank WHERE userid=:userid");
            $REQ->bindValue(":rank",$i,PDO::PARAM_INT);
            $REQ->bindValue(":changerank",$rankchange,PDO::PARAM_INT);
            $REQ->bindValue(":userid",$line['userid'],PDO::PARAM_INT);
            $REQ->execute();
        }
    }    
}
if(isset($_GET['confirmation'])){ // mise a jour
    updateAllPoints();
}


?>
<table class="content"><tr><td><a href="?a=update&confirmation=1">Attention , la mise a jour des points est une opération lourde qui peut prendre du temps surtout si il y a un grand nombre de joueur, si vous etes sur de vouloir procéder, cliquez sur ce message.</a></td></tr></table> 