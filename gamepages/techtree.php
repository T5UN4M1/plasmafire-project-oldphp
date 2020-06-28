<?php

/**
 * @author T5UN4M1
 * @copyright 2015
 */ 



?>

<table class="content">
    <th colspan="2">Batiments</th>
    <?php
        foreach($list['buildings'] AS $id){
            ?><tr><td><?php
            $building = new Building($id,$planet);
            echo '<a href="?a=tech&id='.$id.'" class="'.((!$building->isResearched()) ? 'im': '').'possible">'.$var[$id]['name']. (($planet->getData($id) > 0) ? ' (niveau '. $planet->getData($id).')' : '') .'</a>';
             ?></td><td><?php
            foreach($var[$id]['requirements'] AS $reqId => $reqLvl){
                $lvl = (isTech($reqId)) ? $planet->getPlayer()->getTech($reqId)
                                        : $planet->getData($reqId);
                if($lvl >= $reqLvl){
                    echo '<a href="?a=tech&id='.$reqId.'" class="possible">'.$var[$reqId]['name'].' niveau '. $reqLvl .'(actuel : '. $lvl .')</a><br />';
                } else if($lvl > 0){
                    echo '<a href="?a=tech&id='.$reqId.'" class="presque">'.$var[$reqId]['name'].' niveau '. $reqLvl .'(actuel : '. $lvl .')</a><br />';
                } else {
                    echo '<a href="?a=tech&id='.$reqId.'" class="impossible">'.$var[$reqId]['name'].' niveau '. $reqLvl .'</a><br />';
                }
            }            
            ?></td></tr><?php
        }
    ?>
    
    <tr><th colspan="2">Vaisseaux</th></tr>
    
    <?php
        $cat = -1;
        foreach($list['ships'] AS $id){
            if($var[$id]['stats']['type'] != $cat){
                $cat = $var[$id]['stats']['type'];
                echo '<tr><th colspan="2">'.$shipsType[$cat].'</th></tr>';
            }
            ?><tr><td><?php
            $ship = new Ship($id,$planet);
            echo '<a href="?a=tech&id='.$id.'" class="'.((!$ship->isResearched()) ? 'im': '').'possible">'.$var[$id]['name']. (($planet->getData($id) > 0) ? ' (disponible : '. $planet->getData($id).')' : '') .'</a>';
            ?></td><td><?php
            foreach($var[$id]['requirements'] AS $reqId => $reqLvl){
                $lvl = (isTech($reqId)) ? $planet->getPlayer()->getTech($reqId)
                                        : $planet->getData($reqId);
                if($lvl >= $reqLvl){
                    echo '<a href="?a=tech&id='.$reqId.'" class="possible">'.$var[$reqId]['name'].' niveau '. $reqLvl .'(actuel : '. $lvl .')</a><br />';
                } else if($lvl > 0){
                    echo '<a href="?a=tech&id='.$reqId.'" class="presque">'.$var[$reqId]['name'].' niveau '. $reqLvl .'(actuel : '. $lvl .')</a><br />';
                } else {
                    echo '<a href="?a=tech&id='.$reqId.'" class="impossible">'.$var[$reqId]['name'].' niveau '. $reqLvl .'</a><br />';
                }
            }            
            ?></td></tr><?php
        }
    ?>
    
    <tr><th colspan="2">Technologies</th></tr>
    
    <?php
        foreach($list['researches'] AS $id){
            ?><tr><td><?php
            $research = new Research($id,$planet);
            echo '<a href="?a=tech&id='.$id.'" class="'.((!$research->isResearched()) ? 'im': '').'possible">'.$var[$id]['name']. (($planet->getPlayer()->getTech($id) > 0) ? ' (niveau '. $planet->getPlayer()->getTech($id).')' : '') .'</a>';
             ?></td><td><?php
            foreach($var[$id]['requirements'] AS $reqId => $reqLvl){
                $lvl = (isTech($reqId)) ? $planet->getPlayer()->getTech($reqId)
                                        : $planet->getData($reqId);
                if($lvl >= $reqLvl){
                    echo '<a href="?a=tech&id='.$reqId.'" class="possible">'.$var[$reqId]['name'].' niveau '. $reqLvl .'(actuel : '. $lvl .')</a><br />';
                } else if($lvl > 0){
                    echo '<a href="?a=tech&id='.$reqId.'" class="presque">'.$var[$reqId]['name'].' niveau '. $reqLvl .'(actuel : '. $lvl .')</a><br />';
                } else {
                    echo '<a href="?a=tech&id='.$reqId.'" class="impossible">'.$var[$reqId]['name'].' niveau '. $reqLvl .'</a><br />';
                }
            }            
            ?></td></tr><?php
        }
    ?>
    
    <tr><th colspan="2">Défenses</th></tr>
    
    <?php
        foreach($list['defenses'] AS $id){
            ?><tr><td><?php
            $defense = new Defense($id,$planet);
            echo '<a href="?a=tech&id='.$id.'" class="'.((!$defense->isResearched()) ? 'im': '').'possible">'.$var[$id]['name']. (($planet->getData($id) > 0) ? ' (construit : '. $planet->getData($id).')' : '') .'</a>';
            ?></td><td><?php
            foreach($var[$id]['requirements'] AS $reqId => $reqLvl){
                $lvl = (isTech($reqId)) ? $planet->getPlayer()->getTech($reqId)
                                        : $planet->getData($reqId);
                if($lvl >= $reqLvl){
                    echo '<a href="?a=tech&id='.$reqId.'" class="possible">'.$var[$reqId]['name'].' niveau '. $reqLvl .'(actuel : '. $lvl .')</a><br />';
                } else if($lvl > 0){
                    echo '<a href="?a=tech&id='.$reqId.'" class="presque">'.$var[$reqId]['name'].' niveau '. $reqLvl .'(actuel : '. $lvl .')</a><br />';
                } else {
                    echo '<a href="?a=tech&id='.$reqId.'" class="impossible">'.$var[$reqId]['name'].' niveau '. $reqLvl .'</a><br />';
                }
            }            
            ?></td></tr><?php
        }
    ?>


</table> 