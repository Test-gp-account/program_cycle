<?php
require("classes/class.phpmailer.php");
 
$mail = new PHPMailer();

$email='rgit.rachit@gmai.com';
$name= 'Rachit';
$company = 'RG';
$phone = '8765434567';
$message = 'Test';
$body='';
$address='Abc';
$country = 'India';

$thankyou = "thanks_contact.html";

//mail settinggs
$mail->IsSMTP(); // send via SMTP
$mail->Host = "localhost"; // SMTP server
$mail->SMTPAuth = true; // turn on SMTP authentication

//$mail->SMTPDebug = 2;

//sender acccount information
$mail->Username = "sales@rginfotechnology.com";  // SMTP username
$mail->Password = "sl2000#"; // password on localhost
$mail->From     = "sales@rginfotechnology.com";
$mail->FromName = "Sales Manager";

$subject =  "Contact Us Response";

// Message 
$body.="------------------------<br>";
$body.="Who sent this Info<br>";
$body.="------------------------<br>";
$body.="Name: $name<br>";
$body.="Email: $email<br>";
$body.="Phone: $phone<br>";
$body.="-------------------------------------<br>";
$body.="<br>";
$body.="Message<br>";
$body.="-------------------------------------<br>";
$body.="$message";
$body.=" ";
$body.="--------------------------------------<br>";
if(!empty($address))
{
	$body.="<br>";
	$body.="Address<br>";
	$body.="-------------------------------------<br>";
	$body.="$address";
	$body.="<br>";
	$body.="--------------------------------------<br>";
}

if(!empty($country))
{
	$body.="<br>";
	$body.="Country<br>";
	$body.="-------------------------------------<br>";
	$body.="$country";
	$body.="<br>";
	$body.="--------------------------------------<br>";
}
$body.="<br> USER INFORMATION<br>";
$body.="--------------------------------------<br>";
$body.="<br> Browser: ";
$body.=$_SERVER['HTTP_USER_AGENT'];
$body.="<br>";
$body.="<br> From page: ";
$body.=$_SERVER['HTTP_HOST'];
$body.="<br>";
$body.="<br> IP adress: ";
$body.=$_SERVER['REMOTE_ADDR'];
$body.="<br>";

//reciever acccount information
$mail->AddAddress("rachit@rginfotechnology.com"); //add recepient email
$mail->WordWrap = 50; // set word wrap
//$mail->AddAttachment("files/abc.pdf");
//$mail->AddAttachment("Path to Attachment "); // attachment
$mail->IsHTML(true); // send as HTML
$mail->Subject  =  $subject;
$mail->Body     =  $body;


 

if($mail->Send())
{
	echo "Send Successfully";
	exit;
}
else
{
	echo "Error in sending";
	exit;
}
?>
 