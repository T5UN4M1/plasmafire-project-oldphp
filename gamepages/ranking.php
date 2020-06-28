<?php

/**
 * @author T5UN4M1
 * @copyright 2015
 */ 



$rankingBy = (isset($_POST['ranking']) && $_POST['ranking'] >= 0 && $_POST['ranking'] < 5) ? $_POST['ranking'] : 0;

$rankings = array("","building","fleet","research","defense");
$rankingsFR = array("Points","Points de batiments","Points de flotte","Points de technologie","Points de défenses");
?>
<table class="content"><form method="POST" action="?a=ranking"><tr><td>Classement par :<select name="ranking" onchange="this.form.submit();"><?php 
foreach($rankings AS $i=>$ranking){
    if($rankingBy == $i){
        echo '<option value="'.$i.'" selected="selected">'.$rankingsFR[$i].'</option>';
    } else {
        echo '<option value="'.$i.'">'.$rankingsFR[$i].'</option>';
    }
}
?></select></td></tr></form></table> 

<table class="content2">
<tr><th>N°</th><th>Pseudo</th><th>Message</th><th>Points</th></tr>
<?php 
$r = $rankings[$rankingBy];
$req = $pdo->prepare("SELECT userid,username,".$r."points,".$r."rank,change".$r."rank FROM ".table("rank")." AS rank JOIN ".table("user")." AS user ON rank.userid=user.id ORDER BY ".$rankings[$rankingBy]."rank");
$req->execute();
for(;$line=$req->fetch(PDO::FETCH_ASSOC);){
    echo "<tr><td>".displayRank($line[$r."rank"],$line["change".$r."rank"])."</td><td>".htmlspecialchars($line['username'])."</td><td>".  msgIcon($line['userid']) . "</td><td>".format($line[$r."points"]).'</td></tr>' ; 
}
?>


</table>