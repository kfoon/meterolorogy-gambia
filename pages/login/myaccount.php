<?php
include 'dbc.php';
page_protect();
$id = $_GET['id'];
$data_info = mysql_query("select * from data where id = $id");
$data_row = @mysql_fetch_array($data_info);
?>

<html>
<head>
<title>My Account</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="refresh" content="60; url=login.php?p=myaccount">

<link href="./pages/login/css/styles.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="./pages/login/css/layout.css" />
<link rel="shortcut icon" type="image/ico" <script language="JavaScript" src="./includes/js/jquery-1.3.2.min.js"></script>
</head>

<body>


<?php include "./pages/login/includes/header.php"; ?>
<script type="text/javascript" src="./pages/login/js/quickmenu.js"></script>


<table width="100%" border="0" cellspacing="0" cellpadding="5" class="main">
  <tr>

  </tr>

<tr>
 <td width="19.8%" valign="top">

<?php include("sidebar.php");?>


      </td>
    <td width="100%" valign="top">

<div id="page">

 <h3 class="titlehdr">Welcome <?php echo $_SESSION['user_name'];?></h3>


	  <?php
      if (isset($_GET['msg'])) {
	  echo "<div class=\"error\">$_GET[msg]</div>";
	  }

	  ?>
      <p>

<div id="">
 <form action="login.php?p=mysettings" method="post" name="myform" id="myform">
        <table width="90%" border="0" align="center" cellpadding="3" cellspacing="3" class="">


          <tr>
            <td width="27%">Phone Number</td>
            <td width="73%"><input name="orgin" type="text"  class="required"  value="<?php echo $data_row[orgin];?>"></td>
          </tr>
          <tr>
            <td>AFT Number</td>
            <td><input name="data" type="text" style="width:100%;" value="<?php echo $data_row[data];?>" ></td>
          </tr>

          <tr>
            <td width="27%">Time</td>
            <td width="73%"><input name="upd" type="text"  class="required"  value="<?php echo $data_row[upd];?>"></td>
          </tr>

        </table>

      <?php


	$tbl_name="data";		//your table name
	// How many adjacent pages should be shown on each side?
	$adjacents = 3;

	/*
	   First get total number of rows in data table.
	   If you have a WHERE clause in your query, make sure you mirror it here.
	*/
	$query = "SELECT COUNT(*) as num FROM $tbl_name order by id desc";
	$total_pages = mysql_fetch_array(mysql_query($query));
	$total_pages = @$total_pages[num];

	/* Setup vars for query. */
	$targetpage = "login.php?p=myaccount"; 	//your file name  (the name of this file)
	$limit = 10; 								//how many items to show per page
	$page = @$_GET['page'];
	if($page)
		$start = ($page - 1) * $limit; 			//first item to display on this page
	else
		$start = 0;								//if no page var is given, set start to 0

	/* Get data. */
	$sql = "SELECT * FROM $tbl_name order by id desc LIMIT $start, $limit";
	$result = mysql_query($sql);

	/* Setup page vars for display. */
	if ($page == 0) $page = 1;					//if no page var is given, default to 1.
	$prev = $page - 1;							//previous page is page - 1
	$next = $page + 1;							//next page is page + 1
	$lastpage = ceil($total_pages/$limit);		//lastpage is = total pages / items per page, rounded up.
	$lpm1 = $lastpage - 1;						//last page minus 1

	/*
		Now we apply our rules and draw the pagination object.
		We're actually saving the code to a variable in case we want to draw it more than once.
	*/
	$pagination = "";
	if($lastpage > 1)
	{
		$pagination .= "<div class=\"pagination\">";
		//previous button
		if ($page > 1)
			$pagination.= "<a href=\"$targetpage&page=$prev\">« previous</a>";
		else
			$pagination.= "<span class=\"disabled\">« previous</span>";

		//pages
		if ($lastpage < 7 + ($adjacents * 2))	//not enough pages to bother breaking it up
		{
			for ($counter = 1; $counter <= $lastpage; $counter++)
			{
				if ($counter == $page)
					$pagination.= "<span class=\"current\">$counter</span>";
				else
					$pagination.= "<a href=\"$targetpage&page=$counter\">$counter</a>";
			}
		}
		elseif($lastpage > 5 + ($adjacents * 2))	//enough pages to hide some
		{
			//close to beginning; only hide later pages
			if($page < 1 + ($adjacents * 2))
			{
				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
				{
					if ($counter == $page)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage&page=$counter\">$counter</a>";
				}
				$pagination.= "...";
				$pagination.= "<a href=\"$targetpage&page=$lpm1\">$lpm1</a>";
				$pagination.= "<a href=\"$targetpage&page=$lastpage\">$lastpage</a>";
			}
			//in middle; hide some front and some back
			elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
			{
				$pagination.= "<a href=\"$targetpage&page=1\">1</a>";
				$pagination.= "<a href=\"$targetpage&page=2\">2</a>";
				$pagination.= "...";
				for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
				{
					if ($counter == $page)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage&page=$counter\">$counter</a>";
				}
				$pagination.= "...";
				$pagination.= "<a href=\"$targetpage&page=$lpm1\">$lpm1</a>";
				$pagination.= "<a href=\"$targetpage&page=$lastpage\">$lastpage</a>";
			}
			//close to end; only hide early pages
			else
			{
				$pagination.= "<a href=\"$targetpage&page=1\">1</a>";
				$pagination.= "<a href=\"$targetpage&page=2\">2</a>";
				$pagination.= "...";
				for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
				{
					if ($counter == $page)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage&page=$counter\">$counter</a>";
				}
			}
		}

		//next button
		if ($page < $counter - 1)
			$pagination.= "<span class=\"prev\"><a href=\"$targetpage&page=$next\">next »</a>";

		else
			$pagination.= "<span class=\"disabled\">next »</span>";
		$pagination.= "</div>\n";

	}
?>




<?php

echo '<div id="tablePassport">';
echo '<div class="subHeading"><h3>AFT Lists</h3></div>';
echo '<div class="actionbar">';
echo '<table width="100%" cellspacing="0" cellpadding="0" class="data-table">';
echo '<thead>';
echo '<tr>';
echo '<td>ID</td>';
echo '<td>AFT Number</td>';
echo '<td>Telephone Number</td>';
echo '<td>Timestamp</td>';
echo '<td></td>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';
echo '<tr class="odd">';
while($row = mysql_fetch_array($result))
{

echo '<td>'. $row['id'].'</td> ';
echo '<td>'. $row['data'].'</a></td> ';
echo '<td>'. $row['orgin'].'</a></td> ';
echo '<td>'. $row['upd'].'</a></td> ';
echo '<td width="85px" style="background-color: #FFFFFF">'.'<a href="login.php?p=myaccount&id='.@$row[id].'"><input type="button" style="background-color: #7288B7; color:white; font-weight:bold"" class="view"  value="View"></a>'.'</td>';
echo '</tr>';
}
echo '</tbody>';
echo '</table>';
?>
 <?=$pagination?>
</div>
</div>

</form>

        </tr>
      </table>


      </td>
    <td width="12%">&nbsp</td>
  </tr>
</table>
<div id="footer">
		<p>&copy; <?php echo date("20y") ;?> · All rights reserved.</p>

	</div>
</body>
</html>
