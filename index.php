<?php
//die("Omwille van een verhuizing is de stampot offline gehaald.");
$no_login_need = 1;
include_once('include.php');

if($path[0] == 'logout') {
	unset($_SESSION['login']);
}
$login_error = false;
if(!isset($_SESSION['login'])) {
	if(isset($_POST) && isset($_POST['user']) && isset($_POST['pass'])) {
		$login_error = true;
		
		if($_POST['user'] == 'ijsselgroep' && $_POST['pass'] == 'lordbaden') {
			$_SESSION['login'] = true;
			$login_error = false;
			
			header('location: /');
			die();
		}
	}
}

if(!isset($_SESSION['login'])) {
	include('login.php');
} else {
	include('index2.php');
}
