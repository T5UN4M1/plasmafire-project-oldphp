<?php

/**
 * @author T5UN4M1
 * @copyright 2015
 */
  
 // Player = player data , no game data
class Player {
    
    private $id;
    private $username;
    private $password;
    private $mail;
    private $skin;
    
    private $msg;
    
    private $tech;
    
    function Player(){
        $data = func_get_args();
        
        switch(func_num_args()){
            case 1://from id
                $this->id        = $data[0];
                break;
            case 2://connexion
                $this->username  = $data[0];
                $this->password  = $data[1];
                break;
            case 3://inscription
                $this->username  = $data[0];
                $this->password  = $data[1];
                $this->mail      = $data[2];
                break;
        }
        

    }
	function checkAll(){
        $code = array();
		$code[] = $this->checkUsername();
		$code[] = $this->checkPassword();
		$code[] = $this->checkMail();
                
        /*==========code==========*\
        *0->username -> 0 déjà pris 2 ne respecte pas les regles
        *1->password -> 0 trop court
        *2->mail -> 0 -> déjà pris
        *{1,1,1} = GOOD
        *0 = shit happened
        \*========================*/
        return $code;
        
	}
    function hashPassword(){
        return sha1($this->id . 'somuchsaltintheocean' . $this->password);
    }
    function checkUsername(){
        global $pdo;
        
        if(preg_match('/[^a-z_\-0-9]/i', $this->username)){
            return 2;
        } 
        
        $req = $pdo->prepare("SELECT COUNT(*) AS result FROM ".table('user')." WHERE username=:username");
        $req->bindValue(':username',$this->username);
        $req->execute();
        
        $res=$req->fetch(PDO::FETCH_ASSOC);
        
        if($res['result'] != 0){
            return 0;
        }
        return 1;
       
    }
	function checkPassword(){
	   if(strlen($this->password) < 5){
	       return 0;
	   }
        return 1;
	}
	function checkMail(){
        global $pdo;
        
        $req = $pdo->prepare("SELECT COUNT(*) AS result FROM ".table('user')." WHERE mail=:mail");
        
        $req->bindValue('mail',$this->mail);
        $req->execute();
        
        $res=$req->fetch(PDO::FETCH_ASSOC);
        
        if($res['result'] != 0){
            return 0;
        }
        return 1;
	}
    function create(){
        global $pdo,$config;
        $code = $this->checkAll();
        if($code[0] === 1 && $code[1] === 1 && $code[2] === 1){
            $this->id = keyGen(table('user'));
            $planet = new Planet();
            $planet->createNewPlanet($this->id,"Planete mere",1,Planet::seekSpotForNewPlayer(),mt_rand(1,45),$config['initialPlanet']);
            
            $req = $pdo->prepare("INSERT INTO ".table('user')." (id,username,mail,password,ip,browser) VALUES (:id,:username,:mail,:password,:ip,:browser)");
            
            $req->bindValue('id',$this->id,PDO::PARAM_INT);
            $req->bindValue('username', $this->username);
            $req->bindValue('mail', $this->mail);
            $req->bindValue('password', $this->hashPassword());
            $req->bindValue('ip', $_SERVER['REMOTE_ADDR']);
            $req->bindValue('browser', $_SERVER['HTTP_USER_AGENT']);
            
            $req->execute();
            
            $req = $pdo->prepare("INSERT INTO ".table('tech')." (userid) VALUES (:userid)");
            $req->bindValue(':userid',$this->id,PDO::PARAM_INT);
            $req->execute();
            
            $mail = "Bonjour et bienvenue sur Plasmafire,\n
            Vous recevez ce message car vous vous etes inscrit sur plasmafire.
            Voici vos informations de connexion :
            Utilisateur : ". htmlspecialchars($this->username)."\n
            Mot de passe : ". htmlspecialchars($this->password)."\n
            
            Nous vous remercions pour votre inscription et vous souhaitons bonne chance dans cet univers impitoyable!
            ";
            error_reporting(0);
            $return = mail($this->mail,"Inscription à PlasmaFire",$mail);
            error_reporting(E_ALL);
            return $return;
        } else {
            return $code;
        }
    }
    function tryConnect(){
        /* code :
            1 > connected
            2 > user not found
            3 > bad password
        */
        global $pdo;
        
        if(!checkIfExist(table('user'),array('username' => $this->username))){
            return 2;
        }
        $req = $pdo->prepare('SELECT id,password,skin,level FROM '.table('user').' WHERE username=:username');
        $req->bindValue('username',$this->username);
        $req->execute();
        
        $res = $req->fetch(PDO::FETCH_ASSOC);
        // id is needed to hash password!
        $this->id = $res['id'];
        $level = $res['level'];
        if($res['password'] != $this->hashPassword()){
            return 3;
        } 
        $this->skin = $res['skin'];
        
        if($level < 0){ // potentially banned
            $req = $pdo->prepare("SELECT timeend,reason FROM ".table("banned")." WHERE userid=:userid");
            $req->bindValue(":userid",$this->id,PDO::PARAM_INT);
            $req->execute();
            $res = $req->fetch(PDO::FETCH_ASSOC);
            if(empty($res) || $res["timeend"] < $_SERVER["REQUEST_TIME"]){ // le bannissement a expiré
                $level = 0;
                $req = $pdo->prepare("UPDATE ".table("user")." SET level=0 WHERE id=:userid");
                $req->bindValue(":userid",$this->id,PDO::PARAM_INT);
                $req->execute();
            } else {
                return $res;
            }
        }
                    
                    
        //everything is fine , get planet data & connect
        
        
   
        $req = $pdo->prepare('SELECT id FROM '.table("planet").' WHERE userid=:userid AND ismain=:ismain');
        
        $req->bindValue(':userid',$this->id,PDO::PARAM_INT);
        $req->bindValue(':ismain',1,PDO::PARAM_INT);
        
        $req->execute();
        
        $res = $req->fetch(PDO::FETCH_ASSOC);
        
        

        // if we made it to here then wecan connect the player
        $_SESSION = array(
            "userid" => $this->id,
            "username" => $this->username,
            "skin" => $this->skin,
            "planetid" => $res['id'],
            "level" => $level
        );
        header("Location:game.php?a=accueil");
        exit();
    }
    
    function getData($mode = 0){
        global $pdo,$list;
        switch($mode){
            case 0: // get TECHS
                $select = implode('`,`',$list['researches']);
                $req = $pdo->prepare('SELECT `'.$select.'` FROM '. table('tech').' WHERE userid=:userid');
                $req->bindValue(':userid',$this->id,PDO::PARAM_INT);
                $req->execute();
                $this->tech = $req->fetch(PDO::FETCH_ASSOC);
                $req = $pdo->prepare('SELECT newmsg FROM '.table("user").' WHERE id=:userid');
                $req->bindValue(':userid',$this->id,PDO::PARAM_INT);
                $req->execute();
                $res = $req->fetch(PDO::FETCH_ASSOC);
                $this->msg = $res['newmsg'];
                break;
        }
    }
    function getNewMsg(){
        return $this->msg;
    }
    function hasNewMsg(){
        return $this->msg > 0;
    }
    function &getTech($i=-1){
        if($i===-1){
            return $this->tech;
        } else if(isset($this->tech[$i])){
            return $this->tech[$i];
        } else {
            error(__FILE__,__LINE__,"Requested TECH id not found:" . $i);
        }
    }
    function levelUpTech($key){
        global $pdo;
        if(isTech($key)){
            ++$this->tech[$key];
            $req = $pdo->prepare("UPDATE ".table("tech")." SET `".$key."`=`".$key."`+1 WHERE userid=:userid");
            $req->bindValue(":userid",$this->id,PDO::PARAM_INT);
            $req->execute();
        } else {
            error(__FILE__,__LINE__,"Error , tried to level tech with id(".$key.") but failed, id not found.");
        }
    }
    function addTech($key,$value){
        global $pdo;
        if(isTech($key)){
            $this->tech[$key] = $value;
            $req = $pdo->prepare("UPDATE ".table("tech")." SET `".$key."`=`".$key."`+".$value." WHERE userid=:userid");
            $req->bindValue(":userid",$this->id,PDO::PARAM_INT);
            $req->execute();
        } else {
            error(__FILE__,__LINE__,"Error , tried to level tech with id(".$key.") but failed, id not found.");
        }
    }
    function getId(){
        return $this->id;
    }
}




?>