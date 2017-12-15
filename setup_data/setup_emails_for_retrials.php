<?php

 $connect = mysql_connect("localhost", "root", "") or die("Cannot Connect to the Server !!!!");
 $select_db = mysql_select_db("dvd_mailer_may2012", $connect) or die("Database not found !!!!");
 
 $sql=mysql_query("select * from userround3 where failure = '1'");
 //$sql=mysql_query("select * from user2 where `failure` = '1' ");
 //$sql=mysql_query("select * from usererror where `failure` = '1' ");

$cnt=0;

while($row=mysql_fetch_object($sql))
{	  	

	//$new_id = intval($row->id) + 9000;
	
	//mysql_query("insert into usererror set id='" . $row->id . "'" . ",email='" . $row->email . "'" );

	//insert email id
	$ret = mysql_query("insert into userround4 set id='" . $row->id . "',email='" . $row->email . "',mail_sent='" . $row->mail_sent . "',sent_date='" . $row->sent_date . "',failure='" .
	$row->failure . "',failure_reason='" . $row->failure_reason . "'");

	if ($ret) 
	{
		$cnt++;
	}
	else
	{
		die('Invalid query: ' . mysql_error());	
	}

}	

echo "Added " . $cnt . " email ids to userround4";
 
?>