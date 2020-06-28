<?php

/**
 * @author T5UN4M1
 * @copyright 2015
 */ 


define('ROOT_PATH','../../');
define("skip", "skip");
include ROOT_PATH . 'inc/gLazy.php';
?>

<?php
// no matter the input , some galaxy data WILL be returned
if(empty($_POST['g']) || empty($_POST['s'])){
    $g = 1;
    $s = 1;
} else {
    $g = (int) floor($_POST['g']);
    $s = (int) floor($_POST['s']);
}

if($g < 1 || $g > $config['maxG'] || $s < 1 || $s > $config['maxS']){
    $g = 1;
    $s = 1;
}

$req = $pdo->prepare("SELECT userid,planet.name,g,s,p,planet.img,teamid,username,onlinetime,level,tag,`0` as res0,`1` as res1,`2` as res2 FROM
    ".table("planet")." AS planet JOIN
    
    ".table("user")." AS user 
    ON planet.userid = user.id    LEFT JOIN
    ".table("team")." AS team
    ON user.teamid = team.id      LEFT JOIN 
    ".table("wreck")." AS wreck
    ON planet.g = wreck.posg AND planet.s = wreck.poss AND planet.p = wreck.posp
    WHERE planet.g=:g AND planet.s=:s
    ORDER BY planet.p
");
$req->bindValue(":g",$g,PDO::PARAM_INT);
$req->bindValue(":s",$s,PDO::PARAM_INT);
$req->execute();

$data = array();
for($i=0;$i<=$config['maxP'];++$i){
    $data[$i] = array();
    
}
for(;$line = $req->fetch(PDO::FETCH_ASSOC);){
    $data[$line['p']] = $line;
    //$data[$line['p']]['res'] = array($data[0],$data[1],$data[2]);
}
$req = $pdo->prepare("SELECT posp,`0` as res0,`1` as res1,`2` as res2 FROM
    ".table("wreck")." AS wreck LEFT JOIN
    ".table("planet")." AS planet 
    ON planet.g = wreck.posg AND planet.s = wreck.poss AND planet.p = wreck.posp
    WHERE wreck.posg=:g AND wreck.poss=:s AND planet.g IS NULL
    ORDER BY wreck.posp
");
$req->bindValue(":g",$g,PDO::PARAM_INT);
$req->bindValue(":s",$s,PDO::PARAM_INT);
$req->execute();
for(;$line = $req->fetch(PDO::FETCH_ASSOC);){
    $data[$line['posp']] = $line;
    //$data[$line['posp']]['res'] = array($data[0],$data[1],$data[2]);
}


echo json_encode($data);