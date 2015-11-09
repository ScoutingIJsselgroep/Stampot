<?php

include_once('include.php');

switch ($_GET['action']) {
	case 'fix':
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
			
			
			// Tristan
		}
		
		
		die();
		break;
	case 'list':
		$users = array();
		$user_query = mysql_query("
			SELECT *
			FROM users
			ORDER BY name, id;
		") or die('MySQLerror '.mysql_errno().' : '.mysql_error().'. In '.__FILE__.' on line '.__LINE__);
		while($user = mysql_fetch_assoc($user_query)) {
			$users[$user['id']] = $user;
			$users[$user['id']]['transactions'] = array();
			$transaction_query = mysql_query("
				SELECT *
				FROM transactions
				WHERE user_id = " . (int)$user['id'] . "
				ORDER BY date DESC;
			") or die('MySQLerror '.mysql_errno().' : '.mysql_error().'. In '.__FILE__.' on line '.__LINE__);
			while($transaction = mysql_fetch_assoc($transaction_query)) {
				$users[$user['id']]['transactions'][] = $transaction; 
			}
			if(file_exists('img/products/' . $user['id'] . '.png')) {
				$users[$user['id']]['image'] = 'img/users/' . $user['id'] . '.png';
			}
		}
		
		die(json_encode(array_values($users)));
		break;
	case 'add':
		mysql_query("
			INSERT INTO users
			(name, min_saldo)
			VALUES
			('" . mysql_real_escape_string($_GET['name']) . "', " . (float)$_GET['min_saldo'] . ")
		") or die('MySQLerror '.mysql_errno().' : '.mysql_error().'. In '.__FILE__.' on line '.__LINE__);
		$user_id = mysql_insert_id();
		
		$user_query = mysql_query("
			SELECT *
			FROM users
			WHERE id = " . (int)$user_id . "
			LIMIT 1;
		") or die('MySQLerror '.mysql_errno().' : '.mysql_error().'. In '.__FILE__.' on line '.__LINE__);
		die(json_encode(mysql_fetch_assoc($user_query)));
		
		break;
	case 'save':
		mysql_query("
			UPDATE users
			SET
				name = '" . mysql_real_escape_string($_GET['name']) . "',
				min_saldo = " . (float)$_GET['min_saldo'] . "
			WHERE id = " . (int)$_GET['user_id'] . "
			LIMIT 1
		") or die('MySQLerror '.mysql_errno().' : '.mysql_error().'. In '.__FILE__.' on line '.__LINE__);
		
		$user_query = mysql_query("
			SELECT *
			FROM users
			WHERE id = " . (int)$_GET['user_id'] . "
			LIMIT 1;
		") or die('MySQLerror '.mysql_errno().' : '.mysql_error().'. In '.__FILE__.' on line '.__LINE__);
		die(json_encode(mysql_fetch_assoc($user_query)));
		
		break;
	case 'buy_product':
		$user_query = mysql_query("
			SELECT id, saldo
			FROM users
			WHERE id = " . (int)$_GET['user_id'] . "
			LIMIT 1
		") or die('MySQLerror '.mysql_errno().' : '.mysql_error().'. In '.__FILE__.' on line '.__LINE__);
		$user = mysql_fetch_assoc($user_query);
		
		if(!empty($_GET['product_id'])) {
			$product_query = mysql_query("
				SELECT *
				FROM products
				WHERE id = " . (int)$_GET['product_id'] . "
				LIMIT 1
			") or die('MySQLerror '.mysql_errno().' : '.mysql_error().'. In '.__FILE__.' on line '.__LINE__);
			$product = mysql_fetch_assoc($product_query);
			
			$mutation = (int)$_GET['amount'] * $product['price'];
			mysql_query("
				INSERT INTO transactions
				(user_id, product_id, amount, description, date, mutation, saldo_before, saldo_after)
				VALUES
				(" . $user['id'] . ", " . $product['id'] . ", " . (int)$_GET['amount']. ", '" . $product['name'] . " (" . $product['unit'] . ")', CURRENT_TIMESTAMP, -" . $mutation . ", " . $user['saldo'] . ", " . ($user['saldo']-$mutation) . ")
			") or die('MySQLerror '.mysql_errno().' : '.mysql_error().'. In '.__FILE__.' on line '.__LINE__);
			$transaction_id = mysql_insert_id();
			
			mysql_query("
				UPDATE users
				SET saldo = " . ($user['saldo']-$mutation) . "
				WHERE id = " . (int)$_GET['user_id'] . "
				LIMIT 1
			") or die('MySQLerror '.mysql_errno().' : '.mysql_error().'. In '.__FILE__.' on line '.__LINE__);
			
				/*
				$user_query = mysql_query("
				SELECT *
				FROM users
				WHERE id = " . (int)$_GET['user_id'] . ";
				") or die('MySQLerror '.mysql_errno().' : '.mysql_error().'. In '.__FILE__.' on line '.__LINE__);
				while($users = mysql_fetch_assoc($user_query)){
					if($users['phone'] != null){
						$url = 'http://include.hosting2go.nl/include.php?url=http://natsirt.nl/Codiad/workspace/Whatsapp-Webhook/index.php';
						$data = array('key' => '7pjfKP0EbQ179Ynej31EB3eEz2o0I720', 'message' => 'Hoi! Je saldo bij de Stampot op Scouting IJsselgroep is €weinig, neem dus geld mee!' , 'phone' => $users['phone']);
						$optional_headers = null;
						$params = array('http' => array(
										'method' => 'POST',
										'content' => http_build_query($data)));
						 
						if ($optional_headers !== null) {
							$params['http']['header'] = $optional_headers;
						}
						 
						$ctx = stream_context_create($params);
						$fp = @fopen($url, 'rb', false, $ctx);
					}
				}*/
		}
		$transaction_query = mysql_query("
			SELECT *
			FROM transactions
			WHERE id = " . (int)$transaction_id . "
			LIMIT 1;
		") or die('MySQLerror '.mysql_errno().' : '.mysql_error().'. In '.__FILE__.' on line '.__LINE__);
		die(json_encode(mysql_fetch_assoc($transaction_query)));
		break;
	case 'pay':
		$transaction_id = 0;
		$user_query = mysql_query("
			SELECT id, saldo
			FROM users
			WHERE id = " . (int)$_GET['user_id'] . "
			LIMIT 1
		") or die('MySQLerror '.mysql_errno().' : '.mysql_error().'. In '.__FILE__.' on line '.__LINE__);
		$user = mysql_fetch_assoc($user_query);
		
		if((float)$_GET['amount'] != 0) {
			$mutation = (float)$_GET['amount'];
			mysql_query("
				INSERT INTO transactions
				(user_id, product_id, amount, description, date, mutation, saldo_before, saldo_after)
				VALUES
				(" . $user['id'] . ", NULL, NULL, '" . mysql_real_escape_string($_GET['description']) . "', CURRENT_TIMESTAMP, " . $mutation . ", " . $user['saldo'] . ", " . ($user['saldo']+$mutation) . ")
			") or die('MySQLerror '.mysql_errno().' : '.mysql_error().'. In '.__FILE__.' on line '.__LINE__);
			$transaction_id = mysql_insert_id();
			
			mysql_query("
				UPDATE users
				SET saldo = " . ($user['saldo']+$mutation) . "
				WHERE id = " . (int)$_GET['user_id'] . "
				LIMIT 1
			") or die('MySQLerror '.mysql_errno().' : '.mysql_error().'. In '.__FILE__.' on line '.__LINE__);
		}
		$transaction_query = mysql_query("
			SELECT *
			FROM transactions
			WHERE id = " . (int)$transaction_id . "
			LIMIT 1;
		") or die('MySQLerror '.mysql_errno().' : '.mysql_error().'. In '.__FILE__.' on line '.__LINE__);
		die(json_encode(mysql_fetch_assoc($transaction_query)));
		break;
	default:
		die(json_encode(array('error', 'no valid action')));
		break;
}
