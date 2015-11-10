<?php

include_once('include.php');

switch ($_GET['action']) {
	case 'list':
		$products = array();
		$product_query = mysqli_query($connection, "
			SELECT *
			FROM products WHERE deleted = 0
			ORDER BY name, unit, id;
		") or die('MySQLerror '.mysqli_errno($connection).' : '.mysqli_error($connection).'. In '.__FILE__.' on line '.__LINE__);
		while($product = mysqli_fetch_assoc($product_query)) {
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
