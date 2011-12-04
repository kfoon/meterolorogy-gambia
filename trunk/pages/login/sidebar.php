<?php
$total_row = "select * from patient_information ";
$results = mysql_query($total_row);
$num = mysql_num_rows($results);
/*********************** MYACCOUNT MENU ****************************
This code shows my account menu only to logged in users.
Copy this code till END and place it in a new html or php where
you want to show myaccount options. This is only visible to logged in users
*******************************************************************/
if (isset($_SESSION['user_id'])) {?>
<div class="myaccount">
<div style=" height: auto; margin-left:-6px; overflow: hidden;" id="currentImage">
    <center>
        <a href="personal.php?p=upload_photo">
            <img width="65" height="50" align="left" id="empPic" src="./images/default.gif" alt="Employee Photo" value="" visibility="visible" style="border: 3px solid #EEE;">
        </a>
        <span class="smallHelpText"><strong><a href="#"><h4><?php echo $_SESSION['user_name'];?></h4></a></strong></span>
        <span class="smallHelpText"><strong><a href="login.php?p=mysettings&amp;id=11"><h4>Settings</h4></a></strong></span>

</center>



</div>
<h3>Menu</h3>
  <table width="100%" border="0" style="margin-left:-10px"><tbody><tr><td width="0%"><img src="src/1.png"></td><td width="60%"><a href="login.php?p=myaccount">Home</a></td><td width="30%" style="text-align:center"><div></div> <?php
if (checkAdmin()) {
/*******************************END**************************/
?>
<?php echo $num;?>
<?php } ?>
</td></tr></tbody></table>
  <table width="100%" border="0" style="margin-left:-10px"><tbody><tr><td width="0%"><img src="src/7.jpg"></td><td width="60%"><a href="login.php?p=logout">Log Off / Exit</a></td><td width="30%"><div></div></td></tr></tbody></table>

  </div>
  <p></p>
<?php }
if (checkAdmin()) {
/*******************************END**************************/
?>
<p> <a href="login.php?p=admin">User Management</a><br>
<p> <a href="login.php?p=number">Add Numbers</a><br>



      </p>

       
	  <?php } ?>
