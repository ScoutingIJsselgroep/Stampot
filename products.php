<?php

include_once('include.php');

switch ($_GET['action']) {
	case 'list':
		$products = array();
		$product_query = mysql_query("
			SELECT *
			FROM products WHERE deleted = 0
			ORDER BY name, unit, id;
		") or die('MySQLerror '.mysql_errno().' : '.mysql_error().'. In '.__FILE__.' on line '.__LINE__);
		while($product = mysql_fetch_assoc($product_query)) {
			$products[$product['id']] = $product;
			if(file_exists('img/products/' . $product['id'] . '.png')) {
				$products[$product['id']]['image'] = 'img/products/' . $product['id'] . '.png';
			}
		}
		
		die(json_encode(array_values($products)));
		break;
	default:
		die(json_encode(array('error', 'no valid action')));
		break;
}
