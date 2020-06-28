<?php

/**
 * @author T5UN4M1
 * @copyright 2015
 */ 



?> 


<?php
$making = arrayOrganize($planet->getMaking("ships"));
    foreach($list['ships'] AS $id){ // construction de ships
        if(!empty($_POST["ship"][$id]) && floor($_POST["ship"][$id]) > 0){
            $ship = new Ship($id,$planet);
            if($ship->isResearched()){
                $amount = min((int) floor($_POST["ship"][$id]), $ship->howManyAreAffordable());
                if($amount > 0){
                    $req = $pdo->prepare('INSERT INTO '.table("making")." (id,userid,planetid,itemid,number,timestart,timeend,priority) VALUES (:id,:userid,:planetid,:itemid,:number,:timestart,:timeend,:priority)");
                    $req->bindValue(":id",keyGen(table("making")),PDO::PARAM_INT);
                    $req->bindValue(':userid',$_SESSION['userid'],PDO::PARAM_INT);
                    $req->bindValue(':planetid',$_SESSION['planetid'],PDO::PARAM_INT);
                    $req->bindValue(':itemid',$id,PDO::PARAM_INT);
                    $req->bindValue(':number',$amount,PDO::PARAM_INT);
                    $req->bindValue(':timestart',((empty($making)) ? $_SERVER['REQUEST_TIME'] : 0),PDO::PARAM_INT);
                    $req->bindValue(':timeend',0,PDO::PARAM_INT);
                    $req->bindValue(':priority',((!empty($making)) ? ($making[count($making)-1]['priority'] + 1) : 0 ),PDO::PARAM_INT);
                    
                    $req->execute();
                    
                    $planet->charge($ship->getPrice($amount));
                    
                    header("Location:?a=shipyard");
                    $planet->proceedUpdate();
                    exit();
                }
            }
            break;
        }
    }

?>

<?php // affichage des constructions en cours
if(!empty($making)){
    echo '<table class="content"><tr><th>Liste de construction</th></tr>';
    for($i=0,$max=count($making);$i<$max;++$i){
        $ship = new Ship($making[$i]['itemid'],$planet);
        $buildingTime = $ship->getBuildingTime();
        $remainingTime = $ship->timeToBuild($making[$i]['number']) - (($i==0) ? ($_SERVER['REQUEST_TIME']-$making[0]['timestart']) : 0);
		if($i==0){
			if($buildingTime < 1){ // fast mode
				echo '<tr><td><span id="shipyardAmount">'. format($making[$i]['number']) . '</span> de ' . $var[$making[$i]['itemid']]['name'] . '.Temps de construction restant: <span id="shipyardOverallTimeLeft">' . formateTime($remainingTime) . '</span></td></tr>';
			} else { // slow mode
				$timeToBuildCurrent = $buildingTime - ($_SERVER['REQUEST_TIME']-$making[0]['timestart']);
				echo '<tr><td><span id="shipyardAmount">'. format($making[$i]['number']) . '</span> de ' . $var[$making[$i]['itemid']]['name'] . ".<br />Temps de construction restant de l'unité en cours:".'<span id="shipyardTimeLeft">'.formateTime($timeToBuildCurrent).'</span><br />Temps de construction restant: <span id="shipyardOverallTimeLeft">' . formateTime($remainingTime) . '</span></td></tr>';
				echo '<script>initShipyard('.$remainingTime.','.$making[0]['number'].',1,'.round($buildingTime).',"shipyardAmount","shipyardOverallTimeLeft","shipyardTimeLeft");</script>';
			}
		} else {
			echo '<tr><td>'. format($making[$i]['number']) . ' de ' . $var[$making[$i]['itemid']]['name'] . '.Temps de construction : ' . formateTime($remainingTime) . '</td></tr>';
		}
    }
    echo "</table>";
}


?>
<table class="content2"><?php
$tpl = 
'<tr>
    <td><img src="{{img}}" style="width:250px;height:125px;" /></td>
    <td>
        {{title}}<br />
        {{desc}}<br />
        {{price}}<br />
        {{time}}
    </td>
    <td>
        {{build}}
    </td>
</tr>';

foreach($list['ships'] AS $id){
    $ship = new Ship($id,$planet);
    if($ship->isResearched()){
        $price = $ship->getPrice();
        
        $strPrice = 'Prix : ' . displayPrice($price);
        /*foreach($list['ressources'] AS $key){
            if(!empty($price[$key])){
                $strPrice .= $var[$key]['name'] . ' : ' . $price[$key] . ' ';
            }
        }*/
        
        $strBuild = '<form id="form'.$id.'" method="POST" action="?a=shipyard"><input name="ship['.$id.']" value="0" size="9" /></form>
        <a href="javascript:" onclick="document.getElementsByName(\'ship['.$id.']\')[0].value = \''.$ship->howManyAreAffordable().'\';">Max</a><br />
        <a href="javascript:" onclick="getId(\'form'.$id.'\').submit();">Construire</a>';
        
        
        /*if($planet->isBuilding("ships")){
            if($max < $config['maxAllowedQueue']){
                $strBuild = '<a href="?a=buildings&build='.$id.'" class="possible">Ajouter à la liste</a>';
            } else {
                 $strBuild = '<a class="impossible">Liste de construction pleine!</a>';
            }
        }else if(!$ship->isAffordable()){
            $strBuild = '<a class="impossible">Pas assez de ressources</a>';
        } else {
            $strBuild = '<a href="?a=buildings&build='.$id.'" class="possible">Construire</a>';
        }*/
        $rpl = array(
            "img"   => $_SESSION['skin'] . 'ships/' . $id .'.png',
            "title" => getTechLink($id) . ' (disponible(s) : ' . $planet->getData($id) .')',
            "desc"  => $var[$id]['desc'],
            "price" => $strPrice,
            "time"  => formateTime($ship->getBuildingTime()),
            "build" => $strBuild
        );
        echo tplReplace($tpl,$rpl);
    }
}

?> 
</table>