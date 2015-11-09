<?php

if(!INCLUDED) {
	die();
}

?><!DOCTYPE html>
<html lang="en">
<head><meta content="text/html;charset=utf-8" http-equiv="Content-Type">
	<meta content="utf-8" http-equiv="encoding">
	<title>StamPot | IJsselgroep</title>
	
	<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
	
	<link rel="stylesheet" type="text/css" href="css/Scrollable.css" />
	<link rel="stylesheet" type="text/css" href="css/style.css" />
	
	<script type="text/javascript" src="js/mootools-core-1.4.5.js"></script>
	<script type="text/javascript" src="js/mootools-more-1.4.0.1.js"></script>
	<script type="text/javascript" src="js/Scrollable.js"></script>
	
	<script type="text/javascript">
	/* <![CDATA[ */
		window.addEvent('domready', function() {
			<?php
			if($login_error) {
			?>
			var el = document.getElement('.total_login form');
			el.set('tween', {
			    duration: 70,
			    transition: 'linear',
			    link: 'chain'
			});
			el.tween('margin-left', 0, 5).tween('margin-left', 5, -10).tween('margin-left', -10, 10).tween('margin-left', 10, -10).tween('margin-left', -10, 10).tween('margin-left', 10, -10).tween('margin-left', -10, 0);
			<?php
			}
			?>
		});
	/* ]]> */
	</script>
</head>
<!--[if lte IE 6 ]><body class="ie6"><![endif]-->
<!--[if gt IE 6 ]><body class="ie"><![endif]-->
<!--[if !(IE)]><!--><body><!--<![endif]-->
	<div class="floater floater_login"></div>
	<div class="total total_login">
		<form action="" method="post">
			<div>
				<div class="title"><img src="img/smiley-<?= ($login_error?'eek':'money') ?>.png" alt="" /> Welkom bij de StamPot</div>
				<input type="text" class="user" name="user" placeholder="Gebruikersnaam" />
				<input type="password" class="pass"name="pass" placeholder="*****" />
				
				<input class="submit" type="submit" value="Login" />
			</div>
		</form>
	</div>
</body>
</html>