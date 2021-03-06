<?php

// alle fouten weergeven
error_reporting(E_ALL);
ini_set('display_errors','On');

// constante om in geinclude bestanden te checken
define('INCLUDED', true);

// sessie starten
if(isset($_GET['session'])) {
	session_id($_GET['session']);
}
session_start();

if(!isset($_SESSION['ip'])) {
	// nieuwe sessie
	$_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
} else if($_SESSION['ip'] != $_SERVER['REMOTE_ADDR']) {
	// sessie is niet van dit ip
	unset($_SESSION);
	session_destroy();
	session_start();
	$_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
}

// verbinden met database
$connection = mysqli_connect('localhost', 'stampotscouting', '78921ea3-2b91-43e1-9623-f063dd39d833', 'stampotscouting-ijsselgroepnl') or die("Error" . mysqli_connect_errno() . " : " . mysqli_connect_error());

$path = explode('/', trim(preg_replace('/\?.*$/','',$_SERVER['REQUEST_URI']),'/'));

if(!isset($_SESSION['login']) && !isset($no_login_need)) {
	die(json_encode(array('error', 'login')));
}
