<?PHP
        include_once('include.php');
        require 'vendor/phpmailer/phpmailer/PHPMailerAutoload.php';
        $user_query = mysql_query("
			SELECT *
			FROM users
		") or die('MySQLerror '.mysql_errno().' : '.mysql_error().'. In '.__FILE__.' on line '.__LINE__);
		while($user = mysql_fetch_assoc($user_query)){
		    if($user['email'] != null){
		        if($user['saldo'] < 140.00){
		            $mail             = new PHPMailer();

                    $body             = strtr(file_get_contents('mailSetup.html'), array('{naam}' => $user['name'], '{bedrag}' => $user['saldo']));;

                    //$mail->IsSMTP(); // telling the class to use SMTP
                    $mail->SMTPDebug  = 1;                     // enables SMTP debug information (for testing)
                                                               // 1 = errors and messages
                                                               // 2 = messages only
                    $mail->SMTPAuth   = true;                  // enable SMTP authentication
                    $mail->Host       = "localhost"; // sets the SMTP server
                    $mail->Port       = 25;                    // set the SMTP port for the GMAIL server
                    $mail->Username   = "stampot"; // SMTP account username
                    $mail->Password   = "lordbaden2025";        // SMTP account password

                    $mail->SetFrom('noreply@stampot.scouting-ijsselgroep.nl', 'Stampot');

                    $mail->AddReplyTo("noreply@stampot.scouting-ijsselgroep.nl", 'Stampot');

                    $mail->Subject    = "Stampot Scouting IJsselgroep";

                    $mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
                    $mail->isHTML(true);
                    $mail->MsgHTML($body);

                    $address = $user['email'];
                    $mail->AddAddress($address, $user['name']);

                    if(!$mail->Send()) {
                      echo "Mailer Error: " . $mail->ErrorInfo;
                    } else {
                      echo "Message sent!";
                    }

		        }
		    }
		}
?>