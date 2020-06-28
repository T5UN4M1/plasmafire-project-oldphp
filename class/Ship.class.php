<?php

/**
 * @author T5UN4M1
 * @copyright 2015
 */ 

class Ship{
    
    private $id;
    private $amount;
    private $var;
    private $planet;
    private $planetdata;
    private $tech;

    function Ship($id,&$planet){
        global $var;
        
        $this->id         = $id;
        $this->var        = &$var[$id];
        $this->amount     = $planet->getData($id);
        $this->planet     = $planet;
        $this->planetdata = $planet->getData();
        $this->tech       = $planet->getPlayer()->getTech();
    }
    function getProduction(){ // satellite solaire
        if(isset($this->var['production'])){
            $production = array();
            $bonus = 1;
            foreach($this->var['productionBonus'] AS $key=>$val){
                $bonus *= pow($val,$this->tech[$key]);
            }
            foreach($this->var['production'] AS $key=>$val){
                if(is_int($key)){
                    $production[$key] = $this->amount * $val * // qté * production
                         (($val > 0) ? $bonus : 1);   // si prod et non conso -> bonus de tech
                }
            }
            return $production;
        } else {
            return array();
        }
    }
    function isResearched(){
        global $list;
        foreach($this->var['requirements'] AS $id=>$required){
            if(in_array($id,$list['researches'])){ // tech
                if($this->tech[$id] < $required){
                    return false;
                }
            } else if($this->planetdata[$id] < $required){ // batiment
                return false;
            }
        }
        return true;
    }
    function getStat($stat){ // !!!!! works for POWER SHIELD HULL FRET , DOESNT SUPPORT MOVE !!!
        $statValue = array(
            'total' => $this->var['stats'][$stat],
            'base' => $this->var['stats'][$stat]
        );
        foreach($this->var['stats'][$stat.'Bonus'] AS $techId=>$bonus){
            $statValue['total'] += $statValue[$techId] = $statValue['base'] * ($bonus-1) * $this->tech[$techId];
        }
        return $statValue;
    }
    function getMove(){
        if(!empty($this->var['stats']['move'])){
            $techs = array_keys($this->var['stats']['move']);
            for($i=count($techs)-1;$i>=0;--$i){// on remonte le tableau de bas en haut pour tester si on peut utiliser la propulsion la plus intéressante, puis on teste celles moins intéressantes si la premiere marche pas
                if($this->tech[$techs[$i]] >= $this->var['stats']['move'][$techs[$i]]['level']){ // on a le level pour utiliser  cette propulsion
                    return array(
                        "total"       =>  ($this->var['stats']['move'][$techs[$i]]['speed'] + (($this->var['stats']['move'][$techs[$i]]['bonus']-1) * $this->tech[$techs[$i]]) * $this->var['stats']['move'][$techs[$i]]['speed']),
                        "base"        =>   $this->var['stats']['move'][$techs[$i]]['speed'],
                        $techs[$i]    => (($this->var['stats']['move'][$techs[$i]]['bonus']-1) * $this->tech[$techs[$i]]) * $this->var['stats']['move'][$techs[$i]]['speed'],
                        "consumption" =>   $this->var['stats']['move'][$techs[$i]]['consumption']
                    );
                }
            }
        }
        return array(
            "total" => 0
        );
    }
    function getPrice($amount = 1){
        if($amount == 1){
            return $this->var['price'];
        } else {
            global $var;
            $price = $this->var['price'];
            for($i=0,$max=count($price);$i<$max;++$i){
                if(!empty($price[$i]) && $var[$i]['isPalpable']){
                    $price[$i] *= $amount;
                }
            }
            return $price;
        }
    }
    function getBuildableAmount($time){ // return the amount of ships you can build for a given amount of time
        $buildingTime = $this->getBuildingTime();
        if($buildingTime > 1){
            return floor($time / round($buildingTime));
        } else {
            return $time * convertBuildingTimeToUnitsPerSecond($buildingTime);
        }
    }
    function timeToBuild($amount){ // return the time it takes to build a given amount of ship
        $buildingTime = $this->getBuildingTime();
        if($buildingTime > 1){
            return floor($amount * round($buildingTime));
        } else {
            return ceil($amount / convertBuildingTimeToUnitsPerSecond($buildingTime));
        }
    }
    function isAffordable($amount=1){
        foreach($this->getPrice() AS $resId=>$cost){
            if(($cost*$amount)>$this->planetdata[$resId]){
                return false;
            }
        }
        return true;
    }
    function getBuildingTime(){
        global $config;                                           //!\\ PAS DE FLOOR -> pour conversion en u/s a grande vitesse/haut level de nanites ; ex : si buildingtime = 0.1 sec -> 10 u /s de prod
        $price = $this->getPrice();                              //forcing price to be processed
        return (((($price[0] + $price[1])/5)                    // ((acier + silicium) / 5000)
                * (2/(1 + $this->planetdata[109]))             // * (2/(1+chantierspatial))
                * pow(0.5,$this->planetdata[108]))            // * 0.5 ^ usines nanites
                / $config['buildingSpeed']);                 //  / buildingSpeed
    }
    function howManyAreAffordable(){
        global $var;
        $price = $this->getPrice();
        $qty = 99999999999999999999999999999999999999999;
        for($i=0,$max=count($price);$i<$max;++$i){
            if(!empty($price[$i])){
                if($var[$i]['isPalpable']){
                    $qty = min($qty,floor($this->planetdata[$i]/$price[$i]));    
                } /*else if($price[$i] > $this->planetdata[$i]){ // TODO : FIX DIS SHIT FFS
                    $qty = 0;
                }*/
            }
        }
        return $qty;
    }
}

?> 