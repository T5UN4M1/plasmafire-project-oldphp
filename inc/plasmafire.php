<?php

/**
 * @author T5UN4M1
 * @copyright 2015
 */ 

/*

 (    (              (                     (     (    (         
 )\ ) )\ )    (      )\ )  (  `     (      )\ )  )\ ) )\ )      
(()/((()/(    )\    (()/(  )\))(    )\    (()/( (()/((()/( (    
 /(_))/(_))((((_)(   /(_))((_)()\((((_)(   /(_)) /(_))/(_)))\   
(_)) (_))   )\ _ )\ (_))  (_()((_))\ _ )\ (_))_|(_)) (_)) ((_)  
| _ \| |    (_)_\(_)/ __| |  \/  |(_)_\(_)| |_  |_ _|| _ \| __| 
|  _/| |__   / _ \  \__ \ | |\/| | / _ \  | __|  | | |   /| _|  
|_|  |____| /_/ \_\ |___/ |_|  |_|/_/ \_\ |_|   |___||_|_\|___|


*/
set_time_limit (600);
ini_set('memory_limit', '512M');
class Fighter {
    public $id;
    public $power;
    public $shield;
    public $hull;
    public $hullMax;
    
    public $shieldMax;
    
    function Fighter($id,$power,$shield,$hull){
        $this->id = $id;
        $this->power = $power;
        $this->hull = $hull;
        $this->hullMax = $hull;
        $this->shield = $shield;
        $this->shieldMax = $shield;
    }
    
    function damage($dmg){ // eating damage
        if($this->shield > 0){ // il y a encore du shield
            if($dmg >= $this->shield/100){ // il faut au moins 1/100 eme du shield en power pour l'endomager sinon le tir est simplement absorbé #ogameMechanics
                if($dmg > $this->shield){ // les dégats sont suffisant pour os le shield
                    $dmg-=$this->shield;
                    $this->shield = 0;
                    $this->damage($dmg);
                }else if($dmg < $this->shield){ // on endommage le shield
                    $this->shield -= $dmg;
                } else { // on a pile assez pour faire tomber le shield mais pas plus (rare)
                    $this->shield = 0;
                }
            }
        } else { // pas/plus de shield -> directdamage
            $this->directDamage($dmg);
        }
    }
    function directDamage($dmg){
        $this->hull -= $dmg;
    }
    function attack($target){ // this ship attacks ship "target" , return true if refire , false if no refire
        global $var;
        if(isset($var[$this->id]['stats']['multiplier'][$var[$target->id]['stats']['type']])){ // il y a un multiplicateur de dégats
            $target->damage($this->power * $var[$this->id]['stats']['multiplier'][$var[$target->id]['stats']['type']]);
        }else{ // pas de multiplicateur
            $target->damage($this->power);
        }
        if(isset($var[$this->id]['stats']['rf'][$target->id])){// chances de retirer
            $rf = $var[$this->id]['stats']['rf'][$target->id];
            return mt_rand(1,$rf) < $rf;// ex rf = 3 >  rand(1,3) < 3 -> 1/2 = true 3 = false -> 66% chance etc
        }
        return false;
    }
    function isDestroyed(){
        if($this->hull<= 0){// plus de vie -> destroyed
            return true;
        } else if($this->hull < ($this->hullMax/1.2)){ // vie inférieure a la moitié -> chance d'etre destroyed
            // pourcentage de la moitié ^ 2  resultat-> ~ 0 à 10000 , plus on a de vie restante , 
            // plus ça augmente rapidement , exemple : on a 4999 /10000 pv -> ~ 99% de la moitié ->  quasi 10000 / 10000, donc tres probable de survivre
            // si au contraire on a plus bcp ex : 2000 / 10000 pv -> 40% de la moitié ->  1600 / 10000
            return (mt_rand(0,10000) < pow(($this->hull / ($this->hullMax/1.2) * 100),2));
            
        } else {
            return false;
        }
    }
    function regenShield(){
        $this->shield = $this->shieldMax;
    }
}



/*
$atq = array(
200 => X
201 => X
etc

)
$def = $atq

$planetAtq = new Planet
etc


$return = array(
    "atq" => array(STUFFLEFT)
    "def" => array(STUFFLEFT)
    "wreckField" => array(0,1,2)
    "fret" => array(0,1,2)
    "rapport" => string
)
*/
function getFleetString($fleet,$username,$ships,$shoots,$location,$isAtq,$mode=0){
    global $var,$list;
    $stats = array("power","shield","hull");
    $statsFR = array("Dégats","Bouclier","Coque");
    $cols = 1;
    foreach(array_merge($list['ships'],$list['defenses']) AS $shipId){
        if(!empty($fleet[$shipId])){
            ++$cols;
            if(isset($shoots[$shipId])){
                $shoots[$shipId] += $fleet[$shipId]; // fixing "bug" because first shot is never counted
            }
        }
    }
    $rapport  = '<table class="content2"><tr><th colspan="'.$cols.'">'.(($isAtq) ? "Attaquant " : "Défenseur ") .$username.' ('.$location->toLink().')</th></tr>';
    if($mode == 1 && array_sum($fleet) == 0){
        $rapport .= '<tr><th colspan="'.$cols.'">Détruit!</th></tr>';
        return $rapport;
    }
    $rapport .= '<tr><td>Vaisseau</td>';
    foreach(array_merge($list['ships'],$list['defenses']) AS $shipId){ // noms des vaisseaux
        if(!empty($fleet[$shipId])){
            $rapport .= "<td>".$var[$shipId]['name']."</td>";
        }
    }
    $rapport .= '</tr><tr><td>Nombre</td>';
    foreach(array_merge($list['ships'],$list['defenses']) AS $shipId){ // nombre
        if(!empty($fleet[$shipId])){
            $rapport .= "<td>".format($fleet[$shipId])."</td>";
        }
    }
    $rapport .= "</tr>";
    if($mode == 1){
        return $rapport . "</table>";
    }
    
    for($i=0,$m=count($stats);$i<$m;++$i){ // stats DMG SHIELD HULL
        $rapport .= '<tr><td><span class="'.$stats[$i].'">'.$statsFR[$i].'</span></td>';
        foreach(array_merge($list['ships'],$list['defenses']) AS $shipId){
            if(!empty($fleet[$shipId])){
                $stat = $ships[$shipId]->getStat($stats[$i]);
                $rapport .= '<td><span class="'.$stats[$i].'">'.format($stat['total']).'</span></td>';
            }
        }
        $rapport .= '</tr>';
    }
    $rapport .= '</tr><tr><td>Tirs</td>';
    foreach(array_merge($list['ships'],$list['defenses']) AS $shipId){ // nombre
        if(!empty($fleet[$shipId])){
            $rapport .= "<td>".($shoots[$shipId])."</td>";
        }
    }
    $rapport .= '</tr>';
    $totalFirePower = 0;
    $rapport .= '</tr><tr><td><span class="power">Puissance totale</span></td>';
    foreach(array_merge($list['ships'],$list['defenses']) AS $shipId){ // nombre
        if(!empty($fleet[$shipId])){
            $fp = $ships[$shipId]->getStat("power");
            $totalFirePower += $fp['total'] * $shoots[$shipId];
            $rapport .= '<td><span class="power">'.format($fp['total'] * $shoots[$shipId])."</span></td>";
        }
    }
    $rapport .= '</tr>';
    $rapport .= '</table>';
    $rapport .= "La flotte " . (($isAtq) ? "attaquante" : "en défense") . " inflige un total de " . format($totalFirePower) . " à son ennemi.";
    
    return $rapport;
}
function plasmafire($atq,$def,$planetAtq,$planetDef){
    global $var,$list,$config,$pdo;
    
    $rapport = '';
    
    $atqInitial = $atq;
    $defInitial = $def;
    
    $req = $pdo->prepare("SELECT username FROM ".table("user")." WHERE id=:id");
    $req->bindValue(':id',$planetAtq->getUsedId(),PDO::PARAM_INT);
    $req->execute();
    $res = $req->fetch(PDO::FETCH_ASSOC);
    $atqUsername = $res['username'];
    
    $req->bindValue(':id',$planetDef->getUsedId(),PDO::PARAM_INT);
    $req->execute();
    $res = $req->fetch(PDO::FETCH_ASSOC);
    $defUsername = $res['username'];
    
    $atqFighters = array();
    $defFighters = array();
    
    $stats = array("power","shield","hull");
    $statsFR = array("Feu","Bouclier","Coque");
    
    foreach($list['ships'] AS $shipId){
        if(isset($atq[$shipId])){
            $atqShip[$shipId] = new Ship($shipId,$planetAtq);
            foreach($stats AS $stat){
                $u = $atqShip[$shipId]->getStat($stat);
                $$stat = $u['total'];
            }
            for($i=0;$i<$atq[$shipId];++$i){
                $atqFighters[] = new Fighter($shipId,$power,$shield,$hull);
            }
        }
    }
    foreach(array_merge($list['ships'],$list['defenses']) AS $shipId){
        if(isset($def[$shipId])){
            $defShip[$shipId] = new Ship($shipId,$planetDef);
            foreach($stats AS $stat){
                $u = $defShip[$shipId]->getStat($stat);
                $$stat = $u['total'];
            }
            for($i=0;$i<$def[$shipId];++$i){
                $defFighters[] = new Fighter($shipId,$power,$shield,$hull);
            }
        }
    } 
    $atqShips = count($atqFighters)-1;
    $defShips = count($defFighters)-1;
    for($turn=0;$turn<9;++$turn){ // gestion des TOURS
    
        $cols = 1;
        foreach($list['ships'] AS $shipId){
            if(!empty($atq[$shipId])){
                ++$cols;
            }
        }
        $atqShots = array(200=>0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
        $defShots = array(200=>0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,400=>0,0,0,0,0,0,0,0,0,0);
        for($i=0;$i<=$atqShips;++$i){// pour chaque atq
            while($atqFighters[$i]->attack($defFighters[mt_rand(0,$defShips)])){
                ++$atqShots[$atqFighters[$i]->id];
            }
        }
        for($i=0;$i<=$defShips;++$i){// pour chaque atq
            while($defFighters[$i]->attack($atqFighters[mt_rand(0,$atqShips)])){
                ++$defShots[$defFighters[$i]->id];
            }
        }
        
        $rapport .= getFleetString($atq,$atqUsername,$atqShip,$atqShots,$planetAtq->getLocation(),true);
        $rapport .= getFleetString($def,$defUsername,$defShip,$defShots,$planetDef->getLocation(),false);
        
        
        for($i=0;$i<=$atqShips;++$i){// pour chaque atq
            if($atqFighters[$i]->isDestroyed()){
                --$atq[$atqFighters[$i]->id];
                unset($atqFighters[$i]);
            } else {
                $atqFighters[$i]->regenShield();
            }
        }
        for($i=0;$i<=$defShips;++$i){// pour chaque atq
            if($defFighters[$i]->isDestroyed()){
                --$def[$defFighters[$i]->id];
                unset($defFighters[$i]);
            } else {
                $defFighters[$i]->regenShield();
            }
        }
        
        $atqFighters = arrayOrganize($atqFighters);
        $defFighters = arrayOrganize($defFighters);
        
        $atqShips = array_sum($atq) - 1;//count($atqFighters)-1;
        $defShips = array_sum($def) - 1;//count($defFighters)-1;
        if($atqShips < 0 || $defShips < 0){
            break;
        }
    }
    $rapport .= getFleetString($atq,$atqUsername,$atqShip,null,$planetAtq->getLocation(),true,1);
    $rapport .= getFleetString($def,$defUsername,$defShip,null,$planetDef->getLocation(),false,1);
    // détermination de l'issue de la battaille
    $rapport .= "<br />";
    if(array_sum($atq) > 0 && array_sum($def) == 0){
        $output = 1; // winner : ATQ
        $rapport .= "L'attaquant a gagné la battaille!";
    } else if(array_sum($atq) == 0 && array_sum($def) > 0){
        $output = 2; // winner : DEF
        $rapport .= "Le défenseur a gagné la battaille!";
    } else {
        $output = 0; // draw
        $rapport .= "La battaille se termine par un match nul!";
    }
    if($output == 1){ // calcul des ressources pillées
        $fretTotal = 0;
        foreach($atq AS $id=>$amount){
            if($amount > 0){
                $sh = new Ship($id,$planetAtq);
                $fr = $sh->getStat("fret");
                $fretTotal += $fr['total'] * $amount;
            }
        }
        $pillableRessources = array("total"=>0);
        foreach($list['ressources'] AS $resId){
            if($var[$resId]['isPalpable']){
                $pillableRessources["total"] += $pillableRessources[$resId] = floor($planetDef->getData($resId) * $config['raidRatio']);
            }
        }
        $ratio = min(1,$fretTotal/$pillableRessources['total']);
        $fret = array();
        $rapport .= "<br />Il pille ";
        foreach($list['ressources'] AS $resId){
            if($var[$resId]['isPalpable']){
                $fret[$resId] = floor($pillableRessources[$resId] * $ratio);
                $rapport .= $var[$resId]['name'] . ": " . displayRessource($resId,$fret[$resId]) . (($resId != 2) ? ', ' : '.');
            }
        }
        
    } else {
        $fret = array(0,0,0);
    }
    // Calcul des pertes
    $totalCasualties = array(0,0,0);
    $atqCasualties = array(0,0,0);
    $defCasualties = array(0,0,0);
    $wreckField = array(0,0,0);
    foreach($atqInitial AS $k=>$amount){ // calcul pertes ATQ
        foreach($list['ressources'] AS $resId){
            if($var[$resId]['isPalpable']){
                $atqCasualties[$resId] += $var[$k]["price"][$resId] * ($amount - $atq[$k]);
                $totalCasualties[$resId] += $var[$k]["price"][$resId] * ($amount - $atq[$k]);
                $wreckField[$resId] += floor(($var[$k]["price"][$resId] * ($amount - $atq[$k])) * $config['wreckRatio']);
            }
        }  
    }
    foreach($defInitial AS $k=>$amount){ // calcul pertes DEF
        foreach($list['ressources'] AS $resId){
            if($var[$resId]['isPalpable']){
                $defCasualties[$resId] += $var[$k]["price"][$resId] * ($amount - $def[$k]);
                $totalCasualties[$resId] += $var[$k]["price"][$resId] * ($amount - $def[$k]);
                if(in_array($k,$list['ships'])){
                    $wreckField[$resId] += floor(($var[$k]["price"][$resId] * ($amount - $def[$k])) * $config['wreckRatio']);
                }
            }
        }  
    }
    
    $rapport .= "<br />La flotte attaquante a perdu un total de ".format(array_sum($atqCasualties)).".";
    $rapport .= "<br />La flotte en défense a perdu un total de ".format(array_sum($defCasualties)).".";
    
    
    
    $rapport .= "<br />Un champs de débris contenant "; 
        foreach($list['ressources'] AS $resId){
            if($var[$resId]['isPalpable']){
                $rapport .= $var[$resId]['name'] . ": " . displayRessource($resId,$wreckField[$resId]) . (($resId != 2) ? ', ' : '');
            }
        }
    $rapport .= " se forme autour de la planète.";
    
    
    return array(
            "atq" => $atq,
            "def" => $def,
            "fret" => $fret,
            "wreckField" => $wreckField,
            "rapport" => $rapport,
            "output" => $output,
            "casualties" => array(
                    "atq" => array_sum($atqCasualties), 
                    "def" => array_sum($defCasualties)
                )
            );
}
?> 