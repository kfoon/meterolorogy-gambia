<?php # Script 2.4 - index.php

/*
 *	This is the main page.
 *	This page includes the configuration file,
 *	the templates, and any content-specific modules.
 */

// Require the configuration file before any PHP code:
require_once ('./includes/config.inc.php');

// Validate what page to show:
if (isset($_GET['q'])) {
	$q = $_GET['q'];
} elseif (isset($_POST['q'])) { // Forms
	$q = $_POST['q'];
} else {
	$q = NULL;
}

// Determine what page to display:
switch ($q) {

		case 'search':
		$page = 'admin.php';
		$page_title = 'Login';
		break;

    


	// Default is to include the main page.
	default:
		$page = 'admin.php';
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

