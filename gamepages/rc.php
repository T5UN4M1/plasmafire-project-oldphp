<?php

/**
 * @author T5UN4M1
 * @copyright 2015
 */ 




if(!empty($_GET['key']) && !empty($_GET['id'])){
    $req = $pdo->prepare("SELECT * FROM ".table("battlerapport")." WHERE `key`=:key AND id=:id");
    $req->bindValue(":key",$_GET['key'],PDO::PARAM_STR);
    $req->bindValue(":id",$_GET['id'],PDO::PARAM_INT);
    $req->execute();
    $res = $req->fetch(PDO::FETCH_ASSOC);
    if(!empty($res['rapport'])){
        echo '<table><tr><td style="text-align:center;">'.$res['rapport'].'</td></tr></table>';
    }
}

?> 