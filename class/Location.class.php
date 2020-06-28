<?php

/**
 * @author T5UN4M1
 * @copyright 2015
 */ 

class Location{
    
    private $g;
    private $s;
    private $p;
    
    function Location($g,$s,$p){
        $this->setAll($g,$s,$p);
    }
    
    function setAll($g,$s,$p){
        $this->g = $g;
        $this->s = $s;
        $this->p = $p;
    }
    function setG($g){
        $this->g = $g;
    }
    function setS($s){
        $this->s = $s;
    }
    function setP($p){
        $this->p = $p;
    }
    function getGSP(){
        return array(
            'g' => $this->g,
            's' => $this->s,
            'p' => $this->p
        );
    }
    function getG(){
        return $this->g;
    }
    function getS(){
        return $this->s;
    }
    function getP(){
        return $this->p;
    }
    function toString(){
        return $this->g . ':' . $this->s . ':' . $this->p;
    }
    function toLink(){
        return '<a href="?a=galaxy&g='.$this->g.'&s='.$this->s.'">'.$this->g . ':' . $this->s . ':' . $this->p .'</a>';
    }
    
    
}

?> 