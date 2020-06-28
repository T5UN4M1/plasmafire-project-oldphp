<?php

/**
 * @author T5UN4M1
 * @copyright 2015
 */ 

$req = $pdo->prepare('SELECT * FROM '.table('rank').' WHERE userid=:userid');
$req->bindValue(':userid',$_SESSION['userid'],PDO::PARAM_INT);
$req->execute();

$rank = $req->fetch(PDO::FETCH_ASSOC);

?>

<table class="content">
    <tr>
        <th colspan="2"><?php echo $planet->getPlanetName()?> ( <?php echo $_SESSION['username']?> )</th>
    </tr>
    <tr>
        <td>Heure du serveur</td><td><?php echo date("d/m/Y H:i:s")?></td>
    </tr>

</table> 
<table class="content2">
    <tr>
        <th colspan="2">Evênements</th>
    </tr>
<?php
$location = $planet->getPlanetLocation();
$fleets = array();
$req = $pdo->prepare("SELECT * FROM ".table("fleet")."  AS fleet
    JOIN ".table("user")." AS user ON fleet.userid = user.id
    WHERE (userid=:userid) OR (enduserid=:userid AND mission<100)" // flottes parties de ce joueur ou flottes arrivant sur ce joueur
);
$req->bindValue(":userid",$planet->getUsedId(),PDO::PARAM_INT);
$req->execute();
while($line = $req->fetch(PDO::FETCH_ASSOC)){
    $fleets[] = $line;
    if($line['userid'] == $planet->getUsedId() && $line['mission'] < 100){
        $duration = $line['endtime'] - $line['starttime'];
        $line['endtime'] += $duration;
        $line['starttime'] += $duration;
        $line['mission'] += 100;
        $fleets[] = $line; 
    }
}
$fl = arraySort($fleets,'endtime');
for($i=0,$m=count($fl);$i<$m;++$i){
    echo '<tr><td><span id="'.$i.'"></span><script>countDown("'.$i.'",'.($fl[$i]['endtime'] - $_SERVER['REQUEST_TIME']).');</script></td><td>';
    $start = new Location($fl[$i]['startg'],$fl[$i]['starts'],$fl[$i]['startp']);
    $end = new Location($fl[$i]['endg'],$fl[$i]['ends'],$fl[$i]['endp']);
    
    $fleetTooltip = '';
    foreach($list['ships'] AS $id){
        if($fl[$i][$id] > 0){
            $fleetTooltip .= $var[$id]["name"] . ": " .format($fl[$i][$id]) . '<br />';
        }
    }
    $flotte = '<a title="'.$fleetTooltip.'">flotte</a>';
    $ressourcesTooltip = '';
    foreach($list["ressources"] AS $id){
        if($var[$id]['isPalpable'] && $fl[$i][$id] > 0){
            $ressourcesTooltip .= $var[$id]["name"] . ": " .displayRessource($id,$fl[$i][$id])/*$fl[$i][$id]*/ . '<br />';
        }
    }
    $mission = '<a title="'.$ressourcesTooltip.'">'.$missionList[$fl[$i]['mission']%100].'</a>';
    if($fl[$i]['userid'] == $planet->getUsedId()){ // flotte a nous
        if($fl[$i]['mission'] < 100){ // aller
            echo '<span class="myMission'.$fl[$i]['mission'].'">Votre '.$flotte.' venant de '.$start->toLink().' atteint la planète '.$end->toLink().'.Elle a pour mission : '.$mission.'</span></td></tr>';
        } else { // retour
            echo '<span class="back">Votre '.$flotte.' qui était partie à '.$end->toLink().' revient à votre planète '.$start->toLink().'.Elle avait pour mission : '.$mission.'</span></td></tr>';
        }
    } else {
        '<span class="otMission'.$fl[$i]['mission'].'">Une '.$flotte.' de '.htmlspecialchars($fl[$i]['username']).' venant de '.$start->toLink().' atteint la planète '.$end->toLink().'.Elle a pour mission : '.$mission.'</span></td></tr>';
    }
}
?>
</table>

<table class="content2">
    <tr>
        <td>
            <img src="<?php echo $_SESSION['skin'] . 'planet/'.$planet->getPlanetImg()?>" />
        </td>
        <td>
            <table>
                <tr>
                    <th colspan="3">Informations sur la planète</th>
                </tr>
                <tr>
                    <td>Taille</td>
                    <td colspan="2"><?php echo format($planet->getPlanetRadius()) . 'km de rayon ('.$planet->getPlanetUsedCases().'/'.$planet->getPlanetCases().' cases)'?></td>
                </tr>
                <tr>
                    <td>Température</td>
                    <td colspan="2"><?php echo $planet->getPlanetTemperature()?></td>
                </tr>
                <tr>
                    <td>Position</td>
                    <td colspan="2"><?php echo $planet->getPlanetLocation()->toString()?></td>
                </tr>
                <tr>
                    <th colspan="3">Classement</th>
                </tr>
                <tr>
                    <td>Général</td>
                    <td>N°<?php echo  format($rank['rank'])?></td>
                    <td><?php echo format($rank['points'])?> points</td>
                </tr>
                
                <tr>
                    <td>Batiments</td>
                    <td>N°<?php echo  format($rank['buildingrank'])?></td>
                    <td><?php echo format($rank['buildingpoints'])?> points</td>
                </tr>
                <tr>
                    <td>Vaisseaux</td>
                    <td>N°<?php echo  format($rank['fleetrank'])?></td>
                    <td><?php echo  format($rank['fleetpoints'])?> points</td>
                </tr>
                    <tr>
                    <td>Recherches</td>
                    <td>N°<?php echo  format($rank['researchrank'])?></td>
                    <td><?php echo  format($rank['researchpoints'])?> points</td>
                </tr>
                <tr>
                    <td>Défenses</td>
                    <td>N°<?php echo  format($rank['defenserank'])?></td>
                    <td><?php echo  format($rank['defensepoints'])?> points</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
