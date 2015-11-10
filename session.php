<?php

include_once('include.php');

switch ($_GET['action']) {
	case 'fix':
		$users = array();
		$user_query = mysqli_query($connection, "
			SELECT *
			FROM users
			ORDER BY name, id;
		") or die('MySQLerror '.mysqli_errno($connection).' : '.mysqli_error($connection).'. In '.__FILE__.' on line '.__LINE__);
		while($user = mysqli_fetch_assoc($user_query)) {
			$saldo = 0;
			$transaction_query = mysqli_query($connection, "
				SELECT *
				FROM transactions
				WHERE user_id = " . (int)$user['id'] . "
				ORDER BY date ASC;
			") or die('MySQLerror '.mysqli_errno($connection).' : '.mysqli_error($connection).'. In '.__FILE__.' on line '.__LINE__);			
			while($transaction = mysqli_fetch_assoc($transaction_query)) {
				$before = $saldo;
				$saldo += $transaction['mutation'];
				
				mysqli_query($connection, "
					UPDATE transactions
					SET 
					saldo_before = " . $before . ",
					saldo_after = " . $saldo . "
					WHERE id = " . (int) $transaction['id'] . "
					LIMIT 1
				") or die('MySQLerror '.mysqli_errno($connection).' : '.mysqli_error($connection).'. In '.__FILE__.' on line '.__LINE__);
				
			}			
			mysqli_query($connection, "
				UPDATE users
				SET
				saldo = " . $saldo . "
				WHERE id = " . (int) $user['id'] . "
				LIMIT 1
			") or die('MySQLerror '.mysqli_errno($connection).' : '.mysqli_error($connection).'. In '.__FILE__.' on line '.__LINE__);
		}
		
		
		die();
		break;
	case 'list':
		$users = array();
		$user_query = mysqli_query($connection, "
			SELECT *
			FROM users
			ORDER BY name, id;
		") or die('MySQLerror '.mysqli_errno($connection).' : '.mysqli_error($connection).'. In '.__FILE__.' on line '.__LINE__);
		while($user = mysqli_fetch_assoc($user_query)) {
			$users[$user['id']] = $user;
			$users[$user['id']]['transactions'] = array();
			$transaction_query = mysqli_query($connection, "
				SELECT *
				FROM transactions
				WHERE user_id = " . (int)$user['id'] . "
				ORDER BY date DESC;
			") or die('MySQLerror '.mysqli_errno($connection).' : '.mysqli_error($connection).'. In '.__FILE__.' on line '.__LINE__);
			while($transaction = mysqli_fetch_assoc($transaction_query)) {
				$users[$user['id']]['transactions'][] = $transaction; 
			}
			if(file_exists('img/products/' . $user['id'] . '.png')) {
				$users[$user['id']]['image'] = 'img/users/' . $user['id'] . '.png';
			}
		}
		
		die(json_encode(array_values($users)));
		break;
	case 'add':
		mysqli_query($connection, "
			INSERT INTO users
			(name, min_saldo)
			VALUES
			('" . mysqli_real_escape_string($connection, $_GET['name']) . "', " . (float)$_GET['min_saldo'] . ")
		") or die('MySQLerror '.mysqli_errno($connection).' : '.mysqli_error($connection).'. In '.__FILE__.' on line '.__LINE__);
		$user_id = mysqli_insert_id($connection);
		
		$user_query = mysqli_query($connection, "
			SELECT *
			FROM users
			WHERE id = " . (int)$user_id . "
			LIMIT 1;
		") or die('MySQLerror '.mysqli_errno($connection).' : '.mysqli_error($connection).'. In '.__FILE__.' on line '.__LINE__);
		die(json_encode(mysqli_fetch_assoc($user_query)));
		
		break;
	case 'save':
		mysqli_query($connection, "
			UPDATE users
			SET
				name = '" . mysqli_real_escape_string($connection, $_GET['name']) . "',
				min_saldo = " . (float)$_GET['min_saldo'] . "
			WHERE id = " . (int)$_GET['user_id'] . "
			LIMIT 1
		") or die('MySQLerror '.mysqli_errno($connection).' : '.mysqli_error($connection).'. In '.__FILE__.' on line '.__LINE__);
		
		$user_query = mysqli_query($connection, "
			SELECT *
			FROM users
			WHERE id = " . (int)$_GET['user_id'] . "
			LIMIT 1;
		") or die('MySQLerror '.mysqli_errno($connection).' : '.mysqli_error($connection).'. In '.__FILE__.' on line '.__LINE__);
		die(json_encode(mysqli_fetch_assoc($user_query)));
		
		break;
	case 'buy_product':
		$user_query = mysqli_query($connection, "
			SELECT id, saldo
			FROM users
			WHERE id = " . (int)$_GET['user_id'] . "
			LIMIT 1
		") or die('MySQLerror '.mysqli_errno($connection).' : '.mysqli_error($connection).'. In '.__FILE__.' on line '.__LINE__);
		$user = mysqli_fetch_assoc($user_query);
		
		if(!empty($_GET['product_id'])) {
			$product_query = mysqli_query($connection, "
				SELECT *
				FROM products
				WHERE id = " . (int)$_GET['product_id'] . "
				LIMIT 1
			") or die('MySQLerror '.mysqli_errno($connection).' : '.mysqli_error($connection).'. In '.__FILE__.' on line '.__LINE__);
			$product = mysqli_fetch_assoc($product_query);
			
			$mutation = (int)$_GET['amount'] * $product['price'];
			mysqli_query($connection, "
				INSERT INTO transactions
				(user_id, product_id, amount, description, date, mutation, saldo_before, saldo_after)
				VALUES
				(" . $user['id'] . ", " . $product['id'] . ", " . (int)$_GET['amount']. ", '" . $product['name'] . " (" . $product['unit'] . ")', CURRENT_TIMESTAMP, -" . $mutation . ", " . $user['saldo'] . ", " . ($user['saldo']-$mutation) . ")
			") or die('MySQLerror '.mysqli_errno($connection).' : '.mysqli_error($connection).'. In '.__FILE__.' on line '.__LINE__);
			$transaction_id = mysqli_insert_id($connection);
			
			mysqli_query($connection, "
				UPDATE users
				SET saldo = " . ($user['saldo']-$mutation) . "
				WHERE id = " . (int)$_GET['user_id'] . "
				LIMIT 1
			") or die('MySQLerror '.mysqli_errno($connection).' : '.mysqli_error($connection).'. In '.__FILE__.' on line '.__LINE__);
			
				/*
				$user_query = mysqli_query("
				SELECT *
				FROM users
				WHERE id = " . (int)$_GET['user_id'] . ";
				") or die('MySQLerror '.mysqli_errno($connection).' : '.mysqli_error($connection).'. In '.__FILE__.' on line '.__LINE__);
				while($users = mysqli_fetch_assoc($user_query)){
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
		$transaction_query = mysqli_query($connection, "
			SELECT *
			FROM transactions
			WHERE id = " . (int)$transaction_id . "
			LIMIT 1;
		") or die('MySQLerror '.mysqli_errno($connection).' : '.mysqli_error($connection).'. In '.__FILE__.' on line '.__LINE__);
		die(json_encode(mysqli_fetch_assoc($transaction_query)));
		break;
	case 'pay':
		$transaction_id = 0;
		$user_query = mysqli_query($connection, "
			SELECT id, saldo
			FROM users
			WHERE id = " . (int)$_GET['user_id'] . "
			LIMIT 1
		") or die('MySQLerror '.mysqli_errno($connection).' : '.mysqli_error($connection).'. In '.__FILE__.' on line '.__LINE__);
		$user = mysqli_fetch_assoc($user_query);
		
		if((float)$_GET['amount'] != 0) {
			$mutation = (float)$_GET['amount'];
			mysqli_query($connection, "
				INSERT INTO transactions
				(user_id, product_id, amount, description, date, mutation, saldo_before, saldo_after)
				VALUES
				(" . $user['id'] . ", NULL, NULL, '" . mysqli_real_escape_string($connection, $_GET['description']) . "', CURRENT_TIMESTAMP, " . $mutation . ", " . $user['saldo'] . ", " . ($user['saldo']+$mutation) . ")
			") or die('MySQLerror '.mysqli_errno($connection).' : '.mysqli_error($connection).'. In '.__FILE__.' on line '.__LINE__);
			$transaction_id = mysqli_insert_id($connection);
			
			mysqli_query($connection, "
				UPDATE users
				SET saldo = " . ($user['saldo']+$mutation) . "
				WHERE id = " . (int)$_GET['user_id'] . "
				LIMIT 1
			") or die('MySQLerror '.mysqli_errno($connection).' : '.mysqli_error($connection).'. In '.__FILE__.' on line '.__LINE__);
		}
		$transaction_query = mysqli_query($connection, "
			SELECT *
			FROM transactions
			WHERE id = " . (int)$transaction_id . "
			LIMIT 1;
		") or die('MySQLerror '.mysqli_errno($connection).' : '.mysqli_error($connection).'. In '.__FILE__.' on line '.__LINE__);
		die(json_encode(mysqli_fetch_assoc($transaction_query)));
		break;
	default:
		die(json_encode(array('error', 'no valid action')));
		break;
}
