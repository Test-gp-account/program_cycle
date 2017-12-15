<?php
include("config/config.inc.php");
include("config/config_ip_access.php");
if(isset($_SESSION['user_id']))
{
	if ($_SESSION['user_id']!='') 
	{
		?>
		<script type="text/javascript">
			window.location="index.php";
		</script>
		<?php
	}
}
if(isset($_REQUEST['login']))
{
	$security_code=$_REQUEST['security_code'];
	if (is_null($security_code) || $security_code =='') {
	$error = 'CAPTCHA not entered';
	} 

	elseif ($security_code != $_SESSION['security_code']) {
		$error = 'Invalid CAPTCHA entered';
	}
	else
	{
		$ip_address= $_SERVER['REMOTE_ADDR'];
		$id=session_id();

		$user_id = trim($_POST['user_name']);
		$password = trim($_POST['password']);

		//get salt for user
		$exe_salt = $dbcon->execute_query("select * FROM ".ADMIN_USER." WHERE user_code = '".$user_id."'");
		$res_salt = $dbcon->fetch_one_record();
		$salt = $res_salt['salt_key'];

		//encrypt password
		$encrypted_password = hash('sha512', $salt.$password);
		$query = "select * from ".ADMIN_USER." where user_code = '".$user_id."' and password = '".$encrypted_password."'";
		$execute = $dbcon->execute_query($query);
		$num = $dbcon->count_records();
		$result = $dbcon->fetch_one_record();
		$flag = 0;
		if($num > 0) //login credentials success
		{
			if($result['id']!=5)
			{
				if(!in_array($_SERVER['REMOTE_ADDR'], $ip_array))
				{
					$flag++;
					$error = 'Access not authorised';
				}
			}
			if($flag==0)	
			{
				if($result['status'] == 'Active')
				{
				
					$_SESSION['user_name'] = $result['user_fullname']; // ASSIGNING THE USER NAME IN SESSION
					$_SESSION['user_code'] = $user_id; // ASSIGINING THE USER CODE IN SESSION
					$_SESSION['user_id'] = $result['id'];
					$_SESSION['user_type_id'] = $result['user_type_id'];
					$_SESSION['user_news_display_name'] = $result['news_display_name'];

					//create log
					$login_track_q="INSERT into ".LOGIN_TRACK." (user_id,login_time,ip_address,session_id,system_comments,authentication_status) VALUES ('".$result['id']."', now(),'".$ip_address."','".$id."','Login Authenticated','Success')";

					mysql_query($login_track_q)or die(mysql_error());

					//update last_successful_login timestamp for user
					//code to be written

					//send to admin home page
					header("Location: index.php");
				}
				elseif($result['status'] == 'Deactivated')
				{
					$login_track_q="INSERT into ".LOGIN_TRACK." (user_id,login_time,ip_address,session_id,system_comments,authentication_status) VALUES ('".$result['id']."', now(),'".$ip_address."','".$id."','Login Attempt on Deactivated ID','Fail')";

					mysql_query($login_track_q)or die(mysql_error());

					//increment no_of_failed_login_attempts for user
					//code to be written

					//if no_of_failed_login_attempts > 3 then 
					//change user status to 'blocked' and 'system_comments' = 'User Blocked due to repeated login attempt on deactivated id'

				}
				elseif($result['status'] == 'Blocked')
				{
					$login_track_q="INSERT into ".LOGIN_TRACK." (user_id,login_time,ip_address,session_id,system_comments,authentication_status) VALUES ('".$result['id']."', now(),'".$ip_address."','".$id."','Login Attempt on Blocked ID','Fail')";

					mysql_query($login_track_q)or die(mysql_error());

					//increment no_of_failed_login_attempts for user
					//code to be written

				}
			}
		}
		elseif(empty($user_id)) //empty fields
		{
			echo "Please Enter User Id !!";
			exit;
		}
		else //login credentials failure
		{
			//check if attempts are being made on a valid user id 
			$query = "select * from ".ADMIN_USER." where user_code = '".$user_id."'";
			$execute = $dbcon->execute_query($query);
			$num = $dbcon->count_records();
			$result = $dbcon->fetch_one_record();

			if($num > 0) //valid user id
			{
				$system_comments = 'Hacking attempt on existing User id: '.$user_id.' with Password: '.$password;
				$login_track_q="INSERT into ".LOGIN_TRACK." (user_id,login_time,ip_address,session_id,system_comments,authentication_status) VALUES ('".$result['id']."', now(),'".$ip_address."','".$id."','".$system_comments."','Fail')";
				mysql_query($login_track_q)or die(mysql_error());

				//increment no_of_failed_login_attempts for user
				//code to be written

				//if no_of_failed_login_attempts > 3 then 
				//change user status to 'blocked' and 'system_comments' = 'User Blocked due to repeated login attempt with wrong password'
			}
			else //invalid user id
			{
				$system_comments = 'Automated hacking attempt - User id: '.$user_id.', Password: '.$password;
				$login_track_q="INSERT into ".LOGIN_TRACK." (user_id,login_time,ip_address,session_id,system_comments,authentication_status) VALUES (0, now(),'".$ip_address."','".$id."','".$system_comments."','Fail')";
				
				mysql_query($login_track_q)or die(mysql_error());
			}

			//Set mark_for_blacklisting flag to 'Yes'
			//code to be written

			$error="Wrong User Id or Password !!";
		}
	}

}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="./css/admin_style.css" rel="stylesheet" type="text/css">
<link href="./css/style.css" rel="stylesheet" type="text/css">
</head>
<body onLoad="document.getElementById('loginid').focus();">

<div id="container">
<?php require_once("adheader.php");?>


	<div class="blank-div-100"> </div>

	<table width="60%" border="0" align="center">
		<tr>
			<td class="correct" align="center"><?php //echo $_SESSION['forgot_msg'];  ?></td>
		</tr>
	</table>
	<br>
	<table height="400" width="350" border="0" align="center" cellpadding="0" cellspacing="0" class="form_background table_border_css">
		<tr>
			<td class="table_heading" height="24"><font color="#FFFFFF" class="toplinks">Login</font></td>
		</tr>
		<tr>
			<td width="100%">
				<form name="form2" method="post" action="login.php">
					<table width="99%" border="0" align="center" cellpadding="8" cellspacing="0" class="form_background">
						<tr>
							<td colspan="2" class="red" align="center">
								<?php
					  			if (isset($error)) 
					  			{
					  				?>
					  				<b><?php echo $error;?></b>
					  				<?php
					  			}
					  			?>
							<input name="back" type="hidden" id="back" value="<?php echo $_REQUEST['back']?>"></td>
						</tr>
						<tr>
							<td width="31%" align="right"><label for="user_name">Username</label></td>
							<td width="69%" class="fieldbox"><input name="user_name" type="text" class="bodytext" id="loginid" autocomplete="off" size="32"></td>
						</tr>
						<tr>
							<td align="right"><label for="password">Password</label></td>
							<td class="fieldbox"><input name="password" type="password" class="bodytext" id="password" size="32"></td>
						</tr>

						<tr>
							<td>&nbsp;</td>
							<td>
								<img src="captcha/GenerateCaptcha.php">
							</td>
						</tr>

						<tr height="60">
							<td>&nbsp;</td>
							<td>
								<span class="fieldbox">
								<input type="text" class="curveinput" name="security_code" id="security_code" style="width:70px;" autocomplete="off" />
								</span>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<input name="login" type="submit" class="button-submit" value="Login">
							</td>
						</tr>
					</table>
				</form>
			</td>
		</tr>
	</table>

	<div class="blank-div-100"> </div>

	<?php require_once("include/footer.php");?>

</div> <!--end of container div -->
</body>
</html>
