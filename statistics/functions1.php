<?php
error_reporting(E_ALL & ~E_DEPRECATED);
require_once 'include.php';
function getweek(){
	$date[0] = date("Y");
	$date[1] = date("W");	
	return $date;
}
function getstartend($week){

    $begindate = date("Y-m-d", strtotime("-5 week monday")); //Returns the date of monday in week
    $enddate = date("Y-m-d", strtotime("-4 week sunday"));   //Returns the date of sunday in week
 
    
    return $begindate.$enddate;
    
}
function getamount($product_id, $query_name){
	$data = array();
	for($i=0; $i <= 12; $i++){
		$f = $i-1;
		$j = 0;
		switch ($_GET['format']){
			case 'week':
				$endofmonth = date('Y-m-d 12:59:59',strtotime('-'.$f.' week monday'));
				$nameofmonth = date('W-M',strtotime('-'.$f.' week monday'));
				$beginofmonth = date('Y-m-d 00:00:00',strtotime('-'.$i.' week sunday'));
				break;
			case 'month';
				$endofmonth = date('Y-m-t 12:59:59',strtotime('-'.$i.' month'));
				$nameofmonth = date('F',strtotime('-'.$i.' month'));
				$beginofmonth = date('Y-m-01 00:00:00',strtotime('-'.$i.' month'));
				break;
		}
		switch ($query_name){
			case 'amountsold':
				$result = mysql_query("SELECT SUM(amount) AS amount FROM transactions WHERE product_id IN ($product_id) AND date between '$beginofmonth' AND '$endofmonth'");
				break;
			case 'amountsoldtotal':
				$result = mysql_query("SELECT -1*SUM(mutation) AS amount FROM transactions WHERE product_id IS NOT NULL AND date between '$beginofmonth' AND '$endofmonth'");
				break;
			case 'amountbought';
				$result = mysql_query("SELECT SUM(mutation) AS amount FROM transactions WHERE product_id IS NULL AND date between '$beginofmonth' AND '$endofmonth'");
				break;
		}
		
			if($row = mysql_fetch_array($result)){
				if(!$row['amount'] > 0){
					array_push($data, array($nameofmonth,0));
				}else{
					array_push($data, array($nameofmonth,$row['amount']));
				}
			}
			
			
	}
	return $data;
}
function generateprofitdata($datasold, $databought){
	$data = array();
	
	for($i=0; $i < 12; $i++){	
		$profit = -1*($databought[$i][1] - $datasold[$i][1]);
		array_push($data, array($datasold[$i][0], $profit));
	}
	return $data;
}

switch($_GET['action']){
	case 'amountsold':
		$data = getamount($_GET['product_id'], 'amountsold');
		break;
	case 'amountsoldtotal':
		$data = getamount(1, 'amountsoldtotal');
		break;
	case 'amountbought':
		$data = getamount(0, 'amountbought');
		break;
	case 'profit':
		$datasold = getamount(1, 'amountsoldtotal');
		$databought = getamount(0, 'amountbought');
		$data = generateprofitdata($datasold,$databought);
		break;		
}
$data = array_reverse($data);
//Include the code
require_once 'req/phplot.php';

//Define the object
$plot = new PHPlot();



$plot->SetDataValues($data);


$plot->SetFontTTF('title', 'fonts/Lato.ttf', 17);
$plot->SetFontTTF('y_label', 'fonts/Lato.ttf', 12);
$plot->SetFontTTF('x_label', 'fonts/Lato.ttf', 7);
$plot->SetFontTTF('x_title', 'fonts/Lato.ttf', 12);
$plot->SetFontTTF('y_title', 'fonts/Lato.ttf', 12);

$plot->SetTitle($_GET['title']);
$plot->SetXTitle('Tijdsbestek');
$plot->SetYTitle('Aantal');

//Turn off X axis ticks and labels because they get in the way:
$plot->SetXTickLabelPos('none');
$plot->SetXTickPos('none');

//Draw it
$plot->DrawGraph();
header("Content-type: image/png");
?>
