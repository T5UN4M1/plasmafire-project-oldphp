<?php

/**
 * @author T5UN4M1
 * @copyright 2015
 */ 
    
if(!defined("ROOT_PATH")){
    header("Location:game.php");
    exit();
}
    $req = $pdo->prepare("SELECT * FROM ".table("user")." ORDER BY ip");
    $req->execute();
    ?><table class="content"><tr><th>ID</th><th>Pseudo</th><th>IP</th><th>Action</th></tr><?php
    while($res=$req->fetch(PDO::FETCH_ASSOC)){
        echo "<tr><td>".$res['id']."</td><td>".htmlspecialchars($res['username']).'</td><td>'.$res['ip'] .'</td><td><a href="?a=ban&id='.$res['id'].'">Bannir</a></td></tr>';
    }

?> </table>