<?php

switch ($_GET['action']) {
	case 'login':
		session_start();
		
		if(!$_SERVER['REMOTE_ADDR']) {
			die(json_encode(array('error' => 'no REMOTE_ADDR found')));
		} else {
			$_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
		}
		
		if(@$_GET['apikey'] == 'j7y9wjj987S234SDrihS8703j4k029-37h23SDdsdfsd5sd') {
			$_SESSION['login'] = true;
			die(json_encode(array('session' => session_id())));
		
		} else {
			die(json_encode(array('error' => 'key error')));
		}
		
		break;
	default:
		die(json_encode(array('error', 'no valid action')));
		break;
}
