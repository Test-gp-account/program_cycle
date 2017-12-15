<?php

 $connect = mysql_connect("localhost", "root", "") or die("Cannot Connect to the Server !!!!");
 $select_db = mysql_select_db("dvd_mailer_nov2010", $connect) or die("Database not found !!!!");
 
 $sql=mysql_query("select * from user3 where `failure` = '1' ");
 //$sql=mysql_query("select * from user2 where `failure` = '1' ");
 //$sql=mysql_query("select * from usererror where `failure` = '1' ");

$cnt=0;

while($row=mysql_fetch_object($sql))
{	  	

	$cnt++;


	//mysql_query("insert into usererror set id='" . $row->id . "'" . ",email='" . $row->email . "'" );

	
	//insert email id
	mysql_query("insert into usererror set id='" . $row->id . "'" . ",email='" . $row->email . "'" . 
		",failure='" . $row->failure . "'" . ",failure_reason='" . $row->failure_reason . "'" );

}	

echo "Added " . $cnt . " email ids";

 
?>