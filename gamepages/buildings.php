<?php

/**
 * @author T5UN4M1
 * @copyright 2015
 */ 

if(isset($_GET['cancel'])){ // cancelling
    $making = $planet->getMaking("buildings");
    for($i=0,$max=count($making);$i<$max;++$i){// pour chaque construction en cours , on cherche l'id qu'on veut cancel
        if(isset($making[$i]['id']) && $making[$i]['id'] == $_GET['cancel']){//on a trouvé la ligne a cancel
            if(!empty($making[$i]['timestart'])){// le batiment a déjà été payé
            $building = new Building($making[$i]['itemid'],$planet);
                foreach($building->getPrice() AS $resId=>$toRefund){ // on rembourse
                    if($var[$resId]['isPalpable']){
                        $planet->setData($resId,$planet->getData($resId)+$toRefund);
                    }
                }
            }
            
            $req = $pdo->prepare("DELETE FROM ".table("making")." WHERE id=:id");
            $req->bindValue(":id",$making[$i]['id']);
            $req->execute();
            $ids = array();
            for($j=$i+1;$j<$max;++$j){// on continuer a chercher d'autres lignes ayant le meme item id pour recalculer les niveaux (seuleent si c'est APRES la ligne qu'on vient dejarter évidement)
                if(isset($making[$j]['itemid']) && $making[$j]['itemid'] == $making[$i]['itemid']){ // on a trouvé un autre batiment dans la liste
                    $ids[] = "id=" . $making[$j]['id'];
                }
            }
            if(!empty($ids)){// si on a trouvé au moins 1 element on update
                $pdo->query("UPDATE ".table("making")." SET number=number-1 WHERE ".implode(" OR " , $ids));
            }
            header("Location:?a=buildings");
            $planet->proceedUpdate();
            exit();
        }
    }
}
if(isset($_GET['build']) && isBuild($_GET['build'])){
    $building = new Building($_GET['build'],$planet);
    if($planet->isBuilding("buildings")){
        
        $level = $planet->getData($_GET['build'])+1;
        $making = arrayOrganize($planet->getMaking('buildings'));
        $max=count($making);
        $priority = $making[$max-1]['priority'] + 1;
        for($i=0;$i<$max;++$i){
            if($making[$i]["itemid"] == $_GET['build']){
                ++$level;
            }
        }
        
        $req = $pdo->prepare('INSERT INTO '.table("making")." (id,userid,planetid,itemid,number,timestart,timeend,priority) VALUES (:id,:userid,:planetid,:itemid,:number,:timestart,:timeend,:priority)");
        $req->bindValue(":id",keyGen(table("making")),PDO::PARAM_INT);
        $req->bindValue(':userid',$_SESSION['userid'],PDO::PARAM_INT);
        $req->bindValue(':planetid',$_SESSION['planetid'],PDO::PARAM_INT);
        $req->bindValue(':itemid',$_GET['build'],PDO::PARAM_INT);
        $req->bindValue(':number',$level,PDO::PARAM_INT);
        $req->bindValue(':timestart',0,PDO::PARAM_INT);
        $req->bindValue(':timeend',0,PDO::PARAM_INT);
        $req->bindValue(':priority',$priority,PDO::PARAM_INT);
        
        $req->execute();
        
        header("Location:?a=buildings");
    } else if($building->isBuildable()){
        $req = $pdo->prepare('INSERT INTO '.table("making")." (id,userid,planetid,itemid,number,timestart,timeend,priority) VALUES (:id,:userid,:planetid,:itemid,:number,:timestart,:timeend,:priority)");
        $req->bindValue(":id",keyGen(table("making")),PDO::PARAM_INT);
        $req->bindValue(':userid',$_SESSION['userid'],PDO::PARAM_INT);
        $req->bindValue(':planetid',$_SESSION['planetid'],PDO::PARAM_INT);
        $req->bindValue(':itemid',$_GET['build'],PDO::PARAM_INT);
        $req->bindValue(':number',$planet->getData($_GET['build'])+1,PDO::PARAM_INT);
        $req->bindValue(':timestart',$_SERVER['REQUEST_TIME'],PDO::PARAM_INT);
        $req->bindValue(':timeend',$_SERVER['REQUEST_TIME'] + $building->getBuildingTime(),PDO::PARAM_INT);
        $req->bindValue(':priority',0,PDO::PARAM_INT);
        
        $req->execute();
        
        $planet->charge($building->getPrice());
        
        header("Location:?a=buildings");
    }  
}

?>
<?php //<span id="test" onLoad="countDown('test,3600)">0</span><script>countDown("test",3600);</script> ?>
<?php // table that shows buildings under construction
if($planet->isBuilding('buildings')){
    echo '<table class="content2">';
    $making = arrayOrganize($planet->getMaking('buildings'));
    for($i=0,$max=count($making);$i<$max;++$i){
        if($i===0){
            $remainingTime = $making[$i]['timeend'] - $_SERVER['REQUEST_TIME'];
            echo '<tr><td>'.$var[$making[$i]['itemid']]['name'].' niveau '. $making[$i]['number'];
            echo '<div id="gaugeContainer"></div> <script>gauge("gaugeContainer",'.$making[$i]["timestart"].','.$making[$i]['timeend'].','.$_SERVER['REQUEST_TIME'].');</script>';
            echo '</td><td><span id="buildingCD">'.formateTime($remainingTime).'</span><script>countDown("buildingCD",'.$remainingTime.');</script><br /><a href="?a=buildings&cancel='.$making[$i]['id'].'">Annuler</a></td></tr>';
        } else {
            echo '<tr><td>'.$var[$making[$i]['itemid']]['name'].' niveau '. $making[$i]['number'];
            echo '</td><td><a href="?a=buildings&cancel='.$making[$i]['id'].'">Annuler</a></td></tr>';
        }
    }
    
    
    echo '</table>';
}
?>
<table class="content2"><?php
$tpl = 
'<tr>
    <td><img src="{{img}}" style="width:75px;height:75px;" /></td>
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

foreach($list['buildings'] AS $id){
    $building = new Building($id,$planet);
    if($building->isResearched()){
        $price = $building->getPrice();
        
        $strPrice = 'Prix : ' . displayPrice($price);
        /*foreach($list['ressources'] AS $key){
            if(!empty($price[$key])){
                $strPrice .= $var[$key]['name'] . ' : ' . $price[$key] . ' ';
            }
        }*/
        
        $strBuild = '';
        if($planet->isBuilding("buildings")){
            if($max < $config['maxAllowedQueue']){
                $strBuild = '<a href="?a=buildings&build='.$id.'" class="possible">Ajouter à la liste</a>';
            } else {
                 $strBuild = '<a class="impossible">Liste de construction pleine!</a>';
            }
        }else if(!$building->isAffordable()){
            $strBuild = '<a class="impossible">Pas assez de ressources</a>';
        } else if(!$planet->hasFreeCases()){
            $strBuild = '<a class="impossible">Planète pleine!Améliorez le terraformeur.</a>';
        } else if(!$building->isHQHighEnough()){
            $strBuild = '<a class="impossible">QG trop peu développé</a>';
        } else {
            $strBuild = '<a href="?a=buildings&build='.$id.'" class="possible">Construire</a>';
        }
        $rpl = array(
            "img"   => $_SESSION['skin'] . 'buildings/' . $id .'.png',
            "title" => getTechLink($id) . ' (niveau ' . $planet->getData($id) .')',
            "desc"  => $var[$id]['desc'],
            "price" => $strPrice,
            "time"  => formateTime($building->getBuildingTime()),
            "build" => $strBuild
        );
        echo tplReplace($tpl,$rpl);
    }
}

?> 
</table>