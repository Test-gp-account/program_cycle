<?php

 $connect = mysql_connect("localhost", "root", "") or die("Cannot Connect to the Server !!!!");
 $select_db = mysql_select_db("dvd_mailer_may2012", $connect) or die("Database not found !!!!");
 
mysql_query("SET AUTOCOMMIT=0");
mysql_query("START TRANSACTION");

mysql_query("insert into user2 set email='rohit'");
mysql_query("ROLLBACK");


?>