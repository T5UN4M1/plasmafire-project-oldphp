<?php

/**
 * @author T5UN4M1
 * @copyright 2015
 */
 
ob_start();
error_reporting(E_ALL);
session_start();

if(!empty($_SESSION['id'])){ // if user is already connected
    header("Location:game.php?a=accueil");
}


define('PHPEX','.php');

define('ROOT_PATH','./');

define('INCLUDE_PATH',ROOT_PATH . 'inc/');
define('PAGES_PATH',ROOT_PATH . 'pages/');
define('CLASS_PATH',ROOT_PATH . 'class/');


$pages = array(
    'accueil',
    'inscription',
    'connexion'
);

$requestedPage = (!empty($_GET['p']) && in_array($_GET['p'],$pages)) ? $_GET['p'] : $pages[0];

include ROOT_PATH . 'inc' . PHPEX;

?>
