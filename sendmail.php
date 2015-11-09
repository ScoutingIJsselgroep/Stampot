<?php
// multiple recipients
// subject
$subject = 'Stampot Scouting IJsselgroep';

// message
$message = '
<!DOCTYPE>
<html>
<head>
<style>
	body{
		background-image: url(http://www.scouting-ijsselgroep.nl/images/onebackground.jpg);
		font-family: Tahoma,sans-serif;
	}
	.mailcontent{
		max-width: 500px;
		margin-left: auto;
		margin-right: auto;
		background-color: #FFFFFF;
		width: 100%;
		min-height: 500px;
	}
	.text{
		padding: 15px;
		position: relative;
	}
	h1{
		font-size: 30px;
		font-family: "Arial black",Helvetica,sans-serif;
		line-height: 24px;
		margin: 10px 0px 15px;
		padding: 0px;
	}
	.disclaimer {
		font-size: 10px;
		margin-top: 25px;
	}
</style>
<title></title>
</head>
<body>
	<div class="mailcontent">
		<div class="text">
			<h1> Stampot Scouting IJsselgroep </h1>
			<p>Hoi '.$user['name'].',<br> je staat helaas in de min. Om altijd genoeg bier achter de bar te hebben, vragen we je om de komende keer wat geld mee te nemen. Je kunt dan (onder het genot van een biertje) je schulden aflossen!</p>
			<p>Groeten,</p>
			<p>De Stampot</p>
			<p class="disclaimer">Dit is een automatisch gegenereerde mail. Antwoorden heeft geen zin. </p>
		</div>
	</div>
</body>
<html>';
// To send HTML mail, the Content-type header must be set
$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

// Additional headers
$headers .= 'To: '.$user['name'].' <'.$user['email'].'>' . "\r\n";
$headers .= 'From: Stampot Scouting IJsselgroep <noreply@scouting-ijsselgroep.nl>' . "\r\n";

// Mail it
mail($to, $subject, $message, $headers);
?>