<?php

 $connect = mysql_connect("localhost", "root", "") or die("Cannot Connect to the Server !!!!");
 $select_db = mysql_select_db("dvd_mailer_may2013", $connect) or die("Database not found !!!!");
 
 $sql=mysql_query("select * from raw");
 //$sql=mysql_query("select * from user2 where `failure` = '1' ");
 //$sql=mysql_query("select * from usererror where `failure` = '1' ");

$cnt=0;

while($row=mysql_fetch_object($sql))
{	  	

	//mysql_query("insert into usererror set id='" . $row->id . "'" . ",email='" . $row->email . "'" );

	//insert email id
	$ret = mysql_query("insert into user set email='" . addslashes($row->email) . "'");

	if ($ret) 
	{
		$cnt++;
	}
	else
	{
		die('Invalid query: ' . mysql_error());	
	}

}	

echo "Added " . $cnt . " email ids to user";
 
?>