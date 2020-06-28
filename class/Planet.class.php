<?php

/**
 * @author T5UN4M1
 * @copyright 2015
 */ 

class Planet{
    
    private $id;
    private $userid;
    
    private $name;
    
    private $ismain;
    
    private $location;  //  Location
    
    private $img;
    
    private $data = array();
    
    private $updated = array(); //
    
    private $making;
    
    private $player; // Player
    
    function Planet(){
        $data = func_get_args();
        switch(func_num_args()){
            case 0:
                break;
            case 1: // planetID
                $this->id = $data[0];
                break;
            case 3: // GSP
                $this->location = new Location($data[0],$data[1],$data[2]);
                break;
        }
    }
    function getId(){
        return $this->id;
    }
    function lazy($time){// get all data and UPDATE to time
        $this->getPlanetData();
        
        $this->setPlayer(new Player($this->userid));
        
        $this->getPlayer()->getData();
        
        $this->updatePlanet($time);
    }
    static function seekSpotForNewPlayer(){// chercher un spot pour placer un nouveau joueur
        global $config,$pdo;
        $g = 1;
        $s = 1;
        $p = 3;
        $req = $pdo->prepare('SELECT COUNT(*) AS result FROM '. table('planet') .' WHERE g=:g AND s=:s AND p=:p');
        
        $req->bindParam(':g',$g,PDO::PARAM_INT);
        $req->bindParam(':s',$s,PDO::PARAM_INT);
        $req->bindParam(':p',$p,PDO::PARAM_INT);
        
        $level = 0;
        while(true){
            if($level === 0){ // premier passage , tentative de poser un joueur de maniere assez random
                $p += mt_rand(2,6);
                if($p>($config['maxP']-4)){
                    $p = mt_rand(4,8);
                    $s += mt_rand(3,8);
                    if($s>$config['maxS']){
                        $s = mt_rand(1,40);
                        ++$g;
                        if($g>$config['maxG']){
                            $g = 1;
                            $s = 1;
                            $p = 4;
                            ++$level;
                        }
                    }
                }
            } else { // on a pas pu placer un joueur en cherchant des spots aléatoirement , cette fois ci on essaye chaque spot disponible
                ++$p;
                if($p>($config['maxP']-4)){
                    $p = 4;
                    ++$s;
                    if($s>$config['maxS']){
                        $s =1;
                        ++$g;
                        if($g>$config['maxG']){
                            error(__FILE__,__LINE__,"could not add a new player, server seems to be full.PLEASE CONTACT ADMINISTRATOR.");
                        }
                    }
                }
            }
            $req->execute();
            $res = $req->fetch(PDO::FETCH_ASSOC);
            if($res['result'] == 0){
                return new Location($g,$s,$p);
            }
        }
    }
    
    function createNewPlanet($userid,$name,$ismain,$location,$img,$data){
        global $list,$pdo;
        
        $this->id = keyGen(table('planet'));
        $this->userid = $userid;
        $this->name = $name;
        $this->ismain = $ismain;
        $this->location = $location;
        $this->img = $img;
        $this->data = $data;
        
        $req = $pdo->prepare("INSERT INTO ".table('planet')." (id,userid,name,ismain,g,s,p,img) VALUES (:id,:userid,:name,:ismain,:g,:s,:p,:img)");
        
        $req->bindValue(':id',$this->id,PDO::PARAM_INT);
        $req->bindValue(':userid',$this->userid,PDO::PARAM_INT);
        $req->bindValue(':name',$this->name);
        $req->bindValue(':ismain',$this->ismain,PDO::PARAM_INT);
        $req->bindValue(':g',$this->location->getG(),PDO::PARAM_INT);
        $req->bindValue(':s',$this->location->getS(),PDO::PARAM_INT);
        $req->bindValue(':p',$this->location->getP(),PDO::PARAM_INT);
        $req->bindValue(':img',$this->img,PDO::PARAM_INT);
        
        $req->execute();
        
        // case should be given
        if(!isset($data['case'])){
            error(__FILE__,__LINE__,"Error : missing case amount.");
        }
        // it's fine if lastupdate isn't given
        if(!isset($data['lastupdate'])){
            $data['lastupdate'] = $_SERVER['REQUEST_TIME'];
        }
        // we also need to provide id
        $data['planetid'] = $this->id;
        
        //building query
        $keys = array();
        $values = array();
        foreach($data AS $k=>$v){
            $keys[] = '`' . $k . '`';
            $values[] = ':u' . $k; // adding 'u' so that pdo doesn't cry about stuffes like :200
        }
        $keys = implode(',',$keys);
        $values = implode(',',$values);
        
        $req = $pdo->prepare("INSERT INTO ".table('planetdata')." (".$keys.") VALUES (".$values.")");
        
        foreach($data AS $k=>$v){
            $req->bindValue(
                ':u'.$k,
                $v,
                (array_key_exists($k,$list['ressources'])) ? PDO::PARAM_STR : PDO::PARAM_INT  // if we are binding 0|1|2 AKA ressources AKA decimal values then we use PARAM_STR
            );
        }
        
        $req->execute();
    }
    
    function getPlayer(){
        return $this->player;
    }
    function setPlayer(&$player){
        $this->player = $player;
    }
    function getPlanetData($mode = 0){
        global $pdo;
        switch($mode){
            case 0: // get most revelant stuffes
                if(!empty($this->id)){
                    $req = $pdo->prepare(
                        'SELECT * FROM '.table('planet').' AS planet JOIN '.table('planetdata').' AS planetdata ON planet.id=planetdata.planetid
                        WHERE id=:planetid'
                    );
                    $req->bindValue(':planetid',$this->id,PDO::PARAM_INT);
                    $req->execute();
                    $res = $req->fetch(PDO::FETCH_ASSOC);
                    $this->userid = $res['userid'];
                    $this->name = $res['name'];
                    $this->ismain = $res['ismain'];
                    $this->location = new Location($res['g'],$res['s'],$res['p']);
                    $this->img = $res['img'];
                    unset($res['id'],$res['userid'],$res['name'],$res['ismain'],$res['g'],$res['s'],$res['p'],$res['img']);
                    $this->data = $res;
                }
                break;   
        }
    }
    function &getData($key = -1){
        if($key==-1){
            return $this->data;
        } else if(isset($this->data[$key])){
            return $this->data[$key];
        } else {
            error(__FILE__,__LINE__,"Error, requested data with id(".$key.") => couldnt be found, data var_dump :<br />" . var_dump($this->data));
        }
    }
    function setData($key,$value){
        $this->data[$key] = $value;
        $this->updated[$key] = $value;
    }
    function charge($price){
        global $var;
        foreach($price AS $id=>$cost){
            if($var[$id]['isPalpable']){
                $this->updated[$id] = $this->data[$id] -= $cost;
            }
        }
    }
    function updateRessources($time){ // production TODO pas sur d'avoir codé la consommation d hydrogene , et vérifier le ratio d'énergie -> should be done now
        global $list,$var,$config;
        $production = array();
        foreach($list['buildings'] AS $val){ // getting prod values
            if(isset($var[$val]['production'])){
                $building = new Building($val,$this);
                $production[] = $building->getProduction();
            }
        }
        foreach($list['ships'] AS $val){
            if(isset($var[$val]['production'])){ // same for ships
                $ship = new Ship($val,$this);
                $production[] = $ship->getProduction();
            }
        }
        $warehouse = new Building(113,$this);
        $this->data['storage'] = $warehouse->getStorage();
        // first , let see if we have enough H for fusion reactor
        $consumption = 0;
        $m = count($production);
        for($i=0;$i<$m;++$i){
            if(isset($production[$i][2]) && $production[$i][2] < 0){
                $consumption += $production[$i][2];
            }
        }
        if($consumption > 0){ // si on a de la consommation d'h alors c'est qu'il y a un reacteur a fusion fonctionnel
            $workingTime = floor(3600 * ($this->data[2] / $consumption)); // calcul du temps de fonctionnement du reacteur a fusion avec les stocks actuel
            while($workingTime < $time){ // tant que le reacteur n'aura pas assez de carburant
                if($workingTime >= 10){
                    $this->updateRessources($workingTime); // on fait tourner la planete jusqu'a ce que le reacteur n'ait plus de carburant sachant qu'on aura probablement produit du carburant pendant ce laps de temps
                    $time -= $workingTime;
                    $workingTime = floor(3600 * ($this->data[2] / $consumption)); // recalcul du workingTime
                } else { // le reacteur n'a pas assez de carburant pour démarrer , il se désactive
                    for($i=0;$i<$m;++$i){ // on rend nulle la production du reacteur a fusion
                        if($production[$i][2] < 0){
                            $production[$i][2] = 0;
                            $production[$i][3] = 0;
                        }
                    }
                    $workingTime = $time; // et on fait en sorte qu'on puisse sortir de la boucle
                }
            }
        }
        // maintenant il faut calculer la production et la consommation d'électricité
        $energyProduction = 0;
        $energyConsumption = 0;
        $ressourcesProduction = array(); // prod par heure
        for($i=0;$i<$m;++$i){
            if($production[$i][3] < 0){ // calcul de l'énergie
                $energyConsumption -= $production[$i][3]; // on veut la consommation donc on inverse
            } else {
                $energyProduction += $production[$i][3]; // on rajoute la prod
            }
        }
        // on place les resultats du bilan énergétique dans les données
        $this->data[3] = $energyProduction;
        $this->data['energyRatio'] = ($energyConsumption > 0) ? min($energyProduction / $energyConsumption,1) : 1; // si on consomme de l'energie alors on prend la plus petite valeur entre production/consommation & 1 , sinon 1
        // on crée les valeurs de productions pour l'affichage , on commence par mettre les prod de base
        $this->data['production'] = array(
            0 => $config['baseProduction'][0],
            1 => $config['baseProduction'][1],
            2 => $config['baseProduction'][2],
            3 => $energyConsumption
        );
        /*
        ENERGY ->
        data[3] = PRODUCTION
        data['production'][3] = CONSUMPTION
        
        
        */
        // on complete les valeurs de production en rajoutant les productions des batiments
        for($i=0;$i<$m;++$i){ // $m = count($production) ; foreach $production
            for($j=0;$j<3;++$j){ // foreach ressource (palpable)
                if(isset($production[$i][$j])){
                    $this->data['production'][$j] += $production[$i][$j] * $this->data['energyRatio']; // on rajoute les productions de façon à ce que la valeur finale soit la production reelle par heure
                }
            }
        }
        
        // finalement on calcule la production pour chaque ressource
        for($i=0;$i<3;++$i){
            if($i==2 && $consumption > 0){ // conso H
                $this->data[$i] -= $consumption * ($time/3600);
            }
            if($this->data[$i] < $this->data['storage']){
                $this->data[$i] += $this->data['production'][$i] * ($time/3600);
                if($this->data[$i] > $this->data['storage']){
                    $this->data[$i] = $this->data['storage'];
                }
            }
            
            $this->updated[$i] = $this->data[$i];
        }
    }
    function updateMaking($time){
        global $pdo,$var,$list;
        $req = $pdo->prepare('SELECT * FROM '.table("making")." WHERE
            (itemid > 299 AND itemid < 400 AND userid=:userid AND planetid<>:planetid) OR ".    // si c'est une technologie recherchée par nous mais pas sur cette planete
            '(planetid=:planetid) '. // ou si c'est unnimporte quoi sur cette planete ci
            'ORDER BY planetid , priority'); // ordonné par planetid puis priority
        $req->bindValue(':userid',$this->userid);
        $req->bindValue(':planetid',$this->id);
        $req->execute();
        $this->making = array();
        while($line = $req->fetch(PDO::FETCH_ASSOC)){
            
            if($line['planetid'] != $this->id){
                $datPlanet = new Planet($line['planetid']);
                $datPlanet->setPlayer($this->player);
                $datPlanet->getPlanetData();
                $datPlanet->updatePlanet($_SERVER['REQUEST_TIME']);
                if($datPlanet->isBuilding('researches')){
                    $this->making['researchSomeWhereElse'] = $datPlanet->getPlanetName();
                }
                for(;$line['planetid'] != $this->id;$line = $res->fetch(PDO::FETCH_ASSOC)){}
                if(empty($line)){ 
                    /* 2 possibilités si y'avait une recherche en cours sur une autre planete , 
                    ou bien on a dabord traité l'autre planete puis on fait tout le bordel de 
                    celle ci ou on a d'abord fait le bordel puis la recherche de l'autre planete,
                    dans le 2 eme cas , ce qui suit apres la/les recherches de l'autre planete,c'est
                    du vide, donc ... on fait un goto parce que ça ira tfaçon plus vite que retester un
                    empty à chaque passage , c'est moche mais c'est la vie */
                    goto gotoislovegotoislife;
                }
            }
            // getting data into their places
            $this->making[getItemType($line['itemid'])][] = $line;
        }
        gotoislovegotoislife:
        
        //=============================================================================================================================================================================================
        //=============================================================================================================================================================================================
        //=BATIMENTS MAJ===============================================================================================================================================================================
        //=============================================================================================================================================================================================
        //=============================================================================================================================================================================================
        $timeStart = $this->data['lastupdate'];
        if(!empty($this->making['buildings'])){
            for($i=0,$maxI=count($this->making['buildings']);$i<$maxI;++$i){
                if(!empty($this->making['buildings'][$i]) && empty($this->making['buildings'][$i]['timestart'])){ // batiment qu'on a pas encore payé etc (probablement dans la liste)
                    $building = new Building($this->making['buildings'][$i]['itemid'],$this);
                    if($building->isBuildable()){
                        $this->charge($building->getPrice());
                        $timeEnd = $timeStart+$building->getBuildingTime();
                        if($timeEnd<=$time){ // on a le temps de construire le batiment en mode "instabuild"
                            $timeStart = $timeEnd;
                            $this->setData($this->making['buildings'][$i]['itemid'],$this->data[$this->making['buildings'][$i]['itemid']] + 1);
                            $req = $pdo->prepare("DELETE FROM ".table('making')." WHERE id=:id");
                            $req->bindValue(':id',$this->making['buildings'][$i]['id'],PDO::PARAM_INT);
                            $req->execute();
                            unset($this->making['buildings'][$i]);
                            continue;
                        } else { // pas le temps de construire on update le making
                            $this->making['buildings'][$i]['timestart'] = $timeStart;
                            $this->making['buildings'][$i]['timeend'] = $timeEnd;
                            $this->making['buildings'][$i]['number'] = $this->data[$this->making['buildings'][$i]['itemid']] + 1;
                            $req = $pdo->prepare("UPDATE ".table("making")." set timestart=:timestart,timeend=:timeend,number=:number WHERE id=:id");
                            $req->bindValue(":timestart",$timeStart,PDO::PARAM_INT);
                            $req->bindValue(":timeend",$timeEnd,PDO::PARAM_INT);
                            $req->bindValue(":number",$this->making['buildings'][$i]['number'],PDO::PARAM_INT);
                            $req->bindValue(":id",$this->making['buildings'][$i]['id'],PDO::PARAM_INT);
                            $req->execute();
                            break;
                        }
                    } else { // on peut pas construire le batiment, peu importe la raison, on jarte
                        $req = $pdo->prepare("DELETE FROM ".table('making')." WHERE id=:id");
                        $req->bindValue(':id',$this->making['buildings'][$i]['id'],PDO::PARAM_INT);
                        $req->execute();
                        unset($this->making['buildings'][$i]);
                        continue;
                    }
                } else if(!empty($this->making['buildings'][$i])){ // building pour lequel on a le timestart
                    if($this->making['buildings'][$i]['timeend'] <= $time){
                        $timeStart = $this->making['buildings'][$i]['timeend'];
                        $this->setData($this->making['buildings'][$i]['itemid'],$this->data[$this->making['buildings'][$i]['itemid']] + 1);
                        $req = $pdo->prepare("DELETE FROM ".table('making')." WHERE id=:id");
                        $req->bindValue(':id',$this->making['buildings'][$i]['id'],PDO::PARAM_INT);
                        $req->execute();
                        unset($this->making['buildings'][$i]);
                        continue;
                    } else {
                        break;
                    }
                }
            }
        }
        //=============================================================================================================================================================================================
        //=============================================================================================================================================================================================
        //=TECHS MAJ===================================================================================================================================================================================
        //=============================================================================================================================================================================================
        //=============================================================================================================================================================================================
        $timeStart = $this->data['lastupdate'];
		if(!empty($this->making['researches'])){
            for($i=0,$maxI=count($this->making['researches']);$i<$maxI;++$i){
                if(!empty($this->making['researches'][$i]) && empty($this->making['researches'][$i]['timestart'])){ //tech qu'on a pas encore payé etc (probablement dans la liste)
                    $research = new Research($this->making['researches'][$i]['itemid'],$this);
                    if($research->isBuildable()){
                        $this->charge($research->getPrice());
                        $timeEnd = $timeStart+$research->getBuildingTime();
                        if($timeEnd<=$time){ // on a le temps de rechercher insta
                            $timeStart = $timeEnd;
                            $this->getPlayer()->levelUpTech($this->making['researches'][$i]['itemid']);
                            $req = $pdo->prepare("DELETE FROM ".table('making')." WHERE id=:id");
                            $req->bindValue(':id',$this->making['researches'][$i]['id'],PDO::PARAM_INT);
                            $req->execute();
                            unset($this->making['researches'][$i]);
                            continue;
                        } else { // pas le temps de construire on update le making
                            $this->making['researches'][$i]['timestart'] = $timeStart;
                            $this->making['researches'][$i]['timeend'] = $timeEnd;
                            $this->making['researches'][$i]['number'] = $this->getPlayer()->getTech($this->making['researches'][$i]['itemid']) + 1;
                            $req = $pdo->prepare("UPDATE ".table("making")." set timestart=:timestart,timeend=:timeend,number=:number WHERE id=:id");
                            $req->bindValue(":timestart",$timeStart,PDO::PARAM_INT);
                            $req->bindValue(":timeend",$timeEnd,PDO::PARAM_INT);
                            $req->bindValue(":number",$this->making['researches'][$i]['number'],PDO::PARAM_INT);
                            $req->bindValue(":id",$this->making['researches'][$i]['id'],PDO::PARAM_INT);
                            $req->execute();
                            break;
                        }
                    } else { // on peut pas recherchert, peu importe la raison, on jarte
                        $req = $pdo->prepare("DELETE FROM ".table('making')." WHERE id=:id");
                        $req->bindValue(':it',$this->making['researches'][$i]['id'],PDO::PARAM_INT);
                        $req->execute();
                        unset($this->making['researches'][$i]);
                        continue;
                    }
                } else if(!empty($this->making['researches'][$i])){ // building pour lequel on a le timestart
                    if($this->making['researches'][$i]['timeend'] <= $time){ // on arrive a la fin de la construction
                        $timeStart = $this->making['researches'][$i]['timeend'];
                        $this->getPlayer()->levelUpTech($this->making['researches'][$i]['itemid']);
                        $req = $pdo->prepare("DELETE FROM ".table('making')." WHERE id=:id");
                        $req->bindValue(':id',$this->making['researches'][$i]['id'],PDO::PARAM_INT);
                        $req->execute();
                        unset($this->making['researches'][$i]);
                        continue;
                    } else { // si on est pas arrivé a la fin de la construction on a terminé , sert a rien de continuer la boucle -> on break;
                        break;
                    }
                }
            }
        }
        //=============================================================================================================================================================================================
        //=============================================================================================================================================================================================
        //=SHIPYARD MAJ================================================================================================================================================================================
        //=============================================================================================================================================================================================
        //=============================================================================================================================================================================================
        $timeStart = $this->data['lastupdate'];
		if(!empty($this->making['ships'])){
			for($i=0,$maxI=count($this->making['ships']);$i<$maxI;++$i){
				if(!empty($this->making['ships'][$i])){ // ships à construire
					$ship = new Ship($this->making['ships'][$i]['itemid'],$this);
					if(!empty($this->making['ships'][$i]['timestart'])){ // si le timestart est renseigné , il est préféré à  lastupdate
						$timeStart = $this->making['ships'][$i]['timestart'];
					}
					$timeToBuild = $time - $timeStart; // temps dont on dispose pour traiter la commande
					if($timeToBuild > 0){ // security
						if($timeToBuild >= $ship->timeToBuild($this->making['ships'][$i]['number'])){ // assez de temps pour tout construire
							$timeStart += $ship->timeToBuild($this->making['ships'][$i]['number']); // le timestart est augmenté de la durée pour construire tout
							$this->setData($this->making['ships'][$i]['itemid'],$this->data[$this->making['ships'][$i]['itemid']] + $this->making['ships'][$i]['number']);
							$req = $pdo->prepare("DELETE FROM ".table('making')." WHERE id=:id"); // on a updaté les données on peut donc suppriemr l'entrée en bdd
							$req->bindValue(":id",$this->making['ships'][$i]['id'],PDO::PARAM_INT);
							$req->execute();
							
							unset($this->making['ships'][$i]);
							continue; // on a terminé cette partie on peut passer au making suivant
						} else if($timeToBuild >= $ship->getBuildingTime()) { // on a le temps de construire au moins 1 navire
							$buildableAmount = min($ship->getBuildableAmount($timeToBuild),$this->making['ships'][$i]['number']); // calcul du nombre de vsx qu'on peut construire + sécurité avec min
							$timeStart += $ship->timeToBuild($buildableAmount);
							$this->setData($this->making['ships'][$i]['itemid'],$this->data[$this->making['ships'][$i]['itemid']] + $buildableAmount);
							
							$this->making['ships'][$i]['timestart'] = $timeStart;
							$this->making['ships'][$i]['number'] -= $buildableAmount;
							if($this->making['ships'][$i]['number'] < 0) { // securiT
								error(__FILE__,__LINE__,"Fatal error, negative amount for shipyard.");
							}
							$req = $pdo->prepare("UPDATE ".table('making')." SET timestart=:timestart , number=:number WHERE id=:id");
							$req->bindValue(":timestart",$this->making['ships'][$i]['timestart'],PDO::PARAM_INT);
							$req->bindValue(":number",$this->making['ships'][$i]['number'],PDO::PARAM_INT);
							$req->bindValue(":id",$this->making['ships'][$i]['id'],PDO::PARAM_INT);
							
							$req->execute();
							break; // on a pas eu le temps de finir cette construction ci donc sert à rien de continuer les autres
						} else if(empty($this->making['ships'][$i]['timestart'])){ // on peut pas en construire un seul mais le timestart est vide -> on update le timestart
							$req = $pdo->prepare("UPDATE ".table("making")." SET timestart=:timestart WHERE id=:id");
							$this->making['ships'][$i]['timestart'] = $timeStart;
							$req->bindValue(":timestart",$this->making['ships'][$i]['timestart'],PDO::PARAM_INT);
							$req->bindValue(":id",$this->making['ships'][$i]['id'],PDO::PARAM_INT);
							$req->execute();
							break; // pas eu le temps -> on break
						} else { // rien a faire car tous les autres cas on déjà été vus, plus qu'a sortir de la boucle ...
							break;
						}
					}
				}
			}
        }
        //=============================================================================================================================================================================================
        //=============================================================================================================================================================================================
        //=DEFENSES MAJ================================================================================================================================================================================
        //=============================================================================================================================================================================================
        //=============================================================================================================================================================================================
        $timeStart = $this->data['lastupdate'];
		if(!empty($this->making['defenses'])){
			for($i=0,$maxI=count($this->making['defenses']);$i<$maxI;++$i){
				if(!empty($this->making['defenses'][$i])){ // defenses à construire
					$ship = new Ship($this->making['defenses'][$i]['itemid'],$this);
					if(!empty($this->making['defenses'][$i]['timestart'])){ // si le timestart est renseigné , il est préféré à  lastupdate
						$timeStart = $this->making['defenses'][$i]['timestart'];
					}
					$timeToBuild = $time - $timeStart; // temps dont on dispose pour traiter la commande
					if($timeToBuild > 0){ // security
						if($timeToBuild >= $ship->timeToBuild($this->making['defenses'][$i]['number'])){ // assez de temps pour tout construire
							$timeStart += $ship->timeToBuild($this->making['defenses'][$i]['number']); // le timestart est augmenté de la durée pour construire tout
							$this->setData($this->making['defenses'][$i]['itemid'],$this->data[$this->making['defenses'][$i]['itemid']] + $this->making['defenses'][$i]['number']);
							$req = $pdo->prepare("DELETE FROM ".table('making')." WHERE id=:id"); // on a updaté les données on peut donc suppriemr l'entrée en bdd
							$req->bindValue(":id",$this->making['defenses'][$i]['id'],PDO::PARAM_INT);
							$req->execute();
							
							unset($this->making['defenses'][$i]);
							continue; // on a terminé cette partie on peut passer au making suivant
						} else if($timeToBuild >= $ship->getBuildingTime()) { // on a le temps de construire au moins 1 navire
							$buildableAmount = min($ship->getBuildableAmount($timeToBuild),$this->making['defenses'][$i]['number']); // calcul du nombre de vsx qu'on peut construire + sécurité avec min
							$timeStart += $ship->timeToBuild($buildableAmount);
							$this->setData($this->making['defenses'][$i]['itemid'],$this->data[$this->making['defenses'][$i]['itemid']] + $buildableAmount);
							
							$this->making['defenses'][$i]['timestart'] = $timeStart;
							$this->making['defenses'][$i]['number'] -= $buildableAmount;
							if($this->making['defenses'][$i]['number'] < 0) { // securiT
								error(__FILE__,__LINE__,"Fatal error, negative amount for shipyard.");
							}
							$req = $pdo->prepare("UPDATE ".table('making')." SET timestart=:timestart , number=:number WHERE id=:id");
							$req->bindValue(":timestart",$this->making['defenses'][$i]['timestart'],PDO::PARAM_INT);
							$req->bindValue(":number",$this->making['defenses'][$i]['number'],PDO::PARAM_INT);
							$req->bindValue(":id",$this->making['defenses'][$i]['id'],PDO::PARAM_INT);
							
							$req->execute();
							break; // on a pas eu le temps de finir cette construction ci donc sert à rien de continuer les autres
						} else if(empty($this->making['defenses'][$i]['timestart'])){ // on peut pas en construire un seul mais le timestart est vide -> on update le timestart
							$req = $pdo->prepare("UPDATE ".table("making")." SET timestart=:timestart WHERE id=:id");
							$this->making['defenses'][$i]['timestart'] = $timeStart;
							$req->bindValue(":timestart",$this->making['defenses'][$i]['timestart'],PDO::PARAM_INT);
							$req->bindValue(":id",$this->making['defenses'][$i]['id'],PDO::PARAM_INT);
							$req->execute();
							break; // pas eu le temps -> on break
						} else { // rien a faire car tous les autres cas on déjà été vus, plus qu'a sortir de la boucle ...
							break;
						}
					}
				}
			}
        }
        
    }
    function updatePlanet($time){ // update de planete to $time (timestamp)
        global $pdo,$list,$var;
        // on converti le timestamp en différence de durée
        if($time >= $this->data['lastupdate']){
            $updateTime = $time - $this->data['lastupdate'];
            $this->updateRessources($updateTime);
            $this->updateMaking($time);
            $this->updated['lastupdate'] = $this->data['lastupdate'] = $time;
        }
    }
    
    function proceedUpdate(){
        global $pdo;
        if(!empty($this->updated)){ // update BD
            $toUpdate = array();
            foreach($this->updated AS $key=>$value){
                $toUpdate[$key] = "`".$key."`=:".$key;
            }
            $toUpdateParts = implode(',',$toUpdate);
            $req = $pdo->prepare('UPDATE '.table('planetdata').' SET '.$toUpdateParts.' WHERE planetid=:planetid');
            foreach($this->updated AS $key=>$value){
                $req->bindValue(':'.$key,$value);
            }
            $req->bindValue(':planetid',$this->id,PDO::PARAM_INT);
            $req->execute();
        }
    }
    
    
    function getMaking($type){
        if(empty($type)){
            return $this->making;
        } else if(isset($this->making[$type])){
            return $this->making[$type];
        } else {
            return array();
        }
    }
    function getPlanetName(){
        return $this->name;
    }
    function getPlanetImg(){
        return (($this->img<10) ? '000' : '00').$this->img . '.png';
    }
    function getPlanetRadius(){ // donne un diamètre imaginaire en fonction du nombre de cases de base
        return 9000 + round($this->data['case'] *4.592);
    }
    function getPlanetTemperature(){ // idem que diametre, en fonction de la position P
        return 'de ' . (60 - $this->location->getP()*5) . '°C à ' . (90 - $this->location->getP()*4) . '°C';
    }
    function &getPlanetLocation(){
        return $this->location;
    }
    function getPlanetCases(){
        return $this->data['case'] + $this->data[114] * 5;
    }
    function getPlanetUsedCases(){
        global $list;
        $cases = 0;
        foreach($list['buildings'] AS $v){
            $cases += $this->data[$v];
        }
        return $cases;
    }
    function hasFreeCases(){
        return $this->getPlanetCases() > $this->getPlanetUsedCases();
    }
    function getLocation(){
        return $this->location;
    }
    function isBuilding($type){
        return !empty($this->making[$type]);
    }
    function getUsedId(){
        return $this->userid;
    }
    function getUserId(){
        return $this->userid;
    }
    function isResearchingSomewhereElse(){
        return !empty($this->making['researchSomeWhereElse']);
    }
}

?> 