<?php

/**
 * @author T5UN4M1
 * @copyright 2015
 */
 
 
 
 function keyGen($table){
    $i=0;
	 do{
		 $id = mt_rand(1000,2000000000); // 1-1000 are reserved
         ++$i;
         if($i===10000){
            error(__FILE__,__LINE__,"ID for table " . $table . " could NOT be created after 10000 attempts");
         }
	 }while(checkIfExist($table,array("id"=>$id)));
	 return $id;
 }
 function checkIfExist($table,$check){
    global $pdo;
    
    if(is_array($check)){
    	 $whereElements = array();
    	foreach($check as $key=>$value){
    		$whereElements[] = $key . "= :" . $key;
    	}
        $checkStr =  implode(' AND ',$whereElements);
    }
    
    $req = $pdo->prepare("SELECT COUNT(*) AS cnt FROM ".$table." WHERE ". $checkStr);
	$req->execute($check);
    
	$res = $req->fetch();
    
	return $res['cnt'] != 0;
 }
 
 function error($file,$line,$msg){
    exit("FILE -> " . $file . " .::. LINE -> " . $line . " .::. ERROR -> " .$msg);
 }
 
 function format($int){
    return number_format($int,0,',','.');
 }
 function color($int){
    if($int > 0){
        return '<span class="good">'.format($int).'</span>';
    }else if($int < 0){
        return '<span class="bad">'.format($int).'</span>';
    }else{
        return '<span class="average">'.format($int).'</span>';
    }
 }
 function tplReplace($tpl,&$data){
    foreach($data AS $eek=>$replace){
        $tpl = str_replace('{{'.$eek.'}}',$replace,$tpl);
    }
    return $tpl;
 }
 function formateTime($s){
    return 
    ($s>=86400) ? floor($s/86400) . 'j ' . (floor($s/3600)%24) . 'h ' . (floor($s/60)%60) . 'm ' . ($s%60) . 's' : (
    ($s>=3600)  ?                          (floor($s/3600)%24) . 'h ' . (floor($s/60)%60) . 'm ' . ($s%60) . 's' : (
    ($s>=60)    ?                                                       (floor($s/60)%60) . 'm ' . ($s%60) . 's' :
                                                                                                   ($s%60) . 's'
    ));
 }
 function getTechLink($id){
    global $var;
    return '<a href="?a=tech&id='.$id.'">'.$var[$id]['name'].'</a>';
 }
 function isBuild($id){
    global $list;
    return in_array($id,$list['buildings']);
 }
 function isTech($id){
    global $list;
    return in_array($id,$list['researches']);
 }
 function isShip($id){
    global $list;
    return in_array($id,$list['ships']);
 }
 function isDef($id){
    global $list;
    return in_array($id,$list['defenses']);
 }
 function getItemType($id){
    return 
    (isBuild($id))? "buildings" :(
    (isShip($id)) ? "ships":(
    (isTech($id)) ? "researches":(
    (isDef($id))  ? "defenses"
                  : "ressources" 
    )));
 }
 function arrayOrganize($array){
    $new = array();
    foreach($array AS $val){
        if(!empty($val)){
            $new[] = $val;
        }
    }
    return $new;
 }
 function displayPrice($price){
    global $var;
    $str = array();
    foreach($price AS $id=>$cost){
        $str[] = $var[$id]['name'] . ' : ' . displayRessource($id,$cost); 
    }
    return implode(', ',$str);
 }
 function displayRessource($id,$amount){
    return '<span class=\'res'.$id.'\'>'.format($amount).'</span>';
 }
 function convertBuildingTimeToUnitsPerSecond($buildingTime){ // pour convertir un nombre de sec inférieur a 0 en qté par seconde . ex : 0.33333 de building time => 3 unités par sec
    return round(1/$buildingTime);
 }
 function msgIcon($id){
    return '<a href="?a=sendmsg&id='.$id.'"><img src="'.$_SESSION['skin'].'pic/m.gif" /></a>';
 }
 function arraySort($array,$key){
    $out = array();
    if(empty($array)) return array();
    for($i=0,$max=count($array);$i<$max;++$i){
        $minimum = 99999999999999999;
        $k = -1;
        for($j=0;$j<$max;++$j){// find minimum
            if(!empty($array[$j][$key]) && $array[$j][$key] < $minimum){
                $minimum = $array[$j][$key];
                $k = $j;
            }
        }
        $out[] = $array[$k];
        unset($array[$k]);
    }
    return $out;
 }
 function msg($submitid,$receptid,$type,$time,$subject,$content){
    global $pdo;
    $req = $pdo->prepare("SELECT COUNT(*) FROM ".table("user")." WHERE id=:id");
    $req->bindValue(":id",$receptid,PDO::PARAM_INT);
    $req->execute();
    $res = $req->fetch(PDO::FETCH_NUM);
    if($res[0] == 1){
        $req = $pdo->prepare("INSERT INTO ".table("message")." (id,submitid,receptid,type,time,subject,content) VALUES (:id,:submitid,:receptid,:type,:time,:subject,:content)");
        $req->bindValue(":id",keyGen(table("message")),PDO::PARAM_INT);
        $req->bindValue(":submitid",$submitid,PDO::PARAM_INT);
        $req->bindValue(":receptid",$receptid,PDO::PARAM_INT);
        $req->bindValue(":type",$type,PDO::PARAM_INT);
        $req->bindValue(":time",$time,PDO::PARAM_INT);
        $req->bindValue(":subject",$subject,PDO::PARAM_INT);
        $req->bindValue(":content",$content,PDO::PARAM_INT);
        $req->execute();
        
        $req = $pdo->prepare("UPDATE ".table("user").' SET newmsg=newmsg+1 WHERE id=:userid');
        $req->bindValue(":userid",$receptid,PDO::PARAM_INT);
        $req->execute();
        return true;
    } else {
        return false;
    }
 }
 function displayRankChange($change){
    if($change > 0){
        return "<span class='good'>+".format($change).'</span>';
    } else if($change < 0){
        return "<span class='bad'>".format($change).'</span>';
    } else {
        return "<span class='presque'>"."=".'</span>';
    }
 }
 function displayRank($rank,$change){
    return '<a title="'.displayRankChange($change).'">'.format($rank).'</a>';
 }
 ?>