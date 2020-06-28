<?php

/**
 * @author T5UN4M1
 * @copyright 2015
 */
if(isset($_GET['a']) && $_GET['a'] == 1){
    
	$fields = array('username','password0','password1','mailAdress');
	$error = array();
	for($i=0;$i<count($fields);++$i){
		if(empty($_POST[$fields[$i]])){
			$error[] = "Un champ est vide";
		}
	}
	if($_POST['password0'] != $_POST['password1']){
		$error[] = "Les mots de passe sont différents";
	}
    // TODO error management when using create()
	if(empty($error)){
		$user = new Player($_POST['username'],$_POST['password0'],$_POST['mailAdress']);
        $return = $user->create();
	}
}



?>
<table style="position:absolute;left :400px;top: 400px;width:600px;height:300px;">
<tr><td>
<?php 
if(!empty($error[0])){
    echo '<span class="bad">'.$error[0].'</span>';
} else if(isset($return) && is_array($return)){
    if(isset($return[0])){
        if($return[0] == 0){
            echo '<span class="bad">Pseudo déjà pris!</span>';
        } else if($return[0] == 2){
            echo '<span class="bad">Votre pseudo ne respecte pas les regles, veuillez n\'utiliser que des caracteres alphanumériques.</span>';
        }
    }
    if(isset($return[1]) && $return[1] == 0){
        echo '<span class="bad">Votre mot de passe doit faire au moins 4 caractères.</span>';
    }
    if(isset($return[2]) && $return[2] == 0){
        echo '<span class="bad">Votre adresse email est déjà utilisée pour un autre compte.</span>'; 
    }
} else if(isset($return) && is_bool($return) && $return){
    echo '<span class="good">Votre inscription est terminée, un email vous a été envoyé avec vos informations</span>';
} else if(isset($return) && is_bool($return)){
    echo '<span class="presque">Votre inscription est terminée, nous n\'avons pas pu vous envoyer de mail.</span>';
}

?>
<form method="POST" action="index.php?p=inscription&a=1">
<input type="text" name="username" value="pseudo" /> <br />
<input type="password" name="password0" value="password" /><br />
<input type="password" name="password1" value="password" /><br />
<input type="text" name="mailAdress" value="adresse@domain.com" /><br />
<input type="submit" name="Ok" value="Ok" /><br />
<a href="?p=accueil">Retour</a>
</form>
</td></tr></table>