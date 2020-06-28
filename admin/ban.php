<?php

/**
 * @author T5UN4M1
 * @copyright 2015
 */ 
 if(!defined("ROOT_PATH")){
    header("Location:game.php");
    exit();
}

if(!empty($_GET['id'])){
    $req = $pdo->prepare("SELECT username FROM ".table("user")." WHERE id=:id");
    $req->bindValue(":id",$_GET['id'],PDO::PARAM_INT);
    $req->execute();
    $res = $req->fetch(PDO::FETCH_ASSOC);
    $username = $res['username'];
}

if(!empty($_GET['confirm'])){
    $req = $pdo->prepare("SELECT COUNT(*) FROM ".table("banned")." WHERE userid=:userid AND timeend > ".$_SERVER['REQUEST_TIME']);
    $req->bindValue(":userid",$_GET['id'],PDO::PARAM_INT);
    $req->execute();
    $res = $req->fetch(PDO::FETCH_NUM);
    if($res[0] == 0){ // le joueur n'est pas déjà banni
        $req = $pdo->prepare("UPDATE ".table("user")." SET level=-1 WHERE id=:userid");
        $req->bindValue(":userid",$_GET['id'],PDO::PARAM_INT);
        $req->execute();
        
        $req = $pdo->prepare("INSERT INTO ".table('banned')." (id,userid,timestart,timeend,reason) VALUES (:id,:userid,:timestart,:timeend,:reason)");
        $req->bindValue(":id",keyGen(table("banned")),PDO::PARAM_INT);
        $req->bindValue(":userid",$_GET['id'],PDO::PARAM_INT);
        $req->bindValue(":timestart",$_SERVER['REQUEST_TIME'],PDO::PARAM_INT);
        $req->bindValue(":timeend",$_SERVER['REQUEST_TIME'] + ($_POST['length'] * 86400),PDO::PARAM_INT);
        $req->bindValue(":reason",$_POST['reason'],PDO::PARAM_STR);
        $req->execute();
        if(!empty($_GET['goodguy'])){
            msg(0,$_GET['goodguy'],9,$_SERVER['REQUEST_TIME'],"Un joueur que vous avez signalé a été banni",'<span class="good">Un joueur que vous avez signalé a été banni.Merci de nous avoir aidé à éliminer les mauvais joueurs!</span>');
        }
        echo '<table class="content"><tr><td><span class="good">Joueur banni</span></td></tr></table>';
    }
}


?>

<table class="content">

<tr><th colspan="2">Banissement de <?php echo htmlspecialchars($username)?></th></tr>
<form action="?a=ban&confirm=1&id=<?php echo $_GET['id']?><?php if(!empty($_GET['goodguy'])){echo '&goodguy='.$_GET['goodguy'];} ?>" method="post">

<tr><td>Motif du banissement</td><td><input type="text" name="reason" /></td></tr>
<tr><td>Durée du banissement(nombre de jours)</td><td><input type="text" name="length" /></td></tr>
<tr><td colspan="2"><input type="submit" value="Bannir" /></td></tr>

</form> 
</table>