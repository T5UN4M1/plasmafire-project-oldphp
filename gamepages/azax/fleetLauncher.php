<?php

/**
 * @author T5UN4M1
 * @copyright 2015
 */ 
 define('ROOT_PATH','../../');
include ROOT_PATH . 'inc/gLazy.php';
function gto($msg){
    die('<span class="bad">'.$msg.'</span>');
}
$allowedSpeed = array();
$allowedMission = array();
for($i=100;$i>0;$i-=5) $allowedSpeed[]=$i;$allowedSpeed[]=1;
for($i=0;$i<6;++$i) $allowedMission[]=$i;
class FleetLauncher{
    private $ships; // array
    
    private $start; // Location
    private $end; // Location
    
    private $speedPercent;
    
    private $mission;
    
    private $distance;
    private $duration;
    private $consumption;
    
    private $fleetSpeed;
    private $fleetMaxSpeed;
    
    private $fret;
    private $ressources;
    function FleetLauncher($data){
        global $planet;
        foreach($data['ships'] AS $sh){
            $this->setShip($sh['id'],$sh['amount']);
        }
        
        
        $this->start = $planet->getPlanetLocation();
        $this->end = $this->checkLocation($data['endg'],$data['ends'],$data['endp']);
        
        $this->setSpeedPercent($data['speed']);
        $this->setMission($data['mission']);
        
        $this->processFleetMaxSpeed();
        $this->processDistance();
        $this->processFleetSpeed();
        
        $this->processFret();
        $this->processConsumption();
        $this->processDuration();
        
        $this->setRessources($data['ressources']);
        $this->checkComputerTech();
        $this->insert();
        
    }
    function checkComputerTech(){
        global $pdo,$planet;
        $req = $pdo->prepare("SELECT COUNT(*) FROM ".table("fleet")." WHERE userid=:userid");
        $req->bindValue(":userid",$planet->getUserId(),PDO::PARAM_INT);
        $req->execute();
        $res = $req->fetch(PDO::FETCH_NUM);
        $maxFleet = $planet->getPlayer()->getTech(301) + 1;
        if($res[0] >= $maxFleet){
            gto("Vous avez atteint le nombre maximal de flottes en vol, attendez que vos flottes reviennent ou améliorez la technologie ordinateur.");
        }
    }
    function setShip($id,$amount){
        global $planet;
        if($planet->getData($id) >= $amount){
            $this->ships[$id] = (int) $amount;
        } else {
            gto("Erreur, vous ne disposez pas/plus des vaisseaux que vous essayez d'envoyer");
        }
    }
    function checkLocation($g,$s,$p){
        global $config;
        if($g < 1 || $g > $config['maxG'] || $s < 1 || $s > $config['maxS'] || $p < 1 || $p > $config['maxP']){
            gto("Erreur, la destination n'existe pas.");
        } else {
            return new Location((int) $g,(int) $s,(int) $p);
        }
    }
    function setSpeedPercent($speedPercent){
        global $allowedSpeed;
        if(in_array($speedPercent,$allowedSpeed)){
            $this->speedPercent = $speedPercent;
        } else {
            gto("Erreur, votre flotte ne peut utiliser cette vitesse.");
        }
    }
    function setMission($mission){
        global $allowedMission;
        if(in_array($mission,$allowedMission)){
            $this->mission = $mission;
        } else {
            gto("Erreur, cette mission n'existe pas.");
        }
    }
    function processFleetMaxSpeed(){
        global $planet;
        $fleetMaxSpeed = 999999999999999999999999;
        foreach($this->ships AS $id=>$whocares){
            $ship = new Ship($id,$planet);
            $move = $ship->getMove();
            $fleetMaxSpeed = min($move['total'],$fleetMaxSpeed);
        }
        $this->fleetMaxSpeed = $fleetMaxSpeed;
    }
    function processDistance(){
        if($this->start->getG() != $this->end->getG()){
            $this->distance = 4000 + abs($this->start->getG() - $this->end->getG()) * 1000;
        } else if($this->start->getS() != $this->end->getS()){
            $this->distance = 750 + abs($this->start->getS() - $this->end->getS()) * 25;
        }  else if($this->start->getP() != $this->end->getP()){
            $this->distance = 250 + abs($this->start->getP() - $this->end->getP()) * 10;
        } else {
            $this->distance = 5;
        }
    }
    function processFleetSpeed(){
        $this->fleetSpeed = $this->fleetMaxSpeed * ($this->speedPercent / 100);
    }
    function processFret(){
        global $planet;
        $fret = 0;
        foreach($this->ships AS $id=>$n){
            $ship = new Ship($id,$planet);
            $cap = $ship->getStat("fret");
            $fret += $cap['total'] * $n;
        }
        $this->fret = $fret;
    }
    function processConsumption(){
        global $planet;
        $consumption = 0;
        foreach($this->ships AS $id=>$n){
            $ship = new Ship($id,$planet);
            $move = $ship->getMove();
            $consumption += (($move['consumption'] * ($this->fleetSpeed / $move['total'])) * $n) / 10000;
        }
        $this->consumption = $consumption;
    }
    function processDuration(){
        global $config;
        $this->duration = ceil(($this->distance / $this->fleetSpeed) * (100000/$config['fleetSpeed']));
    }
    function setRessources($res){
        global $planet;
        $total = 0;
        foreach($res AS $id=>$qty){
            if($planet->getData($id) >= $qty && $qty>0){
                if($this->mission == 0){ // attaquer
                    gto("N'emportez pas de ressources pour attaquer.");
                }
                $total += $this->ressources[$id] = (int) $qty;
            } else if($planet->getData($id) < $qty){
                gto("Ressources insuffisantes");
            }
        }
        if($total > $this->fret){
            gto("Pas assez d'espace en soute pour transporter les ressources.");
        }
        $totalFuel = $this->consumption + ((!empty($this->ressources[2])) ? $this->ressources[2] : 0);
        if($planet->getData(2) < $totalFuel){
            gto("Pas assez de carburant");
        }
    }
    function insert(){
        global $pdo,$planet,$list,$var;
        $req = $pdo->prepare("SELECT userid FROM ".table("planet")." WHERE g=:g AND s=:s AND p=:p");
        $req->bindValue(":g",$this->end->getG(),PDO::PARAM_INT);
        $req->bindValue(":s",$this->end->getS(),PDO::PARAM_INT);
        $req->bindValue(":p",$this->end->getP(),PDO::PARAM_INT);
        $req->execute();
        $res = $req->fetch(PDO::FETCH_ASSOC);
        $enduserid = (!empty($res['userid'])) ? $res['userid'] : 0;
        
        
        $ressourcesFields = array();
        $ressourcesValues = array();
        
        foreach($list['ressources'] AS $resId){
            if($var[$resId]['isPalpable']){
                $ressourcesFields[] = "`".$resId."`";
                $ressourcesValues[] = ":".$resId;
            }
        }
        $shipsFields = array();
        $shipsValues = array();
        foreach($list['ships'] AS $shipId){
            $shipsFields[] = "`".$shipId."`";
            $shipsValues[] = ":".$shipId;
        }
        

        
        
        $req = $pdo->prepare("INSERT INTO ".table("fleet")."(
        id,userid,enduserid,
        starttime,endtime,
        startg,starts,startp,
        endg,ends,endp,
        mission,
        ".implode(',',$ressourcesFields).",
        ".implode(',',$shipsFields)."
        ) VALUES (
        :id,:userid,:enduserid,
        :starttime,:endtime,
        :startg,:starts,:startp,
        :endg,:ends,:endp,
        :mission,
        ".implode(',',$ressourcesValues).",
        ".implode(',',$shipsValues)."
        )");
        $req->bindValue(":id",keyGen(table('fleet')),PDO::PARAM_INT);
        $req->bindValue(":userid",$planet->getUsedId(),PDO::PARAM_INT);
        $req->bindValue(":enduserid",$enduserid,PDO::PARAM_INT);
        
        $req->bindValue(":starttime",$_SERVER['REQUEST_TIME'],PDO::PARAM_INT);
        $req->bindValue(":endtime",$_SERVER['REQUEST_TIME'] + $this->duration,PDO::PARAM_INT);
        
        $req->bindValue(":startg",$this->start->getG(),PDO::PARAM_INT);
        $req->bindValue(":starts",$this->start->getS(),PDO::PARAM_INT);
        $req->bindValue(":startp",$this->start->getP(),PDO::PARAM_INT);
        
        $req->bindValue(":endg",$this->end->getG(),PDO::PARAM_INT);
        $req->bindValue(":ends",$this->end->getS(),PDO::PARAM_INT);
        $req->bindValue(":endp",$this->end->getP(),PDO::PARAM_INT);
        
        $req->bindValue(":mission",$this->mission,PDO::PARAM_INT);
        foreach($list['ressources'] AS $resId){
            if($var[$resId]['isPalpable']){
                $req->bindValue(":".$resId,(!empty($this->ressources[$resId])) ? floor($this->ressources[$resId]): 0,PDO::PARAM_INT);
                if($resId != 2){
                    if(!empty($this->ressources[$resId])){
                        $planet->setData($resId,$planet->getData($resId)-$this->ressources[$resId]);
                    }
                } else { // fuel
                    $remainingFuel = $planet->getData($resId);
                    $remainingFuel -= $this->consumption;
                    if(!empty($this->ressources[$resId])){
                        $remainingFuel -= $this->ressources[$resId];
                    }
                    $planet->setData($resId,$remainingFuel);
                }
            }
        }
        foreach($list['ships'] AS $shipId){
            $req->bindValue(":".$shipId,(!empty($this->ships[$shipId])) ? floor($this->ships[$shipId]) : 0,PDO::PARAM_INT);
            if(!empty($this->ships[$shipId])){
                $planet->setData($shipId,$planet->getData($shipId)-$this->ships[$shipId]);
            }
        }
        $req->execute();
        $planet->proceedUpdate();
        echo '<span class="good">Flotte envoyée</span>';
    }
    
}
//var_dump($_POST);
if(!isset($_POST['REQUEST'])){ // empty request -> launch fleet
    $fleet = new FleetLauncher($_POST);
} else if($_POST['REQUEST'] == 'RETURN' && isset($_POST['id'])){ // requete de retour de flotte
    $req = $pdo->prepare("SELECT * FROM ".table('fleet')." WHERE id=:id");
    $req->bindValue(":id",$_POST['id'],PDO::PARAM_INT);
    $req->execute();
    $res = $req->fetch(PDO::FETCH_ASSOC);
    if(empty($res['id'])){
        gto("Erreur , cette flotte n'existe pas/plus, elle est peut être déjà rentrée.");
    }
    if($res['userid'] != $planet->getUsedId()){
        gto("Erreur , cette flotte n'est pas a vous.");
    }
    if($res['mission'] > 99){
        gto("Erreur, cette flotte est déjà en train de revenir.");
    }
    // si on arrive ici alors on peut update la flotte
    $ellapsedTime = $_SERVER['REQUEST_TIME'] - $res['starttime'];
    $starttime = $_SERVER['REQUEST_TIME'];
    $endtime = $starttime + $ellapsedTime;
    $mission = $res['mission'] + 100;
    
    $req = $pdo->prepare("UPDATE ".table("fleet")." SET starttime=:starttime,endtime=:endtime,mission=:mission WHERE id=:id");
    
    $req->bindValue(":starttime",$starttime,PDO::PARAM_INT);
    $req->bindValue(":endtime",$endtime,PDO::PARAM_INT);
    $req->bindValue(":mission",$mission,PDO::PARAM_INT);
    $req->bindValue(":id",$_POST['id'],PDO::PARAM_INT);
    
    $req->execute();
    
    echo '<span class="good">Flotte rapellée avec succès.</span>';
    
} else if($_POST['REQUEST'] == "STALK" && isset($_POST['g']) && isset($_POST['s']) && isset($_POST['p'])){
    if($planet->getData(205) < 1){
        gto("Impossible d'espionner , pas de sonde disponible.");
    }
    $data = array(
        "endg" => $_POST['g'],
        "ends" => $_POST['s'],
        "endp" => $_POST['p'],
        "ships" => array(array("id"=>205,"amount"=>1)),
        "speed" => 100,
        "mission" => 5,
        "ressources" => array(0,0,0)
    );
    $fleet = new FleetLauncher($data);
}else if($_POST['REQUEST'] == "VCOLO" && isset($_POST['g']) && isset($_POST['s']) && isset($_POST['p'])){
    if($planet->getData(213) < 1){
        gto("Impossible de coloniser, vous n'avez pas de vaisseau de colonisation.");
    }
    $data = array(
        "endg" => $_POST['g'],
        "ends" => $_POST['s'],
        "endp" => $_POST['p'],
        "ships" => array(array("id"=>213,"amount"=>1)),
        "speed" => 100,
        "mission" => 3,
        "ressources" => array(0,0,0)
    );
    $fleet = new FleetLauncher($data);
}else if($_POST['REQUEST'] == "ECOLO" && isset($_POST['g']) && isset($_POST['s']) && isset($_POST['p'])){
    if($planet->getData(217) < 1 && $planet->getData(212) < 1){
        gto("Impossible de recycler, vous n'avez pas de vaisseau capable de mener à bien cette mission.");
    }
    $req = $pdo->prepare("SELECT `0`,`1`,`2` FROM ".table("wreck")." WHERE posg=:g AND poss=:s AND posp=:p");
    $req->bindValue(":g",$_POST['g'],PDO::PARAM_INT);
    $req->bindValue(":s",$_POST['s'],PDO::PARAM_INT);
    $req->bindValue(":p",$_POST['p'],PDO::PARAM_INT);
    $req->execute();
    
    $res = $req->fetch(PDO::FETCH_ASSOC);
    $total = $res[0] + $res[1] + $res[2];
    
    $recycleur = new Ship(212,$planet);
    $transporteurLourd = new Ship(217,$planet);
    $cargoRecycleur = $recycleur->getStat("fret");
    $cargoTransporteurLourd = $transporteurLourd->getStat("fret");
    $ships = array();
    $neededRecycleurs = ceil($total/$cargoRecycleur["total"]);
    if($neededRecycleurs > $planet->getData(212)){
        $ships[] = array("id"=>212,"amount"=>$planet->getData(212));
        $total -= $planet->getData(212) * $cargoRecycleur["total"];
        $neededTransporteurLourd = ceil($total/$cargoTransporteurLourd["total"]);
        $ships[] = array("id"=>217,"amount"=>min($neededTransporteurLourd,$planet->getData(217)));
    } else {
        $ships[] = array("id"=>212,"amount"=>$neededRecycleurs);
    }
    
    
    $data = array(
        "endg" => $_POST['g'],
        "ends" => $_POST['s'],
        "endp" => $_POST['p'],
        "ships" => $ships,
        "speed" => 100,
        "mission" => 4,
        "ressources" => array(0,0,0)
    );
    $fleet = new FleetLauncher($data);
}

?> 