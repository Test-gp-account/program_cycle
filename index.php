<?php

require_once('classes/class.phpmailer.php');

$connect = mysql_connect("localhost", "root", "") or die("Cannot Connect to the Server !!!!");
$select_db = mysql_select_db("dvd_mailer_may2013", $connect) or die("Database not found !!!!");
 
if(isset($_POST['Submit']))
{
	$sql=mysql_query("select * from config");
	list($smtp,$user,$pass,$from,$from_name)=mysql_fetch_array($sql);
		
	//	echo $smtp;
	$mail = new PHPMailer();  // create a new object
	$mail->IsSMTP(); // enable SMTP
	$mail->SMTPDebug = 0;  // debugging: 1 = errors and messages, 2 = messages only
	$mail->SMTPAuth = true;  // authentication enabled
	$mail->SMTPKeepAlive = true;   
	$mail->Host = $smtp;
	$mail->Port = 25; 
	$mail->Username = $user;  
	$mail->Password = $pass; 
	$mail->SetFrom($from, $from_name);
	$mail->IsHTML(true); //emable html format
	
	//set subject
	$mail->Subject = "PSA Request for Memorial Day";

//set email body for dvd4vets - html format
$mail->Body = '<span style="font-size:12px; font-family:Arial, Helvetica, sans-serif">
As we approach Memorial Day, we thank you again for previously airing our PSAs promoting<br />
DVDs4Vets.<br />
<br />

Since 2006, DVDs4Vets has arranged for the distribution of more than 600,000 DVDs to benefit<br />
military veterans who cannot obtain this type of entertainment on their own.<br />
<br />

If your station can air our PSAs, please go to <a href="http://dvds4vets.org/media_info.htm"> http://dvds4vets.org/media_info.htm</a> to download.<br />
<br />

If you would like these MP3 files forwarded, please ask at your convenience.<br />
Our PSAs are .29 and .31 seconds each.<br />
<br />
 
If you would like to schedule a recorded or live interview with <b>Laurance Baschkin</b> our Director, <br />
we will gladly accommodate your request. Thank you for your time.<br />
<br />

(914) 835-3673 office<br />
(646) 724-5129 cell<br />
<br />
<br />

Sincerely, <br />
<br />
<b>Scott Bowers</b><br />
Media Coordinator <br />
DVDs4Vets <br/>
173 Halstead Avenue <br/>
Harrison, NY 10528 <br/>
dvds4vets@aol.com <br/>
</span>

<br/>

<p style="font-size:10px; font-family:Arial, Helvetica, sans-serif"><strong>Disclaimer</span>:</strong>
If you do not wish to be contacted regarding a requested PSA announcement, please reply<br />
and include "Remove" in the subject line.</p>
</span>';

	$max= (empty($_POST['max']) || $_POST['max']==0) ? "1" : $_POST['max'];

	$user_table = "user"; //set user table for queries
	
	//$sql=mysql_query("select * from " . $user_table . " where mail_sent='0' limit 0," . " $max");
	//$sql=mysql_query("select * from " . $user_table . " where failure='1' limit 0," . " $max");

	$query_emails =	"select * from " . $user_table . " where mail_sent='0' 
					UNION 
					select * from " . $user_table . " where failure='1' and no_of_attempts <= 6 
					limit 0," . $max;

	$sql = mysql_query($query_emails);
	$no_of_rows_to_try = mysql_num_rows($sql);

	echo "<b>Available Rows to try: " . $no_of_rows_to_try . "</b><br>";
	echo "Starting Excecution..." . "<br><br>";

	//A round is completed either when all emails are sent successfully or when there are 20 or more repeated email failures
	$round_counter = 0;
	$total_trials = 0;
	$total_sent = 0;
	$total_failures = 0;

	while($total_trials < $no_of_rows_to_try)
	{
		$round_counter++;
		$trials_in_round = 0; //initialize the trial counter for round

		//initialize mail counters
		$sent_in_round = 0;
		$failures_in_round = 0;
		$repeated_failure_counter = 0;

		echo "-----Round " . $round_counter .  "-----<br>";
		while($row=mysql_fetch_object($sql))
		{	  	
			$mail->AddAddress($row->email,$row->email); //add email address to 'to'
			$mail->AddReplyTo($from,$from_name); //add reply to

			$trials_in_round++;

			if(!$mail->Send()) 
			{
				//mail sending failed. insert error into user table
				mysql_query("update ". $user_table . " set mail_sent='1',sent_date=now(),failure='1',failure_reason='" . $mail->ErrorInfo . "', no_of_attempts = no_of_attempts+1" . " where email='" . $row->email . "'" . " and id='" . $row->id . "'");

				$failures_in_round++;
				$repeated_failure_counter++;

				//exit inner loop on more than xx continuous failures
				if ($repeated_failure_counter > 19) break;
			} 
			else 
			{
				//update table with date
				mysql_query("update " . $user_table . " set mail_sent='1',sent_date=now(), failure='0', failure_reason='', no_of_attempts = no_of_attempts+1 where email='" . $row->email . "'" . " and id='" . $row->id . "'");

				$sent_in_round++;
				
				//reset the repeated failure counter to prevent breaking from inner loop
				$repeated_failure_counter = 0;
			}

			//Clear all addresses in mailer
			$mail->ClearAllRecipients();

		}	//end inner while

		//update the total numbers with the round trial numbers
		$total_trials = $total_trials + $trials_in_round;
		$total_sent = $total_sent + $sent_in_round;
		$total_failures = $total_failures + $failures_in_round;

		echo "<br>Trials: " . $trials_in_round . "<br>";
		echo "Total emails sent: " . $sent_in_round . "<br>";
		echo "Mails Failure: " . $failures_in_round . "<br>";
		echo "------------------<br><br>";

		flush();

		//no of trials reached/exceeded the available rows in database. exit the infinite outer loop
		if($total_trials >= $no_of_rows_to_try) break;

		//no of trials exceeded the user supplied limit. exit the infinite outer loop
		if($total_trials > $max) break;

		sleep(120); //halt execution before next round

	}// end outer while

	echo "Excecution Completed<br>";

	echo "<b>Total No of Trials: " . $total_trials . "</b><br>";
	echo "<b>Total Sent: " . $total_sent . "</b><br>";
	echo "<b>Total Failures: " . $total_failures . "</b><br>";

} //end outer if
?>

<form method="post">
<table width="62%" height="192" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td height="26">No. of mails to Try</td>
    <td><input name="max" type="text" id="max" size="10" maxlength="4" /></td>
  </tr>
  <tr>
    <td height="26">&nbsp;</td>
    <td><input type="submit" name="Submit" value="Start" />
      &nbsp;&nbsp;
      </td>
  </tr>
</table>
</form>
 
