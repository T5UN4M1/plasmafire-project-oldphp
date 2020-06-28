<?php

/**
 * @author T5UN4M1
 * @copyright 2015
 */ 
define('ROOT_PATH','./');
include './inc/gLazy.php';


if(!empty($_GET['switch']) && !empty($_GET['newPlanet'])){
    $coord = explode(':',$_GET['newPlanet']);
    //var_dump($_GET);
    if(!empty($coord[2])){
        $req = $pdo->prepare("SELECT id FROM ".table("planet")." WHERE g=:g AND s=:s AND p=:p AND userid=:userid");
        $req->bindValue(":g",$coord[0],PDO::PARAM_INT);
        $req->bindValue(":s",$coord[1],PDO::PARAM_INT);
        $req->bindValue(":p",$coord[2],PDO::PARAM_INT);
        $req->bindValue(":userid",$planet->getUserId(),PDO::PARAM_INT);
        $req->execute();
        $res = $req->fetch(PDO::FETCH_ASSOC);
        if(!empty($res['id'])){
            $_SESSION['planetid'] = $res['id'];
            header("Location:?a=".$_GET['a']);
        }
    }
}

$pages = array(
    "home",
    "buildings",
    "laboratory",
    "shipyard",
    "defensecenter",
    
    "fleet",
    
    "techtree",
    "tech",
    
    "galaxy",
    
    "sendmsg",
    "readmsg",
    "rc",
    "logout",
    "ranking"
)

?> 
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>Plasmafire</title>
        <link rel="stylesheet" href="./script/jquery.qtip.min.css">
        <link rel="stylesheet" type="text/css" href="<?php echo $_SESSION['skin']?>skin.css" />
        
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script> <!-- jquery -->
        <script src="./script/util.js"></script>
        <script src="./script/countDown.js"></script>
        <script src="./script/gauge.js"></script>
        <script src="./script/shipyard.js"></script>
        <script src="./script/jquery.qtip.min.js"></script>
    </head>

    <body>
        <div id="page">
            <div id="leftMenu">
                <table class="leftMenu">
                    <tr><td><img style="width:150px;heigth:150px;" src="<?php echo $_SESSION['skin']?>officer/<?php echo ($planet->getUsedId()%21)?>.png" /></td></tr>
                    
                    <?php /**************** PLANET SWITCHER ********************* var_dump($_SESSION , $planet);*/ ?>
                    <tr><td><form id="planetSwitcher" method="GET" action="game.php">
                    
                    <input type="hidden" name="a" value="accueil" />
                    <input type="hidden" name="switch" value="1" />
                    <select name="newPlanet" onchange="getId('planetSwitcher').submit();">
                    <?php
                        $req = $pdo->prepare("SELECT * FROM ".table("planet") ." WHERE userid=:userid ORDER BY g,s,p");
                        $req->bindValue(":userid",$planet->getUserId(),PDO::PARAM_INT);
                        $req->execute();
                        while($pl = $req->fetch(PDO::FETCH_ASSOC)){
                            if($pl['id'] == $planet->getId()){
                                echo '<option selected="selected" value="'.$pl['g'].':'.$pl['s'].':'.$pl['p'].'">'.htmlspecialchars($pl['name']).'('.$pl['g'].':'.$pl['s'].':'.$pl['p'].')</option>';
                            } else {
                                 echo '<option value="'.$pl['g'].':'.$pl['s'].':'.$pl['p'].'">'.htmlspecialchars($pl['name']).'('.$pl['g'].':'.$pl['s'].':'.$pl['p'].')</option>';
                            }
                            
                        }
                    ?>
                    </select>
                    </form></td></tr>
                    <tr><td><a class="menuLink" href="?a=home">Accueil</a></td></tr>
                    <tr><td><a class="menuLink" href="?a=buildings">Batiments</a></td></tr>
                    <tr><td><a class="menuLink" href="?a=shipyard">Chantier spatial</a></td></tr>
                    <tr><td><a class="menuLink" href="?a=laboratory">Laboratoire</a></td></tr>
                    <tr><td><a class="menuLink" href="?a=defensecenter">Centre de défenses</a></td></tr>
                    <!--<tr><td><a class="menuLink" href="?a=ressourcesview">Ressources</a></td></tr>
                    <tr><td><a class="menuLink" href="?a=empireview">Vue empire</a></td></tr>-->
                    <tr><td><a class="menuLink" href="?a=techtree">Technologies</a></td></tr>
                    <tr><td><a class="menuLink" href="?a=fleet">Flotte</a></td></tr>
                    <tr><td><a class="menuLink" href="?a=galaxy">Galaxie</a></td></tr>
                    <tr><td><a class="menuLink" href="?a=ranking">Classement</a></td></tr>
                    <tr><td><a class="menuLink" href="?a=readmsg"><?php echo (($planet->getPlayer()->hasNewMsg()) ? '<span class="newmsg">Messages('.$planet->getPlayer()->getNewMsg().')</span>' : 'Messages')?></a></td></tr>
                    <tr><td><a class="menuLink" href="?a=logout">Logout</a></td></tr>
                    <?php if($_SESSION['level'] > 0) { echo '<tr><td><a class="menuLink" href="admin.php">Admin</a></td></tr>';} ?>
                </table>
            </div>
            
            <div id="ressourcePanel">
                <table class="ressourcePanel"><tr>
                    <?php  
                        $planetData = $planet->getData();
                        foreach($list['ressources'] AS $key){
                            if($key < 3){
                                //echo '<td>' . $var[$key]['name'] . ' : ' . format($planet->getData($key)) . '(+ '.format($planetData['production'][$key]).')</td>';
                            }
                            echo '<td><img src="'.$_SESSION['skin'].'ressources/res'.$key.'.gif" />';
                            if($var[$key]['isPalpable']){
                                echo /*$var[$key]['name'] . ' : ' .*/ displayRessource($key,$planet->getData($key)) . '(+ '.displayRessource($key,$planetData['production'][$key]).')';
                            } else {
                                $balance = $planetData[$key] - $planetData['production'][$key];
                                if($balance >= 0){ // assez d'energie.
                                     echo displayRessource($key,$planetData[$key]) . "/" . displayRessource($key,$planetData['production'][$key]) . "(+".color($balance) . ")";
                                }
                               
                            }
                            
                            echo'</td>';
                        }
                    ?>
                </tr></table>
            </div>
            
            <div id="main">
                    <?php
                        include PAGES_PATH . (isset($_GET['a']) && (in_array($_GET['a'],$pages)) ? $_GET['a'] : $pages[0]) . PHPEX;
                        $planet->proceedUpdate();
                    ?>
            </div>
        </div>
        <script>
            tip();
        </script>
    </body>
</html>
