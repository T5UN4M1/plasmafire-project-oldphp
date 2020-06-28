<?php

/**
 * @author T5UN4M1
 * @copyright 2015
 */ 

$config['maxG'] = 5;   // minimum 1 , minimum recommandé : 3   , maximum recommandé : 12
$config['maxS'] = 250; // minimum 5 , minimum recommandé : 50  , maximum recommandé : 500
$config['maxP'] = 15;  // minimum 7 , minimum recommandé : 10  , maximum recommandé : 30


$config['buildingSpeed'] = 1;  // vitesse de construction & recherche & etc
$config['productionSpeed'] = 1; // vitesse de production des ressources
$config['fleetSpeed'] = 10; // vitesse de déplacement des flottes

$config['wreckRatio'] = 0.50; // ratio de ressources qui rentrent dans le champs de dépris
$config['raidRatio'] = 0.75; // ratio de ressources gagnées en raid
$config['fleetConsumptionRatio'] = 1;  // ratio de consommation des vaisseaux 

$config['baseProduction'] = array(20,20,0,0);  // production de base de toute planete
$config['baseRessources'] = array(1000,750,0); // ressources de bases sur une nouvelle planete
$config['baseStorage'] = 100000; // stockage de base sur une planete

$config['initialPlanet'] = array(// crée 1 planète avec 1000 acier 750 silicium 100 hydrogène et 1 petit transporteur
    0 => 1000,
    1 => 750,
    2 => 100,
    204 => 1,   // petit transporteur
    "case" => 300  // nombre de cases
);

$config['version'] = '0.0.0';
$config['maxAllowedQueue'] = 10; // for buildings & researches
$config['maxAllowedQueue2']= 20; // for ships & defenses






?> 