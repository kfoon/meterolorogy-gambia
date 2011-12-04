<?php
/*************** PHP LOGIN SCRIPT V 2.3*********************
(c) Balakrishnan 2009. All Rights Reserved

Usage: This script can be used FREE of charge for any commercial or personal projects. Enjoy!

Limitations:
- This script cannot be sold.
- This script should have copyright notice intact. Dont remove it please...
- This script may not be provided for download except from its original site.

For further usage, please contact me.

***********************************************************/
include 'dbc.php';




//reset
/******************* ACTIVATION BY FORM**************************/
if ($_POST['doReset']=='Reset')
{
$err = array();
$msg = array();

foreach($_POST as $key => $value) {
	$data[$key] = filter($value);
}
if(!isEmail($data['user_email'])) {
$err[] = "ERROR - Please enter a valid email";
}

$user_email = $data['user_email'];

//check if activ code and user is valid as precaution
$rs_check = mysql_query("select id from users where user_email='$user_email'") or die (mysql_error());
$num = mysql_num_rows($rs_check);
  // Match row found with more than 1 results  - the user is authenticated.
    if ( $num <= 0 ) {
	$err[] = "Error - Sorry no such account exists or registered.";
	//header("Location: forgot.php?msg=$msg");
	//exit();
	}


if(empty($err)) {

$new_pwd = GenPwd();
$pwd_reset = PwdHash($new_pwd);
//$sha1_new = sha1($new);
//set update sha1 of new password + salt
$rs_activ = mysql_query("update users set pwd='$pwd_reset' WHERE
						 user_email='$user_email'") or die(mysql_error());

$host  = $_SERVER['HTTP_HOST'];
$host_upper = strtoupper($host);

//send email

$message =
"Here are your new password details ...\n
User Email: $user_email \n
Passwd: $new_pwd \n

Thank You

Administrator
$host_upper
______________________________________________________
THIS IS AN AUTOMATED RESPONSE.
***DO NOT RESPOND TO THIS EMAIL****
";

	mail($user_email, "Reset Password", $message,
    "From: \"Member Registration\" <auto-reply@$host>\r\n" .
     "X-Mailer: PHP/" . phpversion());

$msg[] = "Your account password has been reset and a new password has been sent to your email address.";

//$msg = urlencode();
//header("Location: forgot.php?msg=$msg");
//exit();
 }
}
//end reset




$err = array();

foreach($_GET as $key => $value) {
	$get[$key] = filter($value); //get variables are filtered.
}

if ($_POST['doLogin']=='Login')
{

foreach($_POST as $key => $value) {
	$data[$key] = filter($value); // post variables are filtered
}


$user_email = $data['usr_email'];
$pass = $data['pwd'];


if (strpos($user_email,'@') === false) {
    $user_cond = "user_name='$user_email'";
} else {
      $user_cond = "user_email='$user_email'";

}


$result = mysql_query("SELECT `id`,`pwd`,`full_name`,`approved`,`user_level` FROM users WHERE
           $user_cond
			AND `banned` = '0'
			") or die (mysql_error());
$num = mysql_num_rows($result);

  // Match row found with more than 1 results  - the user is authenticated.
    if ( $num > 0 ) {

	list($id,$pwd,$full_name,$approved,$user_level) = mysql_fetch_row($result);

	if(!$approved) {
	//$msg = urlencode("Account not activated. Please check your email for activation code");
	$err[] = "Account not activated. Please check your email for activation code";

	//header("Location: login.php?msg=$msg");
	 //exit();
	 }

		//check against salt
	if ($pwd === PwdHash($pass,substr($pwd,0,9))) {
	if(empty($err)){

     // this sets session and logs user in
       session_start();
	   session_regenerate_id (true); //prevent against session fixation attacks.

	   // this sets variables in the session
		$_SESSION['user_id']= $id;
		$_SESSION['user_name'] = $full_name;
		$_SESSION['user_level'] = $user_level;
		$_SESSION['HTTP_USER_AGENT'] = md5($_SERVER['HTTP_USER_AGENT']);

		//update the timestamp and key for cookie
		$stamp = time();
		$ckey = GenKey();
		mysql_query("update users set `ctime`='$stamp', `ckey` = '$ckey' where id='$id'") or die(mysql_error());

		//set a cookie

	   if(isset($_POST['remember'])){
				  setcookie("user_id", $_SESSION['user_id'], time()+60*60*24*COOKIE_TIME_OUT, "/");
				  setcookie("user_key", sha1($ckey), time()+60*60*24*COOKIE_TIME_OUT, "/");
				  setcookie("user_name",$_SESSION['user_name'], time()+60*60*24*COOKIE_TIME_OUT, "/");
				   }
		  header("Location: login.php?p=myaccount");
		 }
		}
		else
		{
		//$msg = urlencode("Invalid Login. Please try again with correct user email and password. ");
		$err[] = "<h2>Please re-enter your password</h2> <p id=\"standard_explanation\" class=\"sub_message\"></p><p>
The password you entered is incorrect. Please try again (make sure your caps lock is off).
<p>Forgot your password? <a href=\"login.php?p=forgot\" style=\"color: #DD3C10;\">Request a new one.</a>";
		//header("Location: login.php?msg=$msg");
		}
	} else {
		$err[] = "<h2>Incorrect Username</h2> <p id=\"standard_explanation\" class=\"sub_message\"></p><p>
The username you entered does not belong to any account. You may try clearing your browser's cache and cookies by following <a href=\"#\" style=\"color: #DD3C10;\"> these instructions.</a>
<p>You can log in using any email address or username associated with your account. Make sure that it is typed correctly.";
	  }
}



?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <meta http-equiv="X-UA-Compatible" content="chrome=1" />
  <meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="shortcut icon" href="/favicon.ico" type="image/ico"/>


<meta name="language" content="en"/>
<meta name="revisit-after" content="5 days"/>



<link href="./pages/login/css/common.css" rel="stylesheet" type="text/css">
<link href="./pages/login/css/dlgbox.css" rel="stylesheet" type="text/css">



<script src="./pages/login/js/idselector.js" type="text/javascript"></script>
  <script src="./pages/login/js/en.js" type="text/javascript"></script>
    <script src="./pages/login/js/core.js" type="text/javascript"></script>


  
</head>
<body class="b-win b-ff
   l-en">
<script type="text/javascript">
    var ServerData = {
        user: {
            date_format: "%m/%D/%Y",
            hour_format: "1",
            language: "en"


        },
        googleKey: "ABQIAAAANAjdheoRrTQyuDVfCwn9LRRbLcAhCJoFqgPTclaDpTJ8NW7p8hQxek1WzVKy_FKeyk8-sOdRfC89vg",
        cacheBuster: 19286
    };

    


</script>

<div class="loggedout_menubar_container">
<div class="clearfix loggedout_menubar">



<div class="p40">
    <div class="dialog center">
  


  <div class="close" onclick="window.location.href = '#'"></div>
  <div class="dialog_content">
      

<style type="text/css">
    .flyout.selected {
        box-shadow: none;
    }
</style>


    <div id="panelErrorMsg">

       <?php
	  /******************** ERROR MESSAGES*************************************************
	  This code is to show error messages
	  **************************************************************************/
	  if(!empty($err))  {
	   echo "<div class=\"msg\">";
	  foreach ($err as $e) {
	    echo "$e <br>";
	    }
	  echo "</div>";
	   }
	  /******************************* END ********************************/
	  ?>


</div>
    <div style="margin: 5px 0; text-align: left; clear:both;">
<h2 class="button default">Provide your Authentication details to access the database...... !</h2>
    <br class="clear">


    </div>

<div id="logins">

  <form action="login.php?p=login" id="signin_standard" method="post" name="signin_standard" onsubmit="MM.ui.disableButton('btn_signin');">

      <div class="content" style="height:110px;">
        <p>
          <label for="login" class="large" style="color:#10334D;line-height:24px;font-size:15px">Email or Username</label>
          <input id="login" name="usr_email" style="width: 200px;" tabindex="1" type="text" value="" />
        </p>

        <p>
          <label for="password" class="large" style="color:#10334D;line-height:24px;font-size:15px">Password</label>
          <input id="pwd" name="pwd" style="width: 200px;" tabindex="2" type="password" value="" />
        </p>

        <p style="margin-bottom: 5px">

              <a href="login.php?p=forgot" class="indented large" onclick="return MM.ui.dialogs.showDialog('forgot_password', {controller:'account',height:250, width:500});">I forgot my password</a>


        </p>

        <p class="indented large">
          <input class="checkbox" id="remember_me" name="remember_me" type="checkbox" value="1" />
          <label for="remember_me" class="checkbox">Remember me on this computer</label>
        </p>



        <div style="width:0; height:0; overflow: ;">
         <p></p> <input name="doLogin" type="submit" value="Login" class="button default" id="btnLogin" style="font-size:17px; float:left" tabindex="3"  />



        </div>
      </div>
  </form>

  <form action="" id="signin_openid" method="post" name="signin_openid" onsubmit="MM.ui.disableButton('btn_signin');" style="display:none">

      <div class="content" style="height:110px;padding-top:10px">
        <p style="text-align:center">
          <img alt="Openid" src="pages/login/images/openid.png" />
        </p>

        <p>
          <label for="openid_url" style="color:#10334D;line-height:24px;font-size:15px">OpenID</label>
          <input id="openid_identifier" name="openid_identifier" style="width: 240px;" type="text" />
        </p>

        <p class="indented large">
          <input class="checkbox" id="remember_me_openid" name="remember_me_openid" type="checkbox" value="1" />
          <label for="remember_me_openid" class="checkbox">Remember me on this computer</label>

        </p>

        <div style="width:0; height:0; overflow: hidden;">
          <button type="submit" >&nbsp;</button>

        </div>
      </div>
  </form>

      <form action="login.php?p=pass_reset" id="signin_gmail" method="post" name="signin_gmail" onsubmit="MM.ui.disableButton('btn_signin');" style="display:none">

          <div class="content" style="height:110px;">

            <p style="text-align:center">
              <img alt="Reset Password" src="pages/login/images/gmail.gif" />
            </p>

            <p>
              <label for="gmail" class="large" style="color:#10334D;line-height:24px;font-size:15px">Email Address</label>
              <input id="gmail" name="user_email" style="width: 205px;" type="text" />
              <span class="postfix"><input name="doReset" type="submit" value="Reset" class="button default" id="btnLogin" style="margin:-3px;font-size:17px; float:right" tabindex="3"  /></span>

            </p>

          </div>

          <div style="width:0; height:0; overflow: hidden;">
            <button type="submit">&nbsp;</button>
          </div>
      </form>


      <form action="#" id="signin_gapps" method="post" name="signin_gapps" onsubmit="MM.ui.disableButton('btn_signin'); " style="display:none">


          <div class="content" style="height:110px;">
            <p style="text-align:center">
              <img alt="Gapps" src="pages/login/images/gapps.png" />
            </p>

            <p>
              <label for="gmail" class="large" style="color:#10334D;line-height:24px;font-size:15px;width:150px">Google Apps domain</label>
              <span>www.</span>

              <input id="domain" name="domain" style="width:180px" type="text" />
            </p>
          </div>

          <div style="width:0; height:0; overflow: hidden;">
            <button type="submit">&nbsp;</button>
          </div>
      </form>
  
</div>

<a href="#" class="flyout" id="btn_logintype" style="position:right; bottom: 0; left: 10px; border:none; color:#10334D;background: transparent url('pages/login/images/common-48.png') no-repeat  -12px -865px; width:35px;height:35px">
  <em style="background-position: 20px -700px;width:35px;height:35px"></em>
</a>
<div id="popover_logintype" class="popover menu above icons large" style="display:none"></div>



<script type="text/javascript">
    Object.extend(
        MM.ui, {

        buildLoginPopover: function() {
            MM.ui.logintypePopover = new Popover('logintype');
            var elements = [];
            elements.push((new Element('a', {href: '#', id: 'standard_login', onclick: 'return MM.ui.switchLoginType("standard")'})).update('Login'));
            //elements.push((new Element('a', {href: '#', id: 'unilogin_login', onclick: 'return MM.ui.switchLoginType("unilogin")'})).update('Unilogin'));

            elements.push((new Element('a', {href: '#', id: 'gmail_login', onclick: 'return MM.ui.switchLoginType("gmail", "gmail")'})).update('Reset Password'));
            //elements.push((new Element('a', {href: '#', id: 'gapps_login', onclick: 'return MM.ui.switchLoginType("gapps")'})).update('Google Apps'));

            //elements.push((new Element('a', {href: '#', id: 'openid_login', onclick: 'return MM.ui.switchLoginType("openid")'})).update('OpenID'));
            MM.ui.logintypePopover.addElements(elements);
        },

        switchLoginType: function(login_el, focus_el) {
            if (login_el == 'unilogin') {
                window.location.href = '#';
                return;
            }
            $('logins').childElements().each(function(el) {
                el.hide();
            });
            top.loginType = login_el;
            $('signin_' + login_el).show();
            if (focus_el) $(focus_el).activate();
            if (login_el == 'openid' && !document.getElementById('__idselector_button'))
                MM.utils.loadScript("/javascripts/tools/idselector.js", function() {
                    gen_selector();
                });
            return false;
        }
    });

    MM.ui.switchLoginType("standard", "login");
    MM.ui.buildLoginPopover();
</script>





</div></div>
</div>

</div>

</body>
</html>


