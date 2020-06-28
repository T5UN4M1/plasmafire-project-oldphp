<?php

/**
 * @author T5UN4M1
 * @copyright 2015
 */ 



?>

<?php

function doesPlanetExist($loc){
    global $pdo;
    $req = $pdo->prepare("SELECT id FROM ".table("planet")." WHERE g=:g AND s=:s AND p=:p");
    $req->bindValue(":g",$loc->getG(),PDO::PARAM_INT);
    $req->bindValue(":s",$loc->getS(),PDO::PARAM_INT);
    $req->bindValue(":p",$loc->getP(),PDO::PARAM_INT);
    $req->execute();
    
    $res = $req->fetch(PDO::FETCH_ASSOC);
    return ((!empty($res['id'])) ? $res['id'] : false);
}
function getPlanetLazy($id,$time){
    global $pdo;
    $datPlanet = new Planet($id);
    $datPlanet->lazy($time);
    return $datPlanet;
}
function fleetComeBack(&$fleet){
    $duration = $fleet['endtime'] - $fleet['starttime'];
    $fleet['endtime'] += $duration ;
    $fleet['starttime'] += $duration ;
    $fleet['mission'] += 100;
}
function getShipsAmount($fleet){
    global $list;
    $amount = 0;
    foreach($list['ships'] AS $shipId){
        $amount += $fleet[$shipId];
    }
    return $amount;
}
function updateFleet($fleet){
    global $pdo,$list,$var;
    $ressourcesFields = array();
    foreach($list['ressources'] AS $resId){
        if($var[$resId]['isPalpable']){
            $ressourcesFields[] = "`".$resId."`=:".$resId;
        }
    }
    $shipsFields = array();
    foreach($list['ships'] AS $shipId){
        $shipsFields[] = "`".$shipId."`=:".$shipId;
    }


    $req = $pdo->prepare("UPDATE ".table("fleet")." SET
        starttime=:starttime,endtime=:endtime,
        mission=:mission,
        ".implode(',',$ressourcesFields).",
        ".implode(',',$shipsFields)."
        WHERE id=:id
        ");
    $req->bindValue(":id",$fleet['id'],PDO::PARAM_INT);
    $req->bindValue(":starttime",$fleet['starttime'],PDO::PARAM_INT);
    $req->bindValue(":endtime",$fleet['endtime'],PDO::PARAM_INT);
    
    $req->bindValue(":mission",$fleet['mission'],PDO::PARAM_INT);
    foreach($list['ressources'] AS $resId){
        if($var[$resId]['isPalpable']){
            $req->bindValue(":".$resId,$fleet[$resId],PDO::PARAM_INT);
        }
    }
    foreach($list['ships'] AS $shipId){
        $req->bindValue(":".$shipId,$fleet[$shipId],PDO::PARAM_INT);
    }
    $req->execute();   
}
function deleteFleet($fleet){
    global $pdo;
    $req = $pdo->prepare("DELETE FROM ".table("fleet")." WHERE id=:id");
    $req->bindValue(":id",$fleet['id'],PDO::PARAM_INT);
    $req->execute();
}
function fleetHandler($fleet){
    global $var,$pdo,$config,$list,$missionList;
    //===================================[FLEETS]===========================================
    $end = new Location($fleet['endg'],$fleet['ends'],$fleet['endp']);
    $start =  new Location($fleet['startg'],$fleet['starts'],$fleet['startp']);
    switch($fleet['mission']){
        // ====================================[ATTACK]========================================
        case 0:
            $id1=doesPlanetExist($end);
            if($id1){ // la planete existe
                $id0 = doesPlanetExist($start);
                $planet0 = getPlanetLazy($id0,$fleet['endtime']); // atq
                $planet1 = getPlanetLazy($id1,$fleet['endtime']); // def
                
                $atq = array();
                $def = array();
                
                foreach($list['ships'] AS $shipId){
                    $atq[$shipId] = $fleet[$shipId];
                    $def[$shipId] = $planet1->getData($shipId);
                }
                foreach($list['defenses'] AS $shipId){
                    $def[$shipId] = $planet1->getData($shipId);
                }
                $result = plasmafire($atq,$def,$planet0,$planet1);
                $req = $pdo->prepare("INSERT INTO ".table("battlerapport")." (id,`key`,rapport,time) VALUES (:id,:key,:rapport,:time)");
                $ID = keyGen(table("battlerapport"));
                $KEY = sha1($ID . mt_rand(1,1000000) .  $_SERVER['REQUEST_TIME'] . "TROLOLOLOLOLO" . mt_rand(1,10000000) . $_SERVER["QUERY_STRING"] . "THATSENOUGHRANDOMSTUFFESRIGHT");
                $req->bindValue(":id",$ID,PDO::PARAM_INT);
                $req->bindValue(":key",$KEY,PDO::PARAM_STR);
                $req->bindValue(":rapport",$result['rapport'],PDO::PARAM_STR);
                $req->bindValue(":time",$_SERVER['REQUEST_TIME'],PDO::PARAM_INT);
                $req->execute();
                
                switch($result['output']){
                    case 0: // draw
                        foreach($result['atq'] AS $k=>$v){
                            $fleet[$k] = $v;
                        }
                        foreach($result['def'] AS $k=>$v){
                            $planet1->setData($k,$v);
                        }
                        $msg = "Votre flotte partie de ".$start->toLink()." a livré battaille en ".$end->toLink().".<br />";
                        $msg.= '<span class="presque"><a href="?a=rc&id='.$ID.'&key='.$KEY.'">
                        Rapport (Pertes attaquant : ' . format($result['casualties']['atq']) . ' Pertes défenseur : ' .  format($result['casualties']['def']) . ')</a></span>';
                        msg(0,$fleet['userid'],2,$fleet['endtime'],"Rapport de combat (match nul)",$msg);
                        
                        $msg = "Une flotte ennemie partie de ".$start->toLink()." a livré battaille chez vous en ".$end->toLink().".<br />";
                        $msg.= '<span class="presque"><a href="?a=rc&id='.$ID.'&key='.$KEY.'">
                        Rapport (Pertes attaquant : ' . format($result['casualties']['atq']) . ' Pertes défenseur : ' .  format($result['casualties']['def']) . ')</a></span>';
                        msg(0,$fleet['enduserid'],2,$fleet['endtime'],"Rapport de combat (match nul)",$msg);
                        fleetComeBack($fleet);
                        updateFleet($fleet);
                        break;
                    case 1: // win atq
                        foreach($result['atq'] AS $k=>$v){
                            $fleet[$k] = $v;
                        }
                        foreach($result['def'] AS $k=>$v){
                            $planet1->setData($k,$v);
                        }
                        foreach($result['fret'] AS $resId=>$val){
                            $fleet[$resId] = $val;
                        }
                        $msg = "Votre flotte partie de ".$start->toLink()." a livré battaille en ".$end->toLink().".<br />";
                        $msg.= '<span class="good"><a href="?a=rc&id='.$ID.'&key='.$KEY.'">
                        Rapport (Pertes attaquant : ' . format($result['casualties']['atq']) . ' Pertes défenseur : ' .  format($result['casualties']['def']) . ')</a></span>';
                        msg(0,$fleet['userid'],2,$fleet['endtime'],"Rapport de combat (Victoire)",$msg);
                        
                        $msg = "Une flotte ennemie partie de ".$start->toLink()." a livré battaille chez vous en ".$end->toLink().".<br />";
                        $msg.= '<span class="bad"><a href="?a=rc&id='.$ID.'&key='.$KEY.'">
                        Rapport (Pertes attaquant : ' . format($result['casualties']['atq']) . ' Pertes défenseur : ' .  format($result['casualties']['def']) . ')</a></span>';
                        msg(0,$fleet['enduserid'],2,$fleet['endtime'],"Rapport de combat (Défaite)",$msg);
                        fleetComeBack($fleet);
                        updateFleet($fleet);
                        break;
                    default : // win def
                        foreach($result['def'] AS $k=>$v){
                            $planet1->setData($k,$v);
                        }
                        $msg = "Votre flotte partie de ".$start->toLink()." a livré battaille en ".$end->toLink()." et s'est retrouvée entièrement annéantie.<br />";
                        $msg.= '<span class="bad"><a href="?a=rc&id='.$ID.'&key='.$KEY.'">
                        Rapport (Pertes attaquant : ' . format($result['casualties']['atq']) . ' Pertes défenseur : ' .  format($result['casualties']['def']) . ')</a></span>';
                        msg(0,$fleet['userid'],2,$fleet['endtime'],"Rapport de combat (Défaite)",$msg);
                        
                        $msg = "Une flotte ennemie partie de ".$start->toLink()." a livré battaille chez vous en ".$end->toLink().".<br />";
                        $msg.= '<span class="good"><a href="?a=rc&id='.$ID.'&key='.$KEY.'">
                        Rapport (Pertes attaquant : ' . format($result['casualties']['atq']) . ' Pertes défenseur : ' .  format($result['casualties']['def']) . ')</a></span>';
                        msg(0,$fleet['enduserid'],2,$fleet['endtime'],"Rapport de combat (Victoire)",$msg);
                        deleteFleet($fleet);
                        break;
                }
                if(array_sum($result['wreckField']) > 0){ // il y a un champs de débris
                    $req = $pdo->prepare("SELECT * FROM ".table("wreck")." WHERE posg=:g AND poss=:s AND posp=:p");
                    $req->bindValue(":g",$end->getG(),PDO::PARAM_INT);
                    $req->bindValue(":s",$end->getS(),PDO::PARAM_INT);
                    $req->bindValue(":p",$end->getP(),PDO::PARAM_INT);
                    $req->execute();
                    $res = $req->fetch(PDO::FETCH_ASSOC);
                    if(!empty($res['posg'])){ // un champs de débris existe déjà
                        $req = $pdo->prepare("UPDATE ".table("wreck")." SET `0`=`0`+:0,`1`=`1`+:1,`2`=`2`+:2 WHERE posg=:g AND poss=:s AND posp=:p");
                        $req->bindValue(":g",$end->getG(),PDO::PARAM_INT);
                        $req->bindValue(":s",$end->getS(),PDO::PARAM_INT);
                        $req->bindValue(":p",$end->getP(),PDO::PARAM_INT);
                        for($i=0;$i<3;++$i){
                            $req->bindValue(":".$i,$result['wreckField'][$i],PDO::PARAM_INT);
                        }
                        $req->execute();
                    } else { // on doit creer le champs de débris
                        $req = $pdo->prepare("INSERT INTO ".table("wreck")." (posg,poss,posp,`0`,`1`,`2`) VALUES (:g,:s,:p,:0,:1,:2)");
                        $req->bindValue(":g",$end->getG(),PDO::PARAM_INT);
                        $req->bindValue(":s",$end->getS(),PDO::PARAM_INT);
                        $req->bindValue(":p",$end->getP(),PDO::PARAM_INT);
                        for($i=0;$i<3;++$i){
                            $req->bindValue(":".$i,$result['wreckField'][$i],PDO::PARAM_INT);
                        }
                        $req->execute();
                    }
                }
                // update des planetes
                $planet1->proceedUpdate();
                $planet0->proceedUpdate();
                
                
                
            } else { // la planete n'existe pas
                $msg = "Votre flotte partie de ".$start->toLink()." a atteint une planète inhabitée en ".$end->toLink()." avec comme mission : Attaquer.<br />Elle fait donc demi tour.";
                msg(0,$fleet['userid'],2,$fleet['endtime'],"Attaque (échec)",$msg);
                fleetComeBack($fleet);
                updateFleet($fleet);
            }
            break;
        // ====================================[TRANSPORT]========================================
        case 1:
            $id=doesPlanetExist($end);
            if($id){
                $pl = getPlanetLazy($id,$fleet['endtime']);
                $addedRessources = '';
                foreach($list['ressources'] AS $resId){
                    if($var[$resId]['isPalpable']){
                        if($fleet[$resId] > 0){
                            $pl->setData($resId,$pl->getData($resId) + $fleet[$resId]);
                            $addedRessources .= '<br />' . $var[$resId]['name'] . ' : ' . displayRessource($resId,$fleet[$resId]);
                            $fleet[$resId] = 0;
                        }
                    }
                }
                $ressourcesAdd = "";
                if(!empty($addedRessources)){
                    $ressourcesAdd = "<br /> Elle transportait les ressources suivantes : " . $addedRessources;
                }
                
                if($fleet['userid'] == $fleet['enduserid']){ // un joueur qui s'envoit des ressources a lui meme
                    $msg = "Votre flotte partie de ".$start->toLink()." a atteint votre autre planète ".$end->toLink()." avec comme mission : Transporter." . $ressourcesAdd;

                     msg(0,$fleet['userid'],2,$fleet['endtime'],"Transport de ressources",$msg);
                } else { // un joueur a livré a un autre
                    $msg = "Votre flotte partie de ".$start->toLink()." a atteint la planète ".$end->toLink()." avec comme mission : Transporter." . $ressourcesAdd;
                     msg(0,$fleet['userid'],2,$fleet['endtime'],"Transport de ressources",$msg);
                     $msg = "Une flotte venant de ".$start->toLink()." a atteint votre planète ".$end->toLink()." avec comme mission : Transporter." . $ressourcesAdd;
                     msg(0,$fleet['enduserid'],2,$fleet['endtime'],"Transport de ressources",$msg);
                }
                $pl->proceedUpdate();
            } else {
                $msg = "Votre flotte partie de ".$start->toLink()." a atteint une planète inhabitée en ".$end->toLink()." avec comme mission : Transporter.<br />Elle fait donc demi tour.";
                 msg(0,$fleet['userid'],2,$fleet['endtime'],"Transport de ressources",$msg);
            }
            
            fleetComeBack($fleet);
            updateFleet($fleet);
            break;
        // ====================================[BASE]========================================
        case 2:
            $id=doesPlanetExist($end);
            if($id){
                $pl = getPlanetLazy($id,$fleet['endtime']);
                $addedRessources = '';
                foreach($list['ressources'] AS $resId){
                    if($var[$resId]['isPalpable']){
                        if($fleet[$resId] > 0){
                            $pl->setData($resId,$pl->getData($resId) + $fleet[$resId]);
                            $addedRessources .= '<br />' . $var[$resId]['name'] . ' : ' . displayRessource($resId,$fleet[$resId]);
                            $fleet[$resId] = 0;
                        }
                    }
                }
                $addedShips = '';
                foreach($list['ships'] AS $shipId){
                    if($fleet[$shipId] > 0){
                        $pl->setData($shipId,$pl->getData($shipId) + $fleet[$shipId]);
                        $addedShips .= '<br />' . $var[$shipId]['name'] . ' : ' . format($fleet[$shipId]);
                        $fleet[$shipId] = 0;
                    }
                }
                $ressourcesAdd = "";
                $shipsAdd = "";
                if(!empty($addedRessources)){
                    $ressourcesAdd = "<br /> Elle transportait les ressources suivantes : " . $addedRessources;
                }
                if(!empty($addedRessources)){
                    $shipsAdd = "<br /> La flotte ajoutée consistait de : " . $addedShips;
                }
                if($fleet['userid'] == $fleet['enduserid']){ // un joueur qui s'envoit une flotte a lui meme
                    $msg = "Votre flotte partie de ".$start->toLink()." a atteint votre autre planète ".$end->toLink()." avec comme mission : Stationner." . $ressourcesAdd . $shipsAdd;

                     msg(0,$fleet['userid'],2,$fleet['endtime'],"Stationnement de flotte",$msg);
                } else { // un joueur a livré a un autre
                    $msg = "Votre flotte partie de ".$start->toLink()." a atteint la planète ".$end->toLink()." avec comme mission : Stationner." . $ressourcesAdd . $shipsAdd;
                     msg(0,$fleet['userid'],2,$fleet['endtime'],"Stationnement de flotte",$msg);
                     $msg = "Une flotte venant de ".$start->toLink()." a atteint votre planète ".$end->toLink()." avec comme mission : Stationner." . $ressourcesAdd . $shipsAdd;
                     msg(0,$fleet['enduserid'],2,$fleet['endtime'],"Stationnement de flotte",$msg);
                }
                $pl->proceedUpdate();
                deleteFleet($fleet);
            } else {
                $msg = "Votre flotte partie de ".$start->toLink()." a atteint une planète inhabitée en ".$end->toLink()." avec comme mission : Stationner.<br />Elle fait donc demi tour.";
                msg(0,$fleet['userid'],2,$fleet['endtime'],"Stationnement de flotte",$msg);
                fleetComeBack($fleet);
                updateFleet($fleet);
            }

            break;
        // ====================================[VCOLO]========================================
        case 3:
            $id = doesPlanetExist($end);
            if(!$id && $fleet[213] > 0){ // on cherche une planete qui n'existe pas & il faut 1 vcolo
                $req = $pdo->prepare("SELECT COUNT(*) FROM ".table("planet")." WHERE userid=:userid");
                $req->bindValue(":userid",$fleet['userid'],PDO::PARAM_INT);
                $req->execute();
                $res = $req->fetch(PDO::FETCH_NUM);
                $planetAmount = $res[0];
                $origin = doesPlanetExist($start);
                $planetOrigin = getPlanetLazy($origin,$fleet['endtime']);
                $administrationLevel = $planetOrigin->getPlayer()->getTech(312);
                if(($administrationLevel + 1) > $planetAmount){// on a assez en tech , on colonise
                    $newPlanetData = $config['baseRessources'];
                    $newPlanet = new Planet();
                    foreach($list['ressources'] AS $resId){
                        if($var[$resId]['isPalpable']){
                            if($fleet[$resId] > 0){
                                $newPlanetData[$resId] += $fleet[$resId];
                                $fleet[$resId] = 0;
                            }
                        }
                    }
                    $cases = 0;
                    switch(($end->getP())){ ////////////////////////// CASES ///////////////////////////////// ==={}===
                        case 1:
                            if(mt_rand(1,100) < 81){ /*80chances sur 100*/$cases = mt_rand(80,180);} else {$cases = mt_rand(40,600);}break;
                        case 2:
                            if(mt_rand(1,100) < 81){$cases = mt_rand(300,600);} else {$cases = mt_rand(40,600);}break;
                        case 3:
                            if(mt_rand(1,100) < 81){$cases = mt_rand(240,480);} else {$cases = mt_rand(40,600);}break;
                        case 4:
                            if(mt_rand(1,100) < 81){$cases = mt_rand(180,360);} else {$cases = mt_rand(40,600);}break;
                        case 5:
                            if(mt_rand(1,100) < 81){$cases = mt_rand(120,240);} else {$cases = mt_rand(40,600);} break;
                        default:$cases = mt_rand(40,600);
                    }
                    $newPlanetData['case'] = $cases;
                    $newPlanet->createNewPlanet($fleet['userid'],"Colonie",false,$end,mt_rand(1,55),$newPlanetData);
                    --$fleet[213];
                    if(getShipsAmount($fleet) > 0){ // on a colonisé et il reste des vaisseaux
                        fleetComeBack($fleet);
                        updateFleet($fleet);
                        
                        $msg = "Votre flotte partie de ".$start->toLink()." a colonisé la planète en ".$end->toLink().".Les vaisseaux restants ont livré leurs ressources et fait demi-tour.";
                         msg(0,$fleet['userid'],2,$fleet['endtime'],"Colonisation",$msg);
                    }else{
                        deleteFleet($fleet);
                        $msg = "Votre flotte partie de ".$start->toLink()." a colonisé la planète en ".$end->toLink().".";
                        msg(0,$fleet['userid'],2,$fleet['endtime'],"Colonisation",$msg);
                    }
                } else { // pas de tech , on revient
                    
                    $msg = "Votre flotte partie de ".$start->toLink()." a voulu coloniser la planète en ".$end->toLink().".Cela n'a pas été possible car les facultés d'administration de votre empire ne sont pas assez développées.Les vaisseaux ont fait demi-tour";
                    msg(0,$fleet['userid'],2,$fleet['endtime'],"Colonisation (échec)",$msg);
                    fleetComeBack($fleet);
                    updateFleet($fleet);
                }
                $planetOrigin->proceedUpdate();
            } else if($id){// on a une planete qui existe déjà à cette endroit
                
                $msg = "Votre flotte partie de ".$start->toLink()." a atteint la planète en ".$end->toLink()." dans le but de la coloniser.Ils se sont cependant aperçu que la planète est déjà habitée.Ils ont donc fait demi-tour.";
                msg(0,$fleet['userid'],2,$fleet['endtime'],"Colonisation (échec)",$msg);
                fleetComeBack($fleet);
                updateFleet($fleet);
            } else { // la planete existe pas mais on a pas de Vcolo
                $msg = "Votre flotte partie de ".$start->toLink()." a atteint la planète en ".$end->toLink()." dans le but de la coloniser.Il n'y avait pas devaisseau capable de coloniser la planète et ils ont donc fait demi-tour.";
                msg(0,$fleet['userid'],2,$fleet['endtime'],"Colonisation (échec)",$msg);
                fleetComeBack($fleet);
                updateFleet($fleet);
            }
            break;
        // ====================================[ECOLO]========================================
        case 4:
            if($fleet[212] > 0 || $fleet[217 > 0]){ // on a des vaisseaux capables de recycler
                // on commence par choper le wreckField
                $ressourcesFields = array();
                foreach($list['ressources'] AS $resId){
                    if($var[$resId]['isPalpable']){
                        $ressourcesFields[] = "`".$resId."`";
                    }
                }
                $req = $pdo->prepare("SELECT ". implode(",",$ressourcesFields) . " FROM ".table('wreck') . " WHERE posg=:g AND poss=:s AND posp=:p");
                $req->bindValue(":g",$end->getG(),PDO::PARAM_INT);
                $req->bindValue(":s",$end->getS(),PDO::PARAM_INT);
                $req->bindValue(":p",$end->getP(),PDO::PARAM_INT);
                $req->execute();
                $wreckField = $req->fetch(PDO::FETCH_NUM);
                if(array_sum($wreckField) > 0){ // on a wreckfield
                    // il faut maintenant calculer le fret total , le fret capable de recycler et la qté de ressource déjà embarquer pour conclure quelle quantité de débris peuvent etre recyclés
                    $origin = doesPlanetExist($start);
                    $planetOrigin = getPlanetLazy($origin,$fleet['endtime']);
                    
                    $fretNonRecyclage = 0;
                    $fretRecyclage = 0;
                    foreach($list['ships'] AS $shipId){
                        if($fleet[$shipId] > 0){
                            $ship = new Ship($shipId,$planetOrigin);
                            $fret = $ship->getStat("fret");
                            if($shipId == 212 || $shipId == 217){
                                $fretRecyclage += $fret['total'] * $fleet[$shipId];
                            } else {
                                $fretNonRecyclage += $fret['total'] * $fleet[$shipId];
                            }
                        }
                    }
                    $totalRes = 0;
                    foreach($list['ressources'] AS $resId){
                        if($var[$resId]['isPalpable']){
                            $totalRes += $fleet[$resId];
                        }
                    }
                    $fretNonRecyclage -= $totalRes;
                    if($fretNonRecyclage < 0){
                        $fretRecyclage += $fretNonRecyclage;
                    }
                    if($fretRecyclage > 0){ // on a encore de la place pour recycler
                        $ratio = $fretRecyclage / array_sum($wreckField);
                        $addedRessources = "";
                        if($ratio >= 1){ // on a la place pour recycler tout le wreckfield
                            foreach($list['ressources'] AS $resId){
                                if($var[$resId]['isPalpable']){
                                    $fleet[$resId] += $wreckField[$resId];
                                    $addedRessources .= '<br />' . $var[$resId]['name'] . displayRessource($resId,$wreckField[$resId]);
                                }
                            }
                            $ressourcesAdd = "<br /> Elle a recyclé les ressources suivantes : " . $addedRessources;
                            // on a tout recyclé , supression du champs de débris.
                            $req = $pdo->prepare("DELETE FROM ".table("wreck")." WHERE posg=:g AND poss=:s AND posp=:p");
                            $req->bindValue(":g",$end->getG(),PDO::PARAM_INT);
                            $req->bindValue(":s",$end->getS(),PDO::PARAM_INT);
                            $req->bindValue(":p",$end->getP(),PDO::PARAM_INT);
                            $req->execute();
                            $msg = "Votre flotte partie de ".$start->toLink()." a atteint le champs de débris en ".$end->toLink()."." . $ressourcesAdd;
                            msg(0,$fleet['userid'],2,$fleet['endtime'],"Recyclage",$msg);
                            fleetComeBack($fleet);
                            updateFleet($fleet);
                        } else {
                            foreach($list['ressources'] AS $resId){
                                if($var[$resId]['isPalpable']){
                                    $ecoed[$resId] = floor($wreckField[$resId]*$ratio);
                                    $fleet[$resId] += $ecoed[$resId];
                                    $addedRessources .= '<br />' . $var[$resId]['name'] . format($ecoed[$resId]);
                                    $wreckField[$resId] -= $ecoed[$resId];
                                }
                            }
                            $ressourcesAdd = "<br /> Elle a recyclé les ressources suivantes : " . $addedRessources;
                            // on a recyclé une partie , on update
                            $ressourcesFields = array();
                            foreach($list['ressources'] AS $resId){
                                if($var[$resId]['isPalpable']){
                                    $ressourcesFields[] = "`".$resId."`=:".$resId;
                                }
                            }
                            $req = $pdo->prepare("UPDATE ".table("wreck")." SET ".implode(",",$ressourcesFields)." WHERE posg=:g AND poss=:s AND posp=:p");
                            foreach($list['ressources'] AS $resId){
                                if($var[$resId]['isPalpable']){
                                    $req->bindValue(":".$resId,$wreckField[$resId],PDO::PARAM_INT);
                                }
                            }
                            $req->bindValue(":g",$end->getG(),PDO::PARAM_INT);
                            $req->bindValue(":s",$end->getS(),PDO::PARAM_INT);
                            $req->bindValue(":p",$end->getP(),PDO::PARAM_INT);
                            $req->execute();
                            $msg = "Votre flotte partie de ".$start->toLink()." a atteint le champs de débris en ".$end->toLink()."." . $ressourcesAdd;
                            msg(0,$fleet['userid'],2,$fleet['endtime'],"Recyclage",$msg);
                            fleetComeBack($fleet);
                            updateFleet($fleet);
                        }
                    } else { // on peut pas recycler car on a pas de place
                        $msg = "Votre flotte partie de ".$start->toLink()." a atteint la planète en ".$end->toLink()." dans le but de recycler un champs de débris.Il n'y avait cependant plus de place dans les soutes et ils ont donc fait demi-tour.";
                        msg(0,$fleet['userid'],2,$fleet['endtime'],"Recyclage (échec)",$msg);
                        fleetComeBack($fleet);
                        updateFleet($fleet);
                    }
                    $planetOrigin->proceedUpdate();
                }else{ // pas de wreckfield
                    $msg = "Votre flotte partie de ".$start->toLink()." a atteint la planète en ".$end->toLink()." dans le but de recycler un champs de débris.Il n'y avait cependant pas de champs de débris et ils ont donc fait demi-tour.";
                    msg(0,$fleet['userid'],2,$fleet['endtime'],"Recyclage (échec)",$msg);
                    fleetComeBack($fleet);
                    updateFleet($fleet);
                }
            } else { // pas de vs qui peut recycler
                $msg = "Votre flotte partie de ".$start->toLink()." a atteint la planète en ".$end->toLink()." dans le but de recycler un champs de débris.Il n'y avait cependant pas de vaisseau capable de mener à bien cette mission et ils ont donc fait demi-tour.";
                msg(0,$fleet['userid'],2,$fleet['endtime'],"Recyclage (échec)",$msg);
                fleetComeBack($fleet);
                updateFleet($fleet);
            }
        
            
            break;
        // ====================================[YUNO]========================================
        case 5:
            $id0 = doesPlanetExist($end);
            if($id0 && $fleet[205] > 0){// la planete existe et on a au moins une sonde
                $id1 = doesPlanetExist($start);
                $planet[0] = getPlanetLazy($id0,$fleet['endtime']);
                $planet[1] = getPlanetLazy($id1,$fleet['endtime']);
                $planet[0]->proceedUpdate();
                $planet[1]->proceedUpdate();
                for($bonusProbes=-1,$probes=$fleet[205];$probes>=1;++$bonusProbes){
                    $probes /= 3;
                }
                $oyashirosamaGaStalkinLevel = /* = 4 FOR >>SPYHACK<<*/($planet[1]->getPlayer()->getTech(302) - $planet[0]->getPlayer()->getTech(302)) + $bonusProbes;
                $rapport = '';
                $rapport .= '<table style="width:800px;">';
                $rapport .= '<tr><th colspan="5">Rapport d\'espionnage de la planete '.$end->toLink().'</th></tr>';
                $rapport .= '<tr><th colspan="5">Ressources</th></tr>';
                $rapport .= '<tr>';
                foreach($list['ressources'] AS $resId){
                    if($var[$resId]['isPalpable']){
                        $rapport .= '<td>'.$var[$resId]['name'].": ".displayRessource($resId,$planet[0]->getData($resId)) . "</td>";
                    }
                }
                $rapport .= '</tr>';
                if($oyashirosamaGaStalkinLevel > 0){ // assez de stalkin level pour stalk les batiments
                    $rapport .= '<tr><th colspan="5">Batiments</th></tr>';
                    $col = 0;
                    foreach($list['buildings'] AS $itemId){
                        if($planet[0]->getData($itemId) > 0){
                            if($col == 0){
                                $rapport .= "<tr>";
                            }
                            $rapport .= "<td>".$var[$itemId]['name']. ": ". $planet[0]->getData($itemId)."</td>";
                            ++$col;
                            if($col == 5){
                                $rapport .= "</tr>";
                                $col = 0;
                            }
                        }
                    }
                    if($col != 0){
                        $rapport .= '</tr>';
                    }
                }
                if($oyashirosamaGaStalkinLevel > 1){ // assez de stalkin level pour stalk les vaisseaux
                    $rapport .= '<tr><th colspan="5">Vaisseaux</th></tr>';
                    $col = 0;
                    foreach($list['ships'] AS $itemId){
                        if($planet[0]->getData($itemId) > 0){
                            if($col == 0){
                                $rapport .= "<tr>";
                            }
                            $rapport .= "<td>".$var[$itemId]['name']. ": ". format($planet[0]->getData($itemId))."</td>";
                            ++$col;
                            if($col == 5){
                                $rapport .= "</tr>";
                                $col = 0;
                            }
                        }
                    }
                    if($col != 0){
                        $rapport .= '</tr>';
                    }
                }
                if($oyashirosamaGaStalkinLevel > 2){ // assez de stalkin level pour stalk les défenses
                    $rapport .= '<tr><th colspan="5">Défenses</th></tr>';
                    $col = 0;
                    foreach($list['defenses'] AS $itemId){
                        if($planet[0]->getData($itemId) > 0){
                            if($col == 0){
                                $rapport .= "<tr>";
                            }
                            $rapport .= "<td>".$var[$itemId]['name']. ": ". format($planet[0]->getData($itemId))."</td>";
                            ++$col;
                            if($col == 5){
                                $rapport .= "</tr>";
                                $col = 0;
                            }
                        }
                    }
                    if($col != 0){
                        $rapport .= '</tr>';
                    }
                }
                if($oyashirosamaGaStalkinLevel > 3){ // assez de stalkin level pour stalk les recherches
                    $rapport .= '<tr><th colspan="5">Recherches</th></tr>';
                    $col = 0;
                    foreach($list['researches'] AS $itemId){
                        if($planet[0]->getPlayer()->getTech($itemId) > 0){
                            if($col == 0){
                                $rapport .= "<tr>";
                            }
                            $rapport .= "<td>".$var[$itemId]['name']. ": ". $planet[0]->getPlayer()->getTech($itemId)."</td>";
                            ++$col;
                            if($col == 5){
                                $rapport .= "</tr>";
                                $col = 0;
                            }
                        }
                    }
                    if($col != 0){
                        $rapport .= '</tr>';
                    }
                }
                $rapport .= '</table>';
                msg(0,$fleet['userid'],2,$fleet['endtime'],"Espionnage",$rapport);
                $msg = "Une flotte partie de ".$start->toLink()." a été aperçue près de votre planète en ".$end->toLink()." en train de l'espionner.";
                msg(0,$fleet['enduserid'],2,$fleet['endtime'],"Espionnage hostile détecté",$msg);
                fleetComeBack($fleet);
                updateFleet($fleet);
            }else if($id0){// y'a une planete mais on a pas de sondes
                $msg = "Votre flotte partie de ".$start->toLink()." a atteint la planète en ".$end->toLink()." dans le but de l'espionner.Il n'y avait cependant pas de vaisseau capable de mener à bien cette mission et ils ont donc fait demi-tour.";
                msg(0,$fleet['userid'],2,$fleet['endtime'],"Espionnage (échec)",$msg);
                fleetComeBack($fleet);
                updateFleet($fleet);
            }else{// pas de planete
                $msg = "Votre flotte partie de ".$start->toLink()." a atteint la planète en ".$end->toLink()." dans le but de l'espionner.Il n'y avait cependant pas de trace de civilisation à cet endroit et ils ont donc fait demi-tour.";
                msg(0,$fleet['userid'],2,$fleet['endtime'],"Espionnage (échec)",$msg);
                fleetComeBack($fleet);
                updateFleet($fleet);
            }
            break;
        // ====================================[RETOURS]========================================
        case 100:case 101:case 102:case 103:case 104:case 105:
            $id = doesPlanetExist($start);
            $planet = getPlanetLazy($id,$fleet['endtime']);
               $addedRessources = '';
                foreach($list['ressources'] AS $resId){
                    if($var[$resId]['isPalpable']){
                        if($fleet[$resId] > 0){
                            $planet->setData($resId,$planet->getData($resId) + $fleet[$resId]);
                            $addedRessources .= '<br />' . $var[$resId]['name'] . ' : ' . displayRessource($resId,$fleet[$resId]);
                            $fleet[$resId] = 0;
                        }
                    }
                }
                $addedShips = '';
                foreach($list['ships'] AS $shipId){
                    if($fleet[$shipId] > 0){
                        $planet->setData($shipId,$planet->getData($shipId) + $fleet[$shipId]);
                        $addedShips .= '<br />' . $var[$shipId]['name'] . ' : ' . format($fleet[$shipId]);
                        $fleet[$shipId] = 0;
                    }
                }
                $ressourcesAdd = "";
                $shipsAdd = "";
                if(!empty($addedRessources)){
                    $ressourcesAdd = "<br /> Elle transportait les ressources suivantes : " . $addedRessources;
                }
                if(!empty($addedRessources)){
                    $ressourcesAdd = "<br /> La flotte ajoutée consistait de : " . $addedShips;
                }
               $planet->proceedUpdate();
                $msg = "Votre flotte revient sur ".$start->toLink()." après etre parti en ".$end->toLink()." avec comme mission : ".$missionList[$fleet["mission"]%100].".".$addedRessources . $addedShips;
                msg(0,$fleet['userid'],2,$fleet['endtime'],"Retour",$msg);
                deleteFleet($fleet);
            break;
        
        
        
        
    }
}



$fleetReq = $pdo->prepare("SELECT * FROM ".table("fleet")." WHERE endtime<=:endtime ORDER BY endtime");
$fleetReq->bindValue(":endtime",$_SERVER['REQUEST_TIME'],PDO::PARAM_INT);
$fleetReq->execute();
$fleets = array();
while($line = $fleetReq->fetch(PDO::FETCH_ASSOC)){
    $fleets[] = $line;
}
for($i=0,$max=count($fleets);$i<$max;++$i){
    fleetHandler($fleets[$i]);
}