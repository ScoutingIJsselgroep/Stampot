<?PHP
$products = "";
error_reporting(E_ALL & ~E_DEPRECATED);
	include("include.php");
	$query = mysql_query("SELECT * FROM products");
	while($row = mysql_fetch_array($query)){
		$products .= '<div><input type="checkbox" name="drink[]" value="'.$row['id'].'" /><dl style="display: inline-block;"><dt>'.$row['name'].'</dt><dd>'.$row['unit'].'</dd></dl></div>';
	}
?>