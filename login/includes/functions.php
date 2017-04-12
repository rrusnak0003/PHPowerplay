<?php
// This function checks the requirements before starting the installation
function requirements() {
	global $m;
	
	$success = 0;
	
	// Check if config.php has permission 777
	if(substr(sprintf('%o', fileperms('config.php')), -4) == "0777") {
		echo "<div class='alert alert-success' role='alert'>". $m['config_writable'] ."</div>";
		$success++;
	} else {
		echo "<div class='alert alert-danger' role='alert'>". $m['config_not_writable'] ."</div>";
	}
	
	
	// Check if the folder uploads has permission 777
	if(substr(sprintf('%o', fileperms('uploads')), -4) == "0777") {
		echo "<div class='alert alert-success' role='alert'>". $m['uploads_writable'] ."</div>";
		$success++;
	} else {
		echo "<div class='alert alert-danger' role='alert'>". $m['uploads_not_writable'] ."</div>";
	}
	
	
	// If both the checks are successful than show a next button (if someone wasn't moved to the next tab already)
	if($success == 2) {
		?>
		<div class='row row-0 row-6'>
			<button onclick='$("#tabs li:eq(1) a").tab("show");' class='btn btn-primary'><?php echo $m['nextstep']; ?></button>
		</div>
		<?php
	}
}



// Function to give a number of successful checks
function checkRequirements() {
	global $con;
	
	$success = 0;
	
	// Check if config.php has permission 777
	if(substr(sprintf('%o', fileperms('config.php')), -4) == "0777" || !empty($con)) {
		$success++;
	}
	
	// Check if the folder uploads has permission 777
	if(substr(sprintf('%o', fileperms('uploads')), -4) == "0777" || !empty($con)) {
		$success++;
	}
	
	return $success;
}



// Function to check if installation is complete
function installCheck() {
	global $m;
	
	// Check if config.php has permission 644
	if(substr(sprintf('%o', fileperms('config.php')), -4) != "0644") {
		echo "<div class='alert alert-danger' role='alert'>". $m['config_still_writable'] ."</div>";
	}
	
	// Check if database.sql exists
	if(file_exists("database.sql")) {
		echo "<div class='alert alert-danger' role='alert'>". $m['delete_database'] ."</div>";
	}
	
	// Check if install.php exists
	if(file_exists("install.php")) {
		echo "<div class='alert alert-danger' role='alert'>". $m['delete_install'] ."</div>";
	}
}



// Function to get something out of a table
function get($value, $table) {
	global $con;
	
	$value = htmlentities(mysqli_real_escape_string($con,$value), ENT_QUOTES);
	$table = htmlentities(mysqli_real_escape_string($con,$table), ENT_QUOTES);
	
	$get = mysqli_query($con,"SELECT * FROM login_". $table); // Select FROM login_given table
	$g = mysqli_fetch_array($get);
	
	return $g[$value]; // Return given value
}



// Function to edit a setting or create one
function setting($setting, $value) {
	global $con;
	
	$setting = htmlentities(mysqli_real_escape_string($con,$setting), ENT_QUOTES);
	$value = htmlentities(mysqli_real_escape_string($con,$value), ENT_QUOTES);
	
	$check = mysqli_query($con,"SELECT * FROM login_settings WHERE setting='$setting'");
	
	// Check if setting already exists, if so update it, else create it
	if(mysqli_num_rows($check) > 0) {
		mysqli_query($con,"UPDATE login_settings SET value='$value' WHERE setting='$setting'");
	} else {
		mysqli_query($con,"INSERT INTO login_settings (setting, value) VALUES ('$setting','$value')");
	}
}



// Function to get the value out of a setting
function getSetting($setting, $type) {
	global $con;
	
	$setting = mysqli_real_escape_string($con,$setting);
	$getsetting = mysqli_query($con,"SELECT * FROM login_settings WHERE setting='$setting'");
	
	// Check if setting exists
	if(mysqli_num_rows($getsetting) == 1) {
		$gs = mysqli_fetch_array($getsetting);
		
		// Type checkbox return a boolean, this is only for checkbox settings
		if($type == "checkbox") {
			if($gs['value'] == "true") {
				return true;
			} else {
				return false;
			}
		}
		// Type text returns the value
		if($type == "text") {
			return $gs['value'];
		}
		// Type value returns the value in a input way
		if($type == "value") {
			if(!empty($gs['value']) || $gs['value'] == "0") {
				return " value='". $gs['value'] ."'";
			} else {
				return false;
			}
		}
		// Type option returns the value in a select way
		if($type == "option") {
			if(!empty($gs['value'])) {
				$id = $gs['value'];
				$getperm = mysqli_query($con,"SELECT name FROM login_permissions WHERE id='$id'");
				$gp = mysqli_fetch_array($getperm);
				
				return "<option value='". $gs['value'] ."'>". $gp['name'] ."</option>";
			} else {
				// Nothing
			}
		}
	}
}



// Function to show the extra inputs, it is possible to fill them in with the data of a user
function getExtraInputs($filled = false, $uid = null) {
	global $con;
	
	$inputs = mysqli_query($con,"SELECT * FROM login_inputs ORDER BY place DESC");
	
	while($i = mysqli_fetch_array($inputs)) {
		if($i['type'] == "textarea") {
			?>
			<div class='row row-1'>
				<div class='form-group'>
					<label class='col-sm-4 control-label'><?php if(!empty($i['public_name'])) { echo $i['public_name']; } else { echo $i['name']; } ?><?php if($i['required'] == "true") { echo "*"; } ?></label>
					<div class='col-sm-8'>
						<textarea name='<?php echo $i['name']; ?>' class='form-control'<?php if(!empty($i['maxlength']) && $i['maxlength'] != "0") { echo " maxlength='". $i['maxlength'] ."'"; } ?> rows='<?php if(!empty($i['rows'])) { echo $i['rows']; } else { echo "5"; } ?>'<?php if(!empty($i['placeholder'])) { echo " placeholder='". $i['placeholder'] ."'"; } ?>><?php if($filled && userValue($uid,$i['name']) != "") { echo userValue($uid,$i['name']); } elseif(!empty($i['value']) || $i['value'] == "0") { echo $i['value']; } else { } ?></textarea>
					</div>
				</div>
			</div>
			<?php
		} elseif($i['type'] == "checkbox") {
			?>
			<div class='row row-1'>
				<div class='form-group'>
					<label class='col-sm-4 control-label'><?php if(!empty($i['public_name'])) { echo $i['public_name']; } else { echo $i['name']; } ?><?php if($i['required'] == "true") { echo "*"; } ?></label>
					<div class='col-sm-8'>
						<div class='checkbox'>
							<label><input type='checkbox' name='<?php echo $i['name']; ?>'<?php if($filled && userValue($uid,$i['name']) == "true") { echo " checked"; } elseif($i['checked'] == "true") { echo " checked"; } else { } ?>> <?php if(!empty($i['placeholder'])) { echo $i['placeholder']; } else { echo "Placeholder empty"; } ?></label>
						</div>
					</div>
				</div>
			</div>
			<?php
		} elseif($i['type'] == "select") {
			?>
			<div class='row row-1'>
				<div class='form-group'>
					<label class='col-sm-4 control-label'><?php if(!empty($i['public_name'])) { echo $i['public_name']; } else { echo $i['name']; } ?><?php if($i['required'] == "true") { echo "*"; } ?></label>
					<div class='col-sm-8'>
						<select name='<?php echo $i['name']; ?>' class='form-control'>
							<?php
							$pairs = explode("|||", $i['options']); // Make pairs of names with values
							
							foreach($pairs as $pair) {
							$pa = explode("***", $pair); // Split name and value
							?>
							<option value='<?php echo $pa[1]; ?>'><?php echo $pa[0]; ?></option>
							<?php
							}
							?>
						</select>
						
						<?php if($filled) { ?>
						<script>
						$('select[name=<?php echo $i['name']; ?>]').val("<?php echo userValue($uid,$i['name']); ?>");
						</script>
						<?php } ?>
					</div>
				</div>
			</div>
			<?php
		} elseif($i['type'] == "hidden") {
			// Don't show
		} else {
			?>
			<div class='row row-1'>
				<div class='form-group'>
					<label class='col-sm-4 control-label'><?php if(!empty($i['public_name'])) { echo $i['public_name']; } else { echo $i['name']; } ?><?php if($i['required'] == "true") { echo "*"; } ?></label>
					<div class='col-sm-8'>
						<input type='<?php echo $i['type']; ?>' name='<?php echo $i['name']; ?>' class='form-control'<?php if((!empty($i['maxlength']) && $i['maxlength'] != "0") && in_array($i['type'], array("text", "textarea", "url", "email"))) { echo " maxlength='". $i['maxlength'] ."'"; } ?><?php if((!empty($i['min']) || $i['min'] == "0") && in_array($i['type'], array("number", "range"))) { echo " min='". $i['min'] ."'"; } ?><?php if((!empty($i['max']) || $i['max'] == "0") && in_array($i['type'], array("number", "range", "date"))) { echo " max='". $i['max'] ."'"; } ?><?php if((!empty($i['step']) || $i['step'] == "0") && in_array($i['type'], array("number", "range"))) { echo " step='". $i['step'] ."'"; } ?><?php if(!empty($i['placeholder'])) { echo " placeholder='". $i['placeholder'] ."'"; } ?><?php if($filled && userValue($uid,$i['name']) != "") { echo " value='". userValue($uid,$i['name']) ."'"; } elseif(!empty($i['value']) || $i['value'] == "0") { echo " value='". $i['value'] ."'"; } else { } ?>>
					</div>
				</div>
			</div>
			<?php
		}
	}
}



// Simple function to check if a email address is valid
function checkEmail($email) {
	if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
		return true;
	} else {
		return false;
	}
}



// Simple function to check if a ip is valid
function checkIp($ip) {
	if (filter_var($ip, FILTER_VALIDATE_IP)) {
		return true;
	} else {
		return false;
	}
}



// Function to add a successful login or a failed login try
function addLog($success, $ip, $uid = 0, $try, $type) {
	global $con;
	
	$time = time();
	
	// If uid is 0, the user was not found (this is already check in posthandler.php)
	if($uid == 0) {
		mysqli_query($con,"INSERT INTO login_log (success, time, ip, try, type)
		VALUES ('$success','$time','$ip','$try','$type')");
	} else {
		mysqli_query($con,"INSERT INTO login_log (success, time, ip, uid, try, type)
		VALUES ('$success','$time','$ip','$uid','$try','$type')");
	}
}



// Function to get the permission id by user id
function getPermId($uid) {
	global $con;
	
	$uid = mysqli_real_escape_string($con,$uid);
	$guser = mysqli_query($con,"SELECT * FROM login_users WHERE id='$uid'");
	$gu = mysqli_fetch_array($guser);
	
	return $gu['permission'];
}



// Function to send a mail
function sendMail($to, $subject, $message, $uid = 0) {
	require 'phpmailer/PHPMailerAutoload.php'; // Requires PHPMailer
	
	$mail = new PHPMailer;
	
	// Check if SMTP is enabled
	if(getSetting("mailtype", "text") == "smtp") {
		$mail->isSMTP();
		$mail->Host = getSetting("smtp_hostname", "text");
		$mail->SMTPAuth = true;
		$mail->Username = getSetting("smtp_username", "text");
		$mail->Password = getSetting("smtp_password", "text");
		
		// Check if SSL is enabled
		if(getSetting("smtp_ssl", "text") == "true") {
			$mail->SMTPSecure = 'ssl';
		} else {
			$mail->SMTPSecure = 'tls';
		}
		$mail->Port = getSetting("smtp_port", "text");
	}

	$mail->From = getSetting("admin_email", "text"); // Get admin email, this is the sender of the mail
	$mail->FromName = getSetting("email_name", "text"); // Get email name, this is the name of the sender of the mail
	
	// If uid is 0 the mail will not have a username, only the address to send it to
	if($uid = 0) {
		$mail->addAddress($to);
	} else {
		$mail->addAddress($to, userValue($uid, "username"));
	}
	
	$mail->WordWrap = 50;
	$mail->isHTML(true); // Enable HTML
	
	$mail->Subject = $subject;
	$mail->Body = $message;
	
	// If the mail is send, it returns true, else it will return false
	if(!$mail->send()) {
		return false;
	} else {
		return true;
	}
}



// Check if a SMTP connection can be made
function checkSMTP($hostname, $username, $password, $port, $ssl) {
	require 'phpmailer/PHPMailerAutoload.php'; // Required PHPMailer
	
	$mail = new PHPMailer;
	
	$mail->isSMTP();
	$mail->SMTPDebug = 0;
	$mail->Host = $hostname;
	$mail->SMTPAuth = true;
	$mail->Username = $username;
	$mail->Password = $password;
	
	// Check if SSL is enabled
	if($ssl == "true") {
		$mail->SMTPSecure = 'ssl';
	} else {
		$mail->SMTPSecure = 'tls';
	}
	$mail->Port = $port;
	
	
	// Check if SMTP connection can be made, if so return true, else return false
	if(!$mail->smtpConnect()) {
		return false;
	} else {
		return true;
	}
}



// Simple function that returns the current domain name, example: domain.com
function getCurrentDomain() {
	if(isset($_SERVER['HTTPS'])) {
		$host = "https://". $_SERVER['HTTP_HOST'];
	} else {
		$host = "http://". $_SERVER['HTTP_HOST'];
	}
	
	$host = parse_url($host);
	preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $host['host'], $matches);
	
	return $matches['domain'];
}



// Function to create the right URL, and checks if SSL is enabled
function getTypeUrl($type, $uid = null) {
	global $con;
	global $script_path;
	global $m;
	
	$domain = htmlentities(mysqli_real_escape_string($con,$_SERVER['SERVER_NAME']), ENT_QUOTES);
	
	// Check which type it is, if type is not found it will only return a domain name
	if($type == "notify") {
		// Check if SSL is enabled
		if(isset($_SERVER['HTTPS'])) {
			return "https://". $domain . $script_path ."callback/paypal.php";
		} else {
			return "http://". $domain . $script_path ."callback/paypal.php";
		}
	} elseif($type == "return") {
		// Check if SSL is enabled
		if(isset($_SERVER['HTTPS'])) {
			return "https://". $domain . $script_path ."login.php?m=4";
		} else {
			return "http://". $domain . $script_path ."login.php?m=4";
		}
	} elseif($type == "cancel") {
		// Check if SSL is enabled
		if(isset($_SERVER['HTTPS'])) {
			return "https://". $domain . $script_path ."login.php?m=5";
		} else {
			return "http://". $domain . $script_path ."login.php?m=5";
		}
	} elseif($type == "reset") {
		// Check if SSL is enabled
		if(isset($_SERVER['HTTPS'])) {
			return "https://". $domain . $script_path ."login.php?forgot&code=";
		} else {
			return "http://". $domain . $script_path ."login.php?forgot&code=";
		}
	} elseif($type == "activation") {
		// Check if SSL is enabled
		if(isset($_SERVER['HTTPS'])) {
			return "https://". $domain . $script_path ."login.php?activation&activate_code=";
		} else {
			return "http://". $domain . $script_path ."login.php?activation&activate_code=";
		}
	} elseif($type == "google_redirect") {
		// Check if SSL is enabled
		if(isset($_SERVER['HTTPS'])) {
			return "https://". $domain . $script_path ."social.php?return=google";
		} else {
			return "http://". $domain . $script_path ."social.php?return=google";
		}
	} elseif($type == "facebook_callback") {
		// Check if SSL is enabled
		if(isset($_SERVER['HTTPS'])) {
			return "https://". $domain . $script_path ."social.php?return=facebook";
		} else {
			return "http://". $domain . $script_path ."social.php?return=facebook";
		}
	} elseif($type == "twitter_callback") {
		// Check if SSL is enabled
		if(isset($_SERVER['HTTPS'])) {
			return "https://". $domain . $script_path ."social.php?return=twitter";
		} else {
			return "http://". $domain . $script_path ."social.php?return=twitter";
		}
	} elseif($type == "on_login") {
		if($uid == null) {
			$userid = $_SESSION['uid'];
		} else {
			$userid = $uid;
		}
		$getuser = mysqli_query($con,"SELECT * FROM login_users WHERE id='$userid'");
		$gu = mysqli_fetch_array($getuser);
		
		$perm = $gu['permission'];
		$getperm = mysqli_query($con,"SELECT * FROM login_permissions WHERE id='$perm'");
		$gp = mysqli_fetch_array($getperm);
		
		if(getSetting("redirect_last_page", "text") == "true" && !empty($_COOKIE['last_url'])) {
			$last_url = $_COOKIE['last_url'];
			setcookie("last_url", "", time() - 3600, "/", getCurrentDomain());
			unset($_COOKIE['last_url']); // Delete last URL cookie to avoid infinite redirections if the user is not allowed to visit the URL
			
			return "1|||". $last_url;
		} elseif(!empty($gp['on_login'])) { // Check if the user's permission has a logged in redirect URL
			return "1|||". $gp['on_login'];
		} elseif(getSetting("use_redirect_login", "text") == "true") { // Check if on login redirect is enabled
			if(getSetting("use_redirect_login", "text") != "") { // Extra check if the URL is filled in
				return "1|||". getSetting("redirect_login", "text");
			} else {
				return "1|||". $script_name ."login.php";
			}
		} else {
			if(getSetting("message_login", "text") != "") { // Check if there is a custom message filled in, else display default message
				return "0|||<h5 class='text-center green'>". nl2br(getSetting("message_login", "text")) ."</h5>";
			} else {
				return "0|||<h5 class='text-center green'>". $m['successful_login'] ."</h5>";
			}
		}
	} else {
		// Check if SSL is enabled
		if(isset($_SERVER['HTTPS'])) {
			return "https://". $domain;
		} else {
			return "http://". $domain;
		}
	}
}



// Function to get the full current URL
function getCurrentUrl() {
	$currentURL = (@$_SERVER['HTTPS'] == "on") ? "https://" : "http://";
	$currentURL .= $_SERVER['SERVER_NAME'];
	
	if($_SERVER['SERVER_PORT'] != "80" && $_SERVER['SERVER_PORT'] != "443") {
		$currentURL .= ":".$_SERVER['SERVER_PORT'];
	}
	
	$currentURL .= $_SERVER['REQUEST_URI'];
	
    return $currentURL;
}



// Function to check how many users are registered on a specific date
function getUsersByDate($datetype, $date) {
	global $con;
	
	$users = 0;
	
	$user = mysqli_query($con,"SELECT * FROM login_users");
	while($u = mysqli_fetch_array($user)) {
		// If registration date and given date match, a user is registered on that date
		if(date($datetype, $u['registered_on']) == date($datetype, $date)) {
			$users++;
		}
	}
	
	return $users;
}



// Function to check how many messages are send on a specific date
function getMessagesByDate($datetype, $date) {
	global $con;
	
	$messages = 0;
	
	$message = mysqli_query($con,"SELECT * FROM login_messages");
	while($msg = mysqli_fetch_array($message)) {
		// If send date and given date match, a message is found on that date
		if(date($datetype, $msg['time']) == date($datetype, $date)) {
			$messages++;
		}
	}
	
	return $messages;
}



// Check if a given username or email is admin, if so the function will return true so an admin can always login, even if the login page is disabled
function isAdminByNameOrEmail($logginginwith) {
	global $con;
	
	$logginginwith = htmlentities(mysqli_real_escape_string($con,$logginginwith));
	$check1 = mysqli_query($con,"SELECT * FROM login_users WHERE username='$logginginwith' AND permission='1'"); // Check if the given input is a username
	$check2 = mysqli_query($con,"SELECT * FROM login_users WHERE email='$logginginwith' AND permission='1'"); // Check if the given input is a email address
	
	// Check if one of the checks is found
	if(mysqli_num_rows($check1) > 0 || mysqli_num_rows($check2) > 0) {
		$c1 = mysqli_fetch_array($check1);
		$c2 = mysqli_fetch_array($check2);
		
		// Extra check to make sure if the user is an admin
		if(getPermName($c1['id']) == "Admin" || getPermName($c2['id']) == "Admin") {
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
}



// Check if a given sid is admin, if so the function will return true so an admin can always login, even if the login page is disabled
function isAdminBySid($sid) {
	global $con;
	
	$sid = htmlentities(mysqli_real_escape_string($con,$sid));
	$check = mysqli_query($con,"SELECT * FROM login_users WHERE sid='$sid' AND permission='1'"); // Check if the given input is a username
	
	// Check if one of the checks is found
	if(mysqli_num_rows($check) > 0) {
		$c = mysqli_fetch_array($check);
		
		// Extra check to make sure if the user is an admin
		if(getPermName($c['id']) == "Admin") {
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
}



// Function to turn a format and an amount into seconds
function formatToSeconds($amount, $format) {
	// Check format and calculate how many seconds it is
	if($format == "minutes") {
		$amount = $amount * 60;
	} elseif($format == "hours") {
		$amount = $amount * 60 * 60;
	} elseif($format == "days") {
		$amount = $amount * 60 * 60 * 24;
	} elseif($format == "months") {
		$amount = $amount * 60 * 60 * 24 * 30;
	} elseif($format == "years") {
		$amount = $amount * 60 * 60 * 24 * 30 * 365;
	} elseif($format == "forever") {
		$amount = 0;
	} else {
		$amount = $amount;
	}
	
	return $amount;
}



// Function to check if an user id exists
function checkUid($uid) {
	global $con;
	
	$uid = mysqli_real_escape_string($con,$uid);
	$check = mysqli_query($con,"SELECT * FROM login_users WHERE id='$uid' AND banned='0'");
	
	// If the uid is found the user exists and the function returns true, else it will return false
	if(mysqli_num_rows($check) == 1) {
		return true;
	} else {
		return false;
	}
}



// Function to check if an IP is blocked
function checkIpBlock($ip) {
	global $con;
	
	$ip = mysqli_real_escape_string($con,$ip);
	$checkblock = mysqli_query($con,"SELECT * FROM login_blocks WHERE ip='$ip'");
	$cb = mysqli_fetch_array($checkblock);
	$timenow = time();
	
	// Check if an IP is blocked
	if(mysqli_num_rows($checkblock) > 0 && ($cb['until'] > $timenow || empty($cb['until']) || $cb['until'] == "0")) {
		return true;
	} else {
		return false;
	}
}



// Function to log someone out
function logout() {
	global $con;
	global $script_path;
	
	// Get the data out of the database before the session is destroyed
	$uid = $_SESSION['uid'];
	$user = mysqli_query($con,"SELECT * FROM login_users WHERE id='$uid'");
	$u = mysqli_fetch_array($user);
	
	$permid = $u['permission'];
	$perm = mysqli_query($con,"SELECT * FROM login_permissions WHERE id='$permid'");
	$p = mysqli_fetch_array($perm);
	
	
	// Unset and destroy the session
	unset($_SESSION['uid']);
	unset($_SESSION['ip']);
	session_destroy();
	
	// Unset and destroy the last remembered page
	unset($_COOKIE['last_url']);
	setcookie("last_url", null, -1, '/');
	
	
	// Check where the user should be redirected to
	if(!empty($p['on_logout'])) {
		header('Location: '. $p['on_logout']);
	} elseif(getSetting("use_redirect_logout", "text") == "true") {
		header('Location: '. getSetting("redirect_logout", "text"));
	} else {
		header('Location: '. $script_path .'login.php?m=3');
	}
}



// Function to active someone by a activate code
function activateCode($activate_code) {
	global $con;
	global $m;
	
	$activate_code = mysqli_real_escape_string($con,$activate_code);
	$check = mysqli_query($con,"SELECT * FROM login_users WHERE activate_code='$activate_code'");
	
	// Check if the activation code exists
	if(mysqli_num_rows($check) == 1) {
		$u = mysqli_fetch_array($check);
		$uid = $u['id'];
		
		// Make sure the user isn't already active
		if($u['active'] == "0") {
			mysqli_query($con,"UPDATE login_users SET active='1' WHERE id='$uid'"); // Activate the user
			
			echo "<h5 class='text-center green'>". $m['activation_success'] ."</h5>";
			
			
			// Check if an welcome mail should be send
			if(getSetting("send_welcome_mail", "text") == "true") {
				// Send welcome mail
				$subject = getSetting("welcome_mail_subject", "text");
				$subject = str_replace("{name}", $u['username'], $subject);
				$subject = str_replace("{email}", $u['email'], $subject);
				$subject = str_replace("{date}", date("j-n-Y", $u['registered_on']), $subject);
				$subject = str_replace("{ip}", $u['ip'], $subject);
				$subject = str_replace("{perm}", getPermName($u['id']), $subject);
				
				$message = getSetting("welcome_mail", "text");
				$message = str_replace("{name}", $u['username'], $message);
				$message = str_replace("{email}", $u['email'], $message);
				$message = str_replace("{date}", date("j-n-Y", $u['registered_on']), $message);
				$message = str_replace("{ip}", $u['ip'], $message);
				$message = str_replace("{perm}", getPermName($u['id']), $message);
				$message = nl2br($message);
				$message = html_entity_decode($message);
				
				sendMail($u['email'], $subject, $message, $u['id']);
			}
		} else {
			echo "<h5 class='text-center red'>". $m['already_active'] ."</h5>";
		}
	} else {
		echo "<h5 class='text-center red'>". $m['activate_code_not_found'] ."</h5>";
	}
}



function google() {
	global $script_path;
	
	require_once('includes/social/Google/autoload.php');
	
	$client_id = getSetting("client_id", "text");
	$client_secret = getSetting("client_secret", "text");
	$returnurl = getTypeUrl("google_redirect");
	$simple_api_key = getSetting("api_key", "text");
	
	$client = new Google_Client();
	$client->setApplicationName("Login");
	$client->setClientId($client_id);
	$client->setClientSecret($client_secret);
	$client->setRedirectUri($returnurl);
	$client->setDeveloperKey($simple_api_key);
	$client->setScopes(array("https://www.googleapis.com/auth/userinfo.email", "https://www.googleapis.com/auth/userinfo.profile"));
	
	return $client;
}



function facebook() {
	global $script_path;
	
	require_once('includes/social/facebook/facebook.php');
	
	$facebook = new Facebook(array(
	'appId' => getSetting("fb_appid", "text"),
	'secret' => getSetting("fb_appsecret", "text"),
	'fileUpload' => false,
	'allowSignedRequest' => false));
	
	return $facebook;
}



function twitter() {
	global $script_path;
	
	require_once("includes/social/twitter/autoload.php"); 
	
	$consumer_key = getSetting("consumer_key", "text");
	$consumer_secret = getSetting("consumer_secret", "text");
	
	if(!empty($_SESSION['oauth_token']) && !empty($_SESSION['oauth_token_secret'])) {
		$twitter = new Abraham\TwitterOAuth\TwitterOAuth(getSetting("consumer_key", "text"), getSetting("consumer_secret", "text"), $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
	} elseif(!empty($_SESSION['twitter_token']) && !empty($_SESSION['twitter_token_secret'])) {
		$twitter = new Abraham\TwitterOAuth\TwitterOAuth(getSetting("consumer_key", "text"), getSetting("consumer_secret", "text"), $_SESSION['twitter_token'], $_SESSION['twitter_token_secret']);
	} else {
		$twitter = new Abraham\TwitterOAuth\TwitterOAuth($consumer_key, $consumer_secret);
	}
	
	return $twitter;
}



function socialLogin($sid, $type) {
	global $con;
	global $m;
	
	// Check if the user is already logged in
	if(!is_logged_in()) {
		$sid = mysqli_real_escape_string($con,$sid);
		$type = mysqli_real_escape_string($con,$type);
		$ip = $_SERVER['REMOTE_ADDR'];
		
		// Check if login isn't disabled or if the user is admin
		if(getSetting("disable_login", "text") == "false" || isAdminBySid($sid)) {
			// Check if everything required is filled in
			 if($type != "google" && $type != "facebook" && $type != "twitter") { // Check if the social login type exists
				return "0|||<div class='alert alert-danger' role='alert'>". $m['unknown_social_login'] ."</div>";
			} else {
				// Check if there is a failed login attempts limit and if failed logins are logged
				if(getSetting("max_failed_attempts", "text") > 0 && getSetting("log_failed_logins", "text") == "true") {
					$logs = mysqli_query($con,"SELECT * FROM login_log WHERE ip='$ip' ORDER BY id DESC");
					
					$failed = 0;
					while($l = mysqli_fetch_array($logs)) {
						if($l['success'] == "1") {
							break; // Stop while loop because a successful login is found
						} elseif(date("j-n-Y", $l['time']) != date("j-n-Y")) {
							break; // Stop while loop because the log is not FROM login_today, so it is irrelevant
						} else {
							$failed++; // Count 1 to failed to get how many failed login the IP has
						}
					}
				}
				
				
				$checkblock = mysqli_query($con,"SELECT * FROM login_blocks WHERE ip='$ip'");
				$cb = mysqli_fetch_array($checkblock);
				$timenow = time();
				
				$unblocked = 0;
				// Check if an IP is blocked, but the block has expired
				if(mysqli_num_rows($checkblock) > 0 && $cb['until'] < $timenow && !empty($cb['until']) && $cb['until'] != "0") {
					$logs = mysqli_query($con,"SELECT * FROM login_log WHERE ip='$ip' ORDER BY id DESC");
					while($l = mysqli_fetch_array($logs)) {
						$lid = $l['id'];
						if($l['success'] == "1") {
							break; // Stop while loop because the last successful log is found
						} elseif(date("j-n-Y", $l['time']) != date("j-n-Y")) {
							break; // Stop while loop because the log is not FROM login_today so it is irrelevant
						} else {
							mysqli_query($con,"DELETE FROM login_log WHERE id='$lid'"); // Delete failed log
						}
					}
					
					$bid = $cb['id'];
					mysqli_query($con,"DELETE FROM login_blocks WHERE id='$bid'"); // Delete IP block
					$unblocked = 1; // Set unblocked to 1 to let the script know the block is irrelevant
				}
				
				// Check if an IP is blocked
				if(mysqli_num_rows($checkblock) > 0 && ($cb['until'] > $timenow || empty($cb['until']) || $cb['until'] == "0") && $unblocked == 0) {
					// Check if the block is forever
					if(empty($cb['until']) || $cb['until'] == "0") {
						return "0|||<div class='alert alert-danger' role='alert'>". $m['you_are_banned'] ."<br>". $cb['reason'] ."<br><br>". $m['block_expires'] ."<br>". $m['never'] ."</div>";
					} else {
						return "0|||<div class='alert alert-danger' role='alert'>". $m['you_are_banned'] ."<br>". $cb['reason'] ."<br><br>". $m['block_expires'] ."<br>". date("d M Y", $cb['until']) ." ". $m['at'] ." ". date("G:i", $cb['until']) ."</div>";
					}
				} elseif(getSetting("max_failed_attempts", "text") > 0 && $failed >= getSetting("max_failed_attempts", "text") && $unblocked == 0) { // Check if the user has exceeded the maximum login attempts
					// Check if the IP isn't already blocked
					if(mysqli_num_rows($checkblock) == 0) {
						$time = time();
						$reason = $m['blocked'];
						
						$blocked_time = formatToSeconds(getSetting("blocked_amount", "text"), getSetting("blocked_format", "text")); // Calculate the blocked time and format to seconds
						
						if($blocked_time == "0") {
							$until = 0; // Forever
						} else {
							$until = $time + $blocked_time; // Current time with the blocked time added
						}
						
						mysqli_query($con,"INSERT INTO login_blocks(time, ip, logs, reason, until)
						VALUES ('$time','$ip','$failed','$reason','$until')");
					}
					
					return "0|||<div class='alert alert-danger' role='alert'>". $m['blocked'] ."</div>";
				} else {
					$check = mysqli_query($con,"SELECT * FROM login_users WHERE sid='$sid' AND type='$type'");
					
					// Check if the login is correct
					if(mysqli_num_rows($check) == 0) {
						return "0|||<div class='alert alert-danger' role='alert'>". $m['account_not_found'] ."</div>";
					} else {
						$c = mysqli_fetch_array($check);
						$uid = $c['id'];
						
						$bancheck = mysqli_query($con,"SELECT * FROM login_bans WHERE uid='$uid'");
						// Check if the user is banned or if the user isn't active
						if(mysqli_num_rows($bancheck) > 0) {
							return "0|||<div class='alert alert-danger' role='alert'>". $m['you_are_banned'] ."</div>";
						} elseif($c['active'] != "1") {
							if(getSetting("enable_paypal", "text") == "true" && getSetting("enable_stripe", "text") == "true") {
								if(!empty($c['paypal'])) {
									return "0|||<div class='alert alert-danger' role='alert'>". $m['need_paypal_activation'] ."<a href='login.php?retry&uid=". $c['id'] ."'>". $m['clicking_here'] ."</a></div>";
								} else {
									return "0|||<div class='alert alert-danger' role='alert'>". $m['need_stripe_activation'] ."<a href='login.php?stripe&uid=". $c['id'] ."'>". $m['clicking_here'] ."</a></div>";
								}
							} elseif(getSetting("enable_paypal", "text") == "true") {
								return "0|||<div class='alert alert-danger' role='alert'>". $m['need_paypal_activation'] ."<a href='login.php?retry&uid=". $c['id'] ."'>". $m['clicking_here'] ."</a></div>";
							} elseif(getSetting("enable_stripe", "text") == "true") {
								return "0|||<div class='alert alert-danger' role='alert'>". $m['need_stripe_activation'] ."<a href='login.php?stripe&uid=". $c['id'] ."'>". $m['clicking_here'] ."</a></div>";
							} elseif(getSetting("activation", "text") == "0") {
								mysqli_query($con,"UPDATE login_users SET active='1' WHERE id='$uid'");
								return "0|||<h5 class='text-center green'>". $m['activation_success'] ."</div>";
							} elseif(getSetting("activation", "text") == "1") {
								return "0|||<div class='alert alert-danger' role='alert'>". $m['need_email_activation'] ."<a href='login.php?resend&uid=". $c['id'] ."'>". $m['clicking_here'] ."</a></div>";
							} else {
								return "0|||<div class='alert alert-danger' role='alert'>". $m['need_activation'] ."</div>";
							}
						} else {
							$last_login = time();
							
							mysqli_query($con,"UPDATE login_users SET last_login='$last_login' WHERE id='$uid'"); // Update last login
							
							// Add needed session data
							$_SESSION['uid'] = $uid;
							$_SESSION['ip'] = $ip;
							
							if(empty($c['ip']) || empty($c['registered_on'])) {
								$registered_on = time();
								mysqli_query($con,"UPDATE login_users SET registered_on='$registered_on', ip='$ip' WHERE id='$uid'");
							}
							
							// Check if log successful logins is enabled, if so, log this login try
							if(getSetting("log_successful_logins", "text") == "true") {
								addLog("1", $_SERVER['REMOTE_ADDR'], $uid, $c['username'], $type);
							}
							
							return getTypeUrl("on_login");
						}
					}
				}
			}
		} else {
			if(getSetting("page_disabled_message", "text") == "") {
				return "0|||<div class='alert alert-danger' role='alert'>". $m['page_disabled_default'] ."</div>";
			} else {
				return "0|||<div class='alert alert-danger' role='alert'>". nl2br(getSetting("page_disabled_message", "text")) ."</div>";
			}
		}
	} else {
		return "<div class='alert alert-danger' role='alert'>". $m['already_logged_in'] ."</div>";
	}
}
?>