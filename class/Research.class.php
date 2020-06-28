<?php

/**
 * @author T5UN4M1
 * @copyright 2015
 */ 



class Research{
    
    private $id;
    private $level;
    private $var;
    private $planet;
    private $planetdata;
    private $tech;
    private $price;
    
    function Research($id,&$planet){
        global $var;
        
        $this->id         = $id;
        $this->var        = &$var[$id];
        $this->level      = $planet->getPlayer()->getTech($id);
        $this->planet     = $planet;
        $this->planetdata = $planet->getData();
        $this->tech       = $planet->getPlayer()->getTech();
    }
    function getPrice(){ // return array price{0,1,2,3}
        global $list;
        if(empty($this->price)){
            $multiplier = /*1 + $this->level * */ pow($this->var['price']['multiplier'],$this->level);
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
    function isBuildable(){ // return bool
        return (
            $this->isAffordable() &&
            $this->isResearched() &&
            !$this->planet->isResearchingSomewhereElse()
        );
    }
    function getBuildingTime(){
        global $config;
        $this->getPrice();                                 //forcing price to be processed
        return floor((($this->price[0] + $this->price[1] + $this->price[2])/5)   // ((acier + silicium) / 5000)
                * (2/(1 + $this->planetdata[111]))        // * (2/(1+laboratoire))
                * pow(0.5,$this->tech[313])              // réseau de recherche intergalactique
                / $config['buildingSpeed']);            //  / buildingSpeed
    }
}

?> 