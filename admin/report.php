<?php

/**
 * @author T5UN4M1
 * @copyright 2015
 */ 


if(!defined("ROOT_PATH")){
    header("Location:game.php");
    exit();
}
$req = $pdo->prepare("SELECT * FROM ".table("report") . " ORDER BY time");
$req->execute();
echo '<table class="content2">';
while($res = $req->fetch(PDO::FETCH_ASSOC)){
    echo '<tr><td>'.htmlspecialchars($res['content']).'</td>
    <td><a href="?a=ban&id='.$res['idreported'].'&goodguy='.$res['idreporter'].'">Bannir ce joueur</a><br />
    </td></tr>';
}
?> </table>