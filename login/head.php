<?php
define('HOME', dirname(__FILE__));

if(!isset($con)) {
	require(HOME .'/config.php');
}
if(!isset($titles)) {
	require(HOME .'/lang/'. $language .'.php');
}
if(!function_exists('getSetting')) {
	require(HOME .'/includes/functions.php');
}

// Start the session and set it for the whole domain
@session_set_cookie_params(0, '/', getCurrentDomain());
@session_start();

@ob_start();

$sn = explode('/', $_SERVER['SCRIPT_NAME']);
$title = $sn[count($sn) - 1];

// Check if the install is completed
if((file_exists("install.php") || file_exists("database.sql")) && $title != "install.php") {
	header('Location: install.php');
	exit;
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
	
	<title><?php echo $titles[$title]; ?></title>
	
	<link rel='stylesheet' href='<?php echo $script_path; ?>assets/css/bootstrap/3.3.5/css/bootstrap.min.css'>
	<link rel='stylesheet' href='<?php echo $script_path; ?>assets/css/bootstrap/3.3.5/css/bootstrap-theme.css'>
	<?php
	// Include the login screen style if the page is login.php or register.php or profile.php or social.php
	if($title == "login.php" || $title == "register.php" || $title == "profile.php" || $title == "social.php") {
	?>	
	
	<link rel='stylesheet' href='<?php echo $script_path; ?>assets/css/bootstrap/3.3.5/css/default-theme.css'>
	<?php
	}
	?>
	
	<link rel='stylesheet' href='<?php echo $script_path; ?>includes/datatables/media/css/jquery.dataTables.min.css'>
	<link rel='stylesheet' href='<?php echo $script_path; ?>assets/css/prettify.css'>
	
	<script src='<?php echo $script_path; ?>assets/js/jquery-1.11.0.min.js' type='text/javascript'></script>
	<script src='<?php echo $script_path; ?>assets/js/prettify.js' type='text/javascript'></script>
	<script src='<?php echo $script_path; ?>assets/css/bootstrap/3.3.5/js/bootstrap.min.js'></script>
	<script src='<?php echo $script_path; ?>includes/datatables/media/js/jquery.dataTables.min.js'></script>
	<script src='<?php echo $script_path; ?>includes/datatables/examples/resources/bootstrap/3/dataTables.bootstrap.js'></script>
	<?php
	// Includes amcharts if the page is controlpanel.php
	if($title == "controlpanel.php") {
	?>
	
	<script src='<?php echo $script_path; ?>includes/amcharts/amcharts/amcharts.js' type='text/javascript'></script>
	<script src='<?php echo $script_path; ?>includes/amcharts/amcharts/serial.js' type='text/javascript'></script>
	<?php
	}
	?>
	<?php
	// Include bootstrap-select if the page is profile.php and messages are allowed
	if($title == "profile.php" && getSetting("send_messages", "text") == "true") {
	?>

	<link rel='stylesheet' href='<?php echo $script_path; ?>assets/css/bootstrap-select.min.css'>
	<script src='<?php echo $script_path; ?>assets/js/bootstrap-select.min.js' type='text/javascript'></script>
	<?php
	}
	?>
</head>
<body>