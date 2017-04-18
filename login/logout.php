<?php
define('ROOT', dirname(__FILE__));

if(!isset($con)) {
	require(ROOT .'/config.php');
}
if(!isset($titles)) {
	require(ROOT .'/lang/'. $language .'.php');
}
if(!function_exists('getSetting')) {
	require(ROOT .'/includes/functions.php');
}

@session_set_cookie_params(0, '/', getCurrentDomain()); 
@session_start();

// Log the user out
logout();
?>