<?php

/**
 * @author T5UN4M1
 * @copyright 2015
 */

if(isset($_GET['a']) && $_GET['a'] == 1){
	$fields = array('username','password');
	$error = array();
	for($i=0;$i<count($fields);++$i){
		if(empty($_POST[$fields[$i]])){
			$error[] = "Un champ est vide";
		}
	}
    if(empty($error)){
         $user = new Player($_POST['username'],$_POST['password']);
         $errorCode = $user->tryConnect();
    }
   
}

?>
<table style="position:absolute;left :400px;top: 400px;width:600px;height:300px;">
<tr><td>
<?php
    if(!empty($error)){
        echo '<span class="bad">'.$error[0].'</span>';
    } else if(isset($errorCode)){
        if(is_array($errorCode)){ // if we are banned then an array with keys timeent & reason is returned
            echo '<span class="bad">Vous etes banni jusque : '.date("d/m/Y H:i:s",$errorCode['timeend']) .' pour le motif suivant : '.$errorCode['reason'].'</span>';
        } else {
            if($errorCode == 2){
                echo '<span class="bad">Ce pseudo n\'existe pas.</span>';
            } else if($errorCode == 3){
                echo '<span class="bad">Votre mot de passe est incorrect.</span>';
            }
        }
    }
?>
<form method="POST" action="index.php?p=connexion&a=1">
<input type="text" name="username" value="pseudo" /><br />
<input type="password" name="password" value="password" /><br />
<input type="submit" name="Ok" value="Ok" />
</form>
<a href="?p=accueil">Retour</a>
</td></tr>
</table>
