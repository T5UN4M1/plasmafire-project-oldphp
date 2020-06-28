<?php

/**
 * @author T5UN4M1
 * @copyright 2015
 */ 



?> 

<table class="content2">
<tr><th>Date</th><th>Envoyé par</th><th>Sujet</th><th>Action (<a href="#" onclick="confirmRedirect('?a=readmsg&suba=deleteall&id=0','Etes vous sur de vouloir supprimer tous vos messages?Cette action est irréversible.');">Supprimer tous les messages</a>)</a></th></tr>
<?php
if($planet->getPlayer()->hasNewMsg()){
    $req = $pdo->prepare("UPDATE ".table("user")." set newmsg=0 WHERE id=:userid");
    $req->bindValue(":userid",$planet->getUsedId(),PDO::PARAM_INT);
    $req->execute();
}
if(isset($_GET['suba']) && isset($_GET['id'])){
    if($_GET['suba'] == "report"){
        $req = $pdo->prepare("SELECT id,submitid,type,time,subject,content FROM ".table('message')." WHERE receptid=:userid AND id=:id");
        $req->bindValue(":id",$_GET['id'],PDO::PARAM_INT);
        $req->bindValue(":userid",$planet->getUsedId(),PDO::PARAM_INT);
        $req->execute();
        $res = $req->fetch(PDO::FETCH_ASSOC);
        if(!empty($res['content'])){
            $req = $pdo->prepare("INSERT INTO ".table("report")." (id,idreporter,idreported,content,time) VALUES (:id,:idreporter,:idreported,:content,:time)");
            $req->bindValue(":id",keyGen(table("report")),PDO::PARAM_INT);
            $req->bindValue(":idreporter",$planet->getUsedId(),PDO::PARAM_INT);
            $req->bindValue(":idreported",$res['submitid'],PDO::PARAM_INT);
            $req->bindValue(":content",$res['content']);
            $req->bindValue(":time",$_SERVER['REQUEST_TIME'],PDO::PARAM_INT);
            $req->execute();
            echo '<tr><td colspan="4"><span class="good">Message signalé.Vous recevrez un message d\'un administrateur/modérateur pour vous tenir au courrant de la prise de décision.Veuillez ne pas signaler plusieurs fois le même message.</span></th></tr>';
        }
    } else if($_GET['suba'] == "delete"){
        $req = $pdo->prepare("DELETE FROM ".table("message")." WHERE id=:id AND receptid=:userid");
        $req->bindValue(':id',$_GET['id'],PDO::PARAM_INT);
        $req->bindValue(":userid",$planet->getUsedId(),PDO::PARAM_INT);
        $req->execute();
    } else if($_GET['suba'] == "deleteall"){
        $req = $pdo->prepare("DELETE FROM ".table("message")." WHERE receptid=:userid");
        $req->bindValue(":userid",$planet->getUsedId(),PDO::PARAM_INT);
        $req->execute();
    }
}

$req = $pdo->prepare("SELECT message.id,submitid,type,time,subject,content,username FROM ".table('message')." AS message LEFT JOIN ".table("user")." AS user ON message.submitid = user.id  WHERE receptid=:userid ORDER BY time DESC");
$req->bindValue(":userid",$planet->getUsedId(),PDO::PARAM_INT);
$req->execute();
while($msg = $req->fetch(PDO::FETCH_ASSOC)){
    $submitter = ($msg['type'] != 1 && $msg['type'] != 6) ? "Tour de contrôle" : htmlspecialchars($msg['username']) . msgIcon($msg['submitid']);
    $actions = '<a href="?a=readmsg&suba=delete&id='.$msg['id'].'">Supprimer</a>';
    if($msg['type'] == 1 || $msg['type'] == 6){
        $actions .= '<br /><a href="#" onclick="confirmRedirect(\'?a=readmsg&suba=report&id='.$msg['id'].'\',\'Etes vous sur de vouloir signaler ce message? Abuser de cette fonction pour des messages ne comportant aucun élément nuisible pourrait entrainer des sanctions.Ne signalez les messages que si ils contiennent des éléments que vous jugez comme innapropriés.\');">Signaler comme nuisible</a>';
        
    }
    echo "<tr><td>".date("d/m/Y H:i:s",$msg['time'])."</td><td>".$submitter."</td><td>".htmlspecialchars($msg['subject']).'</td><td>'.$actions.'</td>';
    echo '<tr><td colspan="4">';
    if($msg['type'] == 1 || $msg['type'] == 6){
        echo'<pre>'.htmlspecialchars($msg['content']).'</pre>';
    } else {
        echo $msg['content'];
    }
    echo '</td></tr>';
}


?>

</table>