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
http://$host$path/login.php?p=login

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
 
 $ret = $_SERVER['PHP_SELF'] . '?'.$_POST['query_str'];	 
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
	</td>

<td width="100%" valign="top" style="padding: 5px;">
<div id="page">

<fieldset class="bc">
 <legend><a onmouseover="set_CSS('a1','color','#33FFFF')" class="ac" id="a1" href=""><h4>Administration Page</h4></a></legend>
      <table width="100%" border="0" cellpadding="5" cellspacing="0" class="myaccount1">
        <tr>
          <td>Total users: <?php echo $all;?></td>
          <td>Active users: <?php echo $active; ?></td>
          <td>Pending users: <?php echo $total_pending; ?></td>
        </tr>

      </table>
      <p>
<?php 
	  if(!empty($msg)) {
	  echo $msg[0];
	  }
	  ?>
</p>


      <table width="80%" border="0" align="center" cellpadding="10" cellspacing="0" style="background-color: #ffffff;padding: 2px 5px;border: 1px solid #ccc;" >
        <tr>

          <td><form name="form1" method="get" action="search.php?q=admin">
              <p align="center">Search
                <input name="q" type="text" id="q" size="40">
                <br>
                [Type email or user name] </p>
              <p align="center">
                <input type="radio" name="qoption" value="pending">
                Pending users
                <input type="radio" name="qoption" value="recent">
                Recently registered
                <input type="radio" name="qoption"  value="banned">
                Banned users <br>
                <br>
                [You can leave search blank to if you use above options]</p>
              <p align="center">
                <input name="doSearch" type="submit" class="buttons" id="doSearch2" value="Search">
              </p>
              </form></td>
        </tr>
      </table>
      <p>
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
		</p>

		<form name "searchform" action="search.php?p=admin" method="post">

   <table width=100%" border="0" align="center" cellpadding="2" cellspacing="0" class="data-table">
          <tr class="odd">
            <td width="4%"><strong>ID</strong></td>
            <td> <strong>Date</strong></td>
            <td><div align="center"><strong>User Name</strong></div></td>
            <td width="24%"><strong>Email</strong></td>
            <td width="10%"><strong>Approval</strong></td>
            <td width="10%"> <strong>Banned</strong></td>
            <td width="25%">&nbsp;</td>
          </tr>
          <tr> 
            <td>&nbsp;</td>
            <td width="10%">&nbsp;</td>
            <td width="17%"><div align="center"></div></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <?php while ($rrows = mysql_fetch_array($rs_results)) {?>
          <tr>
            <td><input name="u[]" type="checkbox" value="<?php echo $rrows['id']; ?>" id="u[]"></td>
            <td><strong><?php echo $rrows['date']; ?></strong></td>
            <td><strong><div align="center"><?php echo $rrows['user_name'];?></strong></div></td>
            <td><strong><?php echo $rrows['user_email']; ?></strong></td>
            <td><strong><span id="approve<?php echo $rrows['id']; ?>" >
              <?php if(!$rrows['approved']) { echo "Pending"; } else {echo "Active"; }?>
              </span></strong></td>
            <td><strong><span id="ban<?php echo $rrows['id']; ?>">
              <?php if(!$rrows['banned']) { echo "no"; } else {echo "yes"; }?>
              </span></strong></td>
            <td style="background:white"> <font size="2"><a href="javascript:void(0);" onclick='$.get("login.php?p=do",{ cmd: "approve", id: "<?php echo $rrows['id']; ?>" } ,function(data){ $("#approve<?php echo $rrows['id']; ?>").html(data); });'><input type="button" value="Approve" class="buttons"></a>
              <a href="javascript:void(0);" onclick='$.get("login.php?p=do",{ cmd: "ban", id: "<?php echo $rrows['id']; ?>" } ,function(data){ $("#ban<?php echo $rrows['id']; ?>").html(data); });'><input type="button" value="Ban" class="buttons"></a>
              <a href="javascript:void(0);" onclick='$.get("login.php?p=do",{ cmd: "unban", id: "<?php echo $rrows['id']; ?>" } ,function(data){ $("#ban<?php echo $rrows['id']; ?>").html(data); });'><input type="button" value="Unban" class="buttons"></a>
              <a href="javascript:void(0);" onclick='$("#edit<?php echo $rrows['id'];?>").show("slow");'><input type="button" value="Edit" class="buttons"></a>
              </font> </td>
          </tr>
          <tr> 
            <td colspan="7" style="background:white"; padding:"10px">
			
			<div style="display:none;font: normal 11px arial; padding:10px; background: #fff" id="edit<?php echo $rrows['id']; ?>">
			
			<input type="hidden" name="id<?php echo $rrows['id']; ?>" id="id<?php echo $rrows['id']; ?>" value="<?php echo $rrows['id']; ?>">
			User Name: <input name="user_name<?php echo $rrows['id']; ?>" id="user_name<?php echo $rrows['id']; ?>" type="text" size="10" value="<?php echo $rrows['user_name']; ?>" >
			User Email:<input id="user_email<?php echo $rrows['id']; ?>" name="user_email<?php echo $rrows['id']; ?>" type="text" size="20" value="<?php echo $rrows['user_email']; ?>" >
			Level: <input id="user_level<?php echo $rrows['id']; ?>" name="user_level<?php echo $rrows['id']; ?>" type="text" size="5" value="<?php echo $rrows['user_level']; ?>" > 1->user,5->admin
			<br><br>New Password: <input id="pass<?php echo $rrows['id']; ?>" name="pass<?php echo $rrows['id']; ?>" type="text" size="20" value="" > (leave blank)
			<input name="doSave" type="button" class="buttons" id="doSave" value="Save"
			onclick='$.get("login.php?p=do",{ cmd: "edit", pass:$("input#pass<?php echo $rrows['id']; ?>").val(),user_level:$("input#user_level<?php echo $rrows['id']; ?>").val(),user_email:$("input#user_email<?php echo $rrows['id']; ?>").val(),user_name: $("input#user_name<?php echo $rrows['id']; ?>").val(),id: $("input#id<?php echo $rrows['id']; ?>").val() } ,function(data){ $("#msg<?php echo $rrows['id']; ?>").html(data); });'> 
			<a  onclick='$("#edit<?php echo $rrows['id'];?>").hide();' href="javascript:void(0);"><input type="button" value="close" class="buttons"></a>
		 
		  <div style="color:red" id="msg<?php echo $rrows['id']; ?>" name="msg<?php echo $rrows['id']; ?>"></div>
		  </div>
		  
		  </td>
          </tr>
          <?php } ?>
        </table>
	    <p><br>
          <input name="doApprove" type="submit" class="buttons"  id="doApprove" value="Approve">
          <input name="doBan" type="submit" class="buttons" id="doBan" value="Ban">
          <input name="doUnban" type="submit" class="buttons" id="doUnban" value="Unban">
          <input name="doDelete" type="submit" class="buttons" id="doDelete" value="Delete">
          <input name="query_str" type="hidden" id="query_str" value="<?php echo $_SERVER['QUERY_STRING']; ?>">
          <strong>Note:</strong> If you delete the user can register again, instead 
          ban the user. </p>
        <p><strong>Edit Users:</strong> To change email, user name or password, 
          you have to delete user first and create new one with same email and 
          user name.</p>
      </form>
	  
	  <?php } ?>
      &nbsp;</p>
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

mysql_query("INSERT INTO users (`user_name`,`user_email`,`pwd`,`approved`,`date`,`user_level`)
			 VALUES ('$post[user_name]','$post[user_email]','$hash','1',now(),'$post[user_level]')
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





	  






      </table>
      <p>&nbsp;</p>

      </td>
    <td width="12%">&nbsp</td>
  </tr>
</table>
   <div id="footer">
	     <p>&copy; <?php echo date("20y") ;?> · All rights reserved.</p>
	</div>
</body>
</html>
