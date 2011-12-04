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
	


		case 'add_products':
		$page = 'add_products.php';
		$page_title = 'Add Products';
		break;

        	case 'edit_contacts_processor':
		$page = 'edit_contacts_processor.php';
		$page_title = 'Edit Contacts Processor';
		break;
	
		case 'personal':
		$page = 'personal.php';
		$page_title = 'Personal Details';
		break;

                case 'products':
		$page = 'products.php';
		$page_title = 'products Details';
		break;


                case 'update_prescriptions':
		$page = 'update_prescriptions.php';
		$page_title = 'Update Prescriptions';
		break;

                case 'add_department':
		$page = 'add_department.php';
		$page_title = 'Add Department';
		break;

		case 'add_patient_processor':
		$page = 'add_patient_processor.php';
		$page_title = 'Add Patient';
		break;
		
            case 'patient_information_processor':
		$page = 'patient_information_processor.php';
		$page_title = 'Pation Information Processor';
		break;


	      case 'update_doctor':
		$page = 'update_doctor.php';
		$page_title = 'Update Doctor';
		break;

	      case 'add_doctor':
		$page = 'add_doctor.php';
		$page_title = 'Add Doctor';
		break;

	      case 'billing':
		$page = 'billing.php';
		$page_title = 'Billing Details';
		break;

	        case 'update_billing':
		$page = 'update_billing.php';
		$page_title = 'Update Billing';
		break;

	        case 'appointment_schedule':
		$page = 'appointment_schedule.php';
		$page_title = 'Schedule Appointment';
		break;

                case 'update_appointment_schedule':
		$page = 'update_appointment_schedule.php';
		$page_title = 'Update Appointment';
		break;

                case 'emergency_contacts':
		$page = 'emergency_contacts.php';
		$page_title = 'Emergency Contacts';
		break;


                case 'edit_emergency_contacts':
		$page = 'edit_emergency_contacts.php';
		$page_title = 'Edit Emergency Contacts';
		break;

                case 'delete':
		$page = 'delete.php';
		$page_title = 'DDelete Records';
		break;

                case 'add_insurance':
		$page = 'add_insurance.php';
		$page_title = 'Insurance Company Added Successfully.';
		break;

                case 'add_bank':
		$page = 'add_bank.php';
		$page_title = 'New Bank Added Successfully.';
		break;

                case 'pharmacy':
		$page = 'pharmacy.php';
		$page_title = 'New Bank Added Successfully.';
		break;



	// Default is to include the main page.
	default:
		$page = 'main.inc.php';
		$page_title = 'Home Page';
		break;
		
} // End of main switch.

// Make sure the file exists:
if (!file_exists('./pages/processors/' . $page)) {
	$page = 'main.inc.php';
	$page_title = 'Site Home Page';
}

// Include the header file:
include_once ('./includes/header.php');

// Include the content-specific module:
// $page is determined from the above switch.
include ('./pages/processors/' . $page);

// Include the footer file to complete the template:
include_once ('./includes/footer.php');

?>
