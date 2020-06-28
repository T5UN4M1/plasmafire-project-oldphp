<?php

/**
 * @author T5UN4M1
 * @copyright 2015
 */ 


class Building{
    
    private $id;
    private $level;
    private $var;
    private $planet;
    private $planetdata;
    private $tech;
    private $price;
    
    function Building($id,&$planet){
        global $var;
        
        $this->id         = $id;
        $this->var        = &$var[$id];
        $this->level      = $planet->getData($id);
        $this->planet     = $planet;
        $this->planetdata = $planet->getData();
        $this->tech       = $planet->getPlayer()->getTech();
    }
    function getStorage($level=-1){
        global $config;
        if($this->id != 113) { return 0;}
        $level = ($level == -1) ? $this->level : $level;
        return $config['baseStorage'] + pow($this->var['storageMultiplier'],$level) * $level * $this->var['storage'];
    }
    function getProduction(){ // donne la prod par heure
        global $var;
        if(isset($this->var['production'])){ // on vérifie que le batiment produit bien quelque chose
            /*$production = array();
            $multiplier = $this->level * pow($this->var['production']['multiplier'],$this->level);
            $bonus = 1;
            foreach($this->var['productionBonus'] AS $key=>$val){
                $bonus *= pow($val,$this->tech[$key]);
            }
            foreach($this->var['production'] AS $key=>$val){
                if(is_int($key)){
                    $production[$key] = $val * $multiplier *               // qté * multiplicateur de niveau
                        ($this->planetdata['ratio' . $this->id]/100) *    // multiplication par le ratio
                        (($val > 0) ? $bonus : 1);                       // si c'est une prod et non une conso alors on applique le bonus de tech
                }
            }
            return $production;*/
            $prod = $this->getDetailledProduction(null,null,$this->planetdata['ratio' . $this->id]);
            return $prod['total'];
        }
        return array(); 
    }
    /*
     * $planetdata : any data to be used instead of $this->planetdata
     * $tech : any data to be used instead of $this->tech
     * $ratio : ratio to be used (min 0 max 100)
     *
     * RETURN VALUE
     * $production = array(
     *      'total' => array(0,1,2,3),
     *      'building' => array(0,1,2,3),
     *      [techid] => array(0,1,2,3),
     *      ...
     * )
     */
    function getDetailledProduction($planetdata,$tech,$ratio){ // retourne la production par heure
        global $var,$config;
        if(isset($this->var['production'])){ // on vérifie que le batiment produit bien quelque chose
            $production = array();
            
            $level = (isset($planetdata[$this->id])) ? $planetdata[$this->id] : $this->level;
            
            $multiplier = $level * pow($this->var['production']['multiplier'],$level); // multiplier =  level * (multiplicateurparlevel^level)
            $bonus = array();
            foreach($this->var['productionBonus'] AS $key=>$val){
                $bonus[$key] = pow($val,((isset($tech[$key])) ? $tech[$key] : $this->tech[$key])) - 1; // bonus par tech = (mult ^level) - 1
            }
            foreach($this->var['production'] AS $key=>$val){
                if(is_int($key)){
                    $production['total'][$key] = $production['building'][$key] = ($val * $multiplier * ($ratio/100)) * $config['productionSpeed'];
                    if($val > 0){ // if prod and not consumption then process BONUSES
                        foreach($bonus AS $techId=>$mult){
                            $production['total'][$key] += $production[$techId][$key] = ($production['building'][$key] * $mult);
                        }
                    }
                }
            }
            return $production;
        }
        return array(); 
    }
    function getPrice(){ // return array price{0,1,2,3}
        global $list;
        if(empty($this->price)){
            $multiplier = /*1 + $this->level * */pow($this->var['price']['multiplier'],$this->level);
            $price = array();
            foreach($list['ressources'] AS $value){
                if(isset($this->var['price'][$value])){
                    $price[$value] = floor($multiplier * $this->var['price'][$value]);
                }
            }
            $this->price = $price;
        }
        return $this->price;
    }
    function getTotalPrice(){// get total price for this building , ex : we have lvl 40 , return price lvl 1 + lvl 2 + lvl 3 etc for ranking purpose
        global $list,$var;
        if($this->level > 1){
            $multiplier = (1- pow($this->var['price']['multiplier'],$this->level)) / (1 - $this->var['price']['multiplier']);
        } else {
            $multiplier = $this->level;
        }
        $price = array();
        foreach($list['ressources'] AS $value){
            if(isset($this->var['price'][$value]) && $var[$value]['isPalpable']){
                $price[$value] = floor($multiplier * $this->var['price'][$value]);
            }
        }
        return $price;
    }
    function isAffordable(){
        $price = $this->getPrice();
        foreach($price AS $key=>$value){
            if($this->planetdata[$key] < $value){
                return false;
            }
        }
        return true;
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
    function isHQHighEnough(){
        return ($this->id == 100) ? true : ($this->planetdata[100] > $this->level);
    }
    function isBuildable(){ // return boolµ
        return (
            $this->planet->hasFreeCases() &&
            $this->isAffordable() &&
            $this->isResearched() &&
            $this->isHQHighEnough()
        );
    }
    function getBuildingTime(){
        global $config;
        $this->getPrice();                                 //forcing price to be processed
        return floor(((($this->price[0] + $this->price[1])/5)   // ((acier + silicium) / 5000)
                * (2/(1 + $this->planetdata[107]))       // * (2/(1+usine robot))
                * pow(0.5,$this->planetdata[108]))      // * 0.5 ^ usines nanites
                / $config['buildingSpeed']);            //  / buildingSpeed
    }
    
}


?> 