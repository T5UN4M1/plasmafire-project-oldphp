<?php

/**
 * @author T5UN4M1
 * @copyright 2015
 */ 



?> 


<?php
$making = arrayOrganize($planet->getMaking("defenses"));
    foreach($list['defenses'] AS $id){ // construction de defenses
        if(!empty($_POST["defense"][$id]) && floor($_POST["defense"][$id]) > 0){
            $defense = new Defense($id,$planet);
            if($defense->isResearched()){
                $amount = min((int) floor($_POST["defense"][$id]), $defense->howManyAreAffordable());
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
                    
                    $planet->charge($defense->getPrice($amount));
                    
                    header("Location:?a=defensecenter");
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
        $defense = new Ship($making[$i]['itemid'],$planet);
        $buildingTime = $defense->getBuildingTime();
        $remainingTime = $defense->timeToBuild($making[$i]['number']) - (($i==0) ? ($_SERVER['REQUEST_TIME']-$making[0]['timestart']) : 0);
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
    <td><img src="{{img}}" style="width:150px;height:150px;" /></td>
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

foreach($list['defenses'] AS $id){
    $defense = new Defense($id,$planet);
    if($defense->isResearched()){
        $price = $defense->getPrice();
        
        $strPrice = 'Prix : ' . displayPrice($price);
        /*foreach($list['ressources'] AS $key){
            if(!empty($price[$key])){
                $strPrice .= $var[$key]['name'] . ' : ' . $price[$key] . ' ';
            }
        }*/
        
        $strBuild = '<form id="form'.$id.'" method="POST" action="?a=defensecenter"><input name="defense['.$id.']" value="0" size="9" /></form>
        <a href="javascript:" onclick="document.getElementsByName(\'defense['.$id.']\')[0].value = \''.$defense->howManyAreAffordable().'\';">Max</a><br />
        <a href="javascript:" onclick="getId(\'form'.$id.'\').submit();">Construire</a>';
        
        
        /*if($planet->isBuilding("defenses")){
            if($max < $config['maxAllowedQueue']){
                $strBuild = '<a href="?a=buildings&build='.$id.'" class="possible">Ajouter à la liste</a>';
            } else {
                 $strBuild = '<a class="impossible">Liste de construction pleine!</a>';
            }
        }else if(!$defense->isAffordable()){
            $strBuild = '<a class="impossible">Pas assez de ressources</a>';
        } else {
            $strBuild = '<a href="?a=buildings&build='.$id.'" class="possible">Construire</a>';
        }*/
        $rpl = array(
            "img"   => $_SESSION['skin'] . 'defenses/' . $id .'.png',
            "title" => getTechLink($id) . ' (disponible(s) : ' . $planet->getData($id) .')',
            "desc"  => $var[$id]['desc'],
            "price" => $strPrice,
            "time"  => formateTime($defense->getBuildingTime()),
            "build" => $strBuild
        );
        echo tplReplace($tpl,$rpl);
    }
}

?> 
</table>