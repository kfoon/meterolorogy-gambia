
<?php 
/********************** MYSETTINGS.PHP**************************
This updates user settings and password
************************************************************/
include 'dbc.php';
page_protect();

$err = array();
$msg = array();

if($_POST['doUpdate'] == 'Update')
{


$rs_pwd = mysql_query("select pwd from users where id='$_SESSION[user_id]'");
list($old) = mysql_fetch_row($rs_pwd);
$old_salt = substr($old,0,9);

//check for old password in md5 format
	if($old === PwdHash($_POST['pwd_old'],$old_salt))
	{
	$newsha1 = PwdHash($_POST['pwd_new']);
	mysql_query("update users set pwd='$newsha1' where id='$_SESSION[user_id]'");
	$msg[] = "Your new password is updated";
	//header("Location: mysettings.php?msg=Your new password is updated");
	} else
	{
	 $err[] = "Your old password is invalid";
	 //header("Location: mysettings.php?msg=Your old password is invalid");
	}

}

if($_POST['doSave'] == 'Save')
{
// Filter POST data for harmful code (sanitize)
foreach($_POST as $key => $value) {
	$data[$key] = filter($value);
}


mysql_query("UPDATE users SET
		
		
			`tel` = '$data[tel]',
			`website` = '$data[web]'
			 WHERE id='$_SESSION[user_id]'
			") or die(mysql_error());

//header("Location: mysettings.php?msg=Profile Sucessfully saved");
$msg[] = "Profile Sucessfully saved";
 }

$rs_settings = mysql_query("select * from users where id='$_SESSION[user_id]'");
?>
<html>
<head>
<title>My Account Settings</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
<script language="JavaScript" type="text/javascript" src="js/jquery.validate.js"></script>
  <script>
  $(document).ready(function(){
    $("#myform").validate();
	 $("#pform").validate();
  });
  </script>
<link href="./pages/login/css/styles.css" rel="stylesheet" type="text/css">
 <link rel="stylesheet" type="text/css" href="./pages/login/css/layout.css" />

</head>

<body>
<?php include "./pages/login/includes/header.php"; ?>
<script type="text/javascript" src="./pages/login/js/quickmenu.js"></script>  
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="main">
  <tr>


  </tr>

<tr>
 <td width="19.2%" valign="top">

<?php include("sidebar.php");?>

      <p>&nbsp;</p>
      <p>&nbsp;</p>
      <p>&nbsp;</p>
</td>


    <td width="732" valign="top">
<div id="page">
<fieldset class="bc">
 <legend><a onmouseover="set_CSS('a1','color','#33FFFF')" class="ac" id="a1" href=""><h4>My Account Settings</h4></a></legend>


      <p>
        <?php	
	if(!empty($err))  {
	   echo "<div class=\"msg\">";
	  foreach ($err as $e) {
	    echo "* Error - $e <br>";
	    }
	  echo "</div>";	
	   }
	   if(!empty($msg))  {
	    echo "<div class=\"msg\">" . $msg[0] . "</div>";

	   }
	  ?>
      </p>
      <p>Here you can make changes to your profile. Please note that you will 
        not be able to change your email which has been already registered.</p>
	  <?php while ($row_settings = mysql_fetch_array($rs_settings)) {?>
      <form action="" method="post" name="myform" id="myform">
        <table width="90%" border="0" align="center" cellpadding="3" cellspacing="3" class="forms">

          <tr>
            <td width="27%">Phone</td>
            <td width="73%"><input name="tel" type="text" id="tel" class="required" value="<?php echo $row_settings['tel']; ?>"></td>
          </tr>

          
          <tr> 
            <td>User Name</td>
            <td><input name="user_name" type="text" id="web2" value="<?php echo $row_settings['user_name']; ?>" disabled></td>
          </tr>
          <tr> 
            <td>Email</td>
            <td><input name="user_email" type="text" id="web3"  value="<?php echo $row_settings['user_email']; ?>" disabled></td>
          </tr>
        </table>
        <p align="center"> 
          <input name="doSave" type="submit" class="buttons"  id="doSave" value="Save">
        </p>
      </form>
	  <?php } ?>
      <h3 class="titlehdr">Change Password</h3>
      <p>If you want to change your password, please input your old and new password 
        to make changes.</p>
      <form name="pform" id="pform" method="post" action="">
        <table width="80%" border="0" align="center" cellpadding="3" cellspacing="3" class="forms">
          <tr> 
            <td width="31%">Old Password</td>
            <td width="69%"><input name="pwd_old" type="password" class="required password"  id="pwd_old"></td>
          </tr>
          <tr> 
            <td>New Password</td>
            <td><input name="pwd_new" type="password" id="pwd_new" class="required password"  ></td>
          </tr>
        </table>
        <p align="center"> 
          <input name="doUpdate" type="submit" class="buttons" id="doUpdate" value="Update">
        </p>
      </form>
	   
      <p align="right">&nbsp; </p></td>


    <td width="196" valign="top">&nbsp;</td>



  </tr>
</table>

</body>
</html>
