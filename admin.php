<?php

/**
 * @author T5UN4M1
 * @copyright 2015
 */ 


define('ROOT_PATH','./');
include './inc/gLazy.php';

$req = $pdo->prepare("SELECT level FROM ".table("user")." WHERE id=:userid");
$req->bindValue(":userid",$planet->getUserId(),PDO::PARAM_INT);
$req->execute();
$res = $req->fetch(PDO::FETCH_ASSOC);
if($res['level'] < 1){
    header("Location:game.php");exit();
}






$pages = array(
    "playerlist",
    "report",
    "ban",
    "update",
    "randomplayer"
)

?> 
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>Plasmafire</title>
        <link rel="stylesheet" type="text/css" href="<?php echo $_SESSION['skin']?>skin.css" />
        
    </head>

    <body>
        <div id="page">
            <div id="leftMenu">
                <table class="leftMenu">
                    <tr><td><a class="menuLink" href="?a=playerlist">Liste des joueurs</a></td></tr>
                    <tr><td><a class="menuLink" href="?a=report">Messages signalés</a></td></tr>
                    <tr><td><a class="menuLink" href="?a=update">Mise a jour des points</a></td></tr>
                    <tr><td><a class="menuLink" href="?a=randomplayer">Creer un joueur aléatoire</a></td></tr>
                    <tr><td><a class="menuLink" href="game.php?a=accueil">Jeu</a></td></tr>
                </table>
            </div>
            
            <div id="main">
                    <?php
                        define("ADMIN_PATH",ROOT_PATH . 'admin/');
                        include ADMIN_PATH . (isset($_GET['a']) && (in_array($_GET['a'],$pages)) ? $_GET['a'] : $pages[0]) . PHPEX;
                        $planet->proceedUpdate();
                    ?>
            </div>
        </div>
        <script>
            tip();
        </script>
    </body>
</html>




