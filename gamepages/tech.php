<?php

/**
 * @author T5UN4M1
 * @copyright 2015
 */ 
 
?> 

<?php
$tpl = '
<table class="content">
    <tr><th colspan="2">{{name}}</th></tr>
    <tr><td><img src="{{img}}" /></td><td>{{desc}}</td></tr>
</table>
';
$tplShips = '
<table class="content">
    <tr><th>{{name}}</th></tr>
    <tr><td><img src="{{img}}" /></td></tr>
    <tr><td>{{desc}}</td></tr>
</table>
';



    $id = (isset($_GET['id']) && (isBuild($_GET['id']) || isTech($_GET['id']) || isShip($_GET['id']) || isDef($_GET['id']))) ? $_GET['id'] : 100;
    
    if(isBuild($id)){
        $rpl = array(
            "name" => $var[$id]['name'],
            "img"  => $_SESSION['skin'] . 'buildings/'.$id.'.png',
            "desc" => $var[$id]['desc']
        );
        echo tplReplace($tpl,$rpl);
        
        
        if(!empty($var[$id]['production'])){ // affichage de la production pour les batiments a production
            $building = new Building($id,$planet);
            $productionData = array();
            $startingLevel = max(0,($planet->getData($id)-2));
            for($i=$startingLevel;$i<($startingLevel+30);++$i){
                $productionData[$i] = $building->getDetailledProduction(array($id=>$i),array(),100);
            }
            //var_dump($productionData);
            
            echo '<table class="content"><tr><th>niveau</th>';
            foreach($list['ressources'] AS $resId){
                if(isset($productionData[$startingLevel+1]['building'][$resId])){ // si notre batiment produit/consomme cette ressource
                    echo "<th>". (($productionData[$startingLevel+1]['building'][$resId] > 0) ? "Production" : "Consommation"). " (".$var[$resId]['name'].")</th><th>Différence</th>";
                    foreach($productionData[$startingLevel+1] AS $productionType=>$productions){ // pour les bonus ressource
                        if(is_int($productionType) && isset($productions[$resId])){ // bonus tech
                            echo '<th>Bonus technologie '.$var[$productionType]['name'].' niveau ('. $planet->getPlayer()->getTech($productionType) .')</th><th>Différence</th>';
                        }
                    }   
                }
            }
            echo '</tr>';
            foreach($productionData AS $buildingLevel => $detailledProduction){ // foreach LINE (level)
                echo '<tr><td><span' . (($buildingLevel==$planet->getData($id)) ? ' class="good"' : '') . '>'.$buildingLevel.'</span></td>';
                foreach($list['ressources'] AS $resId){
                    if(isset($productionData[$startingLevel+1]['building'][$resId])){ // si notre batiment produit/consomme cette ressource
                        echo '<td>'.color($detailledProduction['building'][$resId]).'</span></td><td>'.color($detailledProduction['building'][$resId] - $productionData[$planet->getData($id)]['building'][$resId]).'</td>';
                        foreach($detailledProduction AS $productionType=>$productions){ // pour les bonus ressource
                            if(is_int($productionType) && isset($productions[$resId])){ // bonus tech
                                echo '<td>'.color($productions[$resId]).'</td><td>'.color($productions[$resId] - 0/*$productionData[$planet->getData($id)][$productionType][$resId]*/).'</td>';
                            }
                        }  
                    }
                }
                echo '</tr>';
            }
        } else if(!empty($var[$id]['storage'])){
            $startingLevel = max(0,($planet->getData($id)-2));
            $warehouse = new Building($id,$planet);
            echo '<table class="content"><tr><th>niveau</th><th>Capacité de stockage</th></tr>';
            for($i=$startingLevel;$i<($startingLevel + 30);++$i){
                echo '<tr><td><span' . (($i==$planet->getData($id)) ? ' class="good"' : '') . '>'.$i.'</td><td>'.format($warehouse->getStorage($i)).'</span></td></tr>';
            }
            echo '</table>';
        }
    } else if(isShip($id)){

        
        
        
        $rpl = array(
            "name" => $var[$id]['name'],
            "img"  => $_SESSION['skin'] . 'ships/'.$id.'.png',
            "desc" => $var[$id]['desc'],
        );
        echo tplReplace($tplShips,$rpl);
        // création de la liste de rapidfires
        echo '<table class="content">';
        echo '<tr><th>Rapidfire infligé</th><th>Rapidfire subi</th></tr><tr><td>';
        foreach($var[$id]['stats']['rf'] AS $shipId=>$rf){
            echo "Rapidfire contre " . getTechLink($shipId) . ' : <span class="good">'.$rf.'</span><br />';
        }
        echo '</td><td>';
        foreach($var AS $itemId=>$garbage){
            if(isset($var[$itemId]['stats']['rf']) && array_key_exists($id,$var[$itemId]['stats']['rf'])){ // it has rapidfire
                echo "Rapidfire subi de " .getTechLink($itemId) . ' : <span class="bad">'.$var[$itemId]['stats']['rf'][$id].'</span><br />';
            }
        }
        echo '</td></tr>';
        
        // stats 
        $stats = array(
            "Dégats :" => "power",
            "Bouclier :" => "shield",
            "Blindage :" => "hull",
            "Capacité des soutes :" => "fret"
        );
        $shipInfo = new Ship($id,$planet);
        foreach($stats AS $label=>$neededStat){
            echo "<tr><td>".$label."</td><td>";
            $stat = $shipInfo->getStat($neededStat);
            echo format($stat['total']);
            echo "</td></tr>";
        }
        
        // propulsion
        
        $move = $shipInfo->getMove();
        echo '<tr><td>Vitesse : </td><td>'.format($move['total']).'</td></tr>';
        echo '<tr><td>Consommation :</td><td>'.format($move['consumption']).'</td></tr>';
        
        
        echo'</table>';
    } else if(isTech($id)){
        $rpl = array(
            "name" => $var[$id]['name'],
            "img"  => $_SESSION['skin'] . 'researches/'.$id.'.png',
            "desc" => $var[$id]['desc']
        );
        echo tplReplace($tpl,$rpl);
    } else if(isDef($id)){
        $rpl = array(
            "name" => $var[$id]['name'],
            "img"  => $_SESSION['skin'] . 'defenses/'.$id.'.png',
            "desc" => $var[$id]['desc']
        );
        echo tplReplace($tpl,$rpl);
        // création de la liste de rapidfires
        echo '<table class="content">';
        echo '<tr><th>Rapidfire infligé</th><th>Rapidfire subi</th></tr><tr><td>';
        foreach($var[$id]['stats']['rf'] AS $shipId=>$rf){
            echo "Rapidfire contre " . getTechLink($shipId) . ' : <span class="good">'.$rf.'</span><br />';
        }
        echo '</td><td>';
        foreach($var AS $itemId=>$garbage){
            if(isset($var[$itemId]['stats']['rf']) && array_key_exists($id,$var[$itemId]['stats']['rf'])){ // it has rapidfire
                echo "Rapidfire subi de " .getTechLink($itemId) . ' : <span class="bad">'.$var[$itemId]['stats']['rf'][$id].'</span><br />';
            }
        }
        echo '</td></tr>';
        
        // stats 
        $stats = array(
            "Dégats :" => "power",
            "Bouclier :" => "shield",
            "Blindage :" => "hull"
        );
        $shipInfo = new Ship($id,$planet);
        foreach($stats AS $label=>$neededStat){
            echo "<tr><td>".$label."</td><td>";
            $stat = $shipInfo->getStat($neededStat);
            echo format($stat['total']);
            echo "</td></tr>";
        }
        echo '</table>';
    }


?>


