<?php

/**
 * Plugin Name: Custom Contact Us Plugin
 * Description: Handling Email Sending & Displaying Contact Submission Details - Developed By rapsum97.
 * Version: 1.0.0
 * Author: rapsum97
 * Author URI: https://www.fiverr.com/rapsum97
**/

/* Include Files */ 
function add_stylesheet() {
	wp_enqueue_script('jquery');
    wp_register_script("jqueryJS", plugins_url("/assets/js/jquery.min.js", __FILE__), array("jquery"));
    wp_enqueue_script("jqueryJS");
    wp_register_script("customJS", plugins_url("/assets/js/script.js", __FILE__), array("jquery"));
    wp_enqueue_script("customJS");
    wp_register_script("tableJS", plugins_url("/assets/js/dataTables.bootstrap4.min.js", __FILE__), array("jquery"));
    wp_enqueue_script("tableJS");
    wp_register_style("customCSS", plugins_url("/assets/css/customStyle.css", __FILE__));
    wp_enqueue_style("customCSS");   
    wp_register_style("datatableCSS", plugins_url("/assets/css/dataTables.bootstrap4.min.css", __FILE__));
    wp_enqueue_style("datatableCSS");
}
add_action("admin_print_styles", "add_stylesheet");

/* Plugin Menu and Submenu */
add_action("admin_menu", "ContactPlugin");
function ContactPlugin() {
	$menu = add_menu_page("Contact Submissions", "Contact Details", "manage_options", "contact-submissions", "contactSubmissionsFunction");
	$submenu1 = add_submenu_page("contact-submissions", "Contact Submissions", "Contact Submissions", "manage_options", "contact-submissions", "contactSubmissionsFunction");
	$submenu2 = add_submenu_page("contact-submissions", "Contact Email Information", "Contact Email Information", "manage_options", "contact-emails-form", "contactEmailsFormFunction");

	add_action('admin_print_styles-'.$menu, 'add_stylesheet');
	add_action('admin_print_styles-'.$submenu1, 'add_stylesheet');
	add_action('admin_print_styles-'.$submenu2, 'add_stylesheet');
}

register_activation_hook(__FILE__, 'custom_contact_create_db');
register_activation_hook(__FILE__, 'custom_contact_insert_data');

function custom_contact_create_db() {
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	$email_form_table = $wpdb->prefix.'custom_contact_emails';
	$submission_form_table = $wpdb->prefix.'custom_contact_submission';

	$sql = "CREATE TABLE $email_form_table (
		email_type varchar(50) NOT NULL,
		email_address varchar(100) NULL
	) $charset_collate;";

	$sql2 = "CREATE TABLE $submission_form_table (
		id int(11) NOT NULL AUTO_INCREMENT,
		fullname varchar(100) NOT NULL,
		email varchar(100) NOT NULL,
		phone varchar(15) NULL,
		subject varchar(100) NOT NULL,
		message text NOT NULL,
		timestamp datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta($sql);
	dbDelta($sql2);
}

/*function custom_contact_insert_data() {
    global $wpdb;

    $table_name = $wpdb->prefix.'custom_contact_emails';

    $wpdb->insert($table_name, array('email_type' => 'to', 'email_address' => ''));
}*/

function contactSubmissionsFunction() {
	global $wpdb;
	$prefix = $wpdb->prefix;
	$db = $prefix.'custom_contact_submission';
	$result = $wpdb->get_results("SELECT * FROM $db");
	foreach($result as $value) {
		$name = $value->fullname;
		$email = $value->email;
		$phone = $value->phone;
		$subject = $value->subject;
		$message = $value->message;
		$timestamp = $value->timestamp;
	}

	if (isset($_POST['deleteEmailDetailsbutton']) && !empty($_POST['deleteEmailDetailsbutton']) && !empty($_POST['deleteId'])) {
		global $wpdb;
		$prefix = $wpdb->prefix;
		$deleteID = $_POST['deleteId'];
		$sql = $wpdb->delete($prefix.'custom_contact_submission', array('id' => $deleteID));

		if ($sql == '1') {
			echo "<script>location.reload();</script>";
			echo "<div class='alert alert-succes success' style='display: -webkit-inline-box; display: -moz-inline-box; display: -ms-inline-box; display: -o-inline-box; display: inline-box; color: #155724; background-color: #d4edda; border-color: #c3e6cb; padding: .75rem 1.25rem; border: 1px solid transparent; border-radius: .25rem; margin: 1.75rem 0 0 3.5rem; overflow: hidden; position: relative;'>
				<i class='fa fa-check-circle' style='margin-top: 4px; margin-right: 7px;'></i><p class='success-contact-message'>Contact Email Submission has been Succesfully Deleted!</p>
			</div>";
		}
		else {
			echo "<script>location.reload();</script>";
			echo "<div class='alert alert-danger error' style='display: -webkit-inline-box; display: -moz-inline-box; display: -ms-inline-box; display: -o-inline-box; display: inline-box; color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; padding: .75rem 1.25rem; border: 1px solid transparent; border-radius: .25rem; margin: 1.75rem 0 0 3.5rem; overflow: hidden; position: relative;'>
				<i class='fa fa-times-circle' style='margin-top: 4px; margin-right: 7px;'></i><p class='error-contact-message'>Contact Email Submission Failed to Delete!</p>
			</div>";
		}
	}

	echo "<table class='contact-table' id='contact-submission-table'>
		<thead>
		    <tr>
		        <th>ID</th>
		        <th>Full Name</th>
		        <th>Email</th>
		        <th>Phone</th>
		        <th>Subject</th>
		        <th>Message</th>
		        <th>Action</th>
		    </tr>
		</thead>
		<tbody>";
		    foreach($result as $value) {
		    	$id = $value->id;
				$name = $value->fullname;
				$email = $value->email;
				$phone = $value->phone;
				$subject = $value->subject;
				$message = $value->message;
				$timestamp = $value->timestamp;

				echo "<tr>
					<td>$id</td>
			        <td>$name</td>
			        <td>$email</td>
			        <td>$phone</td>
			        <td>$subject</td>
			        <td>$message</td>
			        <td><form action='' method='post' id='deleteDetails' name='deleteDetails'>
			        <input type='hidden' name='deleteId' id='deleteId' value='$id'>
			        <input type='submit' name='deleteEmailDetailsbutton' id='deleteEmailDetailsbutton' title='Delete Record' value='Delete' /></form></td>
			    </tr>";
			}
		echo "</tbody>
	</table>";
}

/* Contact Form Submission */
function contactEmailsFormFunction() {
	global $wpdb;
	$prefix = $wpdb->prefix;
	$db = $prefix.'custom_contact_emails';
	$result = $wpdb->get_results("SELECT * FROM $db WHERE `email_type` <> '' OR `email_address` <> ''");
	foreach($result as $key=>$value) {
		if ($value->email_type == 'from_email') {
			$from_email = $value->email_address;
		}
		if ($value->email_type == 'from_name') {
			$from_name = $value->email_address;
		}
		if ($value->email_type == 'to_email') {
			$to_email = $value->email_address;
		}
	}

	echo "<div class='customContact'>
		<h2 class='title'>Contact Email Information</h2>
		<div class='bread-section'>
			<ul class='breadcrumb'>
				<li><a href='/about/contact-us/'>Contact US</a></li>
			</ul>
		</div>
		<div class='custom-plugin-domestic'>
			<h2>Contact Email Information</h2>
			<p>Fill up Contact Email Form by entering an Information.</p>
			<div class='contact-div' style='margin-top: 2rem; border: 1px solid #AAA; padding: 20px; background: #E0E0E0; border-radius: 3px;'>
				<form action='' method='post' id='insertEmails' name='insertEmails'>
		  			<div class='form-group' style='margin-bottom: 1rem;'>
	      				<label for='inputFromAddress' style='font-size: 1rem; color: brown; letter-spacing: -0.015rem;'>From Email Address</label>
	      				<input type='email' class='form-control form-control-sm' id='inputFromAddress' name='inputFromAddress' placeholder='Enter From Email Address' required value='"; if (isset($from_email)) { echo $from_email; } echo "' style='width: 100%; border: 1px solid #ccc; background: #FFF; margin: 8px 0 2px; padding: 3px 10px 5px; border-radius: 3px;'>
	      				<div style='padding-top: 0.1rem; font-size: 0.7rem; color: darkgreen;'>The Email Address that Emails are sent From.</div>
		  			</div>
		  			<div class='form-group' style='margin-bottom: 1rem;'>
	      				<label for='inputFromName' style='font-size: 1rem; color: brown; letter-spacing: -0.015rem;'>From Name</label>
	      				<input type='text' class='form-control form-control-sm' id='inputFromName' name='inputFromName' placeholder='Enter From Name' required value='"; if (isset($from_name)) { echo $from_name; } echo "' style='width: 100%; border: 1px solid #ccc; background: #FFF; margin: 8px 0 2px; padding: 3px 10px 5px; border-radius: 3px;'>
	      				<div style='padding-top: 0.1rem; font-size: 0.7rem; color: darkgreen;'>The Name that Emails are sent From.</div>
		  			</div>
		  			<div class='form-group' style='margin-bottom: 1rem;'>
	      				<label for='inputToEmail' style='font-size: 1rem; color: brown; letter-spacing: -0.015rem;'>To Email Address</label>
	      				<input type='email' class='form-control form-control-sm' id='inputToEmail' name='inputToEmail' placeholder='Enter To Email Address' required value='"; if (isset($to_email)) { echo $to_email; } echo "' style='width: 100%; border: 1px solid #ccc; background: #FFF; margin: 8px 0 2px; padding: 3px 10px 5px; border-radius: 3px;'>
	      				<div style='padding-top: 0.1rem; font-size: 0.7rem; color: darkgreen;'>The Email Address that Emails are sent To.</div>
		  			</div>";
				  	if (isset($from_email) && isset($from_name) && isset($to_email)) {
				  		echo '<input type="submit" class="btn btn-success btn-sm" name="updateEmailDetailsbutton" value="Update Contact Email Details" style="margin-top: 5px;">';
				  	}
				  	else {
				  		echo '<input type="submit" class="btn btn-primary btn-sm" name="insertEmailDetailsbutton" value="Add Contact Email Details" style="margin-top: 5px;">'; 
				  	} echo"
				</form>
			</div>
			<span id='message'></span>
		</div>
	<div>";

	$from_email = isset($_POST['inputFromAddress']) ? htmlspecialchars($_POST['inputFromAddress']) : "";
	$from_name = isset($_POST['inputFromName']) ? htmlspecialchars($_POST['inputFromName']) : "";
	$to_email = isset($_POST['inputToEmail']) ? htmlspecialchars($_POST['inputToEmail']) : "";

	if (isset($_POST['insertEmailDetailsbutton'])) {
		global $wpdb;
		$prefix = $wpdb->prefix;
		$sql = $wpdb->insert($prefix.'custom_contact_emails', array('email_type' => 'from_email', 'email_address' => $from_email));
		$sql2 = $wpdb->insert($prefix.'custom_contact_emails', array('email_type' => 'from_name', 'email_address' => $from_name));
		$sql3 = $wpdb->insert($prefix.'custom_contact_emails', array('email_type' => 'to_email', 'email_address' => $to_email));

		if (($sql == true) || ($sql2 == true) || ($sql3 == true)) {
			echo "<script>alert('Contact Email Infomation has been Succesfully Added!');</script>";
			echo "<script>location.reload();</script>";
		}
		else {
			/*echo "<script>jQuery('#message').text('Not Sending');</script>";*/
			echo "<script>alert('Contact Email Infomation Failed to Added!');</script>";
			echo "<script>location.reload();</script>";
		}
	}

	if (isset($_POST['updateEmailDetailsbutton'])) {
		global $wpdb;
		$prefix = $wpdb->prefix;
		$db = $prefix.'custom_contact_emails';
  		$sql = $wpdb->query($wpdb->prepare("UPDATE $db SET email_address = '$from_email' WHERE email_type = 'from_email'"));
  		$sql2 = $wpdb->query($wpdb->prepare("UPDATE $db SET email_address = '$from_name' WHERE email_type = 'from_name'"));
  		$sql3 = $wpdb->query($wpdb->prepare("UPDATE $db SET email_address = '$to_email' WHERE email_type = 'to_email'"));

  		echo "<script>alert('Contact Email Infomation has been Succesfully Updated!');</script>";
		echo "<script>location.reload();</script>";
	}
}

/* Contact Form Submission Function */
if (isset($_POST['submit'])) {
	add_action('plugins_loaded', 'action_function_name_7188');
	function action_function_name_7188() {
		global $wpdb;
		$prefix = $wpdb->prefix;
		$db = $prefix.'custom_contact_emails';
		$result = $wpdb->get_results("SELECT * FROM $db WHERE `email_type` <> '' OR `email_address` <> ''");
		foreach($result as $key=>$value) {
			if ($value->email_type == 'from_email') {
				$from_email = $value->email_address;
			}
			if ($value->email_type == 'from_name') {
				$from_name = $value->email_address;
			}
			if ($value->email_type == 'to_email') {
				$to_email = $value->email_address;
			}
		}

		if (isset($from_email) && isset($from_name) && isset($to_email)) {
			/*$name = $_POST['contact_name'];
			$phone = $_POST['contact_phone'];
			$email = $_POST['contact_email'];
			$subject = $_POST['contact_sub'];
			$body = $_POST['contact_message'];*/
			$name = $_POST['text'];
			$email = $_POST['email'];
			$phone = $_POST['tel'];
			$subject = $_POST['sub'];
			$body = $_POST['message'];
			
			$MESSAGE_BODY = "<p style='margin: 1.75rem 0 1rem 3.5rem;'><b>Full Name:</b> ".$name."</p>";
			$MESSAGE_BODY .= "<p style='margin: 0 0 1rem 3.5rem;'><b>Email Address:</b> ".$email."</p>";
			if (!empty($phone)) {
				$MESSAGE_BODY .= "<p style='margin: 0 0 1rem 3.5rem;'><b>Phone Number:</b> ".$phone."</p>";
			}
			$MESSAGE_BODY .= "<p style='margin: 0 0 1rem 3.5rem;'><b>Subject:</b> ".$subject."</p>";
			$MESSAGE_BODY .= "<p style='margin: 0 0 0 3.5rem;'><b>Message:</b> ".nl2br($body)."</p>";
			// print_r($MESSAGE_BODY);

			// Insert Submission
			$insert = $wpdb->insert($prefix.'custom_contact_submission', array('fullname' => $name, 'email' => $email, 'phone' => $phone, 'subject' => $subject, 'message' => $body));
		}

		$headers = [
			"MIME-Version: 1.0",
			"Content-type: text/html; charset=UTF-8",
			"From: $from_name<$from_email>",
			"Reply-To: $from_email",
		];
		$headers = implode("\r\n", $headers);

		$mailSend = '0';
		$mailSend = wp_mail($to_email, $subject, $MESSAGE_BODY, $headers);
		
		add_action( 'wp_mail_failed', 'onMailError', 10, 1 );
		function onMailError( $wp_error ) {
			echo "<pre>";
			print_r($wp_error);
			echo "</pre>";
		}

		if ($mailSend == '1') {
			echo "<div class='alert alert-succes success' style='display: -webkit-inline-box; display: -moz-inline-box; display: -ms-inline-box; display: -o-inline-box; display: inline-box; color: #155724; background-color: #d4edda; border-color: #c3e6cb; padding: .75rem 1.25rem; border: 1px solid transparent; border-radius: .25rem; margin: 1.75rem 0 0 3.5rem; overflow: hidden; position: relative;'>
				<i class='fa fa-check-circle' style='margin-top: 4px; margin-right: 7px;'></i><p class='success-contact-message'>Email has been Sent Successfully!</p>
			</div>";
		}
		else {
			echo "<div class='alert alert-danger error' style='display: -webkit-inline-box; display: -moz-inline-box; display: -ms-inline-box; display: -o-inline-box; display: inline-box; color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; padding: .75rem 1.25rem; border: 1px solid transparent; border-radius: .25rem; margin: 1.75rem 0 0 3.5rem; overflow: hidden; position: relative;'>
				<i class='fa fa-times-circle' style='margin-top: 4px; margin-right: 7px;'></i><p class='error-contact-message'>Email Sending Failed!</p>
			</div>";
		}
	}
}
?>