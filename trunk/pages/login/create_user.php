<?php
include 'dbc.php';
page_protect();

if(!checkAdmin()) {
header("Location: login.php?p=login");
exit();
}

$page_limit = 10;


$host  = $_SERVER['HTTP_HOST'];
$host_upper = strtoupper($host);
$login_path = @ereg_replace('admin','',dirname($_SERVER['PHP_SELF']));
$path   = rtrim($login_path, '/\\');

// filter GET values
foreach($_GET as $key => $value) {
	$get[$key] = filter($value);
}

foreach($_POST as $key => $value) {
	$post[$key] = filter($value);
}

if($post['doBan'] == 'Ban') {

if(!empty($_POST['u'])) {
	foreach ($_POST['u'] as $uid) {
		$id = filter($uid);
		mysql_query("update users set banned='1' where id='$id' and `user_name` <> 'admin'");
	}
 }
 $ret = $_SERVER['PHP_SELF'] . '?'.$_POST['query_str'];;

 header("Location: $ret");
 exit();
}

if($_POST['doUnban'] == 'Unban') {

if(!empty($_POST['u'])) {
	foreach ($_POST['u'] as $uid) {
		$id = filter($uid);
		mysql_query("update users set banned='0' where id='$id'");
	}
 }
 $ret = $_SERVER['PHP_SELF'] . '?'.$_POST['query_str'];;

 header("Location: $ret");
 exit();
}

if($_POST['doDelete'] == 'Delete') {

if(!empty($_POST['u'])) {
	foreach ($_POST['u'] as $uid) {
		$id = filter($uid);
		mysql_query("delete from users where id='$id' and `user_name` <> 'admin'");
	}
 }
 $ret = $_SERVER['PHP_SELF'] . '?'.$_POST['query_str'];;

 header("Location: $ret");
 exit();
}

if($_POST['doApprove'] == 'Approve') {

if(!empty($_POST['u'])) {
	foreach ($_POST['u'] as $uid) {
		$id = filter($uid);
		mysql_query("update users set approved='1' where id='$id'");

	list($to_email) = mysql_fetch_row(mysql_query("select user_email from users where id='$uid'"));

$message =
"Hello,\n
Thank you for registering with us. Your account has been activated...\n

*****LOGIN LINK*****\n
http://$host$path/login.php

Thank You

Administrator
$host_upper
______________________________________________________
THIS IS AN AUTOMATED RESPONSE.
***DO NOT RESPOND TO THIS EMAIL****
";

@mail($to_email, "User Activation", $message,
    "From: \"Member Registration\" <auto-reply@$host>\r\n" .
     "X-Mailer: PHP/" . phpversion());

	}
 }

 $ret = $_SERVER['PHP_SELF']. '?'.$_POST['query_str'];
 header("Location: $ret");
 exit();
}

$rs_all = mysql_query("select count(*) as total_all from users") or die(mysql_error());
$rs_active = mysql_query("select count(*) as total_active from users where approved='1'") or die(mysql_error());
$rs_total_pending = mysql_query("select count(*) as tot from users where approved='0'");

list($total_pending) = mysql_fetch_row($rs_total_pending);
list($all) = mysql_fetch_row($rs_all);
list($active) = mysql_fetch_row($rs_active);


?>
<html>
<head>
<title>Administration Main Page</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="./pages/login/css/styles.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="./pages/login/css/layout.css" />
<script language="JavaScript" type="text/javascript" src="./pages/login/js/jquery-1.3.2.min.js"></script>
<script type="text/javascript">

<!--

function validate_form ( )
{
	valid = true;

        if ( document.contact_form.full_name.value == "" )
        {
                alert ( "Enter the Full Name. " );
                valid = false;
        }

        if ( document.contact_form.user_name.value == "" )
        {
                alert ( "Enter a User ID. " );
                valid = false;
        }

        if ( document.contact_form.user_email.value == "" )
        {
                alert ( "Enter a User Email. " );
                valid = false;
        }


return valid;
}

//-->

</script>
</head>
<?php include "./pages/login/includes/header.php"; ?>
<script type="text/javascript" src="./pages/login/js/quickmenu.js"></script> 
<body>
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="main">
  <tr>

  </tr>

<tr>
 <td width="19.2%" valign="top">


<?php include("sidebar.php");?>
	</td>

<td width="100%" valign="top" style="padding: 5px;">
<div id="page">



        <?php if ($get['doSearch'] == 'Search') {
	  $cond = '';
	  if($get['qoption'] == 'pending') {
	  $cond = "where `approved`='0' order by date desc";
	  }
	  if($get['qoption'] == 'recent') {
	  $cond = "order by date desc";
	  }
	  if($get['qoption'] == 'banned') {
	  $cond = "where `banned`='1' order by date desc";
	  }

	  if($get['q'] == '') {
	  $sql = "select * from users $cond";
	  }
	  else {
	  $sql = "select * from users where `user_email` = '$_REQUEST[q]' or `user_name`='$_REQUEST[q]' ";
	  }


	  $rs_total = mysql_query($sql) or die(mysql_error());
	  $total = mysql_num_rows($rs_total);

	  if (!isset($_GET['page']) )
		{ $start=0; } else
		{ $start = ($_GET['page'] - 1) * $page_limit; }

	  $rs_results = mysql_query($sql . " limit $start,$page_limit") or die(mysql_error());
	  $total_pages = ceil($total/$page_limit);

	  ?>
      <p>Approve -&gt; A notification email will be sent to user notifying activation.<br>
        Ban -&gt; No notification email will be sent to the user.
      <p><strong>*Note: </strong>Once the user is banned, he/she will never be
        able to register new account with same email address.
      <p align="right">
        <?php

	  // outputting the pages
		if ($total > $page_limit)
		{
		echo "<div><strong>Pages:</strong> ";
		$i = 0;
		while ($i < $page_limit)
		{


		$page_no = $i+1;
		$qstr = ereg_replace("&page=[0-9]+","",$_SERVER['QUERY_STRING']);
		echo "<a href=\"login.php?p=admin.php?$qstr&page=$page_no\">$page_no</a> ";
		$i++;
		}
		echo "</div>";
		}  ?>




	  <?php } ?>

	  <?php
	  if($_POST['doSubmit'] == 'Create')
{
$rs_dup = mysql_query("select count(*) as total from users where user_name='$post[user_name]' OR user_email='$post[user_email]'") or die(mysql_error());
list($dups) = mysql_fetch_row($rs_dup);

if($dups > 0) {
	die("The user name or email already exists in the system");
	}

if(!empty($_POST['pwd'])) {
  $pwd = $post['pwd'];
  $hash = PwdHash($post['pwd']);
 }
 else
 {
  $pwd = GenPwd();
  $hash = PwdHash($pwd);

 }

mysql_query("INSERT INTO users (`full_name`,`user_name`,`user_email`,`pwd`,`approved`,`date`,`user_level`)
			 VALUES ('$post[full_name]','$post[user_name]','$post[user_email]','$hash','1',now(),'$post[user_level]')
			 ") or die(mysql_error());


$message =
"Thank you for registering with us. Here are your login details...\n
User Email: $post[user_email] \n
Passwd: $pwd \n

*****LOGIN LINK*****\n
http://$host$path/hrm/login.php?p=login

Thank You

Administrator
$host_upper
______________________________________________________
THIS IS AN AUTOMATED RESPONSE.
***DO NOT RESPOND TO THIS EMAIL****
";

if($_POST['send'] == '1') {

	mail($post['user_email'], "Login Details", $message,
    "From: \"Member Registration\" <auto-reply@$host>\r\n" .
     "X-Mailer: PHP/" . phpversion());
 }
echo "<div class=\"msg\">User created with password $pwd....done.</div>";
}

	  ?>


                <fieldset class="bc">
 <legend><a onmouseover="set_CSS('a1','color','#33FFFF')" class="ac" id="a1" href=""><h4>Create New User</h4></a></legend>






          <form name="contact_form" method="post" action="" ENCTYPE="multipart/form-data" onSubmit="return validate_form ( );">

             <p>Name
                <input name="full_name" type="text" id="full_name">
                (Type the Full Name)</p>

              <p>User ID
                <input name="user_name" type="text" id="user_name">
                (Type the username)</p>
              <p>Email
                <input name="user_email" type="text" id="user_email">
              </p>
              <p>User Level
                <select name="user_level" id="user_level">
                  <option value="4">User</option>
                  <option value="0">Guest</option>
                  <option value="5">Admin</option>
                </select>
              </p>
              <p>Password
                <input name="pwd" type="password" id="pwd">
                (if empty a password will be auto generated)</p>
              <p>


                <input name="send" type="checkbox" id="send" value="1" checked>
                Send Email</p>
              <p>
                <input name="doSubmit" type="submit" class="buttons" id="doSubmit" value="Create">
              </p>
            </form>
            <p>**All created users will be approved by default.</p></td>
        </tr>
      </table>
      <p>&nbsp;</p>

      </td>
    <td width="12%">&nbsp</td>
  </tr>
</table>
<div id="footer">
		<p>&copy; 2011.</p>
	</div>
</body>
</html>


