<?php

/**
 * @author T5UN4M1
 * @copyright 2015
 */ 



?> 


<?php
$req = $pdo->prepare("SELECT * FROM ".table('fleet').' WHERE userid=:userid ORDER BY endtime DESC');
$req->bindValue(':userid',$planet->getUsedId(),PDO::PARAM_INT);
$req->execute();
$fleets = array();
for($i=0;$fleet=$req->fetch(PDO::FETCH_ASSOC);++$i){
    $fleets[$i] = $fleet;
    $fleets[$i]['ellapsedtime'] = $_SERVER['REQUEST_TIME'] - $fleet['starttime'];
    $fleets[$i]['duration'] = $fleet['endtime'] - $fleet['starttime'];
}
$ships = array();
foreach($list['ships'] AS $id){ // foreach ship
    if($planet->getData($id) > 0){ // on en a min 1
        $ship = new Ship($id,$planet);
        $move = $ship->getMove();
        $fret = $ship->getStat('fret');
        if($move['total'] > 0){ // il peut se déplacer
            $ships[] = array(
                "id" => $id,
                "name" => $var[$id]['name'],
                "amount" => $planet->getData($id),
                "fret" => $fret['total'],
                "move" => $move
            );
        }
    }
}
$loc = $planet->getPlanetLocation();
$revelantData = array(
    "maxg" => $config['maxG'],
    "maxs" => $config['maxS'],
    "maxp" => $config['maxP'],
    "startg" => $loc->getG(),
    "starts" => $loc->getS(),
    "startp" => $loc->getP(),
    "fleetspeed" => $config['fleetSpeed']
);
if(isset($_GET['g'])){
    $revelantData['g'] = $_GET['g'];
    $revelantData['s'] = $_GET['s'];
    $revelantData['p'] = $_GET['p'];
}
foreach($list['ressources'] AS $id){
    if($var[$id]['isPalpable']){
        $revelantData['ressources'][$id] = array( "amount" => $planet->getData($id) , "name" => $var[$id]['name']);
    }
}
$jsonData = json_encode($ships);
$jsonData2= json_encode($revelantData);
$jsonFleets = json_encode($fleets);
?>

<script>
var ships = <?php echo $jsonData?>;
var revData = <?php echo $jsonData2?>;
var fleets = <?php echo $jsonFleets?>
 
</script>
<script src="./script/fleetPage.js"></script>