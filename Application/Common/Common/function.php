<?php
function test()
{
	echo "testfunc";
}

function getWhois($domain){
	$msg = array();
	Vendor('PHPWhois.PHPWhoisAutoload');//load the lib to get the whois infomation
	$phpwhois = new PHPWhois($domain);
	$whois_answer = $phpwhois ->info();
	$msg[0] = $whois_answer;
	$msg[0] = str_replace("\n","<br>",$msg[0]);
	$flag = $phpwhois->isAvailable();
	if(strstr($whois_answer,"No match")||strstr($whois_answer,"No Domain exists")||strstr($whois_answer,"t valid") || strstr($whois_answer,"No Data Found")|| strstr($whois_answer,"has not been registered")) 
	{
		$msg[1] =  "Y";//we can use it!
	}else{
		$msg[1] =  "N";//we can not use it
	}
	return $msg;


}
/**
 * email send
 */
function sendMail($to, $subject, $content) {
		Vendor('PHPMailer.PHPMailerAutoload');	 
		$mail = new PHPMailer(); 
		 $mail->IsSMTP(); // start SMTP
		 $mail->Host=C('MAIL_HOST'); //smtp
		 $mail->SMTPAuth = C('MAIL_SMTPAUTH'); 
		 $mail->Username = C('MAIL_USERNAME'); 
		 $mail->Password = C('MAIL_PASSWORD') ; 
		 $mail->From = C('MAIL_FROM'); 
		 $mail->FromName = C('MAIL_FROMNAME'); 
		 $mail->AddAddress($to,"name");
		 $mail->WordWrap = 50; 
		 $mail->IsHTML(C('MAIL_ISHTML')); // 
		 $mail->CharSet=C('MAIL_CHARSET'); 
		 $mail->Subject =$subject; 
		 $mail->Body = $content; 
		 $mail->AltBody = "This is the body in plain text for non-HTML mail clients"; 
		 if(!$mail->Send()) {
		 return 0;
		 } else{
		 return 1;
		 }
	}




?>
