<?php # Script 2.4 - index.php

/* 
 *	This is the main page.
 *	This page includes the configuration file, 
 *	the templates, and any content-specific modules.
 */

// Require the configuration file before any PHP code:
require_once ('./includes/config.inc.php');

// Validate what page to show:
if (isset($_GET['p'])) {
	$p = $_GET['p'];
} elseif (isset($_POST['p'])) { // Forms
	$p = $_POST['p'];
} else {
	$p = NULL;
}

// Determine what page to display:
switch ($p) {
	
		case 'index':
		$page = 'index.php';
		$page_title = 'Login';
		break;

		case 'number':
		$page = 'numbers.php';
		$page_title = 'Allowed Numbers';
		break;

		case 'admin':
		$page = 'admin.php';
		$page_title = 'Administrator';
		break;


		case 'login':
		$page = 'login.php';
		$page_title = 'Login';
		break;

		case 'do':
		$page = 'do.php';
		$page_title = 'Registration Successful';
		break;

		case 'myaccount':
		$page = 'myaccount.php';
		$page_title = 'Successfully Login';
		break;

		case 'mysettings':
		$page = 'mysettings.php';
		$page_title = 'Successfully Login';
		break;

		case 'register':
		$page = 'register.php';
		$page_title = 'Registration Successful';
		break;

		case 'forgot':
		$page = 'forgot.php';
		$page_title = 'Registration Successful';
		break;

		case 'logout':
		$page = 'logout.php';
		$page_title = 'Logout';
		break;




           case 'create_users':
		$page = 'create_user.php';
		$page_title = 'Registration Successful';
		break;




	// Default is to include the main page.
	default:
		$page = 'index.php';
		$page_title = 'Home Page';
		break;
		

		

} // End of main switch.

// Make sure the file exists:
if (!file_exists('./pages/login/' . $page)) {
	$page = 'index.php';
	$page_title = 'Site Home Page';
}

// Include the header file:

// Include the content-specific module:
// $page is determined from the above switch.
include ('./pages/login/' . $page);

// Include the footer file to complete the template:

?>
