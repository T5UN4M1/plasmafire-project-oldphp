<?php

/**
 * @author T5UN4M1
 * @copyright 2015
 */ 



?>
<table class="content">
<?php 
if(!isset($_GET['id'])){
    header("Location:?");
    exit();
}
if(isset($_POST['message']) && isset($_POST['subject'])){ // on essaye d'envoyer un message'
    if(msg($planet->getUsedId(),$_GET['id'],1,$_SERVER['REQUEST_TIME'],$_POST['subject'],$_POST['message'])){
        echo '<tr><td class="good">Votre message a été envoyé avec succès</td></tr>';
    } else {
        echo '<tr><td class="bad">Une erreur s\'est produite lors de l\'envoi de votre message</td></tr>';
    }
}
$req = $pdo->prepare("SELECT username FROM ".table("user")." WHERE id=:id");
$req->bindValue(":id",$_GET['id'],PDO::PARAM_INT);
$req->execute();
$res = $req->fetch(PDO::FETCH_ASSOC);
if(empty($res['username'])){
    header("Location:?");
    exit();
}?>
<form method="POST" id="formMsg" action="?a=sendmsg&id=<?php echo $_GET['id']?>">

<tr><td><input type="text" name="subject" value="<?php echo "Message pour ". htmlspecialchars($res['username'])?>" size="75" /></td></tr>
<tr><td><textarea rows="20" cols="75" name="message"></textarea></td></tr>
<tr><td><input type="button" onclick="getId('formMsg').submit();" value="Envoyer" /></td></tr>

</form>
</table>