<?PHP
$timeformat = 'month';
include 'functions.php';
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BierStats</title>

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/bootstrap-lightbox.min.css">
    <script src="js/bootstrap-lightbox.min.js"></script>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
<script>

	var timeformat;
	var graphformat;
	var obj, src, pos, date;	
	function timeformat(tf){
		reloadImg("functions1.php?format=" + tf + "&action=profit&title=Winst&time=" + new Date());
		return false;
	}
	function graphformat(gf){
		if($("#week").hasClass('active')) {
		  // Week
			reloadImg("functions1.php?format=week&action="+gf+"&title=Winst&time=" + new Date());
		}else{
		  // Maand
			reloadImg("functions1.php?format=month&action="+gf+"&title=Winst&time=" + new Date());
		}
		if(gf == 'amountsold'){
			document.getElementById('drinks').style.display = "block";
		}else{
			document.getElementById('drinks').style.display = "none";
		}
		
		return false;
	}
    function reloadImg(url) {
	   obj = document.getElementById("img");
	   src = obj.src;
	   pos = src.indexOf('?');
	   if (pos >= 0) {
		  src = src.substr(0, pos);
	   }
	   var date = new Date();
	  
	   obj.src = url;
	   return false;
	}
</script>
  </head>
  <body>
<div class="pagewidth">
	<div class="row">
		<div class="col-md-4">
		<div>
			<div class="panel panel-default">
				<div class="panel-heading">
    					<h3 class="panel-title">Menu</h3>
  				</div>
  				<div class="panel-body">
    					<div class="btn-group" name="timeformat" data-toggle="buttons">
							<label onClick="return timeformat('week');" id="week" class="btn btn-default <?PHP if($timeformat == 'week'){echo 'active';}?>">
								<input type="radio" name="options" id="option1"> Week
							</label>
							<label  onClick="return timeformat('month');" id="maand" class="btn btn-default <?PHP if($timeformat == 'month'){echo 'active';}?>">
								<input type="radio" name="options" id="option2"> Maand
							</label>
						</div>
					<div class="btn-group" data-toggle="buttons">
  						<label onClick="return graphformat('amountsold');" class="btn btn-default <?PHP if($graphformat == 'sold'){echo 'active';}?>">
   							<input type="radio" name="options" id="option1"> # Verkocht
  						</label>
  						<label onClick="return graphformat('amountsoldtotal');" class="btn btn-default <?PHP if($graphformat == 'sold'){echo 'active';}?>">
   							<input type="radio" name="options" id="option1"> <span class="glyphicon glyphicon-euro"></span> Verkocht
  						</label>
  						<label onClick="return graphformat('amountbought');" class="btn btn-default <?PHP if($graphformat == 'bought'){echo 'active';}?>">
   							<input type="radio" name="options" id="option2"> <span class="glyphicon glyphicon-euro"></span> Inkoop
  						</label>
						<label onClick="return graphformat('profit');" class="btn btn-default <?PHP if($graphformat == 'month'){echo 'active';}?>">
   							<input type="radio" name="options" id="option2"> <span class="glyphicon glyphicon-euro"></span> Winst
  						</label>
					</div>				
 		 		</div>				
			</div>	
			<div id="drinks" style="display:none;" class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Dranksoorten</h3>
				</div>
				<div class="panel-body">
						<form action="" method="POST">
							<?PHP echo $products; ?>
							<input type="submit" class="btn btn-primary" value="Bekijk">
						</form>
				</div>
			</div>
		</div>
		</div>
		<div class="col-md-8">
			<div class="panel panel-default">				
  				<div class="panel-body">
					<a href="functions1.php?format=month&action=profit&title=Winst">
					<?PHP if(isset($_POST['drink'])){
						if(!empty($_POST['drink'])) {
							$showproducts = "";			
							foreach($_POST['drink'] as $check) {
								if($showproducts == ""){
									$showproducts = $check;
								}else{
									$showproducts .= ','.$check;
								}
							}
						}
						echo '<img id="img" onload="document.getElementById(\'drinks\').style.display = \'block\';" class="grafiek" src="functions1.php?format=month&action=amountsold&product_id='.$showproducts.'&title=Winst"/></a>';}
					else{ echo '<img id="img" class="grafiek" src="functions1.php?format=month&action=profit&title=Winst"/></a>';}
					?>
    				<a href="#" onClick="return reloadImg('img');">Reload Image</a>
 		 		</div>
			</div>
		</div>
	</div>
</div>
	

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>
