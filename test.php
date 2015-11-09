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
$connection = mysql_connect('localhost', 'm1_e63071af', 'fUkQ9MJHV6', true) or die("Error"  .mysql_errno() . " : " . mysql_error());
mysql_select_db('m1_e63071af', $connection) or die("Error " .mysql_errno() . " : " . mysql_error());

$path = explode('/', trim(preg_replace('/\?.*$/','',$_SERVER['REQUEST_URI']),'/'));

$user['id'] = 7;
$saldo = 30.00;
$users = array();
		$user_query = mysql_query("
			SELECT *
			FROM users
			ORDER BY name, id;
		") or die('MySQLerror '.mysql_errno().' : '.mysql_error().'. In '.__FILE__.' on line '.__LINE__);
		while($user = mysql_fetch_assoc($user_query)) {
			$saldo = 0;
			$transaction_query = mysql_query("
				SELECT *
				FROM transactions
				WHERE user_id = " . (int)$user['id'] . "
				ORDER BY date ASC;
			") or die('MySQLerror '.mysql_errno().' : '.mysql_error().'. In '.__FILE__.' on line '.__LINE__);			
			while($transaction = mysql_fetch_assoc($transaction_query)) {
				$before = $saldo;
				$saldo += $transaction['mutation'];
				
				mysql_query("
					UPDATE transactions
					SET 
					saldo_before = " . $before . ",
					saldo_after = " . $saldo . "
					WHERE id = " . (int) $transaction['id'] . "
					LIMIT 1
				") or die('MySQLerror '.mysql_errno().' : '.mysql_error().'. In '.__FILE__.' on line '.__LINE__);
				
			}			
			mysql_query("
				UPDATE users
				SET
				saldo = " . $saldo . "
				WHERE id = " . (int) $user['id'] . "
				LIMIT 1
			") or die('MySQLerror '.mysql_errno().' : '.mysql_error().'. In '.__FILE__.' on line '.__LINE__);
			// Send mail
			if($saldo < 40.00){
				$user_query = mysql_query("
				SELECT *
				FROM users
				WHERE id = " . (int)$user['id'] . ";
				") or die('MySQLerror '.mysql_errno().' : '.mysql_error().'. In '.__FILE__.' on line '.__LINE__);
				while($users = mysql_fetch_assoc($user_query)){
					$remindersend = strtotime($users['remindersend']);
					if($remindersend < strtotime("-1 week")){
						$to  = $users['email'];

						// subject
						$subject = 'Saldo bierrekening Scouting IJsselgroep';

						// message
						$message = '
						<html>
						<head>
						  <title>Saldo bierrekening Scouting IJsselgroep</title>
						</head>
						<body>
						  <p>Hoi, <br> Je hebt een negatief saldo bij de bierrekening bij Scouting IJsselgroep. Zou je alsjeblieft de komende keer genoeg geld mee willen nemen zodat er weer meer bier ingekocht kan worden?</p>
						  <p> Vriendelijke groeten, <br> Scouting IJsselgroep </p>
						</body>
						</html>
						';

						// To send HTML mail, the Content-type header must be set
						$headers  = 'MIME-Version: 1.0' . "\r\n";
						$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

						// Additional headers
						$headers .= 'To:  '.$users['name'].'<'.$users['email'].'>' . "\r\n";
						$headers .= 'From: Bierrekening <noreply@scouting-ijsselgroep.nl>' . "\r\n";

						// Mail it
						mail($to, $subject, $message, $headers);


					}	
				}
			}
			// Tristan
		}?>