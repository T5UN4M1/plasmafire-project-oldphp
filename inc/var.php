<?php

/**
 * @author T5UN4M1
 * @copyright 2015
 */ 



$var = array(
    0 => array(
        'name' => 'acier',
        'isPalpable' => true
    ),
    
    1 => array(
        'name' => 'silicium',
        'isPalpable' => true
    ),
    
    2 => array(
        'name' => 'hydrogène',
        'isPalpable' => true
    ),
    3 => array(
        'name' => 'électricité', // définition de ressources , isPalpable définit si c'est une ressource que l'on produit en quantité ou si c'est une ressource "abstraite" comme l'électricité
        'isPalpable' => false
    ),
    
    100 => array(
        'name' => 'Quartier général',
        'desc' => "Le quartier général est le batiment principal de votre planète, il permet la construction de batiments toujours plus avancés",
        'price' => array(0 => 100,1 => 50,2 => 0,'multiplier' => 1.2),
        'requirements' => array(
        )
    ),
    /*101 => array(
        'name' => 'Quartier des officiers',
        'desc' => "Le quartier des officiers permet de recruter des officiers servant à gérer les planètes au meilleur de leurs capacités.",
        'price' => array(0 => 500,1 => 200,2 => 50,'multiplier' => 2
        ),
        'requirements' => array(100 => 3
        )
    ),*/
    102 => array(
        'name' => "Mine d'acier", // nom
        'desc' => "La mine d'acier forme la production principale de votre planète, l'acier forme la base de tous les batiments, vaisseaux et défenses et sert à l'acquisition de nombreuses recherches.",
        'price' => array( // prix pour chaque ressources + miltiplicateur par niveau
		0 => 279,1 => 93,2 => 0,'multiplier' => 1.5
        ),
        'production' => array( // production / consommation de ressources + multiplicateur par niveau
		0 => 20,3 => -10,'multiplier' => 1.25
        ),
        'productionBonus' => array( // bonus de technologie par niveau309 => 1.05
        ),
        'requirements' => array( // prérequis , ici vide
        )
    ),
    103 => array(
        'name' => "Mine de silicium",
        'desc' => "La mine de silicium forme la production secondaire de votre planète, le silicium sert à la construction des éléments electroniques pour de nombreux batiments, vaisseaux, défenses et recherches.",
        'price' => array(0 => 279,1 => 186,2 => 0,'multiplier' => 1.5),
        'production' => array(1 => 15,3 => -10,'multiplier' => 1.25),
        'productionBonus' => array(310 => 1.05),
        'requirements' => array()
    ),
    104 => array(
        'name' => "Collecteur d'hydrogene",
        'desc' => "Le collecteur d'hydrogène produit l'hydrogène nécéssaire à la propulsion des vaisseaux et à certains autres éléments comme des recherches ,batiments, vaisseaux, défenses.",
        'price' => array(0 => 660,1 => 550,2 => 0,'multiplier' => 1.5),
        'production' => array(2 => 10,3 => -10,'multiplier' => 1.25),
        'productionBonus' => array(311 => 1.05),
        'requirements' => array()
    ),
    105 => array(
        'name' => "Centrale solaire",
        'desc' => "La centrale solaire fournit l'électricité nécéssaire pour faire fonctionner les batiments de production de ressources.",
        'price' => array(0 => 279,1 => 193,2 => 0,'multiplier' => 1.5),
        'production' => array(3 => 20,'multiplier' => 1.25),
        'productionBonus' => array(300 => 1.05),
        'requirements' => array(
        )
    ),
    /*106 => array(
        'name' => "Centrale a fusion nucléaire",
        'desc' => "La centrale a fusion nucléaire est principalement utilisé comme centrale de secours, elle permet la production de large quantité d'énergie au cout d'hydrogène, elle comprend les infrastructures de rafinnement et d'enrichissement.",
        'price' => array(0 => 279,1 => 186,2 => 93,'multiplier' => 1.5
        ),
        'production' => array(2 => -10,3 => 100,'multiplier' => 1.25
        ),
        'productionBonus' => array(300 => 1.05
        ),
        'requirements' => array(100 => 8,104 => 8,107 => 6,300 => 5
        )
    ),*/
    107 => array(
        'name' => "Usine de robots",
        'desc' => "L'usine de robots permet d'accélérer la production de batiments, de vaisseaux et de défenses.",
        'price' => array(0 => 400,1 => 200,2 => 50,'multiplier' => 2),
        'requirements' => array(100 => 3)
    ),
    108 => array(
        'name' => "Usine de nano-robots",
        'desc' => "L'usine de nano-robots permet d'accélérer énormément la production de batiments, de vaisseaux et de défenses.",
        'price' => array(0 => 100000,1 => 50000,2 => 10000,'multiplier' => 2),
        'requirements' => array(100 => 8,107 => 10,300 => 8,301 => 8)
    ),
    109 => array(
        'name' => "Chantier spatial",
        'desc' => "Le chantier spatial est le lieu où sont construits les vaisseaux.Au plus il est grand et développé, au plus vite les vaisseaux seront construits.",
        'price' => array(0 => 400,1 => 200,2 => 100,'multiplier' => 2),
        'requirements' => array(107 => 2
        )
    ),
    110 => array(
        'name' => "Centre de défenses",
        'desc' => "Le centre de défenses permet et accélère la construction de défenses.",
        'price' => array(0 => 200,1 => 100,2 => 10,'multiplier' => 2),
        'requirements' => array(107 => 1
        )
    ),
    111 => array(
        'name' => "Laboratoire",
        'desc' => "Le laboratoire permet de rechercher de nouvelles technologies ou d'améliorer celles existantes.",
        'price' => array(0 => 200,1 => 150,2 => 75,'multiplier' => 2),
        'requirements' => array(100 => 3)
    ),
    /*112 => array(
        'name' => "Porte de saut intergalactique",
        'desc' => "La porte de saut intergalactique permet les déplacements instantanés et relativement peu couteux entre 2 planètes possédant une porte de saut.L'amélioration de ce batiment permet des déplacements avec une plus grande portée.",
        'price' => array(0 => 50000000,1 => 40000000,2 => 20000000,3 => 100000,'multiplier' => 2
        ),
        'requirements' => array(100 => 15,107 => 15,108 => 7,300 => 16,301 => 16,305 => 12,
        )
    ),*/
    113 => array(
        'name' => "Entrepot",
        'desc' => "Les entrepots permettent le stockage et la dissimulation des ressources.Les mines ne peuvent pas produire au dela de la capacité de vos entrepots et les ressources excédentaires seront plus faciles à subtiliser par d'éventuelles attaques.",
        'price' => array(0 => 2000,1 => 2000,2 => 100,'multiplier' => 2),
        'storage' => 100000,
        'storageMultiplier' => 2,
        'requirements' => array(
        )
    ),
    114 => array(
        'name' => "Terraformeur",
        'desc' => "Le terraformeur permet l'aménagement de nouvelles cases pour la constructions de nouvelles structures.",
        'price' => array(0 => 50000,1 => 50000,2 => 10000,'multiplier' => 2
        ),
        'casesPerLevel' => 5,
        'requirements' => array(107 => 12,108 => 3,300 => 12
        )
    ),


    200 => array(
        'name' => "Chasseur léger",
        'desc' => "Le chasseur léger est le plus petit vaisseau de combat, il est principalement désigné pour traquer et détruire les chasseurs ennemis ainsi que les bombardier, son armement ne lui permet pas réellement de s'attaquer à de plus gros vaisseaux sans support",
        'price' => array(0 => 750,1 => 250,2 => 0
        ),
        'stats' => array('type' => 0,'power' => 25,'powerBonus' => array(306 => 1.1,315 => 1.05 ),'shield' => 0,'shieldBonus' => array(300 => 1.1,307 => 1.3),'hull' => 1000,'hullBonus' => array(308 => 1.2,309 => 1.1),'rf' => array(201 => 3,203 => 4,205 => 10,206 => 10),'multiplier' => array(0 => 2),'move' => array(303 => array('level' => 0,'speed' => 12000,'consumption' => 25,'bonus' => 1.1),304 => array('level' => 3,'speed' => 12500,'consumption' => 15,'bonus' => 1.2)),'fret' => 50,'fretBonus' => array(308 => 1.1)
        ),
        'requirements' => array(109 => 1,303 => 1,306 => 1,315 => 1
        )
    ),

    201 => array(
        'name' => "Chasseur lourd",
        'desc' => "Le chasseur lourd est une version plus blindée et armée du chasseur léger, il est également équipé d'un bouclier.Bien que plus efficace que ce dernier dans sa mission principale, il a le défaut d'être moins maniable ce qui le rend vulnérable aux attaques",
        'price' => array(0 => 1500,1 => 700,2 => 0
        ),
        'stats' => array('type' => 0,'power' => 70,'powerBonus' => array(306 => 1.1,315 => 1.05 ),'shield' => 25,'shieldBonus' => array(300 => 1.1,307 => 1.3),'hull' => 2200,'hullBonus' => array(308 => 1.2,309 => 1.1),'rf' => array(203 => 5,204 => 5,205 => 10,206 => 10,),'multiplier' => array(0 => 2),'move' => array(303 => array('level' => 0,'speed' => 10000,'consumption' => 75,'bonus' => 1.1),304 => array('level' => 4,'speed' => 12000,'consumption' => 40,'bonus' => 1.2)),'fret' => 200,'fretBonus' => array(308 => 1.1)
        ),
        'requirements' => array(109 => 2,303 => 1,306 => 3,308 => 2,315 => 2
        )
    ),
    
    202 => array(
        'name' => "Intercepteur",
        'desc' => "L'intercepteur est une version plus étudiée du chasseur lourd, bien qu'encore plus blindé et plus armé, il est cependant un peu plus maniable que ce dernier au prix d'une vitesse plus faible.",
        'price' => array(0 => 2500,1 => 1000,2 => 50
        ),
        'stats' => array('type' => 0,'power' => 120,'powerBonus' => array(306 => 1.1,315 => 1.05 ),'shield' => 50,'shieldBonus' => array(300 => 1.1,307 => 1.3),'hull' => 3500,'hullBonus' => array(308 => 1.2,309 => 1.1),'rf' => array(200 => 3,201 => 2,202 => 3,203 => 10,204 => 5,205 => 10,206 => 10,),'multiplier' => array(0 => 2),'move' => array(303 => array('level' => 0,'speed' => 8000,'consumption' => 150,'bonus' => 1.1),304 => array('level' => 7,'speed' => 11000,'consumption' => 75,'bonus' => 1.2)),'fret' => 400,'fretBonus' => array(308 => 1.1)
        ),
        'requirements' => array(109 => 3,303 => 2,306 => 3,308 => 3,315 => 2
        )
    ),
    203 => array(
        'name' => "Torpilleur",
        'desc' => "Le torpilleur est un vaisseau léger destiné à infliger de lourd dommages aux vaisseaux de grande taille, son armement secondaire est faible le rendant inneficace contre des chasseurs, il doit être escorté par des chasseurs pour mener à bien sa mission.",
        'price' => array(0 => 1800,1 => 1000,2 => 100
        ),
        'stats' => array('type' => 0,'power' => 3000,'powerBonus' => array(306 => 1.1,315 => 1.01,316 => 1.1 ),'shield' => 40,'shieldBonus' => array(300 => 1.1,307 => 1.3),'hull' => 2800,'hullBonus' => array(308 => 1.2,309 => 1.1),'rf' => array(200 => 10,201 => 10,202 => 10,203 => 10,204 => 10,205 => 10,206 => 10),'multiplier' => array(0 => 0.001,1 => 0.1,2 => 1,3 => 1.5),'move' => array(303 => array('level' => 0,'speed' => 9000,'consumption' => 125,'bonus' => 1.1),304 => array('level' => 5,'speed' => 12000,'consumption' => 60,'bonus' => 1.2)),'fret' => 100,'fretBonus' => array(308 => 1.1)
        ),
        'requirements' => array(109 => 3,303 => 2,306 => 3,316 => 2)
    ),
    
    204 => array(
        'name' => "Petit transporteur",
        'desc' => "Le petit transporteur permet d'acheminer les ressources d'une planète a l'autre.C'est un vaisseau non armé, il est donc déconseillé de l'envoyer seul pour attaquer une planète.",
        'price' => array(0 => 2000,1 => 1000,2 => 50
        ),
        'stats' => array('type' => 0,'power' => 0,'powerBonus' => array(),'shield' => 0,'shieldBonus' => array(),'hull' => 3000,'hullBonus' => array(308 => 1.2,309 => 1.1),'rf' => array(),'multiplier' => array(),'move' => array(303 => array('level' => 0,'speed' => 12000,'consumption' => 50,'bonus' => 1.1),304 => array('level' => 6,'speed' => 15000,'consumption' => 25,'bonus' => 1.2)),'fret' => 5000,'fretBonus' => array(308 => 1.2)
        ),
        'requirements' => array(109 => 1,303 => 1
        )
    ),
    
    205 => array(
        'name' => "Sonde d'espionnage",
        'desc' => "La sonde d'espionnage permet de recueillir des informations sur les empires ennemis, étant donné la nature dangereuse de ce genre de mission, ces sondes sont facilement détruites.",
        'price' => array(0 => 500,1 => 750,2 => 50
        ),
        'stats' => array('type' => 0,'power' => 0,'powerBonus' => array(),'shield' => 0,'shieldBonus' => array(),'hull' => 1250,'hullBonus' => array(308 => 1.2,309 => 1.1),'rf' => array(),'multiplier' => array(),'move' => array(303 => array('level' => 0,'speed' => 75000,'consumption' => 5,'bonus' => 1.1),304 => array('level' => 7,'speed' => 150000,'consumption' => 10,'bonus' => 1.2),305 => array('level' => 10,'speed' => 200000,'consumption' => 1,'bonus' => 1.3)),'fret' => 10,'fretBonus' => array(308 => 1.1)
        ),
        'requirements' => array(109 => 1,302 => 2,303 => 2
        )
    ),
    
    206 => array(
        'name' => "Satellite solaire",
        'desc' => "Le satellite solaire est un vaisseau immobile qui ne peut donc pas être transféré d'une planète a l'autre, il permet la production d'énergie à moindres coût grace au fait que les rayons du soleils sont plus forts dans l'espace.",
        'price' => array(0 => 500,1 => 2000,2 => 50
        ),
        'production' => array(3 => 30
        ),
        'productionBonus' => array(300 => 1.05
        ),
        'stats' => array('type' => 0,'power' => 0,'powerBonus' => array(),'shield' => 0,'shieldBonus' => array(),'hull' => 2500,'hullBonus' => array(308 => 1.2,309 => 1.1),'rf' => array(),'multiplier' => array(),'move' => array(),'fret' => 0,'fretBonus' => array()
        ),
        'requirements' => array(109 => 1,300 => 3
        )
    ),
    
    207 => array(
        'name' => "Corvette",
        'desc' => "La corvette est un vaisseau léger conçu pour éliminer rapidement des chasseurs son armement reste tout de fois très léger et inneficace contre des vaisseaux plus lourds",
        'price' => array(0 => 6000,1 => 2000,2 => 200
        ),
        'stats' => array('type' => 1,'power' => 250,'powerBonus' => array(306 => 1.1,315 => 1.05,318 => 1.1 ),'shield' => 200,'shieldBonus' => array(300 => 1.1,307 => 1.3),'hull' => 8000,'hullBonus' => array(308 => 1.2,309 => 1.1),'rf' => array(200 => 10,201 => 6,202 => 3,203 => 10,204 => 5,205 => 10,206 => 10,209 => 10),'multiplier' => array(0 => 1,1 => 0.5,2 => 0.25,3 => 0.25),'move' => array(303 => array(    'level' => 0,    'speed' => 10000,    'consumption' => 500,    'bonus' => 1.1),304 => array(    'level' => 8,    'speed' => 10000,    'consumption' => 300,    'bonus' => 1.2)),'fret' => 1000,'fretBonus' => array(308 => 1.1)
        ),
        'requirements' => array(109 => 5,303 => 3,306 => 5,307 => 3,308 => 5,315 => 5,318 => 3
        )
    ),
    
    208 => array(
        'name' => "Destroyer",
        'desc' => "Le destroyer est un vaisseau suffisement léger pour risquer de transporter des torpilles, ce qui lui permet d'être efficace contre les vaisseaux capitaux, il dispose d'un armement secondaire raisonnable lui permettant de se défendre contre les chasseurs.",
        'price' => array(0 => 5000,1 => 2500,2 => 500
        ),
        'stats' => array('type' => 1,'power' => 8000,'powerBonus' => array(306 => 1.1,315 => 1.05,316 => 1.1 ),'shield' => 300,'shieldBonus' => array(300 => 1.1,307 => 1.3),'hull' => 7500,'hullBonus' => array(308 => 1.2,309 => 1.1),'rf' => array(200 => 10,201 => 10,202 => 10,203 => 10,204 => 10,205 => 10,206 => 10,209 => 10),'multiplier' => array(0 => 0.01,1 => 0.05,2 => 1,3 => 5),'move' => array(303 => array(    'level' => 0,    'speed' => 12000,    'consumption' => 500,    'bonus' => 1.1),304 => array(    'level' => 6,    'speed' => 12000,    'consumption' => 250,    'bonus' => 1.2)),'fret' => 250,'fretBonus' => array(308 => 1.1)
        ),
        'requirements' => array(109 => 5,303 => 3,306 => 6,307 => 6,308 => 4,315 => 3,316 => 5
        )
    ),
    
    209 => array(
        'name' => "Transporteur",
        'desc' => "Le transporteur est l'amélioration du petit transporteur,il possède désormais un armement et un bouclier lui permettant de repousser des chasseurs",
        'price' => array(0 => 10000,1 => 5000,2 => 500
        ),
        'stats' => array('type' => 1,'power' => 50,'powerBonus' => array(306 => 1.1,315 => 1.1,),'shield' => 100,'shieldBonus' => array(300 => 1.1,307 => 1.3),'hull' => 15000,'hullBonus' => array(308 => 1.2,309 => 1.1),'rf' => array(),'multiplier' => array(),'move' => array(303 => array(    'level' => 0,    'speed' => 7500,    'consumption' => 600,    'bonus' => 1.1),304 => array(    'level' => 8,    'speed' => 9000,    'consumption' => 350,    'bonus' => 1.2),305 => array(    'level' => 6,    'speed' => 11000,    'consumption' => 300,    'bonus' => 1.3)),'fret' => 25000,'fretBonus' => array(308 => 1.1)
        ),
        'requirements' => array(109 => 6,303 => 6,307 => 2,308 => 5,315 => 2,
        )
    ),

    210 => array(
        'name' => "Frégate",
        'desc' => "La frégate est le premier vaisseau capital disponible, elle est équipée d'un armement moyen conçu pour éliminer facilement les vaisseaux moyennement blindés,mais assez inneficace contre les chasseurs.",
        'price' => array(0 => 60000,1 => 20000,2 => 3000
        ),
        'stats' => array('type' => 2,'power' => 3000,'powerBonus' => array(306 => 1.1,318 => 1.1 ),'shield' => 3000,'shieldBonus' => array(300 => 1.1,307 => 1.3),'hull' => 80000,'hullBonus' => array(308 => 1.2,309 => 1.1),'rf' => array(205 => 10,206 => 10,207 => 8,208 => 10,209 => 10,210 => 3,212 => 10,213 => 10,217 => 10),'multiplier' => array(0 => 0.05),'move' => array(304 => array(    'level' => 0,    'speed' => 8000,    'consumption' => 2500,    'bonus' => 1.2),305 => array(    'level' => 6,    'speed' => 10000,    'consumption' => 1000,    'bonus' => 1.3)),'fret' => 5000,'fretBonus' => array(308 => 1.1)
        ),
        'requirements' => array(109 => 8,304 => 6,306 => 7,307 => 5,308 => 7,318 => 6,
        )
    ),
    
    211 => array(
        'name' => "Croiseur",
        'desc' => "Le croiseur est un vaisseau capital ayant un rôle primordial dans une flotte.Equipé d'un armement varié, il est versatile mais n'excelle en rien.Il est également pourvu de puissant propulseurs et de vastes entrepots, lui permettant d'opérer seul bien qu'il est le plus efficace accompagné de vaisseaux spécialisés.",
        'price' => array(0 => 100000,1 => 40000,2 => 10000
        ),
        'stats' => array('type' => 2,'power' => 4000,'powerBonus' => array(306 => 1.1,315 => 1.05,317 => 1.05,318 => 1.05,319 => 1.05,320 => 1.05 ),'shield' => 7500,'shieldBonus' => array(300 => 1.1,307 => 1.3),'hull' => 140000,'hullBonus' => array(308 => 1.2,309 => 1.1),'rf' => array(200 => 20,201 => 20,202 => 15,203 => 30,204 => 25,205 => 50,206 => 50,207 => 5,208 => 7,209 => 20,210 => 4,211 => 3,212 => 10,213 => 10,217 => 5),'multiplier' => array(),'move' => array(304 => array(    'level' => 0,    'speed' => 15000,    'consumption' => 5000,    'bonus' => 1.2),305 => array(    'level' => 8,    'speed' => 14000,    'consumption' => 3000,    'bonus' => 1.3)),'fret' => 20000,'fretBonus' => array(308 => 1.1)
        ),
        'requirements' => array(109 => 9,304 => 5,315 => 6,317 => 5,318 => 5,319 => 5,320 => 3
        )
    ),
    
    212 => array(
        'name' => "Recycleur",
        'desc' => "Le recycleur est un vaisseau non armé conçu pour récupérer les débris d'une battaille afin de les réutiliser.",
        'price' => array(0 => 30000,1 => 15000,2 => 2000
        ),
        'stats' => array('type' => 2,'power' => 0,'powerBonus' => array(),'shield' => 1000,'shieldBonus' => array(300 => 1.1,307 => 1.3),'hull' => 32000,'hullBonus' => array(308 => 1.2,309 => 1.1),'rf' => array(),'multiplier' => array(),'move' => array(304 => array(    'level' => 0,    'speed' => 6000,    'consumption' => 500,    'bonus' => 1.2),305 => array(    'level' => 10,    'speed' => 9000,    'consumption' => 400,    'bonus' => 1.3)),'fret' => 100000,'fretBonus' => array(308 => 1.1)
        ),
        'requirements' => array(109 => 7,300 => 6,304 => 6,307 => 7,308 => 5
        )
    ),
    
    213 => array(
        'name' => "Vaisseau de colonisation",
        'desc' => "Le vaisseau de colonisation est un vaisseau disposant d'une base déployable afin de conquérir de nouvelles planètes en vue d'agrandir votre empire.",
        'price' => array(0 => 30000,1 => 30000,2 => 5000
        ),
        'stats' => array('type' => 2,'power' => 0,'powerBonus' => array(),'shield' => 500,'shieldBonus' => array(300 => 1.1,307 => 1.3),'hull' => 60000,'hullBonus' => array(308 => 1.2,309 => 1.1),'rf' => array(),'multiplier' => array(),'move' => array(304 => array(    'level' => 0,    'speed' => 6000,    'consumption' => 500,    'bonus' => 1.2),305 => array(    'level' => 10,    'speed' => 9000,    'consumption' => 400,    'bonus' => 1.3)),'fret' => 150000,'fretBonus' => array(308 => 1.1)
        ),
        'requirements' => array(109 => 8,301 => 2,304 => 2,307 => 6
        )
    ),
    
    214 => array( // exemple vaisseau
        'name' => "Croiseur lourd", // nom
        'desc' => "Le croiseur lourd est une amélioration du croiseur, dont toutes les caractéristiques ont été revues à la hausse.",
        'price' => array( // prix de chaque ressources
        0 => 270000,1 => 130000,2 => 30000
        ),
        'stats' => array('type' => 3, // statistiques
		'power' => 10000, // puissance de feu
		'powerBonus' => array( // bonus pour chaque technologie , techId => multiplicateurParNiveau
		306 => 1.1,315 => 1.05,317 => 1.05,318 => 1.05,319 => 1.05,320 => 1.05,321 => 1.05),'shield' => 20000, // bouclier 
		'shieldBonus' => array( // bonus de tech
		300 => 1.1,307 => 1.3),'hull' => 400000, // coque
		'hullBonus' => array( // bonus de tech
		308 => 1.2,309 => 1.1),
		'rf' => array( // rapid fire , chance de retirer si on a tirer sur le vaisseau
		200 => 25, // vaisseausurlequelonatire=>chance de rapidfire
		201 => 15, // par défaut on a 1
		202 => 5, // chances de retirer =  RF-1/RF
		203 => 15, // exemple : RF de 100 =>  99 chances sur 100 de retirer
		204 => 50,// autre exemple , RF DE 4 : 3 chances sur 4 de retirer
		205 => 50,206 => 50,207 => 10,208 => 10,209 => 20,210 => 10,211 => 6,212 => 15,213 => 15,214 => 3,215 => 7,217 => 10),
		'multiplier' => array(//
		0 => 1, // multiplicateur de dégats pour un type ou vaisseau précis//
		1 => 1, // typeId => multiplicateur//
		2 => 1, // vaisseauId => multiplicateur//
		3 => 1 // type =  id<100  , ship = id > 100
        ),
		'move' => array( // vitesse du vaisseau selon la technologie , il peut y en avoir plusieurs , utilisant les technologies 303,304,305 , un vaisseau peut en avoir plusieurs , dans ce cas on prend la plus intéressante
		305 => array(    'level' => 0,    'speed' => 13000,    'consumption' => 8000,    'bonus' => 1.3)),
		'fret' => 50000, // capacité de stockage du vaisseau
		'fretBonus' => array(  // bonus
		308 => 1.1)
        ),
        'requirements' => array( // prérequis
			109 => 12,305 => 8,315 => 12,317 => 10,318 => 10,319 => 8,320 => 7,321 => 3
        )
    ),

    215 => array(
        'name' => "Bombardier orbital",
        'desc' => "Le bombardier orbital est un vaisseau de type expérimental, il était nécéssaire d'avoir des vaisseaux lourds efficaces contre les défenses planétaire, pour cela, un vaisseau spécialisé en bombardement.Son armement secondaire permet également de se défendre contre d'autres vaisseaux.",
        'price' => array(0 => 210000,1 => 110000,2 => 50000
        ),
        'stats' => array('type' => 3,'power' => 7500,'powerBonus' => array(306 => 1.1,318 => 1.01,319 => 1.2,),'shield' => 14000,'shieldBonus' => array(300 => 1.1,307 => 1.3),'hull' => 320000,'hullBonus' => array(308 => 1.2,309 => 1.1),'rf' => array(200 => 5,201 => 5,202 => 5,203 => 5,204 => 10,205 => 10,206 => 10,209 => 10,400 => 100,401 => 50,402 => 50,403 => 25,404 => 25,405 => 15,406 => 10,407 => 5,408 => 5),'multiplier' => array(0 => 0.01,1 => 0.01,2 => 0.01,3 => 0.01),'move' => array(305 => array(    'level' => 0,    'speed' => 8000,    'consumption' => 10000,    'bonus' => 1.3)),'fret' => 10000,'fretBonus' => array(308 => 1.1)
        ),
        'requirements' => array(109 => 11,305 => 6,318 => 6,319 => 9,
        )
    ),
    
    216 => array(
        'name' => "Cuirassé",
        'desc' => "Le cuirassé est le vaisseau le plus grand, le plus blindé, le plus armé et évidemment le plus puissant.Lorsque tous ses cannons font feu, il ressembla à un véritable volcan en fusion, une montagne de feu.Son armement comporte le fameux canon annihilateur.Bien que spécialisé contre les vaisseaux lourds, il demeure efficace contre tous les autres types de vaisseaux.",
        'price' => array(0 => 850000,1 => 350000,2 => 100000
        ),
        'stats' => array('type' => 3,'power' => 50000,'powerBonus' => array(306 => 1.1,315 => 1.05,317 => 1.05,318 => 1.05,319 => 1.05,320 => 1.05,321 => 1.05,322 => 1.1),'shield' => 100000,'shieldBonus' => array(300 => 1.1,307 => 1.3),'hull' => 1200000,'hullBonus' => array(308 => 1.2,309 => 1.1),'rf' => array(200 => 5,201 => 5,202 => 3,203 => 3,204 => 50,205 => 50,206 => 50,207 => 10,208 => 10,209 => 25,210 => 15,211 => 10,212 => 20,213 => 20,214 => 10,215 => 10,216 => 5,217 => 10),'multiplier' => array(),'move' => array(305 => array(    'level' => 0,    'speed' => 10000,    'consumption' => 8000,    'bonus' => 1.3)),'fret' => 200000,'fretBonus' => array(308 => 1.1)
        ),
        'requirements' => array(109 => 15,305 => 10,315 => 15,317 => 12,318 => 12,319 => 10,320 => 12,321 => 6,322 => 1
        )
    ),

    217 => array(
        'name' => "Transporteur lourd",
        'desc' => "Le transporteur lourd est l'amélioration finale du transporteur.Faiblement équipé pour le combat, il est le plus économique pour acheminer des ressource, bien qu'il ne soit pas le plus rapide.Il peut également servir de recycleur.",
        'price' => array(0 => 300000,1 => 100000,2 => 10000
        ),
        'stats' => array('type' => 3,'power' => 2000,'powerBonus' => array(306 => 1.1,318 => 1.075,320 => 1.075),'shield' => 20000,'shieldBonus' => array(300 => 1.1,307 => 1.3),'hull' => 400000,'hullBonus' => array(308 => 1.2,309 => 1.1),'rf' => array(),'multiplier' => array(),'move' => array(305 => array(    'level' => 0,    'speed' => 10000,    'consumption' => 10000,    'bonus' => 1.3)),'fret' => 1000000,'fretBonus' => array(308 => 1.1)
        ),
        'requirements' => array(109 => 12,305 => 9,318 => 8,320 => 5
        )
    ),
    
    300 => array(
        'name' => 'Energie',
        'desc' => "La technologie énergie permet de maitriser la production, le transport et l'utilisation d'énergie.",
        'price' => array(0 => 100,1 => 200,2 => 50,'multiplier' => 2
        ),
        'requirements' => array(111 => 1
        )
    ),    

    301 => array(
        'name' => 'Ordinateur',
        'desc' => "La technologie ordinateur permet de gérer un plus grand nombre de flottes simultanément.",
        'price' => array(0 => 100,1 => 200,2 => 50,'multiplier' => 2
        ),
        'requirements' => array(111 => 1
        )
    ),
    
    302 => array(
        'name' => 'Espionnage',
        'desc' => "La technologie espionnage permet d'augmenter l'efficacité des sondes et de diminuer celle des sondes adverses.",
        'price' => array(0 => 100,1 => 200,2 => 50,'multiplier' => 2
        ),
        'requirements' => array(111 => 2
        )
    ),
    
    303 => array(
        'name' => 'Propulsion à combustion',
        'desc' => "La propulsion a combustion est le moyen le plus simple pour se déplacer dans l'espace.Elle est cependant peu efficace et couteuse en carburant.",
        'price' => array(0 => 1000,1 => 50,2 => 2000,'multiplier' => 2
        ),
        'requirements' => array(111 => 3,300 => 2
        )
    ), 
    
    304 => array(
        'name' => 'Propulsion ionique',
        'desc' => "La propulsion ionique est une nouvelle méthode de propulsion, plus efficace et moins gourmande en carburant.",
        'price' => array(0 => 2000,1 => 4000,2 => 2000,'multiplier' => 2
        ),
        'requirements' => array(111 => 6,300 => 5,303 => 5
        )
    ),
    
    305 => array(
        'name' => 'Propulsion hyperspatiale',
        'desc' => "La propulsion hyperspatiale permet des déplacement ultra rapides mais également couteux en carburant, surtout pour les vaisseaux les plus lourds.",
        'price' => array(0 => 8000,1 => 10000,2 => 5000,'multiplier' => 2
        ),
        'requirements' => array(111 => 8,300 => 8,304 => 6
        )
    ),

    306 => array(
        'name' => 'Arme',
        'desc' => "La technologie arme permet la découverte de nouveaux types d'armements et améliore ceux qui existent déjà.",
        'price' => array(0 => 600,1 => 600,2 => 0,'multiplier' => 2
        ),
        'requirements' => array(111 => 2,300 => 1
        )
    ),
    
    307 => array(
        'name' => 'Bouclier',
        'desc' => "La technologie bouclier améliore les boucliers des vaisseaux en améliorant leur approvisionnement en énergie, en augmentant leur capacité,etc",
        'price' => array(0 => 400,1 => 800,2 => 0,'multiplier' => 2
        ),
        'requirements' => array(111 => 4,300 => 3
        )
    ),
    
    308 => array(
        'name' => 'Coque',
        'desc' => "La technologie coque permet l'élaboration de blindages plus épais et d'alliages plus efficaces.Elle permet aussi de creer des soutes plus vastes pour les vaisseaux",
        'price' => array(0 => 1200,1 => 0,2 => 0,'multiplier' => 2
        ),
        'requirements' => array(111 => 3,309 => 2
        )
    ),

    309 => array(
        'name' => 'Alliages',
        'desc' => "La technologie alliages permet de mieux utiliser les métaux extraits, augmentant la production des mines d'acier, et permet l'amélioration des coques de vaisseaux.",
        'price' => array(0 => 400,1 => 50,2 => 200,'multiplier' => 1.4
        ),
        'requirements' => array(111 => 2
        )
    ),
    
    310 => array(
        'name' => 'Raffinement',
        'desc' => "La technologie raffinement permet d'améliorer la production des mines de silicium en améliorant le tri des matières extraites.",
        'price' => array(0 => 100,1 => 300,2 => 100,'multiplier' => 1.4
        ),
        'requirements' => array(111 => 3
        )
    ),
    311 => array(
        'name' => 'Chimie',
        'desc' => "La technologie chimie permet d'améliorer les techniques d'extraction de l'hydrogène, augmentant la production des collecteurs.",
        'price' => array(0 => 200,1 => 200,2 => 100,'multiplier' => 1.4
        ),
        'requirements' => array(111 => 4
        )
    ), 

    312 => array(
        'name' => 'Administration',
        'desc' => "La technologie administration permet a votre empire de controler plus de planètes.",
        'price' => array(0 => 200,1 => 200,2 => 100,3 => 25,'multiplier' => 1.33
        ),
        'requirements' => array(111 => 5,300 => 3,301 => 5
        )
    ), 
    
    313 => array(
        'name' => 'Réseau de recherche inter planétaire',
        'desc' => "Le réseau de recherche interplanétaire permet à vos laboratoires de se connecter entre eux afin d'accélérer la vitesse de recherche.",
        'price' => array(0 => 100000,1 => 200000,2 => 50000,'multiplier' => 2
        ),
        'requirements' => array(111 => 10,300 => 10,301 => 10
        )
    ), 

    /*314 => array(
        'name' => 'Stratégie',
        'desc' => "La technologie de stratégie permet d'enseigner de nouvelles stratégies à vos officiers.",
        'price' => array(0 => 64000,1 => 196000,2 => 64000,'multiplier' => 2
        ),
        'requirements' => array(111 => 9,306 => 9,307 => 9,308 => 9,315 => 9,316 => 5,317 => 6,318 => 4,319 => 3,320 => 2
        )
    ), */
    315 => array(
        'name' => 'Laser',
        'desc' => "La technologie laser utilise des photons concentrés pour infliger des dégats.",
        'price' => array(0 => 1000,1 => 1000,2 => 0,'multiplier' => 2
        ),
        'requirements' => array(111 => 2,300 => 2,306 => 1
        )
    ), 
    
    316 => array(
        'name' => 'Torpilles',
        'desc' => "Les torpilles sont des concentrations de protons destructeurs destinés à infliger de très lourd dégats à des vaisseaux peu maniables.Ces armes sont montées sur des vaisseaux légers car elles sont à double tranchant, un tir touchant la batterie alimentant cette arme cause une déflagration qui endommage ou détruit le vaisseau.",
        'price' => array(0 => 4000,1 => 16000,2 => 8000,'multiplier' => 2
        ),
        'requirements' => array(111 => 5,300 => 5,306 => 3
        )
    ), 
    
    317 => array(
        'name' => 'Ion',
        'desc' => "Les ions sont assez peu utilisés en armement.Ils sont cependant très efficaces contre les bouclier et sont notemment montés sur les redoutables machines à tuer que sont les croiseurs.",
        'price' => array(0 => 10000,1 => 20000,2 => 5000,'multiplier' => 2
        ),
        'requirements' => array(111 => 5,300 => 4,306 => 3,307 => 3,315 => 4
        )
    ), 
    
    318 => array(
        'name' => 'Phaser',
        'desc' => "La phaser est une version améliorée du laser.Il est bien plus versatile que son homologue et permet la destruction de cibles moyennement blindées.",
        'price' => array(0 => 50000,1 => 25000,2 => 5000,'multiplier' => 2
        ),
        'requirements' => array(111 => 6,300 => 6,306 => 5,315 => 7
        )
    ), 

    319 => array(
        'name' => 'Plasma',
        'desc' => "Les armes au plasma sont une nouvelle génération d'arme très destructrices, elles sont utilisées principalement pour détruire des défenses au sol bien qu'elles soient également très puissantes contre les vaisseaux.",
        'price' => array(0 => 100000,1 => 200000,2 => 100000,'multiplier' => 2
        ),
        'requirements' => array(111 => 8,300 => 10,306 => 8,318 => 5
        )
    ), 
    
    320 => array(
        'name' => 'Turbolaser',
        'desc' => "Le turbolaser est une évolution du phaser en arme extremement puissante capable de pénétrer tous les blindages, elle est cependant destinée à la destruction de cibles peu maniable en raison du poids de ce type d'armement.",
        'price' => array(0 => 400000,1 => 200000,2 => 50000,'multiplier' => 2
        ),
        'requirements' => array(111 => 10,300 => 10,306 => 10,315 => 12,318 => 6
        )
    ), 
    
    321 => array(
        'name' => 'Antimatière',
        'desc' => "L'antimatière est une arme de dernière génération, plus efficace que le plasma bien que plus couteuse, son efficacité n'est pas à démontrer.",
        'price' => array(0 => 500000,1 => 500000,2 => 250000,'multiplier' => 2
        ),
        'requirements' => array(111 => 12,300 => 12,306 => 12,319 => 6
        )
    ), 
    
    322 => array(
        'name' => 'Annihilateur',
        'desc' => "L'annihilateur est une arme expérimentale, extrêmement puissante et couteuse, elle ne peut être armée que sur des vaisseaux très imposants, un seul tir de cet arme requiert des quantités d'énergie qui pourraient subvenir aux besoins de planètes entières.",
        'price' => array(0 => 5000000,1 => 5000000,2 => 5000000,3 => 1000000,'multiplier' => 3
        ),
        'requirements' => array(111 => 15,300 => 15,306 => 15,315 => 15,317 => 12,318 => 12,319 => 9,320 => 9,321 => 6
        )
    ),
	
	400 => array(
        'name' => "Tourelle lance missile",
        'desc' => "La tourelle lance missile est une tourelle de base efficace contre les petits vaisseaux.",
        'price' => array(0 => 2000,1 => 500,2 => 0
        ),
        'stats' => array('type' => 10,'power' => 100,'powerBonus' => array(306 => 1.1),'shield' => 0,'shieldBonus' => array(300 => 1.1,307 => 1.3),'hull' => 2500,'hullBonus' => array(308 => 1.2,309 => 1.1),'rf' => array(200 => 5,201 => 3,202 => 2,203 => 5,204 => 5,205 => 5,206 => 10,),'multiplier' => array(
				200 => 2,201 => 2,
				202 => 3,203 => 5,1 => 0.25,2 => 0,3 => 0),'target' => array(0,1,2,3)
        ),
        'requirements' => array(110 => 1
        )
    ),
    
	401 => array(
        'name' => "Tourelle laser",
        'desc' => "La tourelle laser est une défense basique efficace contre les chasseurs.",
        'price' => array(0 => 3000,1 => 1000,2 => 0
        ),
        'stats' => array('type' => 10,'power' => 100,'powerBonus' => array(306 => 1.1,315 => 1.1),'shield' => 100,'shieldBonus' => array(300 => 1.1,307 => 1.3),'hull' => 4000,'hullBonus' => array(308 => 1.2,309 => 1.1),'rf' => array(200 => 5,201 => 3,202 => 2,203 => 5,204 => 5,205 => 5,206 => 10,),'multiplier' => array(
				200 => 2,201 => 2,
				202 => 3,203 => 5,1 => 0.8,2 => 0.6,3 => 0.3),'target' => array(0,1,2,3)
        ),
        'requirements' => array(110 => 2,315 => 2
        )
    ),
 
 	402 => array(
        'name' => "Tourelle laser lourde",
        'desc' => "La tourelle laser lourde est une amélioration de la tourelle laser, grace à la même maniabilité que cette derniere et un plus gros calibre, elle se montre d'une plus grande versatilité.",
        'price' => array(0 => 12000,1 => 4000,2 => 0
        ),
        'stats' => array('type' => 10,'power' => 350,'powerBonus' => array(306 => 1.1,315 => 1.1),'shield' => 500,'shieldBonus' => array(300 => 1.1,307 => 1.3),'hull' => 16000,'hullBonus' => array(308 => 1.2,309 => 1.1),'rf' => array(200 => 8,201 => 5,202 => 3,203 => 10,204 => 10,205 => 10,206 => 20,209 => 5),'multiplier' => array(
				200 => 2,201 => 2,
				202 => 3,203 => 5),'target' => array(0,1,2,3)
        ),
        'requirements' => array(110 => 3,315 => 5
        )
    ),
 
	403 => array(
        'name' => "Tourelle ionique",
        'desc' => "La tourelle ionique est une tourelle particulière qui est conçue principalement pour endommager les boucliers adverses.",
        'price' => array(0 => 5000,1 => 15000,2 => 0
        ),
        'stats' => array('type' => 10,'power' => 250,'powerBonus' => array(306 => 1.1,317 => 1.2),'shield' => 2000,'shieldBonus' => array(300 => 1.1,307 => 1.3),'hull' => 20000,'hullBonus' => array(308 => 1.2,309 => 1.1),'rf' => array(200 => 10,201 => 8,202 => 10,203 => 8,204 => 10,205 => 10,206 => 20,207 => 3,208 => 5,209 => 10),'multiplier' => array(1 => 1,2 => 1.5,3 => 2),'target' => array(0,1,2,3)
        ),
        'requirements' => array(110 => 5,317 => 3 
        )
    ),
    
 	404 => array(
        'name' => "Tourelle phaser",
        'desc' => "La tourelle phaser est une tourelle polyvalente capable de repousser efficacement tout type d'ennemi.",
        'price' => array(0 => 40000,1 => 20000,2 => 2000
        ),
        'stats' => array('type' => 10,'power' => 1750,'powerBonus' => array(306 => 1.1,318 => 1.15),'shield' => 2000,'shieldBonus' => array(300 => 1.1,307 => 1.3),'hull' => 60000,'hullBonus' => array(308 => 1.2,309 => 1.1),'rf' => array(200 => 10,201 => 7,202 => 5,203 => 10,204 => 10,205 => 10,206 => 25,207 => 5,208 => 8,209 => 10,210 => 5,211 => 3,212 => 5,213 => 5,214 => 2,215 => 3,217 => 3),'multiplier' => array(),'target' => array(1,2,0,3)
        ),
        'requirements' => array(110 => 7,318 => 5
        )
    ),
    
 	405 => array(
        'name' => "Tourelle phaser lourde",
        'desc' => "La tourelle a phaser lourde est mieux blindée et dispose d'un meilleure bouclier, le tout couplé à une puissance de feu plus importante.C'est la premiere défense conçue pour pulvériser des vaisseaux de classe plus lourde.",
        'price' => array(0 => 100000,1 => 60000,2 => 5000
        ),
        'stats' => array('type' => 10,'power' => 3500,'powerBonus' => array(306 => 1.1,318 => 1.15),'shield' => 6000,'shieldBonus' => array(300 => 1.1,307 => 1.3),'hull' => 160000,'hullBonus' => array(308 => 1.2,309 => 1.1),'rf' => array(200 => 20,201 => 15,202 => 10,203 => 20,204 => 20,205 => 20,206 => 50,207 => 10,208 => 15,209 => 25,210 => 15,211 => 10,212 => 20,213 => 20,214 => 6,215 => 10,216 => 3,217 => 10),'multiplier' => array(
				0 => 0.1,1 => 0.4,2 => 1.1,3 => 1.2),'target' => array(3,2,1,0)
        ),
        'requirements' => array(110 => 9,318 => 8
        )
    ),
    
  	406 => array(
        'name' => "Tourelle plasma",
        'desc' => "La tourelle plasma est une étape importante dans le développement des défenses, elle marque l'apparition de tourelle construites uniquement dans le but d'annéantir des vaisseaux lourds, originellement conçue pour détruire des installations au sol, l'arme au plasma se révèle également très efficace contre des vaisseaux du moment que le vaisseau n'ait pas le temps de manoeuvrer pour éviter le tir.",
        'price' => array(0 => 200000,1 => 80000,2 => 20000
        ),
        'stats' => array('type' => 10,'power' => 10000,'powerBonus' => array(306 => 1.1,319 => 1.15),'shield' => 10000,'shieldBonus' => array(300 => 1.1,307 => 1.3),'hull' => 280000,'hullBonus' => array(308 => 1.2,309 => 1.1),'rf' => array(200 => 5,201 => 3,202 => 3,203 => 5,204 => 20,205 => 20,206 => 50,207 => 8,208 => 12,209 => 25,210 => 10,211 => 10,212 => 20,213 => 20,214 => 6,215 => 10,216 => 3,217 => 10),'multiplier' => array(),'target' => array(3,2,1,0)
        ),
        'requirements' => array(110 => 10,319 => 5
        )
    ),
    
  	407 => array(
        'name' => "Tourelle turbolaser",
        'desc' => "La tourelle turbolaser est une tourelle relativement polyvalente bien qu'elle soit conçue a l'origine pour causer des dommages importants aux vaisseaux de taille moyenne",
        'price' => array(0 => 210000,1 => 70000,2 => 5000
        ),
        'stats' => array('type' => 10,'power' => 10000,'powerBonus' => array(306 => 1.1,320 => 1.15),'shield' => 8000,'shieldBonus' => array(300 => 1.1,307 => 1.3),'hull' => 280000,'hullBonus' => array(308 => 1.2,309 => 1.1),'rf' => array(200 => 20,201 => 10,202 => 10,203 => 20,204 => 20,205 => 20,206 => 50,207 => 15,208 => 20,209 => 25,210 => 10,211 => 10,212 => 20,213 => 20,214 => 3,215 => 5,217 => 5),'multiplier' => array(),'target' => array(2,1,3,0)
        ),
        'requirements' => array(110 => 10,319 => 5
        )
    ),

 	408 => array(
        'name' => "Tourelle antimatière",
        'desc' => "La tourelle antimatière est une version encore plus puissante de la tourelle plasma.Construite dans le meme but, elle est encore plus efficace.",
        'price' => array(0 => 300000,1 => 100000,2 => 40000
        ),
        'stats' => array('type' => 10,'power' => 15000,'powerBonus' => array(306 => 1.1,321 => 1.15),'shield' => 8000,'shieldBonus' => array(300 => 1.1,307 => 1.3),'hull' => 400000,'hullBonus' => array(308 => 1.2,309 => 1.1),'rf' => array(200 => 5,201 => 3,202 => 3,203 => 5,204 => 20,205 => 20,206 => 50,207 => 8,208 => 12,209 => 25,210 => 10,211 => 10,212 => 20,213 => 20,214 => 6,215 => 10,216 => 3,217 => 10),'multiplier' => array(),'target' => array(3,2,1,0)
        ),
        'requirements' => array(110 => 12,321 => 6
        )
    ),
 
  	409 => array(
        'name' => "Tourelle annihilateur",
        'desc' => "La tourelle annihilateur est la version montée sur tourelle du terrible canon annihilateur que l'on retrouve sur les cuirassés.Elle est conçue pour éliminer tout type de vaisseau avec une efficacité inégalable.",
        'price' => array(0 => 500000,1 => 250000,2 => 40000
        ),
        'stats' => array('type' => 10,'power' => 30000,'powerBonus' => array(306 => 1.1,322 => 1.15),'shield' => 15000,'shieldBonus' => array(300 => 1.1,307 => 1.3),'hull' => 750000,'hullBonus' => array(308 => 1.2,309 => 1.1),'rf' => array(200 => 100,201 => 100,202 => 100,203 => 100,204 => 200,205 => 200,206 => 250,207 => 50,208 => 75,209 => 100,210 => 35,211 => 20,212 => 50,213 => 50,214 => 10,215 => 20,216 => 5,217 => 30),'multiplier' => array(),'target' => array()
        ),
        'requirements' => array(110 => 16,322 => 6
        )
    ),
    
);
$list = array(
    'ressources' => array(),
    'buildings' => array(),
    'ships' => array(),
    'researches' => array(),
    'defenses' => array()
); 
// list permet d'avoir chaque id dans des array
// exemple : $list['ships'] = array(200,201,202,203,204,205,206,etc)
$shipsType = array( // categories for ships and stuff
    0 => "Vaisseaux légers",
    1 => "Vaisseaux moyens",
    2 => "Vaisseaux lourds",
    3 => "Vaisseaux très lourds"
);

foreach($var AS $k=>$v){ // remplissage de $list
    if($k<100){
        $list['ressources'][] = $k;
    } else if($k<200){
        $list['buildings'][] = $k;
    } else if($k<300){
        $list['ships'][] = $k;
    } else if($k<400){
        $list['researches'][] = $k;
    } else if($k<500){
        $list['defenses'][] = $k;
    } else {
        error(__FILE__, __LINE__,"Unexpected var id : " . $k);
    }
}
$missionList = array(
"Attaquer",
"Transporter",
"Stationner",
"Coloniser",
"Exploiter",
"Espionner"
);




?> 