<?php
define('BEGIN', dirname(__FILE__));

require('api.php');

error_reporting(0);

if(!isset($con)) {
	require(BEGIN .'/../config.php');
}
if(!isset($titles)) {
	require(BEGIN .'/../lang/'. $language .'.php');
}
if(!function_exists('getSetting')) {
	require(BEGIN .'/../includes/functions.php');
}

$token = md5(session_id());


// Check if the request method is post
if($_SERVER['REQUEST_METHOD'] == "POST") {
	// Check if the posted token and current token are the same
	if($_POST['token'] == $token) {
		// Check database connection
		if(!empty($_POST['check_database']) && empty($con)) {
			// Check if install.php exists, because else this post shouldn't be used
			if(file_exists("../install.php")) {
				// Check if all fields are filled in
				if(empty($_POST['host'])) {
					echo "0|||<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_mysql_host'] ."</div>";
				} elseif(empty($_POST['user'])) {
					echo "0|||<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_mysql_user'] ."</div>";
				} elseif(empty($_POST['password'])) {
					echo "0|||<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_mysql_password'] ."</div>";
				} elseif(empty($_POST['database'])) {
					echo "0|||<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_mysql_database'] ."</div>";
				} else {
					$host = $_POST['host'];
					$user = $_POST['user'];
					$password = $_POST['password'];
					$database = $_POST['database'];
					
					// Check first if the connection doesn't work, than check if the database exists, and else return that the connection works and the database is found
					if(!mysqli_connect($host, $user, $password)) {
						echo "0|||<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['cantconnect'] ."</div>";
					} elseif(!mysqli_connect($host, $user, $password, $database)) {
						echo "0|||<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['canconnect'] ."</div>";
					} else {
						echo "1|||<div class='alert alert-success' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['connected'] ."</div>";
						
						$config = '&#60;?php
// MySQL settings
$host = "'. $host .'";
$user = "'. $user .'";
$password = "'. $password .'";
$database = "'. $database .'";

// Check if all settings are filled in
if(!empty($host) && !empty($user) && !empty($password) && !empty($database)) {
	// Try connection, if it fails throw exception to stop everything
	try {
		if(!mysqli_connect($host, $user, $password, $database)) {
			throw new Exception("MySQL can not connect, please check your settings in config.php");
		} else {
			$con = mysqli_connect($host, $user, $password, $database); // Do not edit, this is the MySQL connection
		}
	} catch(Exception $e) {
		echo $e->getMessage();
		exit;
	}
}


// Language settings
$language = "english"; // Name of the language file without extension

// Script path setting
$script_path = "/login/"; // Path to main folder from this script, starting in public_html (begin with / and end with /)
?&#62;';
						
						// Edit config, extra check if the function file_put_contents is enabled
						if(function_exists('file_put_contents')) {
							file_put_contents("../config.php", html_entity_decode($config));
						} else {
							echo "<div class='alert alert-warning' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['cant_edit'] ."<br><br>". $config ."</div>";
						}
						
						// Get the SQL code from database.sql to create the database, extra check if file_get_contents is enabled
						if(function_exists('file_get_contents')) {
							$tempcon = mysqli_connect($host, $user, $password, $database);
							$query = file_get_contents("../database.sql");
							mysqli_multi_query($tempcon,$query);
						} else {
							echo "<div class='alert alert-warning' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['cant_get'] ."</div>";
						}
						// Show button to go to next step (javascript will hide the form)
						?>
						<div class='row row-0 row-6'>
							<button onclick='$("#tabs li:eq(2) a").tab("show");' class='btn btn-primary'><?php echo $m['nextstep']; ?></button>
						</div>
						<?php
					}
				}
			}
		}
		
		
		
		// Move a permission up or down
		if(!empty($_POST['move_perm']) && is_logged_in() && is_admin()) {
			// Check if action is up or down
			if($_POST['action'] == "up") {
				$id = htmlentities(mysqli_real_escape_string($con, $_POST['id']), ENT_QUOTES);
				$get = mysqli_query($con,"SELECT * FROM login_permissions WHERE id='$id'");
				
				// Check if permission exists
				if(mysqli_num_rows($get) > 0) {
					$g = mysqli_fetch_array($get);
					$level = $g['level'];
				
					$next = $level + 1; // New permission level
					$get_next = mysqli_query($con,"SELECT * FROM login_permissions WHERE level='$next'");
					
					// Check if the permission name is admin, if so the permission can't be moved
					if($g['name'] == "Admin") {
						echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['cant_move_admin'] ."</div>";
					} else {
						// Check if there is a permission above the current one
						if(mysqli_num_rows($get_next) > 0) {
							$g_next = mysqli_fetch_array($get_next);
							$nextid = $g_next['id'];
							
							// If the next permission is admin, it can't be moved because the highest permission is admin
							if($g_next['name'] == "Admin") {
								echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['not_higher'] ."</div>";
							} else {
								// Move permission, update current one with one level up, update new one with 1 level down
								mysqli_query($con,"UPDATE login_permissions SET level='$next' WHERE id='$id'");
								mysqli_query($con,"UPDATE login_permissions SET level='$level' WHERE id='$nextid'");
							}
						} else {
							// No new permission was found, only update current one
							// NOTE: this shouldn't happen
							mysqli_query($con,"UPDATE login_permissions SET level='$next' WHERE id='$id'");
						}
					}
				} else {
					echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['id_not_found'] ."</div>";
				}
			} elseif($_POST['action'] == "down") {
				$id = mysqli_real_escape_string($con, $_POST['id']);
				$get = mysqli_query($con,"SELECT * FROM login_permissions WHERE id='$id'");
				
				// Check if permission is found
				if(mysqli_num_rows($get) > 0) {
					$g = mysqli_fetch_array($get);
					$level = $g['level'];
				
					$previous = $level - 1; // New permission level
					$get_previous = mysqli_query($con,"SELECT * FROM login_permissions WHERE level='$previous'");
					
					// Check if permission isn't admin, if so the permission can't be moved
					if($g['name'] == "Admin") {
						echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['cant_move_admin'] ."</div>";
					} else {
						// Check if previous permission exists
						if(mysqli_num_rows($get_previous) > 0) {
							$g_previous = mysqli_fetch_array($get_previous);
							$previousid = $g_previous['id'];
							
							// Check if permission level isn't going to be lower than 1
							if($level < 2 || $previous < 1) {
								echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['not_lower'] ."</div>";
							} else {
								// Move permission, update current one with one level down, update new one with 1 level up
								mysqli_query($con,"UPDATE login_permissions SET level='$previous' WHERE id='$id'");
								mysqli_query($con,"UPDATE login_permissions SET level='$level' WHERE id='$previousid'");
							}
						} else {
							// Check if permission level isn't going to be lower than 1
							if($level < 2) {
								echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['not_lower'] ."</div>";
							} else {
								// No new permission was found, only update current one
								// NOTE: this shouldn't happen
								mysqli_query($con,"UPDATE login_permissions SET level='$previous' WHERE id='$id'");
							}
						}
					}
				} else {
					echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['id_not_found'] ."</div>";
				}
			}
		}
		
		
		
		// Create permission
		if(!empty($_POST['create_perm']) && is_logged_in() && is_admin()) {
			// Check if everything that is required is filled in
			if(empty($_POST['perm_name'])) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_name'] ."</div>";
			} elseif(empty($_POST['perm_level'])) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_level'] ."</div>";
			} else {
				$name = htmlentities(mysqli_real_escape_string($con,$_POST['perm_name']));
				$id = htmlentities(mysqli_real_escape_string($con,$_POST['perm_level']));
				
				
				if(!empty($_POST['on_login'])) {
					$on_login = htmlentities(mysqli_real_escape_string($con,$_POST['on_login']), ENT_QUOTES);
				} else {
					$on_login = "";
				}
				if(!empty($_POST['on_logout'])) {
					$on_logout = htmlentities(mysqli_real_escape_string($con,$_POST['on_logout']), ENT_QUOTES);
				} else {
					$on_logout = "";
				}
				if(!empty($_POST['no_permission'])) {
					$no_permission = htmlentities(mysqli_real_escape_string($con,$_POST['no_permission']), ENT_QUOTES);
				} else {
					$no_permission = "";
				}
				
				
				$get = mysqli_query($con,"SELECT * FROM login_permissions WHERE id='$id'");
				$g = mysqli_fetch_array($get);
				$level = $g['level'];
				
				$get_admin = mysqli_query($con,"SELECT * FROM login_permissions WHERE name='Admin'");
				$ga = mysqli_fetch_array($get_admin);
				$adminlevel = $ga['level'];
				
				// Update all permissions above the new one with one level up
				for($i=$adminlevel; $i>$level; $i--) {
					$newlevel = $i + 1;
					mysqli_query($con,"UPDATE login_permissions SET level='$newlevel' WHERE level='$i'");
				}
					
				$newlevel = $level + 1;
					
				mysqli_query($con,"INSERT INTO login_permissions(name, level, on_login, on_logout, no_permission)
				VALUES ('$name','$newlevel','$on_login','$on_logout','$no_permission')"); 
				
				echo 1; // Echo 1, JavaScript will handle the rest
			}
		}
		
		
		
		// Save permission
		if(!empty($_POST['save_perm']) && is_logged_in() && is_admin()) {
			$id = htmlentities(mysqli_real_escape_string($con,$_POST['id']), ENT_QUOTES);
			
			if(!empty($_POST['on_login'])) {
				$on_login = htmlentities(mysqli_real_escape_string($con,$_POST['on_login']), ENT_QUOTES);
			} else {
				$on_login = "";
			}
			if(!empty($_POST['on_logout'])) {
				$on_logout = htmlentities(mysqli_real_escape_string($con,$_POST['on_logout']), ENT_QUOTES);
			} else {
				$on_logout = "";
			}
			if(!empty($_POST['no_permission'])) {
				$no_permission = htmlentities(mysqli_real_escape_string($con,$_POST['no_permission']), ENT_QUOTES);
			} else {
				$no_permission = "";
			}
			
			
			$check = mysqli_query($con,"SELECT * FROM login_permissions WHERE id='$id'");
			
			// Check if permission exists
			if(mysqli_num_rows($check) == 1) {
				$c = mysqli_fetch_array($check);
				
				// Check if permission name is not empty, only Admin can be empty because the input is disabled and it shouldn't be changed anyway
				if(empty($_POST['perm_name']) && $c['name'] != "Admin") {
					echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_name'] ."</div>";
				} else {
					if($c['name'] == "Admin") {
						$name = "Admin";
					} else {
						$name = htmlentities(mysqli_real_escape_string($con,$_POST['perm_name']), ENT_QUOTES);
					}
					
					// Check if the permission is admin, if so the permission name can't be updated
					if($c['name'] == "Admin") {
						mysqli_query($con,"UPDATE login_permissions SET on_login='$on_login', on_logout='$on_logout', no_permission='$no_permission' WHERE id='$id'");
						echo 1; // Echo 1, JavaScript will handle the rest
					} else {
						mysqli_query($con,"UPDATE login_permissions SET name='$name', on_login='$on_login', on_logout='$on_logout', no_permission='$no_permission' WHERE id='$id'");
						echo 1; // Echo 1, JavaScript will handle the rest
					}
				}
			} else {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['id_not_found'] ."</div>";
			}
		}
		
		
		
		// Delete permission
		if(!empty($_POST['delete_perm']) && is_logged_in() && is_admin()) {
			$id = htmlentities(mysqli_real_escape_string($con,$_POST['id']), ENT_QUOTES);
			
			$get = mysqli_query($con,"SELECT * FROM login_permissions WHERE id='$id'");
			$g = mysqli_fetch_array($get);
			$level = $g['level'] + 1;
			
			$get_admin = mysqli_query($con,"SELECT * FROM login_permissions WHERE name='Admin'");
			$ga = mysqli_fetch_array($get_admin);
			$adminlevel = $ga['level'];
			
			// Check if the permission is admin or the permission is the default permission, is so they can't be deleted
			if($g['name'] == "Admin") {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['cant_del_admin'] ."</div>";
			} elseif($g['id'] == getSetting("default_permission", "text")) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['cant_del_default'] ."</div>";
			} else {
				// Update all permissions above the deleted one with one level down
				for($i=$level; $i<=$adminlevel; $i++) {
					$newlevel = $i - 1;
					mysqli_query($con,"UPDATE login_permissions SET level='$newlevel' WHERE level='$i'");
				}
				
				$default = getSetting("default_permission", "text");
				mysqli_query($con,"UPDATE login_users SET permission='$default' WHERE permission='$id'"); // Update all users from this permission to the default permission
				
				mysqli_query($con,"DELETE FROM login_permissions WHERE id='$id'"); 
				echo 1; // Echo 1, JavaScript will handle the rest
			}
		}
		
		
		
		// Move a user from one permission to another
		if(!empty($_POST['move_user']) && is_logged_in() && is_admin()) {
			$pid = mysqli_real_escape_string($con,$_POST['pid']);
			$perm = mysqli_real_escape_string($con,$_POST['perm']);
			$users = mysqli_query($con,"SELECT * FROM login_users WHERE permission='$pid'");
			
			$count = 0;
			// Check if there are users with the permission
			if(mysqli_num_rows($users) > 0) {
				while($u = mysqli_fetch_array($users)) {
					$userid = $u['id'];
					// If the user id is posted and the permission of the user is not the same the user will be moved to the new permission
					if(!empty($_POST[$userid]) && $u['permission'] != $perm) {
						mysqli_query($con,"UPDATE login_users SET permission='$perm' WHERE id='$userid'");
						$count++; // Count moved users
					}
				}
				
				// If count is 0 no users where moved
				if($count == 0) {
					echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['no_users_selected'] ."</div>";
				} else {
					$permission = mysqli_query($con,"SELECT * FROM login_permissions WHERE id='$perm'");
					$p = mysqli_fetch_array($permission);
					$permname = $p['name'];
					
					// Shows something like: "10 users where moved to admin"
					echo "<div class='alert alert-success' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $count . $m['users_moved'] . $permname ."</div>";
				}
			} else {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['no_users_found'] ."</div>";
			}
		}
		
		
		
		// Save mail settings
		if(!empty($_POST['mail']) && is_logged_in() && is_admin()) {
			$mailtype = $_POST['mailtype'];
			$smtp_hostname = $_POST['smtp_hostname'];
			$smtp_username = $_POST['smtp_username'];
			$smtp_password = $_POST['smtp_password'];
			$smtp_port = $_POST['smtp_port'];
			
			if(!empty($_POST['smtp_ssl'])) {
				$smtp_ssl = "true";
			} else {
				$smtp_ssl = "false";
			}
			
			
			$welcome_mail_subject = $_POST['welcome_mail_subject'];
			$welcome_mail = $_POST['welcome_mail'];
			
			$validation_mail_subject = $_POST['validation_mail_subject'];
			$validation_mail = $_POST['validation_mail'];
			
			$reset_mail_subject = $_POST['reset_mail_subject'];
			$reset_mail = $_POST['reset_mail'];
			
			
			// If mail type is SMTP, check if everything required is filled in
			if($mailtype == "smtp" && empty($smtp_hostname)) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_smtp_hostname'] ."</div>";
			} elseif($mailtype == "smtp" && empty($smtp_username)) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_smtp_username'] ."</div>";
			} elseif($mailtype == "smtp" && empty($smtp_password)) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_smtp_password'] ."</div>";
			} elseif($mailtype == "smtp" && empty($smtp_port)) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_smtp_port'] ."</div>";
			} elseif($mailtype == "smtp" && !checkSMTP($smtp_hostname, $smtp_username, $smtp_password, $smtp_port, $smtp_ssl)) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['smtp_connect_failed'] ."</div>";
			} else {
				$settings = array("mailtype" => $mailtype, 
				"smtp_hostname" => $smtp_hostname, 
				"smtp_username" => $smtp_username, 
				"smtp_password" => $smtp_password, 
				"smtp_port" => $smtp_port, 
				"smtp_ssl" => $smtp_ssl, 
				"welcome_mail_subject" => $welcome_mail_subject, 
				"welcome_mail" => $welcome_mail, 
				"validation_mail_subject" => $validation_mail_subject, 
				"validation_mail" => $validation_mail, 
				"reset_mail_subject" => $reset_mail_subject,
				"reset_mail" => $reset_mail);
				
				// Update settings
				foreach($settings as $setting => $value) {
					setting($setting, $value);
				}
				
				echo "<div class='alert alert-success' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['settings_saved'] ."</div>";
			}
		}
		
		
		
		// Save main settings
		if(!empty($_POST['main_settings']) && is_logged_in() && is_admin()) {
			$page_disabled_message = $_POST['page_disabled_message'];
			$timezone = $_POST['timezone'];
			$default_permission = $_POST['default_permission'];
			$login_with = $_POST['login_with'];
			$admin_email = $_POST['admin_email'];
			$email_name = $_POST['email_name'];
			$online_time = $_POST['online_time'];
			
			if(!empty($_POST['disable_register'])) {
				$disable_register = "true";
			} else {
				$disable_register = "false";
			}
			
			if(!empty($_POST['disable_login'])) {
				$disable_login = "true";
			} else {
				$disable_login = "false";
			}
			
			if(!empty($_POST['disable_profile'])) {
				$disable_profile = "true";
			} else {
				$disable_profile = "false";
			}
			
			if(!empty($_POST['public_profiles'])) {
				$public_profiles = "true";
			} else {
				$public_profiles = "false";
			}
			
			if(!empty($_POST['username_change'])) {
				$username_change = "true";
			} else {
				$username_change = "false";
			}
			
			if(!empty($_POST['email_change'])) {
				$email_change = "true";
			} else {
				$email_change = "false";
			}
			
			if(!empty($_POST['password_change'])) {
				$password_change = "true";
			} else {
				$password_change = "false";
			}
			
			if(!empty($_POST['send_messages'])) {
				$send_messages = "true";
			} else {
				$send_messages = "false";
			}
			
			
			$settings = array("page_disabled_message" => $page_disabled_message, 
			"timezone" => $timezone, 
			"default_permission" => $default_permission, 
			"login_with" => $login_with, 
			"admin_email" => $admin_email, 
			"email_name" => $email_name, 
			"online_time" => $online_time, 
			"disable_register" => $disable_register, 
			"disable_login" => $disable_login, 
			"disable_profile" => $disable_profile, 
			"public_profiles" => $public_profiles,
			"username_change" => $username_change,
			"email_change" => $email_change,
			"password_change" => $password_change,
			"send_messages" => $send_messages);
			
			
			// Update settings
			foreach($settings as $setting => $value) {
				setting($setting, $value);
			}
				
			echo "<div class='alert alert-success' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['settings_saved'] ."</div>";
		}
		
		
		
		// Save login settings
		if(!empty($_POST['login_settings']) && is_logged_in() && is_admin()) {
			$max_failed_attempts = $_POST['max_failed_attempts'];
			$blocked_amount = $_POST['blocked_amount'];
			$blocked_format = $_POST['blocked_format'];
			
			if(empty($blocked_amount) || $blocked_amount == "0") {
				$blocked_format = "forever";
			}
			if($blocked_format == "forever") {
				$blocked_amount = "0";
			}
			
			if(!empty($_POST['log_successful_logins'])) {
				$log_successful_logins = "true";
			} else {
				$log_successful_logins = "false";
			}
			
			if(!empty($_POST['log_failed_logins'])) {
				$log_failed_logins = "true";
			} else {
				$log_failed_logins = "false";
			}
			
			
			
			if(!empty($_POST['redirect_last_page'])) {
				$redirect_last_page = "true";
			} else {
				$redirect_last_page = "false";
			}
			
			if(!empty($_POST['case_sensitive'])) {
				$case_sensitive = "true";
			} else {
				$case_sensitive = "false";
			}
			
			
			$settings = array("max_failed_attempts" => $max_failed_attempts,
			"blocked_amount" => $blocked_amount,
			"blocked_format" => $blocked_format,
			"log_successful_logins" => $log_successful_logins, 
			"log_failed_logins" => $log_failed_logins,
			"redirect_last_page" => $redirect_last_page,
			"case_sensitive" => $case_sensitive);
			
			
			// Update settings
			foreach($settings as $setting => $value) {
				setting($setting, $value);
			}
			
			// If 'Max. login attempts' is more than 0, and 'Log failed logins' is not enabled show a message that it won't work
			if(getSetting("max_failed_attempts", "text") > 0 && getSetting("log_failed_logins", "text") == "false") {
				echo "<div class='alert alert-info' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['enable_log'] ."</div>";
			}
			
			echo "<div class='alert alert-success' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['settings_saved'] ."</div>";
		}
		
		
		
		// Save registration settings
		if(!empty($_POST['registration_settings']) && is_logged_in() && is_admin()) {
			$publickey = $_POST['publickey'];
			$privatekey = $_POST['privatekey'];
			$max_ip = $_POST['max_ip'];
			
			if(!empty($_POST['recaptcha'])) {
				$recaptcha = "true";
			} else {
				$recaptcha = "false";
			}
			
			if(!empty($_POST['require_email'])) {
				$require_email = "true";
			} else {
				$require_email = "false";
			}
			
			if(!empty($_POST['send_welcome_mail'])) {
				$send_welcome_mail = "true";
			} else {
				$send_welcome_mail = "false";
			}
			
			$activation = $_POST['activation'];
			$username_minlength = $_POST['username_minlength'];
			$username_maxlength = $_POST['username_maxlength'];
			$password_minlength = $_POST['password_minlength'];
			$password_maxlength = $_POST['password_maxlength'];
			
			
			$settings = array("publickey" => $publickey, 
			"privatekey" => $privatekey, 
			"max_ip" => $max_ip, 
			"recaptcha" => $recaptcha, 
			"require_email" => $require_email, 
			"send_welcome_mail" => $send_welcome_mail,
			"activation" => $activation,
			"username_minlength" => $username_minlength,
			"username_maxlength" => $username_maxlength,
			"password_minlength" => $password_minlength,
			"password_maxlength" => $password_maxlength);
			
			
			// If reCAPTCHA is enabled and the public key or the private key is not filled in, show error
			if($recaptcha == "true" && empty($publickey)) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_publickey'] ."</div>";
			} elseif($recaptcha == "true" && empty($privatekey)) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_privatekey'] ."</div>";
			} else {
				// Update settings
				foreach($settings as $setting => $value) {
					setting($setting, $value);
				}
				
				echo "<div class='alert alert-success' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['settings_saved'] ."</div>";
			}
		}
		
		
		
		// Add input
		if(!empty($_POST['add_input']) && is_logged_in() && is_admin()) {
			$input_name = htmlentities(mysqli_real_escape_string($con, $_POST['input_name']), ENT_QUOTES);
			$input_name = str_replace(" ", "", $input_name);
			$input_public_name = htmlentities(mysqli_real_escape_string($con, $_POST['input_public_name']), ENT_QUOTES);
			$input_type = htmlentities(mysqli_real_escape_string($con, $_POST['input_type']), ENT_QUOTES);
			
			// begin extra inputs
			$input_maxlength = htmlentities(mysqli_real_escape_string($con, $_POST['input_maxlength']), ENT_QUOTES);
			$input_rows = htmlentities(mysqli_real_escape_string($con, $_POST['input_rows']), ENT_QUOTES);
			$input_min = htmlentities(mysqli_real_escape_string($con, $_POST['input_min']), ENT_QUOTES);
			$input_max = htmlentities(mysqli_real_escape_string($con, $_POST['input_max']), ENT_QUOTES);
			$input_step = htmlentities(mysqli_real_escape_string($con, $_POST['input_step']), ENT_QUOTES);
			// end extra inputs
			
			$input_placeholder = htmlentities(mysqli_real_escape_string($con, $_POST['input_placeholder']), ENT_QUOTES);
			$input_value = htmlentities(mysqli_real_escape_string($con, $_POST['input_value']), ENT_QUOTES);
			$input_error = htmlentities(mysqli_real_escape_string($con,$_POST['input_error']), ENT_QUOTES);
			
			// options
			
			if(!empty($_POST['input_required'])) {
				$input_required = "true";
			} else {
				$input_required = "false";
			}
			if(!empty($_POST['input_checked'])) {
				$input_checked = "true";
			} else {
				$input_checked = "false";
			}
			if(!empty($_POST['input_public'])) {
				$input_public = "true";
			} else {
				$input_public = "false";
			}
			
			
			// Check if everything required is filled in
			if(empty($input_name)) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_input_name'] ."</div>";
			} elseif(empty($input_public_name) && $input_type != "hidden") {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_input_public_name'] ."</div>";
			} elseif(empty($input_type)) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_input_type'] ."</div>";
			} elseif(!preg_match("/^[a-zA-Z0-9]+$/", $input_name)) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['letters_and_numbers_only'] ."</div>";
			} elseif(empty($_POST['input_error']) && $input_required == "true") {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_input_error'] ."</div>";
			} elseif($input_type == "select" && empty($_POST['name'][0])) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_option'] ."</div>";
			} elseif($input_type == "select" && empty($_POST['value'][0])) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_option'] ."</div>";
			} else {
				$input_check = mysqli_query($con,"SELECT * FROM login_inputs WHERE name='$input_name'");
				
				// Check if input name already exists
				if(mysqli_num_rows($input_check) > 0) {
					echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['input_exists'] ."</div>";
				} else {
					$get = mysqli_query($con,"SELECT * FROM login_inputs ORDER BY place DESC LIMIT 1");
					$g = mysqli_fetch_array($get);
					$place = $g['place']; // Current highest place
					$newplace = $place + 1; // New place
					
					$num = 0;
					$options = "";
					foreach($_POST['name'] as $name) {
						$options = $options ."|||". $name ."***". $_POST['value'][$num];
						$num++;
					}
					$options = htmlentities(mysqli_real_escape_string($con,substr($options, 3)), ENT_QUOTES);
					
					mysqli_query($con,"INSERT INTO login_inputs (place, name, public_name, type, placeholder, value, required, input_error, maxlength, min, max, step, rows, checked, options, public)
					VALUES ('$newplace','$input_name','$input_public_name','$input_type','$input_placeholder','$input_value','$input_required','$input_error','$input_maxlength','$input_min','$input_max','$input_step','$input_rows','$input_checked','$options','$input_public')");
					
					mysqli_query($con,"ALTER TABLE login_users ADD ". $input_name ." TEXT NOT NULL"); // Create column in user table
					
					echo 1; // Echo 1, JavaScript will handle the rest
				}
			}
		}
		
		
		
		// Move an input
		if(!empty($_POST['move_input']) && is_logged_in() && is_admin()) {
			// Check if the action is up or down
			if($_POST['action'] == "up") {
				$id = htmlentities(mysqli_real_escape_string($con, $_POST['id']), ENT_QUOTES);
				$get = mysqli_query($con,"SELECT * FROM login_inputs WHERE id='$id'");
				
				// Check if input exists
				if(mysqli_num_rows($get) > 0) {
					$g = mysqli_fetch_array($get);
					$place = $g['place']; // Current place
				
					$next = $place + 1; // New place
					$get_next = mysqli_query($con,"SELECT * FROM login_inputs WHERE place='$next'");
					
					// Check if new place exists, is not, the input can't level higher
					if(mysqli_num_rows($get_next) > 0) {
						$g_next = mysqli_fetch_array($get_next);
						$nextid = $g_next['id'];
						
						// Update current input 1 place up, and new input 1 place down
						mysqli_query($con,"UPDATE login_inputs SET place='$next' WHERE id='$id'");
						mysqli_query($con,"UPDATE login_inputs SET place='$place' WHERE id='$nextid'");
					} else {
						echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['not_higher'] ."</div>";
					}
				} else {
					echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['id_not_found'] ."</div>";
				}
			} elseif($_POST['action'] == "down") {
				$id = mysqli_real_escape_string($con, $_POST['id']);
				$get = mysqli_query($con,"SELECT * FROM login_inputs WHERE id='$id'");
				
				// Check if input exists
				if(mysqli_num_rows($get) > 0) {
					$g = mysqli_fetch_array($get);
					$place = $g['place']; // Current place
				
					$previous = $place - 1; // New place
					$get_previous = mysqli_query($con,"SELECT * FROM login_inputs WHERE place='$previous'");
					
					// Check if previous place exists
					if(mysqli_num_rows($get_previous) > 0) {
						$g_previous = mysqli_fetch_array($get_previous);
						$previousid = $g_previous['id'];
						
						// Check if new place is not lower than 1
						if($place < 2 || $previous < 1) {
							echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['not_lower'] ."</div>";
						} else {
							// Update current input 1 place down, and new input 1 place up
							mysqli_query($con,"UPDATE login_inputs SET place='$previous' WHERE id='$id'");
							mysqli_query($con,"UPDATE login_inputs SET place='$place' WHERE id='$previousid'");
						}
					} else {
						// Check if new place is not going to be lower than 1
						if($place < 2) {
							echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['not_lower'] ."</div>";
						} else {
							// Update current input 1 place down
							mysqli_query($con,"UPDATE login_inputs SET place='$previous' WHERE id='$id'");
						}
					}
				} else {
					echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['id_not_found'] ."</div>";
				}
			}
		}
		
		
		
		// Save input
		if(!empty($_POST['save_input']) && is_logged_in() && is_admin()) {
			$id = htmlentities(mysqli_real_escape_string($con,$_POST['id']), ENT_QUOTES);
			
			$input_name = htmlentities(mysqli_real_escape_string($con,$_POST['input_name']), ENT_QUOTES);
			$input_name = str_replace(" ", "", $input_name);
			$input_public_name = htmlentities(mysqli_real_escape_string($con,$_POST['input_public_name']), ENT_QUOTES);
			$type = htmlentities(mysqli_real_escape_string($con,$_POST['input_type']), ENT_QUOTES);
			$input_error = htmlentities(mysqli_real_escape_string($con,$_POST['input_error']), ENT_QUOTES);
			$input_value = htmlentities(mysqli_real_escape_string($con,$_POST['input_value']), ENT_QUOTES);
			
			// begin extra inputs
			$input_maxlength = htmlentities(mysqli_real_escape_string($con, $_POST['input_maxlength']), ENT_QUOTES);
			$input_rows = htmlentities(mysqli_real_escape_string($con, $_POST['input_rows']), ENT_QUOTES);
			$input_min = htmlentities(mysqli_real_escape_string($con, $_POST['input_min']), ENT_QUOTES);
			$input_max = htmlentities(mysqli_real_escape_string($con, $_POST['input_max']), ENT_QUOTES);
			$input_step = htmlentities(mysqli_real_escape_string($con, $_POST['input_step']), ENT_QUOTES);
			// end extra inputs
			
			if(!empty($_POST['input_placeholder'])) {
				$placeholder = htmlentities(mysqli_real_escape_string($con,$_POST['input_placeholder']), ENT_QUOTES);
			} else {
				$placeholder = "";
			}
			if(!empty($_POST['input_required'])) {
				$required = "true";
			} else {
				$required = "false";
			}
			if(!empty($_POST['input_checked'])) {
				$checked = "true";
			} else {
				$checked = "false";
			}
			if(!empty($_POST['input_public'])) {
				$input_public = "true";
			} else {
				$input_public = "false";
			}
			
			
			$check = mysqli_query($con,"SELECT * FROM login_inputs WHERE id='$id'");
			
			// Check if input exists
			if(mysqli_num_rows($check) == 1) {
				$c = mysqli_fetch_array($check);
				
				// Check if everything required is filled in
				if(empty($_POST['input_name'])) {
					echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_name'] ."</div>";
				} elseif(empty($input_public_name) && $input_type != "hidden") {
					echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_input_public_name'] ."</div>";
				} elseif(empty($_POST['input_type'])) {
					echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_type'] ."</div>";
				} elseif(!preg_match("/^[a-zA-Z0-9]+$/", $input_name)) {
					echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['letters_and_numbers_only'] ."</div>";
				} elseif(empty($_POST['input_error']) && $required == "true") {
					echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_input_error'] ."</div>";
				} else {
					$input_check = mysqli_query($con,"SELECT * FROM login_inputs WHERE name='$input_name'");
					
					// Check if input name is not already taken, except if the name has not changed
					if(mysqli_num_rows($input_check) > 0 && $c['name'] != $input_name) {
						echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['input_exists'] ."</div>";
					} else {
						$num = 0;
						$options = "";
						foreach($_POST['name'] as $name) {
							if(!empty($name) && !empty($_POST['value'][$num])) {
								$options = $options ."|||". $name ."***". $_POST['value'][$num];
							}
							$num++;
						}
						$options = htmlentities(mysqli_real_escape_string($con,substr($options, 3)), ENT_QUOTES);
					
						mysqli_query($con,"ALTER TABLE login_users CHANGE ". $c['name'] ." ". $input_name ." TEXT NOT NULL"); // Change column in user table
						mysqli_query($con,"UPDATE login_inputs SET name='$input_name', public_name='$input_public_name', type='$type', placeholder='$placeholder', required='$required', input_error='$input_error', value='$input_value', maxlength='$input_maxlength', min='$input_min', max='$input_max', step='$input_step', rows='$input_rows', checked='$checked', options='$options', public='$input_public' WHERE id='$id'");
						echo 1; // Echo 1, JavaScript will handle the rest
					}
				}
			} else {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['id_not_found'] ."</div>";
			}
		}
		
		
		
		// Delete input
		if(!empty($_POST['delete_input']) && is_logged_in() && is_admin()) {
			$id = htmlentities(mysqli_real_escape_string($con,$_POST['id']), ENT_QUOTES);
			$get = mysqli_query($con,"SELECT * FROM login_inputs WHERE id='$id'");
			$g = mysqli_fetch_array($get);
			$place = $g['place'];
			
			$last_place = mysqli_query($con,"SELECT * FROM login_inputs ORDER BY place DESC LIMIT 1");
			$lp = mysqli_fetch_array($last_place);
			$highest_place = $lp['place'];
			
			// Update all inputs with a place above the deleted one with 1 place down
			for($i=$place + 1; $i<=$highest_place; $i++) {
				$newplace = $i - 1;
				mysqli_query($con,"UPDATE login_inputs SET place='$newplace' WHERE place='$i'");
			}
			
			mysqli_query($con,"ALTER TABLE login_users DROP COLUMN ". $g['name']); // Delete column in user table
			mysqli_query($con,"DELETE FROM login_inputs WHERE id='$id'"); // Delete input
			
			echo 1; // Echo 1, JavaScript will handle the rest
		}
		
		
		
		// Save redirect settings
		if(!empty($_POST['save_redirect']) && is_logged_in() && is_admin()) {
			$redirect_login = $_POST['redirect_login'];
			if(!empty($_POST['use_redirect_login'])) {
				$use_redirect_login = "true";
			} else {
				$use_redirect_login = "false";
			}
			
			$redirect_logout = $_POST['redirect_logout'];
			if(!empty($_POST['use_redirect_logout'])) {
				$use_redirect_logout = "true";
			} else {
				$use_redirect_logout = "false";
			}
			
			$redirect_nopermission = $_POST['redirect_nopermission'];
			if(!empty($_POST['use_redirect_nopermission'])) {
				$use_redirect_nopermission = "true";
			} else {
				$use_redirect_nopermission = "false";
			}
			
			$redirect_notloggedin = $_POST['redirect_notloggedin'];
			
			
			$settings = array("use_redirect_login" => $use_redirect_login, 
			"redirect_login" => $redirect_login, 
			"use_redirect_logout" => $use_redirect_logout, 
			"redirect_logout" => $redirect_logout, 
			"use_redirect_nopermission" => $use_redirect_nopermission, 
			"redirect_nopermission" => $redirect_nopermission, 
			"use_redirect_notloggedin" => "true",
			"redirect_notloggedin" => $redirect_notloggedin);
			
			// Check if a enabled redirect is not empty
			if($use_redirect_login == "true" && empty($redirect_login)) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_url'] ."</div>";
			} elseif($use_redirect_logout == "true" && empty($redirect_logout)) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_url'] ."</div>";
			} elseif($use_redirect_nopermission == "true" && empty($redirect_nopermission)) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_url'] ."</div>";
			} elseif(empty($redirect_notloggedin)) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_notloggedin'] ."</div>";
			} else {
				// Update settings
				foreach($settings as $setting => $value) {
					setting($setting, $value);
				}
				
				echo "<div class='alert alert-success' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['settings_saved'] ."</div>";
			}
		}
		
		
		
		// Save messages
		if(!empty($_POST['save_messages']) && is_logged_in() && is_admin()) {
			$message_login = $_POST['message_login'];
			$message_logout = $_POST['message_logout'];
			$message_nopermission = $_POST['message_nopermission'];
			$message_notloggedin = $_POST['message_notloggedin'];
			
			
			$settings = array("message_login" => $_POST['message_login'], 
			"message_logout" => $message_logout, 
			"message_nopermission" => $message_nopermission, 
			"message_notloggedin" => $message_notloggedin);
			
			
			// Update settings
			foreach($settings as $setting => $value) {
				setting($setting, $value);
			}
			
			echo "<div class='alert alert-success' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['settings_saved'] ."</div>";
		}
		
		
		
		// When an admin changes a profile in the controlpanel
		if(!empty($_POST['adminprofile']) && is_logged_in() && is_admin()) {
			$uid = mysqli_real_escape_string($con,$_POST['uid']);
			
			// Check if all the required inputs are not empty, and if there is a username minimum or maximum length check if the username is correct
			if(empty($_POST['username'])) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_username'] ."</div>";
			} elseif(empty($_POST['email'])) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_email'] ."</div>";
			} elseif(empty($_POST['permission'])) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_permission'] ."</div>";
			} elseif(getSetting("username_minlength", "text") != "" && getSetting("username_minlength", "text") != "0" && strlen($_POST['username']) < getSetting("username_minlength", "text")) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['username_min'] . getSetting("username_minlength", "text") . $m['characters'] ."</div>";
			} elseif(getSetting("username_maxlength", "text") != "" && getSetting("username_maxlength", "text") != "0" && strlen($_POST['username']) > getSetting("username_maxlength", "text")) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['username_max'] . getSetting("username_maxlength", "text") . $m['characters'] ."</div>";
			} else {
				$username = htmlentities(mysqli_real_escape_string($con,$_POST['username']), ENT_QUOTES);
				$email = htmlentities(mysqli_real_escape_string($con,$_POST['email']), ENT_QUOTES);
				$permission = htmlentities(mysqli_real_escape_string($con,$_POST['permission']), ENT_QUOTES);
				
				$check_username = mysqli_query($con,"SELECT * FROM login_users WHERE username='$username'");
				$check_email = mysqli_query($con,"SELECT * FROM login_users WHERE email='$email'");
				
				// Check if a username of email is changed, if so check if the new one isn't already taken
				if(userValue($uid, "username") != $username && mysqli_num_rows($check_username) > 0) {
					echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['username_taken'] ."</div>";
				} elseif(userValue($uid, "email") != $email && mysqli_num_rows($check_email) > 0) {
					echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['email_taken'] ."</div>";
				} else {
					$inputs = mysqli_query($con,"SELECT * FROM login_inputs");
					$errors = 0;
					
					// Check if there are any inputs
					if(mysqli_num_rows($inputs) > 0) {
						while($i = mysqli_fetch_array($inputs)) {
							$name = $i['name'];
							// If a required input is empty, if so sop while loop and show error
							if(empty($_POST[$name]) && $i['required'] == "true" && $_POST[$name] != "0") {
								if(!empty($i['input_error'])) {
									echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". nl2br($i['input_error']) ."</div>";
								} else {
									echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in'] ." ". $i['name'] ."</div>";
								}
								
								$errors++; // Add 1 to errors, so the code below will not continue
								break;
							}
						}
					}
					
					// Make sure there are no errors
					if($errors == 0) {
						$inputs2 = mysqli_query($con,"SELECT * FROM login_inputs");
						while($i2 = mysqli_fetch_array($inputs2)) {
							$name = $i2['name'];
							// Check if a input is posted, or the input is a checkbox
							if(isset($_POST[$name]) || $i2['type'] == "checkbox" && empty($_POST[$name])) {
								// If the input is a checkbox, check if it is posted, if not, it is unchecked
								if($i2['type'] == "checkbox") {
									if(!isset($_POST[$name])) {
										$option = "false";
									} else {
										$option = "true";
									}
								} elseif($i2['type'] == "hidden") {
									// Use value from input, to make sure the user hasn't changed it in the browser
									$option = $i2['value'];
								} else {
									$option = htmlentities(mysqli_real_escape_string($con,$_POST[$name]), ENT_QUOTES);
								}
								
								// Update input column in user table with the input value
								mysqli_query($con,"UPDATE login_users SET ". $name ."='$option' WHERE id='$uid'");
							}
						}
						
						mysqli_query($con,"UPDATE login_users SET username='$username', email='$email', permission='$permission' WHERE id='$uid'");
						
						echo "<div class='alert alert-success' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['profile_saved'] ."</div>";
					} else {
						// Error
					}
				}
			}
		}
		
		
		
		// When a admin changes a user's password in the controlpanel
		if(!empty($_POST['adminchangepass']) && is_logged_in() && is_admin()) {
			// Check if everything required is filled in
			if(empty($_POST['newpass'])) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_newpass'] ."</div>";
			} elseif(empty($_POST['newpass2'])) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_newpass2'] ."</div>";
			} elseif($_POST['newpass'] != $_POST['newpass2']) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['password_dont_match'] ."</div>";
			} elseif(getSetting("password_minlength", "text") != "" && getSetting("password_minlength", "text") != "0" && strlen($_POST['newpass']) < getSetting("password_minlength", "text")) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['password_min'] . getSetting("password_minlength", "text") . $m['characters'] ."</div>";
			} elseif(getSetting("password_maxlength", "text") != "" && getSetting("password_maxlength", "text") != "0" && strlen($_POST['newpass']) > getSetting("password_maxlength", "text")) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['password_max'] . getSetting("password_maxlength", "text") . $m['characters'] ."</div>";
			} else {
				require_once('pbkdf2.php'); // Requires password encrypt script
				
				$newpass = mysqli_real_escape_string($con,$_POST['newpass']);
				
				$uid = mysqli_real_escape_string($con,$_POST['uid']);
				$newsalt = md5($newpass); // Create salt
				$newpassword = pbkdf2($newpass, $newsalt); // Encrypt password
				
				mysqli_query($con,"UPDATE login_users SET password='$newpassword' WHERE id='$uid'"); // Update password
				
				echo "<div class='alert alert-success' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['password_changed'] ."</div>";
			}
		}
		
		
		
		// When a user changes his profile
		if(!empty($_POST['profile']) && is_logged_in()) {
			$uid = $_SESSION['uid'];
			
			// Check if everything required is filled in
			if(empty($_POST['username']) && getSetting("username_change", "text") == "true") {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_username'] ."</div>";
			} elseif(empty($_POST['email']) && getSetting("email_change", "text") == "true") {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_email'] ."</div>";
			} elseif(getSetting("username_change", "text") == "true" && getSetting("username_minlength", "text") != "" && getSetting("username_minlength", "text") != "0" && strlen($_POST['username']) < getSetting("username_minlength", "text")) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['username_min'] . getSetting("username_minlength", "text") . $m['characters'] ."</div>";
			} elseif(getSetting("username_change", "text") == "true" && getSetting("username_maxlength", "text") != "" && getSetting("username_maxlength", "text") != "0" && strlen($_POST['username']) > getSetting("username_maxlength", "text")) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['username_max'] . getSetting("username_maxlength", "text") . $m['characters'] ."</div>";
			} else {
				// If it is allowed to change a username, check if the new username is already taken
				if(getSetting("username_change", "text") == "true") {
					$username = htmlentities(mysqli_real_escape_string($con,$_POST['username']), ENT_QUOTES);
					$check_username = mysqli_query($con,"SELECT * FROM login_users WHERE username='$username'");
				}
				// If it is allowed to change a email address, check if the new email address is already taken
				if(getSetting("email_change", "text") == "true") {
					$email = htmlentities(mysqli_real_escape_string($con,$_POST['email']), ENT_QUOTES);
					$check_email = mysqli_query($con,"SELECT * FROM login_users WHERE email='$email'");
				}
				
				// Check if a email or username is already taken if it is allowed and the username or email has changed
				if(getSetting("username_change", "text") == "true" && userValue($uid, "username") != $username && mysqli_num_rows($check_username) > 0) {
					echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['username_taken'] ."</div>";
				} elseif(getSetting("email_change", "text") == "true" && userValue($uid, "email") != $email && mysqli_num_rows($check_email) > 0) {
					echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['email_taken'] ."</div>";
				} else {
					$inputs = mysqli_query($con,"SELECT * FROM login_inputs");
					$errors = 0;
					
					// Check if there are any inputs
					if(mysqli_num_rows($inputs) > 0) {
						while($i = mysqli_fetch_array($inputs)) {
							$name = $i['name'];
							// If an input is required but empty, show error
							if(empty($_POST[$name]) && $i['required'] == "true" && $_POST[$name] != "0") {
								if(!empty($i['input_error'])) {
									echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". nl2br($i['input_error']) ."</div>";
								} else {
									echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in'] ." ". $i['name'] ."</div>";
								}
								
								$errors++; // Add 1 to errors so the script will not continue
								break; // Stop while loop
							}
						}
					}
					
					// Check if there are any errors
					if($errors == 0) {
						$inputs2 = mysqli_query($con,"SELECT * FROM login_inputs");
						while($i2 = mysqli_fetch_array($inputs2)) {
							$name = $i2['name'];
							// Check if a input is posted or the input is a checkbox
							if(isset($_POST[$name]) || $i2['type'] == "checkbox" && empty($_POST[$name])) {
								// If the input is a checkbox, and it is not posted the checkbox is unchecked
								if($i2['type'] == "checkbox") {
									if(!isset($_POST[$name])) {
										$option = "false";
									} else {
										$option = "true";
									}
								} elseif($i2['type'] == "hidden") {
									// Use value from input, to make sure the user hasn't changed it in the browser
									$option = $i2['value'];
								} else {
									$option = htmlentities(mysqli_real_escape_string($con,$_POST[$name]), ENT_QUOTES);
								}
								
								// Update input column in user table with the input value
								mysqli_query($con,"UPDATE login_users SET ". $name ."='$option' WHERE id='$uid'");
							}
						}
						
						// If it is allowed to change a username, update it
						if(getSetting("username_change", "text") == "true") {
							mysqli_query($con,"UPDATE login_users SET username='$username' WHERE id='$uid'");
						}
						// If it is allowed to change an email address, update it
						if(getSetting("email_change", "text") == "true") {
							mysqli_query($con,"UPDATE login_users SET email='$email' WHERE id='$uid'");
						}
						
						echo "<div class='alert alert-success' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['profile_saved'] ."</div>";
					} else {
						// Error
					}
				}
			}
		}
		
		
		
		// When a user changes his password, and it is allowed to change a password
		if(!empty($_POST['changepass']) && is_logged_in() && getSetting("password_change", "text") == "true" && userValue(null, "password") != "") {
			// Check if everything required is filled in and some extra checks
			if(empty($_POST['oldpass'])) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_oldpass'] ."</div>";
			} elseif(empty($_POST['newpass'])) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_newpass'] ."</div>";
			} elseif(empty($_POST['newpass2'])) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_newpass2'] ."</div>";
			} elseif($_POST['newpass'] != $_POST['newpass2']) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['password_dont_match'] ."</div>";
			} elseif(getSetting("password_minlength", "text") != "" && getSetting("password_minlength", "text") != "0" && strlen($_POST['newpass']) < getSetting("password_minlength", "text")) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['password_min'] . getSetting("password_minlength", "text") . $m['characters'] ."</div>";
			} elseif(getSetting("password_maxlength", "text") != "" && getSetting("password_maxlength", "text") != "0" && strlen($_POST['newpass']) > getSetting("password_maxlength", "text")) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['password_max'] . getSetting("password_maxlength", "text") . $m['characters'] ."</div>";
			} else {
				require_once('pbkdf2.php'); // Requires password encrypt script
				
				$oldpass = mysqli_real_escape_string($con,$_POST['oldpass']);
				$newpass = mysqli_real_escape_string($con,$_POST['newpass']);
				$newpass2 = mysqli_real_escape_string($con,$_POST['newpass2']);
				
				$uid = $_SESSION['uid'];
				$oldsalt = md5($oldpass); // Create salt from old password
				$oldpassword = pbkdf2($oldpass, $oldsalt); // Encrypt old password
			
				$check = mysqli_query($con,"SELECT * FROM login_users WHERE id='$uid' AND password='$oldpassword'");
				
				// Check if the old password is correct
				if(mysqli_num_rows($check) == 0) {
					echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['old_password_incorrect'] ."</div>";
				} else {
					$newsalt = md5($newpass); // Create salt from new password
					$newpassword = pbkdf2($newpass, $newsalt); // Encrypt new password
					
					mysqli_query($con,"UPDATE login_users SET password='$newpassword' WHERE id='$uid' AND password='$oldpassword'"); // Update new password
					
					echo "<div class='alert alert-success' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['password_changed'] ."</div>";
				}
			}
		}
		
		
		
		// Set a password, only available for social login users with no password
		if(!empty($_POST['setpass']) && is_logged_in() && userValue($_SESSION['uid'], "password") == "") {
			// Check if everything required is filled in and some extra checks
			if(empty($_POST['newpass'])) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_newpass'] ."</div>";
			} elseif(empty($_POST['newpass2'])) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_newpass2'] ."</div>";
			} elseif($_POST['newpass'] != $_POST['newpass2']) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['password_dont_match'] ."</div>";
			} elseif(getSetting("password_minlength", "text") != "" && getSetting("password_minlength", "text") != "0" && strlen($_POST['newpass']) < getSetting("password_minlength", "text")) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['password_min'] . getSetting("password_minlength", "text") . $m['characters'] ."</div>";
			} elseif(getSetting("password_maxlength", "text") != "" && getSetting("password_maxlength", "text") != "0" && strlen($_POST['newpass']) > getSetting("password_maxlength", "text")) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['password_max'] . getSetting("password_maxlength", "text") . $m['characters'] ."</div>";
			} else {
				require_once('pbkdf2.php'); // Requires password encrypt script
				
				$newpass = mysqli_real_escape_string($con,$_POST['newpass']);
				$newpass2 = mysqli_real_escape_string($con,$_POST['newpass2']);
				
				$newsalt = md5($newpass); // Create salt from new password
				$newpassword = pbkdf2($newpass, $newsalt); // Encrypt new password
				
				$uid = $_SESSION['uid'];
				mysqli_query($con,"UPDATE login_users SET password='$newpassword' WHERE id='$uid'"); // Update new password
				
				echo "<div class='alert alert-success' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['password_set'] ."</div>";
			}
		}
		
		
		
		// Add IP block
		if(!empty($_POST['add_block']) && is_logged_in() && is_admin()) {
			// Check if everything required is filled in
			if(empty($_POST['ip'])) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_ip'] ."</div>";
			} elseif(empty($_POST['reason'])) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_reason'] ."</div>";
			} elseif(checkIp($_POST['ip']) == false) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_valid_ip'] ."</div>";
			} else {
				$ip = htmlentities(mysqli_real_escape_string($con,$_POST['ip']), ENT_QUOTES);
				$reason = htmlentities(mysqli_real_escape_string($con,$_POST['reason']), ENT_QUOTES);
				$time = time();
				$logs = "0";
				
				$blocked_amount = htmlentities(mysqli_real_escape_string($con,$_POST['blocked_amount']), ENT_QUOTES);
				$blocked_format = htmlentities(mysqli_real_escape_string($con,$_POST['blocked_format']), ENT_QUOTES);
				
				// If blocked amount is not filled in or is 0, the block will be forever
				if(empty($blocked_amount) || $blocked_amount == "0") {
					$blocked_format = "forever";
				}
				// If the blocked format is forever, the blocked amount is 0
				if($blocked_format == "forever") {
					$blocked_amount = "0";
				}
				
				$blocked_time = formatToSeconds($blocked_amount, $blocked_format); // Calculate format and amount to seconds
				
				if($blocked_time == "0") {
					$until = 0; // Forever
				} else {
					$until = $time + $blocked_time; // Current time with the blocked time added
				}
				
				$check = mysqli_query($con,"SELECT * FROM login_blocks WHERE ip='$ip'");
				// Check if IP is already blocked
				if(mysqli_num_rows($check) == 0) {
					mysqli_query($con,"INSERT INTO login_blocks(time, ip, logs, reason, until) VALUES('$time','$ip','$logs','$reason','$until')"); // Add block
					
					echo "<div class='alert alert-success' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['block_added'] ."</div>";
				} else {
					echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['ip_already_blocked'] ."</div>";
				}
			}
		}
		
		
		
		// Delete IP block
		if(!empty($_POST['delete_block']) && is_logged_in() && is_admin()) {
			$deleted = 0;
			
			$blocks = mysqli_query($con,"SELECT * FROM login_blocks");
			while($b = mysqli_fetch_array($blocks)) {
				$id = $b['id'];
				$ip = $b['ip'];
				// Check if block id is posted
				if(!empty($_POST[$id])) {
					$logs = mysqli_query($con,"SELECT * FROM login_log WHERE ip='$ip' ORDER BY id DESC");
					// This deletes all failed logins from the IP after the last successful login
					// This is because else the user would be blocked at the first login because he still has too many failed login
					while($l = mysqli_fetch_array($logs)) {
						$lid = $l['id'];
						if($l['success'] == "1") {
							break; // Stop the while loop because it has reached the last successful login
						} elseif(date("j-n-Y", $l['time']) != date("j-n-Y")) {
							break; // Stop the while loop because the last login is longer as a day ago, so it is not longer relevant
						} else {
							mysqli_query($con,"DELETE FROM login_log WHERE id='$lid'"); // Delete failed login
						}
					}
					
					mysqli_query($con,"DELETE FROM login_blocks WHERE id='$id'"); // Delete IP block
					$deleted++; // Add 1 to deleted
				}
			}
			
			// If deleted is not 0, show how many blocks are deleted
			if($deleted > 0) {
				echo "<div class='alert alert-success' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $deleted . $m['blocks_deleted'] ."</div>";
			} else {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['no_blocks_selected'] ."</div>";
			}
		}
		
		
		
		// Change the permission of multiple users in the controlpanel
		if(!empty($_POST['change_permission']) && is_logged_in() && is_admin()) {
			// Check if permission isn't empty
			if(empty($_POST['permission'])) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_permission'] ."</div>";
			} else {
				$permission = htmlentities(mysqli_real_escape_string($con,$_POST['permission']), ENT_QUOTES);
				$count = 0;
				
				$users = mysqli_query($con,"SELECT * FROM login_users");
				while($u = mysqli_fetch_array($users)) {
					$uid = $u['id'];
					// Check if user id is posted and the permission is not the same
					if(!empty($_POST[$uid]) && $u['permission'] != $permission) {
						mysqli_query($con,"UPDATE login_users SET permission='$permission' WHERE id='$uid'"); // Update user with new permission
						$count++; // Add 1 to count
					}
				}
				
				// If count is not 0, show how many users have moved
				if($count == 0) {
					echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['no_users_selected'] ."</div>";
				} else {
					$perm = mysqli_query($con,"SELECT * FROM login_permissions WHERE id='$permission'");
					$p = mysqli_fetch_array($perm);
					
					echo "<div class='alert alert-success' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $count . $m['users_moved'] . $p['name'] ."</div>";
				}
			}
		}
		
		
		
		// Ban a user in the controlpanel
		if(!empty($_POST['ban_user']) && is_logged_in() && is_admin()) {
			$banned = 0;
			
			$users = mysqli_query($con,"SELECT * FROM login_users");
			while($u = mysqli_fetch_array($users)) {
				$uid = $u['id'];
				$check = mysqli_query($con,"SELECT * FROM login_bans WHERE uid='$uid'");
				// Check if the user id is posted, and the user is not an admin and the user isn't already banned
				if(!empty($_POST[$uid]) && $u['permission'] != "1" && mysqli_num_rows($check) == 0) {
					$time = time();
					
					mysqli_query($con,"INSERT INTO login_bans (time, uid) VALUES ('$time', '$uid')"); // Ban user
					$banned++; // Add 1 to banned
				}
			}
			
			// If banned more than 0, show how many users are banned
			if($banned > 0) {
				echo "<div class='alert alert-success' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $banned . $m['users_banned'] ."</div>";
			} else {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['no_user_selected'] ."</div>";
			}
		}
		
		
		
		// Unban a user in the controlpanel
		if(!empty($_POST['unban_user']) && is_logged_in() && is_admin()) {
			$unbanned = 0;
			
			$users = mysqli_query($con,"SELECT * FROM login_users");
			while($u = mysqli_fetch_array($users)) {
				$uid = $u['id'];
				$check = mysqli_query($con,"SELECT * FROM login_bans WHERE uid='$uid'");
				// Check if the user id is posted, and the user is not an admin and the user is banned
				if(!empty($_POST[$uid]) && $u['permission'] != "1" && mysqli_num_rows($check) != 0) {
					$time = time();
					
					mysqli_query($con,"DELETE FROM login_bans WHERE uid='$uid'"); // Unban user
					$unbanned++; // Add 1 to unbanned
				}
			}
			
			// If unbanned more than 0, show how many users are unbanned
			if($unbanned > 0) {
				echo "<div class='alert alert-success' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $unbanned . $m['users_unbanned'] ."</div>";
			} else {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['no_user_selected'] ."</div>";
			}
		}
		
		
		
		// Activate a user in the controlpanel
		if(!empty($_POST['activate_user']) && is_logged_in() && is_admin()) {
			$activated = 0;
			
			$users = mysqli_query($con,"SELECT * FROM login_users");
			while($u = mysqli_fetch_array($users)) {
				$uid = $u['id'];
				// Check if the user id is posted, and the user is not an admin and the user isn't already active
				if(!empty($_POST[$uid]) && $u['permission'] != "1" && $u['active'] != "1") {
					$time = time();
					
					mysqli_query($con,"UPDATE login_users SET active='1' WHERE id='$uid'"); // Activate user
					$activated++; // Add 1 to activated
				}
			}
			
			// If activated more than 0, show how many users are activated
			if($activated > 0) {
				echo "<div class='alert alert-success' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $activated . $m['users_activated'] ."</div>";
			} else {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['no_user_selected'] ."</div>";
			}
		}
		
		
		
		// Deactivate a user in the controlpanel
		if(!empty($_POST['deactivate_user']) && is_logged_in() && is_admin()) {
			$deactivated = 0;
			
			$users = mysqli_query($con,"SELECT * FROM login_users");
			while($u = mysqli_fetch_array($users)) {
				$uid = $u['id'];
				// Check if the user id is posted, and the user is not an admin and the user is active
				if(!empty($_POST[$uid]) && $u['permission'] != "1" && $u['active'] != "0") {
					$time = time();
					
					mysqli_query($con,"UPDATE login_users SET active='0' WHERE id='$uid'"); // Deactivate user
					$deactivated++; // Add 1 to deactivated
				}
			}
			
			// If deactivated more than 0, show how many users are deactivated
			if($deactivated > 0) {
				echo "<div class='alert alert-success' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $deactivated . $m['users_deactivated'] ."</div>";
			} else {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['no_user_selected'] ."</div>";
			}
		}
		
		
		
		// Delete a user in the controlpanel
		if(!empty($_POST['delete_user']) && is_logged_in() && is_admin()) {
			$deleted = 0;
			
			$users = mysqli_query($con,"SELECT * FROM login_users");
			while($u = mysqli_fetch_array($users)) {
				$uid = $u['id'];
				// Check if the user id is posted, and the user is not an admin
				if(!empty($_POST[$uid]) && $u['permission'] != "1") {
					$time = time();
					
					mysqli_query($con,"DELETE FROM login_users WHERE id='$uid'"); // Delete user
					$deleted++; // Add 1 to deleted
				}
			}
			
			// If deleted more than 0, show how many users are deleted
			if($deleted > 0) {
				echo "<div class='alert alert-success' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $deleted . $m['users_deleted'] ."</div>";
			} else {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['no_user_selected'] ."</div>";
			}
		}
		
		
		
		// Send mass message
		if(!empty($_POST['mass_message']) && is_logged_in() && is_admin()) {
			// Check if everything required is filled in
			if(empty($_POST['sendto'])) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_sendto'] ."</div>";
			} elseif(empty($_POST['subject'])) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_subject'] ."</div>";
			} elseif(empty($_POST['message'])) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_message'] ."</div>";
			} else {
				$sendto = htmlentities(mysqli_real_escape_string($con,$_POST['sendto']), ENT_QUOTES);
				$subject = htmlentities(mysqli_real_escape_string($con,$_POST['subject']), ENT_QUOTES);
				$message = htmlentities(mysqli_real_escape_string($con,$_POST['message']), ENT_QUOTES);
				$from = $_SESSION['uid'];
				$time = time();
				
				// If sendto is everyone, everyone will receive the message, else only a specific permission will receive the message
				if($sendto == "everyone") {
					$users = mysqli_query($con,"SELECT * FROM login_users");
					$total = mysqli_num_rows($users);
					
					while($u = mysqli_fetch_array($users)) {
						$to = $u['id'];
						mysqli_query($con,"INSERT INTO login_messages(sendfrom, sendto, subject, message, time, opened) VALUES ('$from','$to','$subject','$message','$time','0')"); // Create message
					}
				} else {
					$users = mysqli_query($con,"SELECT * FROM login_users WHERE permission='$sendto'");
					$total = mysqli_num_rows($users);
					
					while($u = mysqli_fetch_array($users)) {
						$to = $u['id'];
						mysqli_query($con,"INSERT INTO login_messages(sendfrom, sendto, subject, message, time, opened) VALUES ('$from','$to','$subject','$message','$time','0')"); // Create message
					}
				}
					
				echo "<div class='alert alert-success' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $total . $m['message_send'] ."</div>";
			}
		}
		
		
		
		// Create backup
		if(!empty($_POST['create_backup']) && is_logged_in() && is_admin()) {
			$return = "";
			$tables = array();
			$result = mysqli_query($con,"SHOW TABLES"); // Get tables
			while($row = mysqli_fetch_row($result)) {
				$tables[] = $row[0];
			}
			
			foreach($tables as $table) {
				$result = mysqli_query($con,"SELECT * FROM ". $table);
				$rows = mysqli_num_fields($result);
				
				$return .= "DROP TABLE IF EXISTS ". $table .";";
				$row2 = mysqli_fetch_row(mysqli_query($con,"SHOW CREATE TABLE ". $table));
				$return .= "\n\n".$row2[1].";\n\n";
				
				for ($i=0; $i<$rows; $i++) {
					while($row = mysqli_fetch_row($result)) {
						$return .= "INSERT INTO ". $table ." VALUES(";
						for($j=0; $j<$rows; $j++) {
							$row[$j] = addslashes($row[$j]);
							$row[$j] = preg_replace("/\n/", "\\n", $row[$j]);
							
							if(isset($row[$j])) { 
								$return .= "'". $row[$j] ."'"; 
							} else { 
								$return .= "''"; 
							}
							
							if($j<($rows - 1)) { 
								$return .= ","; 
							}
						}
						$return .= ");\n";
					}
				}
				$return .= "\n\n\n";
			}
			
			echo $m['save_backup'];
			echo "<pre id='backup_code'>". htmlentities($return, ENT_QUOTES) ."</pre>"; // Show backup code, this code should be saved to a .sql file or just filled in in a SQL query
			echo "<script>function selectText(id){var doc=document,text=doc.getElementById(id),range,selection;if(doc.body.createTextRange){range=document.body.createTextRange();range.moveToElementText(text);range.select()}else if(window.getSelection){selection=window.getSelection();range=document.createRange();range.selectNodeContents(text);selection.removeAllRanges();selection.addRange(range)}} selectText('backup_code');</script>";
		}
		
		
		
		// Mark messages as opened
		if(!empty($_POST['open_messages']) && is_logged_in()) {
			$opened = 0;
			
			$messages = mysqli_query($con,"SELECT * FROM login_messages");
			while($msg = mysqli_fetch_array($messages)) {
				$mid = $msg['id'];
				$uid = $_SESSION['uid'];
				
				// Check if the message id is posted, and the receiver is the current user and the message isn't already opened
				if(!empty($_POST[$mid]) && $msg['sendto'] == $uid && $msg['opened'] != "1") {
					mysqli_query($con,"UPDATE login_messages SET opened='1' WHERE id='$mid' AND sendto='$uid'"); // Open message
					$opened++; // Add 1 to opened to count how many message there are opened
				}
			}
			
			echo "<div class='alert alert-success' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $opened . $m['messages_opened'] ."</div>";
		}
		
		
		
		// Delete selected messages
		if(!empty($_POST['delete_messages']) && is_logged_in()) {
			$deleted = 0;
			
			$messages = mysqli_query($con,"SELECT * FROM login_messages");
			while($msg = mysqli_fetch_array($messages)) {
				$mid = $msg['id'];
				$uid = $_SESSION['uid'];
				
				// Check if the message is id posted, and the receiver is the current user
				if(!empty($_POST[$mid]) && $msg['sendto'] == $uid) {
					mysqli_query($con,"DELETE FROM login_messages WHERE id='$mid' AND sendto='$uid'"); // Delete message
					$deleted++; // Add 1 to messages to count how many messages are deleted
				}
			}
			
			echo "<div class='alert alert-success' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $deleted . $m['messages_deleted'] ."</div>";
		}
		
		
		
		// When a users sends a message
		if(!empty($_POST['send_message']) && is_logged_in()) {
			// Check if users are allowed to send messages
			if(getSetting("send_messages", "text") == "true") {
				// Check if everything required is filled in
				if(empty($_POST['sendto'])) {
					echo "0|||<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_sendto'] ."</div>";
				} elseif(empty($_POST['subject'])) {
					echo "0|||<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_subject'] ."</div>";
				} elseif(empty($_POST['message'])) {
					echo "0|||<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_message'] ."</div>";
				} else {
					$subject = htmlentities(mysqli_real_escape_string($con,$_POST['subject']), ENT_QUOTES);
					$message = htmlentities(mysqli_real_escape_string($con,$_POST['message']), ENT_QUOTES);
					$from = $_SESSION['uid'];
					$time = time();
					
					$check = mysqli_query($con,"SELECT * FROM login_messages WHERE sendfrom='$from' ORDER BY id DESC LIMIT 1");
					// Check if the user already has send a message, and if so, check when that was
					if(mysqli_num_rows($check) > 0) {
						$c = mysqli_fetch_array($check);
						$wait = time() - $c['time'];
					}
					
					// Check if the user has already send a message in the last 30 seconds
					// NOTE: you can change the number 30 to how much seconds you want it to be before a user can send a message again
					if(mysqli_num_rows($check) > 0 && $wait < 30) {
						$wait2 = 30 - $wait; // NOTE: this 30 has to be the same as the 30 above, if you want a correct message
						echo "0|||<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['message_cooldown1'] . $wait2 . $m['message_cooldown2'] ."</div>";
					} else {
						$total = 0;
						// For every selected user
						foreach($_POST['sendto'] as $to) {
							$to = htmlentities(mysqli_real_escape_string($con,$to), ENT_QUOTES);
							
							mysqli_query($con,"INSERT INTO login_messages(sendfrom, sendto, subject, message, time, opened) VALUES ('$from','$to','$subject','$message','$time','0')"); // Create message
							$total++; // Add 1 to total
						}
						
						echo "1|||<div class='alert alert-success' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $total . $m['message_send'] ."</div>";
					}
				}
			}
		}
		
		
		
		// This is for the clean tools, javascript will update how many messages or logs or users a clean action would delete
		if(!empty($_POST['count_items']) && is_logged_in() && is_admin()) {
			$from = htmlentities(mysqli_real_escape_string($con,$_POST['from']), ENT_QUOTES);
			$from = strtotime($from);
			$item = htmlentities(mysqli_real_escape_string($con,$_POST['item']), ENT_QUOTES);
			
			// Check the item, what is gonna be deleted
			if($item == "messages" && $from) {
				$get = mysqli_query($con,"SELECT * FROM login_messages WHERE time < ". $from);
				$count = mysqli_num_rows($get);
				
				// If count is 0, show 0 in green, else show the number in red
				if($count == 0) {
					echo "<font color='green'>". $count ." ". $item ."</font>";
				} else {
					echo "<font color='red'>". $count ." ". $item ."</font>";
				}
			} elseif($item == "logs" && $from) {
				$get = mysqli_query($con,"SELECT * FROM login_log WHERE time < ". $from);
				$count = mysqli_num_rows($get);
				
				// If count is 0, show 0 in green, else show the number in red
				if($count == 0) {
					echo "<font color='green'>". $count ." ". $item ."</font>";
				} else {
					echo "<font color='red'>". $count ." ". $item ."</font>";
				}
			} elseif($item == "users" && $from) {
				$based = htmlentities(mysqli_real_escape_string($con,$_POST['based']), ENT_QUOTES);
				
				// Check if based on is last login or registration date
				if($based == "activity") {
					$get = mysqli_query($con,"SELECT * FROM login_users WHERE last_login < ". $from ." AND last_login != '' AND username != 'admin'");
				} else {
					$get = mysqli_query($con,"SELECT * FROM login_users WHERE registered_on < ". $from ." AND username != 'admin'");
				}
				
				$count = mysqli_num_rows($get);
				
				// If count is 0, show 0 in green, else show the number in red
				if($count == 0) {
					echo "<font color='green'>". $count ." ". $item ."</font>";
				} else {
					echo "<font color='red'>". $count ." ". $item ."</font>";
				}
			} elseif($item == "delete_inactive") {
				$get = mysqli_query($con,"SELECT * FROM login_users WHERE active='0'");
				$count = mysqli_num_rows($get);
				
				// If count is 0, show 0 in green, else show the number in red
				if($count == 0) {
					echo "<div class='alert alert-danger' role='alert'>". $m['this_will_delete'] ." 0 ". $m['inactive_users'] ."</div>";
				} else {
					echo "<div class='alert alert-warning' role='alert'>". $m['this_will_delete'] ." ". $count ." ". $m['inactive_users'] ."</div>";
				}
			} elseif($item == "delete_never_loggedin") {
				$get = mysqli_query($con,"SELECT * FROM login_users WHERE last_login=''");
				$count = mysqli_num_rows($get);
				
				// If count is 0, show 0 in green, else show the number in red
				if($count == 0) {
					echo "<div class='alert alert-danger' role='alert'>". $m['this_will_delete'] ." 0 ". $m['never_loggedin_users'] ."</div>";
				} else {
					echo "<div class='alert alert-warning' role='alert'>". $m['this_will_delete'] ." ". $count ." ". $m['never_loggedin_users'] ."</div>";
				}
			} else {
				// Invalid timestamp or item not found
				echo "<font color='red'>". $m['fill_in_legit_date'] ."</font>";
			}
		}
		
		
		
		// Clean messages
		if(!empty($_POST['clean_messages']) && is_logged_in() && is_admin()) {
			// Check if everything required is filled in
			if(empty($_POST['from'])) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_from'] ."</div>";
			} elseif(empty($_POST['password'])) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_admin_pass'] ."</div>";
			} else {
				$from = htmlentities(mysqli_real_escape_string($con,$_POST['from']), ENT_QUOTES);
				
				require_once('pbkdf2.php'); // Requires password encryption script
				$pass = mysqli_real_escape_string($con,$_POST['password']);
				$salt = md5($pass); // Create salt
				$password = pbkdf2($pass, $salt); // Encrypt password
				$uid = $_SESSION['uid'];
				
				$check = mysqli_query($con,"SELECT * FROM login_users WHERE id='$uid' AND password='$password'");
				// Check if password is correct
				if(mysqli_num_rows($check) != "1") {
					echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['wrong_admin_pass'] ."</div>";
				} else {
					$from = strtotime($from); // Set given time to timestamp
					$deleted = 0;
					
					$messages = mysqli_query($con,"SELECT * FROM login_messages WHERE time < ". $from); // Select everything older than the given timestamp
					while($msg = mysqli_fetch_array($messages)) {
						$id = $msg['id'];
						mysqli_query($con,"DELETE FROM login_messages WHERE id='$id'"); // Delete message
						$deleted++; // Add 1 to deleted to show how many message are deleted
					}
					
					echo "<div class='alert alert-success' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $deleted . $m['messages_cleaned'] ."</div>";
				}
			}
		}
		
		
		
		// Clean logs
		if(!empty($_POST['clean_logs']) && is_logged_in() && is_admin()) {
			// Check if everything required is filled in
			if(empty($_POST['from'])) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_from'] ."</div>";
			} elseif(empty($_POST['password'])) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_admin_pass'] ."</div>";
			} else {
				$from = htmlentities(mysqli_real_escape_string($con,$_POST['from']), ENT_QUOTES);
				
				require_once('pbkdf2.php'); // Requires password encryption script
				$pass = mysqli_real_escape_string($con,$_POST['password']);
				$salt = md5($pass); // Create salt
				$password = pbkdf2($pass, $salt); // Encrypt password
				$uid = $_SESSION['uid'];
				
				$check = mysqli_query($con,"SELECT * FROM login_users WHERE id='$uid' AND password='$password'");
				// Check if password is correct
				if(mysqli_num_rows($check) != "1") {
					echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['wrong_admin_pass'] ."</div>";
				} else {
					$from = strtotime($from); // Set given time to timestamp
					$deleted = 0;
					
					$logs = mysqli_query($con,"SELECT * FROM login_log WHERE time < ". $from); // Select everything older than the given timestamp
					while($l = mysqli_fetch_array($logs)) {
						$id = $l['id'];
						mysqli_query($con,"DELETE FROM login_log WHERE id='$id'"); // Delete log
						$deleted++; // Add 1 to deleted to show how many logs are deleted
					}
					
					echo "<div class='alert alert-success' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $deleted . $m['logs_cleaned'] ."</div>";
				}
			}
		}
		
		
		
		// Clean users
		if(!empty($_POST['clean_users']) && is_logged_in() && is_admin()) {
			// Check if everything required is filled in
			if(empty($_POST['from'])) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_from'] ."</div>";
			} elseif(empty($_POST['based'])) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_based_on'] ."</div>";
			} elseif(empty($_POST['password'])) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_admin_pass'] ."</div>";
			} else {
				$from = htmlentities(mysqli_real_escape_string($con,$_POST['from']), ENT_QUOTES);
				$based = htmlentities(mysqli_real_escape_string($con,$_POST['based']), ENT_QUOTES);
				
				require_once('pbkdf2.php'); // Requires password encryption script
				$pass = mysqli_real_escape_string($con,$_POST['password']);
				$salt = md5($pass); // Create salt
				$password = pbkdf2($pass, $salt); // Encrypt password
				$uid = $_SESSION['uid'];
				
				$check = mysqli_query($con,"SELECT * FROM login_users WHERE id='$uid' AND password='$password'");
				// Check if password is correct
				if(mysqli_num_rows($check) != "1") {
					echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['wrong_admin_pass'] ."</div>";
				} else {
					$from = strtotime($from); // Set given time to timestamp
					$deleted = 0;
					
					// Check if it is based on registration date or last login (activity)
					if($based == "activity") {
						$users = mysqli_query($con,"SELECT * FROM login_users WHERE last_login < ". $from ." AND last_login != ''"); // Select everything older than the given timestamp and where the user has already logged in
						while($u = mysqli_fetch_array($users)) {
							// Make sure that the user is not an admin
							if($u['permission'] != "1") {
								$id = $u['id'];
								mysqli_query($con,"DELETE FROM login_users WHERE id='$id'"); // Delete user
								$deleted++; // Add 1 to deleted to show how many users are deleted
							}
						}
					} else {
						$users = mysqli_query($con,"SELECT * FROM login_users WHERE registered_on < ". $from); // Select everything older than the given timestamp
						while($u = mysqli_fetch_array($users)) {
							// Make sure that the user is not an admin
							if($u['permission'] != "1") {
								$id = $u['id'];
								mysqli_query($con,"DELETE FROM login_users WHERE id='$id'"); // Delete user
								$deleted++; // Add 1 to deleted to show how many users are deleted
							}
						}
					}
					
					echo "<div class='alert alert-success' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $deleted . $m['users_cleaned'] ."</div>";
				}
			}
		}
		
		
		
		// Deletes all the inactive users
		if(!empty($_POST['delete_inactive']) && is_logged_in() && is_admin()) {
			// Check if everything required is filled in
			if(empty($_POST['password'])) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_admin_pass'] ."</div>";
			} else {		
				require_once('pbkdf2.php'); // Requires the encryption script
				$pass = mysqli_real_escape_string($con,$_POST['password']);
				$salt = md5($pass); // Create salt
				$password = pbkdf2($pass, $salt); // Encrypt password
				$uid = $_SESSION['uid'];
				
				$check = mysqli_query($con,"SELECT * FROM login_users WHERE id='$uid' AND password='$password'");
				// Check if password is correct
				if(mysqli_num_rows($check) != "1") {
					echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['wrong_admin_pass'] ."</div>";
				} else {
					mysqli_query($con,"DELETE FROM login_users WHERE active='0'"); // Delete if user is not active
					
					echo "<div class='alert alert-success' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['deleted_all_inactive'] ."</div>";
				}
			}
		}
		
		
		
		// Deletes all the users who never logged in
		if(!empty($_POST['delete_never_loggedin']) && is_logged_in() && is_admin()) {
			// Check if everything required is filled in
			if(empty($_POST['password'])) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_admin_pass'] ."</div>";
			} else {
				require_once('pbkdf2.php'); // Requires the encryption script
				$pass = mysqli_real_escape_string($con,$_POST['password']);
				$salt = md5($pass); // Create salt
				$password = pbkdf2($pass, $salt); // Encrypt password
				$uid = $_SESSION['uid'];
				
				$check = mysqli_query($con,"SELECT * FROM login_users WHERE id='$uid' AND password='$password'");
				// Check if the password is correct
				if(mysqli_num_rows($check) != "1") {
					echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['wrong_admin_pass'] ."</div>";
				} else {
					mysqli_query($con,"DELETE FROM login_users WHERE last_login=''"); // Delete all users where last login is empty
					
					echo "<div class='alert alert-success' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['deleted_never_loggedin'] ."</div>";
				}
			}
		}
		
		
		
		// When a user changes his avatar image
		if(!empty($_POST['change_avatar']) && is_logged_in()) {
			// 
			if(empty($_FILES["file"]["type"])) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_file'] ."</div>";
			} else {
				$extensions = array("jpeg", "jpg", "png", "gif"); // All allowed extensions, you can add one if you want, but you have to add it also in the if statement below
				$split = explode(".", $_FILES["file"]["name"]); // Split filename
				$extension = end($split); // Last split result is the extension
				$size = getimagesize($_FILES["file"]["tmp_name"]); // Get image type
				
				// Checks if the file type is an image, and if the size of the file isn't too big, and a extra check if the filename extensions matches the image extension
				// NOTE: you can add and file type here, but you have to add it in the array above too, and you can change the maximum file size
				if(((($size["mime"] == "image/png") 
				|| ($size["mime"] == "image/jpg") 
				|| ($size["mime"] == "image/gif")
				|| ($size["mime"] == "image/jpeg"))
				&& ($_FILES["file"]["size"] < 2097152)
				&& in_array($extension, $extensions))) {
					// Check if the file contains an error
					if ($_FILES["file"]["error"] > 0) {
						echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['unknown_error'] ."</div>";
					} else {
						$uid = $_SESSION['uid'];
						$image = $_FILES['file']['tmp_name']; // Get temporary image
						$newname = mysqli_real_escape_string($con, time() ."_". $_SESSION['uid'] ."_". $_FILES['file']['name']); // Create new name with current timestamp and user id and image name
						$target = "../uploads/". $newname;
						
						$check = mysqli_query($con,"SELECT avatar FROM login_users WHERE id='$uid'");
						$c = mysqli_fetch_array($check);
						// Check if image is not the same as current one to prevent image spamming
						if(file_get_contents($_FILES['file']['tmp_name']) != file_get_contents("../uploads/". $c['avatar'])) {
							move_uploaded_file($image, $target); // Move file to uploads folder
							mysqli_query($con,"UPDATE login_users SET avatar='$newname' WHERE id='$uid'"); // Update the users avatar with only the new file name
							
							echo "<div class='alert alert-success' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['file_saved'] ."</div>";
						} else {
							echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['already_saved'] ."</div>";
						}
					}
				} else {
					echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['invalid_file'] ."</div>";
				}
			}
		}
		
		
		
		// Add user via controlpanel
		if(!empty($_POST['add_user']) && is_logged_in() && is_admin()) {
			if(empty($_POST['username'])) { // Check if the username is filled in
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_username'] ."</div>";
			} elseif(getSetting("username_minlength", "text") != "" && getSetting("username_minlength", "text") != "0" && strlen($_POST['username']) < getSetting("username_minlength", "text")) { // Check if the username isn't shorter as the minimum length
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['username_min'] . getSetting("username_minlength", "text") . $m['characters'] ."</div>";
			} elseif(getSetting("username_maxlength", "text") != "" && getSetting("username_maxlength", "text") != "0" && strlen($_POST['username']) > getSetting("username_maxlength", "text")) { // Check if the username isn't longer as the maximum length
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['username_max'] . getSetting("username_maxlength", "text") . $m['characters'] ."</div>";
			} elseif(empty($_POST['email'])) { // Check if the email address is filled in
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_email'] ."</div>";
			} elseif(!checkEmail($_POST['email'])) { // Check if the mail address is valid
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['invalid_email'] ."</div>";
			} elseif(empty($_POST['password'])) { // Check if the password is filled in
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_password'] ."</div>";
			} elseif(empty($_POST['password2'])) { // Check if the password is repeated
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_password2'] ."</div>";
			} elseif($_POST['password'] != $_POST['password2']) { // Check if the repeated password matches the password
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['password_dont_match'] ."</div>";
			} elseif(getSetting("password_minlength", "text") != "" && getSetting("password_minlength", "text") != "0" && strlen($_POST['password']) < getSetting("password_minlength", "text")) { // Check if the password isn't shorter as the minimum length
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['password_min'] . getSetting("password_minlength", "text") . $m['characters'] ."</div>";
			} elseif(getSetting("password_maxlength", "text") != "" && getSetting("password_maxlength", "text") != "0" && strlen($_POST['password']) > getSetting("password_maxlength", "text")) { // Check if the password isn't longer as the maximum length
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['password_max'] . getSetting("password_maxlength", "text") . $m['characters'] ."</div>";
			} else {
				// Basic variables
				$username = htmlentities(mysqli_real_escape_string($con,$_POST['username']), ENT_QUOTES);
				$email = htmlentities(mysqli_real_escape_string($con,$_POST['email']), ENT_QUOTES);
				$registered_on = time();
				$permission = mysqli_real_escape_string($con,$_POST['permission']);
				
				
				$check_username = mysqli_query($con,"SELECT * FROM login_users WHERE username='$username'");
				$check_email = mysqli_query($con,"SELECT * FROM login_users WHERE email='$email'");
				
				// Check if username or email is already taken
				if(mysqli_num_rows($check_username) > 0) {
					echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['username_taken'] ."</div>";
				} elseif(mysqli_num_rows($check_email) > 0) {
					echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['email_taken'] ."</div>";
				} else {
					require_once('pbkdf2.php'); // Requires password encryption script
					$pass = mysqli_real_escape_string($con,$_POST['password']);
					$salt = md5($pass); // Create salt
					$password = pbkdf2($pass, $salt); // Encrypt password
					
					$inputs = mysqli_query($con,"SELECT * FROM login_inputs");
					$errors = 0;
					
					if(mysqli_num_rows($inputs) > 0) {
						while($i = mysqli_fetch_array($inputs)) {
							$name = $i['name'];
							// If the input is required but not posted, show error
							if(empty($_POST[$name]) && $i['required'] == "true" && $_POST[$name] != "0") {
								if(!empty($i['input_error'])) {
									echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". nl2br($i['input_error']) ."</div>";
								} else {
									echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in'] ." ". $i['name'] ."</div>";
								}
								
								$errors++; // Add 1 to errors, so the script won't continue
								break;
							}
						}
					}
					
					// Check if there are any errors
					if($errors == 0) {
						$activate_code = md5(sha1($username . mt_rand(100, 1000000))); // Create random activate code
						
						mysqli_query($con,"INSERT INTO login_users (username, email, avatar, password, registered_on, last_login, last_active, last_action, ip, permission, active, activate_code, paypal, banned, type, sid)
						VALUES ('$username','$email','','$password','$registered_on','','','','','$permission','1','$activate_code','','0','admin','')");
						
						
						$inputs2 = mysqli_query($con,"SELECT * FROM login_inputs");
						while($i2 = mysqli_fetch_array($inputs2)) {
							$name = $i2['name'];
							// Check if input is posted, or the if the input is an checkbox
							if(!empty($_POST[$name]) || $i2['type'] == "checkbox" && empty($_POST[$name])) {
								// If the input is an checkbox, and is it not posted, the checkbox is unchecked
								if($i2['type'] == "checkbox") {
									if(!isset($_POST[$name])) {
										$option = "false";
									} else {
										$option = "true";
									}
								} elseif($i2['type'] == "hidden") {
									// Use the value from the input table, because the user might have changed it in his browser
									$option = $i2['value'];
								} else {
									$option = htmlentities(mysqli_real_escape_string($con,$_POST[$name]), ENT_QUOTES);
								}
								
								// Update the input column in the user table
								mysqli_query($con,"UPDATE login_users SET ". $name ."='$option' WHERE username='$username'");
							}
						}
						
						
						echo "<div class='alert alert-success' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['user_created'] ."</div>";
					} else {
						// Error
					}
				}
			}
		}
		
		
		
		// Save social login settings
		if(!empty($_POST['social_settings']) && is_logged_in() && is_admin()) {
			if(!empty($_POST['social_verification'])) {
				$social_verification = "true";
			} else {
				$social_verification = "false";
			}
			if(!empty($_POST['social_pay'])) {
				$social_pay = "true";
			} else {
				$social_pay = "false";
			}
			if(!empty($_POST['enable_google'])) {
				$enable_google = "true";
			} else {
				$enable_google = "false";
			}
			if(!empty($_POST['enable_facebook'])) {
				$enable_facebook = "true";
			} else {
				$enable_facebook = "false";
			}
			if(!empty($_POST['enable_twitter'])) {
				$enable_twitter = "true";
			} else {
				$enable_twitter = "false";
			}
			
			// If Facebook is enabled, check if everything required is filled in
			if($enable_google == "true" && empty($_POST['client_id'])) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_client_id'] ."</div>";
			} elseif($enable_google == "true" && empty($_POST['client_secret'])) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_client_secret'] ."</div>";
			} elseif($enable_google == "true" && empty($_POST['api_key'])) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_api_key'] ."</div>";
			} elseif($enable_facebook == "true" && empty($_POST['fb_appid'])) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_fb_appid'] ."</div>";
			} elseif($enable_facebook == "true" && empty($_POST['fb_appsecret'])) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_fb_appsecret'] ."</div>";
			} elseif($enable_twitter == "true" && empty($_POST['consumer_key'])) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_consumer_key'] ."</div>";
			} elseif($enable_twitter == "true" && empty($_POST['consumer_secret'])) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_consumer_secret'] ."</div>";
			} else {
				$client_id = htmlentities(mysqli_real_escape_string($con,$_POST['client_id']), ENT_QUOTES);
				$client_secret = htmlentities(mysqli_real_escape_string($con,$_POST['client_secret']), ENT_QUOTES);
				$api_key = htmlentities(mysqli_real_escape_string($con,$_POST['api_key']), ENT_QUOTES);
				
				$fb_appid = htmlentities(mysqli_real_escape_string($con,$_POST['fb_appid']), ENT_QUOTES);
				$fb_appsecret = htmlentities(mysqli_real_escape_string($con,$_POST['fb_appsecret']), ENT_QUOTES);
				
				$consumer_key = htmlentities(mysqli_real_escape_string($con,$_POST['consumer_key']), ENT_QUOTES);
				$consumer_secret = htmlentities(mysqli_real_escape_string($con,$_POST['consumer_secret']), ENT_QUOTES);
				
				$settings = array("social_verification" => $social_verification,
				"social_pay" => $social_pay,
				"enable_google" => $enable_google,
				"client_id" => $client_id,
				"client_secret" => $client_secret,
				"api_key" => $api_key,
				"enable_facebook" => $enable_facebook,
				"fb_appid" => $fb_appid,
				"fb_appsecret" => $fb_appsecret,
				"enable_twitter" => $enable_twitter,
				"consumer_key" => $consumer_key,
				"consumer_secret" => $consumer_secret);
				
				// Update settings
				foreach($settings as $setting => $value) {
					setting($setting, $value);
				}
				
				echo "<div class='alert alert-success' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['settings_saved'] ."</div>";
			}
		}
		
		
		
		// Save PayPal settings
		if(!empty($_POST['paypal']) && is_logged_in() && is_admin()) {
			if(!empty($_POST['enable_paypal'])) {
				$enable_paypal = "true";
			} else {
				$enable_paypal = "false";
			}
			
			// If paypal is enabled, check if everything required is filled in
			if($enable_paypal == "true" && empty($_POST['paypal_email'])) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_paypal_email'] ."</div>";
			} elseif($enable_paypal == "true" && !checkEmail($_POST['paypal_email'])) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['invalid_paypal_email'] ."</div>";
			} else {
				$paypal_email = htmlentities(mysqli_real_escape_string($con,$_POST['paypal_email']), ENT_QUOTES);
				$paypal_currency = htmlentities(mysqli_real_escape_string($con,$_POST['paypal_currency']), ENT_QUOTES);
				$paypal_cost = htmlentities(mysqli_real_escape_string($con,$_POST['paypal_cost']), ENT_QUOTES);
				
				$settings = array("paypal_email" => $paypal_email,
				"enable_paypal" => $enable_paypal,
				"paypal_currency" => $paypal_currency,
				"paypal_cost" => $paypal_cost);
				
				// Update settings
				foreach($settings as $setting => $value) {
					setting($setting, $value);
				}
				
				echo "<div class='alert alert-success' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['settings_saved'] ."</div>";
			}
		}
		
		
		
		// Save Stripe settings
		if(!empty($_POST['stripe_settings']) && is_logged_in() && is_admin()) {
			if(!empty($_POST['enable_stripe'])) {
				$enable_stripe = "true";
			} else {
				$enable_stripe = "false";
			}
			
			// If Stripe is enabled, check if everything required is filled in
			if($enable_stripe == "true" && empty($_POST['stripe_key'])) {
				echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['fill_in_stripe_key'] ."</div>";
			} else {
				$stripe_key = htmlentities(mysqli_real_escape_string($con,$_POST['stripe_key']), ENT_QUOTES);
				$stripe_currency = htmlentities(mysqli_real_escape_string($con,$_POST['stripe_currency']), ENT_QUOTES);
				$stripe_cost = htmlentities(mysqli_real_escape_string($con,$_POST['stripe_cost']), ENT_QUOTES);
				
				$settings = array("enable_stripe" => $enable_stripe,
				"stripe_key" => $stripe_key,
				"stripe_currency" => $stripe_currency,
				"stripe_cost" => $stripe_cost);
				
				// Update settings
				foreach($settings as $setting => $value) {
					setting($setting, $value);
				}
				
				echo "<div class='alert alert-success' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['settings_saved'] ."</div>";
			}
		}
		
		
		
		// When a user requests the forgotten password function
		if(!empty($_POST['forgot_pass']) && !is_logged_in()) {
			// Check if everything required is filled in
			if(empty($_POST['username'])) {
				echo "<h5 class='text-center red'>". $m['fill_in_username'] ."</h5>";
			} elseif(empty($_POST['email'])) {
				echo "<h5 class='text-center red'>". $m['fill_in_email'] ."</h5>";
			} else {
				$username = htmlentities(mysqli_real_escape_string($con,$_POST['username']), ENT_QUOTES);
				$email = htmlentities(mysqli_real_escape_string($con,$_POST['email']), ENT_QUOTES);
				
				$check = mysqli_query($con,"SELECT * FROM login_users WHERE username='$username' AND email='$email'");
				// Check if username and email address matches
				if(mysqli_num_rows($check) == 1) {
					$u = mysqli_fetch_array($check);
					
					$ip = mysqli_real_escape_string($con,$_SERVER['REMOTE_ADDR']);
					$code_check = mysqli_query($con,"SELECT * FROM login_forgot_codes WHERE ip='$ip' ORDER BY id DESC LIMIT 1"); // Get last forgotten password request from IP
					$cc = mysqli_fetch_array($code_check);
					$newtime = $cc['time'] + 60; // NOTE: you can change this to how much seconds you want it to be
					
					// Check if last request is longer as 60 seconds ago
					if(mysqli_num_rows($code_check) == 0 || mysqli_num_rows($code_check) == 1 && $newtime < time()) {
						$time = time();
						$uid = $u['id'];
						$code = sha1(md5($uid + mt_rand(100, 1000000))); // Create random code
						$ip = $_SERVER['REMOTE_ADDR'];
						$reset_url = getTypeUrl("reset") . $code; // Get reset URL with the code
						
						mysqli_query($con,"INSERT INTO login_forgot_codes (time, uid, used, code, ip)
						VALUES ('$time','$uid','0','$code','$ip')");
						
						$subject = getSetting("reset_mail_subject", "text");
						$subject = str_replace("{reset_url}", $reset_url, $subject);
						$subject = str_replace("{name}", $username, $subject);
						$subject = str_replace("{email}", $email, $subject);
						$subject = str_replace("{date}", date("j-n-Y", $time), $subject);
						$subject = str_replace("{ip}", $ip, $subject);
						
						$message = getSetting("reset_mail", "text");
						$message = str_replace("{reset_url}", $reset_url, $message);
						$message = str_replace("{name}", $username, $message);
						$message = str_replace("{email}", $email, $message);
						$message = str_replace("{date}", date("j-n-Y", $time), $message);
						$message = str_replace("{ip}", $ip, $message);
						$message = nl2br($message);
						$message = html_entity_decode($message);
						
						sendMail($email, $subject, $message, $uid); // Send mail to user to give the link to change his password
						
						echo "<h5 class='text-center green'>". $m['reset_mail_send'] ."</h5>";
					} else {
						echo "<h5 class='text-center red'>". $m['forgot_wait'] ."</h5>";
					}
				} else {
					echo "<h5 class='text-center red'>". $m['not_found'] ."</h5>";
				}
			}
		}
		
		
		
		// When a user clicks on the reset URL in his mail and fills in his new password
		if(!empty($_POST['forgot_changepass']) && !is_logged_in()) {
			// Check if everything required is filled in and some extra checks
			if(empty($_POST['password'])) {
				echo "<h5 class='text-center red'>". $m['fill_in_password'] ."</h5>";
			} elseif(empty($_POST['password2'])) {
				echo "<h5 class='text-center red'>". $m['fill_in_password2'] ."</h5>";
			} elseif($_POST['password'] != $_POST['password2']) {
				echo "<h5 class='text-center red'>". $m['password_dont_match'] ."</h5>";
			} elseif(getSetting("password_minlength", "text") != "" && getSetting("password_minlength", "text") != "0" && strlen($_POST['password']) < getSetting("password_minlength", "text")) {
				echo "<h5 class='text-center red'>". $m['password_min'] . getSetting("password_minlength", "text") . $m['characters'] ."</h5>";
			} elseif(getSetting("password_maxlength", "text") != "" && getSetting("password_maxlength", "text") != "0" && strlen($_POST['password']) > getSetting("password_maxlength", "text")) {
				echo "<h5 class='text-center red'>". $m['password_max'] . getSetting("password_maxlength", "text") . $m['characters'] ."</h5>";
			} else {
				$ip = mysqli_real_escape_string($con,$_SERVER['REMOTE_ADDR']);
				$code = mysqli_real_escape_string($con,$_POST['code']);
				$check_code = mysqli_query($con,"SELECT * FROM login_forgot_codes WHERE code='$code' AND ip='$ip'");
				// Check if code exists
				if(mysqli_num_rows($check_code) == 0) {
					echo "<h5 class='text-center red'>". $m['code_not_found'] ."</h5>";
				} else {
					$cc = mysqli_fetch_array($check_code);
					// Check if code is not older than 6 hours
					if(strtotime("+6 hours", $cc['time']) > time()) {
						// Check if code isn't used already
						if($cc['used'] == "0") {
							require_once('pbkdf2.php'); // Requires password encryption script
							$pass = mysqli_real_escape_string($con,$_POST['password']);
							$salt = md5($pass); // Create salt
							$password = pbkdf2($pass, $salt); // Encrypt password
							
							$uid = $cc['uid'];
							
							mysqli_query($con,"UPDATE login_users SET password='$password' WHERE id='$uid'"); // Update user with new password
							mysqli_query($con,"UPDATE login_forgot_codes SET used='1' WHERE code='$code'"); // Update code to used
							
							echo "<h5 class='text-center green'>". $m['password_changed'] ."</h5>";
						} else {
							echo "<h5 class='text-center red'>". $m['code_used'] ."</h5>";
						}
					} else {
						echo "<h5 class='text-center red'>". $m['code_expired'] ."</h5>";
					}
				}
			}
		}
		
		
		
		// Stripe payment
		if(!empty($_POST['stripe_pay'])) {
			if(getSetting("enable_stripe", "text") == "true") {
				// Assign post variables
				$card = $_POST['card'];
				$month = $_POST['month'];
				$year = $_POST['year'];
				$cvc = $_POST['cvc'];
				
				// Check if everything is filled in
				if(empty($card)) {
					echo "0|||<h5 class='text-center red'>". $m['fill_in_cart'] ."</h5>";
				} elseif(empty($month)) {
					echo "0|||<h5 class='text-center red'>". $m['fill_in_month'] ."</h5>";
				} elseif(empty($year)) {
					echo "0|||<h5 class='text-center red'>". $m['fill_in_year'] ."</h5>";
				} elseif(empty($cvc)) {
					echo "0|||<h5 class='text-center red'>". $m['fill_in_cvc'] ."</h5>";
				} else {
					// Require Stripe API
					require_once('stripe/lib/Stripe.php');
					Stripe::setApiKey(getSetting("stripe_key", "text")); // Private API key
					
					$uid = mysqli_real_escape_string($con,$_POST['uid']);
					
					$amount = getSetting("stripe_cost", "text") * 100;
					$currency = getSetting("stripe_currency", "text");
					$description = $m['registration_of'] . userValue($uid, "username");
					
					$month = str_pad($month, 2, "0", STR_PAD_LEFT);
					
					// Send Stripe payment
					try {
						$pay = Stripe_Charge::create(array(
						"amount" 		=> $amount,
						"currency" 		=> $currency,
						"card" 			=> array(
										"number" => $card,
										"exp_month" => $month,
										"exp_year" => $year,
										"cvc" => $cvc),
						"description" 	=> $description));
						
						$pay = json_decode(str_replace("Stripe_Charge JSON: ", "", $pay), true);
						
						if($pay['paid'] == 1) {
							mysqli_query($con,"UPDATE login_users SET active='1' WHERE id='$uid'");
							
							echo "1|||<h5 class='text-center green'>". $m['stripe_paid'] ."</h5>";
						} else {
							echo "0|||<h5 class='text-center red'>". $m['stripe_not_paid'] ."</h5>";
						} 
					} catch(Stripe_CardError $e) {
						// Stripe error
						$error = $e->getJsonBody();
						$er  = $error['error'];

						if($er['code'] == "incorrect_number") {
							echo "0|||<h5 class='text-center red'>". $m['stripe_error_num1'] ."</h5>";
						} elseif($er['code'] == "invalid_number") {
							echo "0|||<h5 class='text-center red'>". $m['stripe_error_num2'] ."</h5>";
						} elseif($er['code'] == "invalid_expiry_month") {
							echo "0|||<h5 class='text-center red'>". $m['stripe_error_month'] ."</h5>";
						} elseif($er['code'] == "invalid_expiry_year") {
							echo "0|||<h5 class='text-center red'>". $m['stripe_error_year'] ."</h5>";
						} elseif($er['code'] == "invalid_cvc") {
							echo "0|||<h5 class='text-center red'>". $m['stripe_error_cvc1'] ."</h5>";
						} elseif($er['code'] == "expired_card") {
							echo "0|||<h5 class='text-center red'>". $m['stripe_error_card'] ."</h5>";
						} elseif($er['code'] == "incorrect_cvc") {
							echo "0|||<h5 class='text-center red'>". $m['stripe_error_cvc2'] ."</h5>";
						} elseif($er['code'] == "incorrect_zip") {
							echo "0|||<h5 class='text-center red'>". $m['stripe_error_zip'] ."</h5>";
						} elseif($er['code'] == "card_declined") {
							echo "0|||<h5 class='text-center red'>". $m['stripe_error_declined'] ."</h5>";
						} elseif($er['code'] == "missing") {
							echo "0|||<h5 class='text-center red'>". $m['stripe_error_missing'] ."</h5>";
						} elseif($er['code'] == "processing_error") {
							echo "0|||<h5 class='text-center red'>". $m['stripe_error'] ."</h5>";
						} elseif($er['code'] == "rate_limit") {
							echo "0|||<h5 class='text-center red'>". $m['stripe_error'] ."</h5>";
						} else {
							echo "0|||<h5 class='text-center red'>". $m['stripe_error'] ."</h5>";
						}
					} catch (Stripe_InvalidRequestError $e) {
						// Invalid parameters were supplied to Stripe's API
						echo "0|||<h5 class='text-center red'>". $m['stripe_error'] ."</h5>";
					} catch (Stripe_AuthenticationError $e) {
						// Authentication with Stripe's API failed
						// (maybe you changed API keys recently)
						echo "0|||<h5 class='text-center red'>". $m['stripe_error'] ."</h5>";
					} catch (Stripe_ApiConnectionError $e) {
						// Network communication with Stripe failed
						echo "0|||<h5 class='text-center red'>". $m['stripe_error'] ."</h5>";
					} catch (Stripe_Error $e) {
						// Display a very generic error to the user, and maybe send
						// yourself an email
						echo "0|||<h5 class='text-center red'>". $m['stripe_error'] ."</h5>";
					} catch (Exception $e) {
						// Something else happened, completely unrelated to Stripe
						echo "0|||<h5 class='text-center red'>". $m['stripe_error'] ."</h5>";
					}
				}
			}
		}
		
		
		
		// Registration
		if(!empty($_POST['register'])) {
			$ip = mysqli_real_escape_string($con,$_SERVER['REMOTE_ADDR']);
			$checkblock = mysqli_query($con,"SELECT * FROM login_blocks WHERE ip='$ip'");
			$cb = mysqli_fetch_array($checkblock);
			$timenow = time();
			
			// Check if an IP is blocked
			if(mysqli_num_rows($checkblock) > 0 && ($cb['until'] > $timenow || empty($cb['until']) || $cb['until'] == "0")) {
				echo "<h5 class='text-center red'>". $m['ip_blocked'] ."</h5>";
			} else {
				// Check if the user is already logged in
				if(!is_logged_in()) {
					// Check if reCAPTCHA is enabled, if so, check if the user filled in the right reCAPTCHA
					if(getSetting("recaptcha", "text") == "true") {
						require_once('recaptchalib.php');
						$privatekey = getSetting("privatekey", "text");
						$resp = null;
						$error = null;
						
						$reCaptcha = new ReCaptcha($privatekey);
						
						if(isset($_POST['g-recaptcha-response'])) {
							$resp = $reCaptcha->verifyResponse($_SERVER['REMOTE_ADDR'], $_POST['g-recaptcha-response']);
						}
					}
					
					// If there is a registration limit per IP, check if this IP already has registered before and how many times
					if(getSetting("max_ip", "text") > 0) {
						$checkip = mysqli_query($con,"SELECT * FROM login_users WHERE ip='$ip'");
						$numip = mysqli_num_rows($checkip);
					}
					
					if(getSetting("max_ip", "text") > 0 && $numip >= getSetting("max_ip", "text")) { // Checks if the IP has exceeded his registration limit
						echo "<h5 class='text-center red'>". $m['max_accounts'] ."</h5>";
					} elseif(getSetting("recaptcha", "text") == "true" && ($resp == null || !$resp->success)) { // Check if reCAPTCHA is enabled and if the given answer is correct
						echo "<h5 class='text-center red'>". $m['recaptcha_invalid'] ."</h5>";
					} elseif(empty($_POST['username'])) { // Check if the username is filled in
						echo "<h5 class='text-center red'>". $m['fill_in_username'] ."</h5>";
					} elseif(getSetting("username_minlength", "text") != "" && getSetting("username_minlength", "text") != "0" && strlen($_POST['username']) < getSetting("username_minlength", "text")) { // Check if the username isn't shorter as the minimum length
						echo "<h5 class='text-center red'>". $m['username_min'] . getSetting("username_minlength", "text") . $m['characters'] ."</h5>";
					} elseif(getSetting("username_maxlength", "text") != "" && getSetting("username_maxlength", "text") != "0" && strlen($_POST['username']) > getSetting("username_maxlength", "text")) { // Check if the username isn't longer as the maximum length
						echo "<h5 class='text-center red'>". $m['username_max'] . getSetting("username_maxlength", "text") . $m['characters'] ."</h5>";
					} elseif(empty($_POST['email'])) { // Check if the email address is filled in
						echo "<h5 class='text-center red'>". $m['fill_in_email'] ."</h5>";
					} elseif(!checkEmail($_POST['email'])) { // Check if the mail address is valid
						echo "<h5 class='text-center red'>". $m['invalid_email'] ."</h5>";
					} elseif(empty($_POST['password'])) { // Check if the password is filled in
						echo "<h5 class='text-center red'>". $m['fill_in_password'] ."</h5>";
					} elseif(empty($_POST['password2'])) { // Check if the password is repeated
						echo "<h5 class='text-center red'>". $m['fill_in_password2'] ."</h5>";
					} elseif($_POST['password'] != $_POST['password2']) { // Check if the repeated password matches the password
						echo "<h5 class='text-center red'>". $m['password_dont_match'] ."</h5>";
					} elseif(getSetting("password_minlength", "text") != "" && getSetting("password_minlength", "text") != "0" && strlen($_POST['password']) < getSetting("password_minlength", "text")) { // Check if the password isn't shorter as the minimum length
						echo "<h5 class='text-center red'>". $m['password_min'] . getSetting("password_minlength", "text") . $m['characters'] ."</h5>";
					} elseif(getSetting("password_maxlength", "text") != "" && getSetting("password_maxlength", "text") != "0" && strlen($_POST['password']) > getSetting("password_maxlength", "text")) { // Check if the password isn't longer as the maximum length
						echo "<h5 class='text-center red'>". $m['password_max'] . getSetting("password_maxlength", "text") . $m['characters'] ."</h5>";
					} else {
						// Basic variables
						$username = htmlentities(mysqli_real_escape_string($con,$_POST['username']), ENT_QUOTES);
						$email = htmlentities(mysqli_real_escape_string($con,$_POST['email']), ENT_QUOTES);
						$registered_on = time();
						$permission = getSetting("default_permission", "text"); // Get the default permission id
						
						
						$check_username = mysqli_query($con,"SELECT * FROM login_users WHERE username='$username'");
						$check_email = mysqli_query($con,"SELECT * FROM login_users WHERE email='$email'");
						
						// Check if username or email is already taken
						if(mysqli_num_rows($check_username) > 0) {
							echo "<h5 class='text-center red'>". $m['username_taken'] ."</h5>";
						} elseif(mysqli_num_rows($check_email) > 0) {
							echo "<h5 class='text-center red'>". $m['email_taken'] ."</h5>";
						} else {
							require_once('pbkdf2.php'); // Requires password encryption script
							$pass = mysqli_real_escape_string($con,$_POST['password']);
							$salt = md5($pass); // Create salt
							$password = pbkdf2($pass, $salt); // Encrypt password
							
							if(getSetting("activation", "text") == "1") { // If activation type is 1, the user has to be activated by validating his mail
								$active = "0";
							} elseif(getSetting("activation", "text") == "2") { // If activation type is 2, the user has to be manually activated by an admin
								$active = "0";
							} elseif(getSetting("enable_paypal", "text") == "true" &&  getSetting("paypal_cost", "text") != "0") { // If paypal is enabled, the user has to pay before he is activated
								$active = "0";
							} elseif(getSetting("enable_stripe", "text") == "true" &&  getSetting("stripe_cost", "text") != "0") { // If stripe is enabled, the user has to pay before he is activated
								$active = "0";
							} else {
								$active = "1"; // No activation needed
							}
							
							$inputs = mysqli_query($con,"SELECT * FROM login_inputs");
							$errors = 0;
							
							if(mysqli_num_rows($inputs) > 0) {
								while($i = mysqli_fetch_array($inputs)) {
									$name = $i['name'];
									// If the input is required but not posted, show error
									if(empty($_POST[$name]) && $i['required'] == "true" && $_POST[$name] != "0") {
										if(!empty($i['input_error'])) {
											echo "<h5 class='text-center red'>". nl2br($i['input_error']) ."</h5>";
										} else {
											echo "<h5 class='text-center red'>". $m['fill_in'] ." ". $i['name'] ."</h5>";
										}
										
										$errors++; // Add 1 to errors, so the script won't continue
										break;
									}
								}
							}
							
							// Extra check if registration page is not disabled
							if(getSetting("disable_register", "text") == "false") {
								// Check if there are any errors
								if($errors == 0) {
									$activate_code = md5(sha1($username . mt_rand(100, 1000000))); // Create random activate code
									
									mysqli_query($con,"INSERT INTO login_users (username, email, avatar, password, registered_on, last_login, last_active, last_action, ip, permission, active, activate_code, paypal, banned, type, sid)
									VALUES ('$username','$email','','$password','$registered_on','','','','$ip','$permission','$active','$activate_code','','0','website','')");
									
									
									// Check if there is a payment method posted, in case there are 2 payment gateways enabled
									if(empty($_POST['pay_method'])) {
										$method = "0";
									} elseif($_POST['pay_method'] == "Stripe") {
										$method = "2";
									} else {
										$method = "1";
									}
									
									// Check if paypal is enabled, and check if the price is more than 0, if so, create PayPal pay URL
									if((getSetting("enable_paypal", "text") == "true" && getSetting("paypal_cost", "text") != "0" && getSetting("enable_stripe", "text") != "true") || $method == "1") {
										// Send to paypal site and if paid, the activation mail will be send
										$getuid = mysqli_query($con,"SELECT id FROM login_users WHERE username='$username'");
										$gu = mysqli_fetch_array($getuid);
										
										$querystring = "?business=". urlencode(getSetting("paypal_email", "text")) ."&";
										
										$querystring .= "item_name=". urlencode($m['registration_of'] . $username) ."&";
										$querystring .= "amount=". urlencode(getSetting("paypal_cost", "text")) ."&";
										
										$querystring .= "currency_code=". urlencode(stripslashes(getSetting("paypal_currency", "text"))) ."&";
										$querystring .= "no_shipping=". urlencode(stripslashes("1")) ."&";
										
										$querystring .= "cmd=". urlencode(stripslashes("_xclick")) ."&";
										$querystring .= "no_note=". urlencode(stripslashes("1")) ."&";
										$querystring .= "bn=". urlencode(stripslashes("PP-BuyNowBF")) ."&";
										$querystring .= "tax=". urlencode(stripslashes("0")) ."&";
										$querystring .= "rm=". urlencode(stripslashes("0")) ."&";
										$querystring .= "cbt=". urlencode(stripslashes($m['back_to'])) ."&";
										$querystring .= "quantity=". urlencode(stripslashes("1")) ."&";
										
										$querystring .= "return=". urlencode(stripslashes(getTypeUrl("return"))) ."&";
										$querystring .= "cancel_return=". urlencode(stripslashes(getTypeUrl("cancel") ."&uid=". $gu['id'])) ."&";
										$querystring .= "notify_url=". urlencode(getTypeUrl("notify"));
										
										$querystring .= "&custom=". urlencode($activate_code);
										
										mysqli_query($con,"UPDATE login_users SET paypal='$querystring' WHERE username='$username'"); // Update user with paypal query string for if the user cancels his payment to retry
										
										echo "link|||https://www.sandbox.paypal.com/cgi-bin/webscr". $querystring; // Links the user to paypal
									} elseif((getSetting("enable_stripe", "text") == "true" && getSetting("stripe_cost", "text") != "0" && getSetting("enable_paypal", "text") != "true") || $method == "2") { // Check for Stripe
										$getuid = mysqli_query($con,"SELECT id FROM login_users WHERE username='$username'");
										$gu = mysqli_fetch_array($getuid);
										
										echo "link|||". $script_path ."login.php?stripe&uid=". $gu['id'];
									} elseif(getSetting("activation", "text") == "1") { // Check if activation type is 1, if so, the user has to activate by validating his email
										// Send activation mail, only if paypal is not activated
										$getuid = mysqli_query($con,"SELECT id FROM login_users WHERE username='$username'");
										$gu = mysqli_fetch_array($getuid);
										$val_url = getTypeUrl("activation") . $activate_code;
										
										$subject = getSetting("validation_mail_subject", "text");
										$subject = str_replace("{val_url}", $val_url, $subject);
										$subject = str_replace("{name}", $username, $subject);
										$subject = str_replace("{email}", $email, $subject);
										$subject = str_replace("{date}", date("j-n-Y", $registered_on), $subject);
										
										$message = getSetting("validation_mail", "text");
										$message = str_replace("{val_url}", $val_url, $message);
										$message = str_replace("{name}", $username, $message);
										$message = str_replace("{email}", $email, $message);
										$message = str_replace("{date}", date("j-n-Y", $registered_on), $message);
										$message = nl2br($message);
										$message = html_entity_decode($message);
										
										sendMail($email, $subject, $message, $gu['id']); // Send activation mail
									} elseif(getSetting("send_welcome_mail", "text") == "true") {
										// Send welcome mail, only if no activation mail is send
										$getuid = mysqli_query($con,"SELECT id FROM login_users WHERE username='$username'");
										$gu = mysqli_fetch_array($getuid);
										
										$subject = getSetting("welcome_mail_subject", "text");
										$subject = str_replace("{name}", $username, $subject);
										$subject = str_replace("{email}", $email, $subject);
										$subject = str_replace("{date}", date("j-n-Y", $registered_on), $subject);
										$subject = str_replace("{ip}", $ip, $subject);
										$subject = str_replace("{perm}", getPermName($gu['id']), $subject);
										
										$message = getSetting("welcome_mail", "text");
										$message = str_replace("{name}", $username, $message);
										$message = str_replace("{email}", $email, $message);
										$message = str_replace("{date}", date("j-n-Y", $registered_on), $message);
										$message = str_replace("{ip}", $ip, $message);
										$message = str_replace("{perm}", getPermName($gu['id']), $message);
										$message = nl2br($message);
										$message = html_entity_decode($message);
										
										sendMail($email, $subject, $message, $gu['id']); // Send welcome mail
									} else {
										// Nothing
									}
									
									$inputs2 = mysqli_query($con,"SELECT * FROM login_inputs");
									while($i2 = mysqli_fetch_array($inputs2)) {
										$name = $i2['name'];
										// Check if input is posted, or the if the input is an checkbox
										if(!empty($_POST[$name]) || $i2['type'] == "checkbox" && empty($_POST[$name])) {
											// If the input is an checkbox, and is it not posted, the checkbox is unchecked
											if($i2['type'] == "checkbox") {
												if(!isset($_POST[$name])) {
													$option = "false";
												} else {
													$option = "true";
												}
											} elseif($i2['type'] == "hidden") {
												// Use the value from the input table, because the user might have changed it in his browser
												$option = $i2['value'];
											} else {
												$option = htmlentities(mysqli_real_escape_string($con,$_POST[$name]), ENT_QUOTES);
											}
											
											// Update the input column in the user table
											mysqli_query($con,"UPDATE login_users SET ". $name ."='$option' WHERE username='$username'");
										}
									}
									
									// If PayPal is not enabled or the price is 0 and if stripe is not enabled or the price is 0, the user will be redirected to the login page
									if(getSetting("enable_paypal", "text") == "false" || getSetting("paypal_cost", "text") == "0") {
										if(getSetting("enable_stripe", "text") == "false" || getSetting("stripe_cost", "text") == "0") {
											echo "link|||". $script_path ."login.php?m=2";
										}
									}
								} else {
									// Error
								}
							} else {
								echo "<h5 class='text-center red'>". getSetting("page_disabled_message", "text") ."</h5>";
							}
						}
					}
				} else {
					echo "<h5 class='text-center red'>". $m['already_logged_in'] ."</h5>";
				}
			}
		}
		
		
		
		// Social registration
		if(!empty($_POST['social_register'])) {
			$ip = mysqli_real_escape_string($con,$_SERVER['REMOTE_ADDR']);
			$checkblock = mysqli_query($con,"SELECT * FROM login_blocks WHERE ip='$ip'");
			$cb = mysqli_fetch_array($checkblock);
			$timenow = time();
			
			// Check if an IP is blocked
			if(mysqli_num_rows($checkblock) > 0 && ($cb['until'] > $timenow || empty($cb['until']) || $cb['until'] == "0")) {
				echo "<h5 class='text-center red'>". $m['ip_blocked'] ."</h5>";
			} else {
				// Check if the user is already logged in
				if(!is_logged_in()) {
					// If there is a registration limit per IP, check if this IP already has registered before and how many times
					if(getSetting("max_ip", "text") > 0) {
						$checkip = mysqli_query($con,"SELECT * FROM login_users WHERE ip='$ip'");
						$numip = mysqli_num_rows($checkip);
					}
					
					if(getSetting("max_ip", "text") > 0 && $numip >= getSetting("max_ip", "text")) { // Checks if the IP has exceeded his registration limit
						echo "<h5 class='text-center red'>". $m['max_accounts'] ."</h5>";
					} elseif(empty($_POST['username'])) { // Check if the username is filled in
						echo "<h5 class='text-center red'>". $m['fill_in_username'] ."</h5>";
					} elseif(getSetting("username_minlength", "text") != "" && getSetting("username_minlength", "text") != "0" && strlen($_POST['username']) < getSetting("username_minlength", "text")) { // Check if the username isn't shorter as the minimum length
						echo "<h5 class='text-center red'>". $m['username_min'] . getSetting("username_minlength", "text") . $m['characters'] ."</h5>";
					} elseif(getSetting("username_maxlength", "text") != "" && getSetting("username_maxlength", "text") != "0" && strlen($_POST['username']) > getSetting("username_maxlength", "text")) { // Check if the username isn't longer as the maximum length
						echo "<h5 class='text-center red'>". $m['username_max'] . getSetting("username_maxlength", "text") . $m['characters'] ."</h5>";
					} elseif(empty($_POST['email'])) { // Check if the email address is filled in
						echo "<h5 class='text-center red'>". $m['fill_in_email'] ."</h5>";
					} elseif($_POST['type'] != "google" && $_POST['type'] != "facebook" && $_POST['type'] != "twitter") { // Check if the social login type exists
						echo "<h5 class='text-center red'>". $m['unknown_social_login'] ."</h5>";
					} elseif(empty($_SESSION['sid'])) {
						echo "<h5 class='text-center red'>". $m['sid_not_found'] ."</h5>";
					} else {
						// Basic variables
						$username = htmlentities(mysqli_real_escape_string($con,$_POST['username']), ENT_QUOTES);
						$email = htmlentities(mysqli_real_escape_string($con,$_POST['email']), ENT_QUOTES);
						$type = htmlentities(mysqli_real_escape_string($con,$_POST['type']), ENT_QUOTES);
						$sid = mysqli_real_escape_string($con,$_SESSION['sid']);
						$registered_on = time();
						$permission = getSetting("default_permission", "text"); // Get the default permission id
						
					
						$check_username = mysqli_query($con,"SELECT * FROM login_users WHERE username='$username'");
						$check_email = mysqli_query($con,"SELECT * FROM login_users WHERE email='$email'");
						
						// Check if username or email is already taken
						if(mysqli_num_rows($check_username) > 0) {
							echo "<h5 class='text-center red'>". $m['username_taken'] ."</h5>";
						} elseif(mysqli_num_rows($check_email) > 0) {
							echo "<h5 class='text-center red'>". $m['email_taken'] ."</h5>";
						} else {
							if(getSetting("social_verification", "text") == "true" && getSetting("activation", "text") == "1") { // If activation type is 1, the user has to be activated by validating his mail
								$active = "0";
							} elseif(getSetting("activation", "text") == "2") { // If activation type is 2, the user has to be manually activated by an admin
								$active = "0";
							} elseif(getSetting("social_pay", "text") == "true" && getSetting("enable_paypal", "text") == "true" && getSetting("paypal_cost", "text") != "0") { // If paypal is enabled, the user has to pay before he is activated
								$active = "0";
							} elseif(getSetting("social_pay", "text") == "true" && getSetting("enable_stripe", "text") == "true" && getSetting("stripe_cost", "text") != "0") { // If stripe is enabled, the user has to pay before he is activated
								$active = "0";
							} else {
								$active = "1"; // No activation needed
							}
							
							$inputs = mysqli_query($con,"SELECT * FROM login_inputs");
							$errors = 0;
							
							if(mysqli_num_rows($inputs) > 0) {
								while($i = mysqli_fetch_array($inputs)) {
									$name = $i['name'];
									// If the input is required but not posted, show error
									if(empty($_POST[$name]) && $i['required'] == "true" && $_POST[$name] != "0") {
										if(!empty($i['input_error'])) {
											echo "<h5 class='text-center red'>". nl2br($i['input_error']) ."</h5>";
										} else {
											echo "<h5 class='text-center red'>". $m['fill_in'] ." ". $i['name'] ."</h5>";
										}
										
										$errors++; // Add 1 to errors, so the script won't continue
										break;
									}
								}
							}
							
							
							// Extra check if registration page is not disabled
							if(getSetting("disable_register", "text") == "false") {
								// Check if there are any errors
								if($errors == 0) {
									$activate_code = md5(sha1($username . mt_rand(100, 1000000))); // Create random activate code
									
									mysqli_query($con,"INSERT INTO login_users (username, email, password, registered_on, ip, permission, active, activate_code, paypal, banned, type, sid)
									VALUES ('$username','$email','','$registered_on','$ip','$permission','$active','$activate_code','','0','$type','$sid')");
									
									
									if($active == 1) {
										mysqli_query($con,"UPDATE login_users SET last_login='$registered_on' WHERE username='$username'"); // Update last login
										
										$get_uid = mysqli_query($con,"SELECT id FROM login_users WHERE username='$username'");
										$gu = mysqli_fetch_array($get_uid);
										
										// Add needed session data
										$_SESSION['uid'] = $gu['id'];
										$_SESSION['ip'] = $ip;
									}
									
									
									// Check if there is a payment method posted, in case there are 2 payment gateways enabled
									if(empty($_POST['pay_method'])) {
										$method = "0";
									} elseif($_POST['pay_method'] == "Stripe") {
										$method = "2";
									} else {
										$method = "1";
									}
									
									// Check if paypal is enabled, and check if the price is more than 0, if so, create PayPal pay URL
									if((getSetting("social_pay", "text") == "true" && getSetting("enable_paypal", "text") == "true" && getSetting("paypal_cost", "text") != "0" && getSetting("enable_stripe", "text") != "true") || getSetting("social_pay", "text") == "true" && $method == "1") {
										// Send to paypal site and if paid, the activation mail will be send
										$getuid = mysqli_query($con,"SELECT id FROM login_users WHERE username='$username'");
										$gu = mysqli_fetch_array($getuid);
										
										$querystring = "?business=". urlencode(getSetting("paypal_email", "text")) ."&";
										
										$querystring .= "item_name=". urlencode($m['registration_of'] . $username) ."&";
										$querystring .= "amount=". urlencode(getSetting("paypal_cost", "text")) ."&";
										
										$querystring .= "currency_code=". urlencode(stripslashes(getSetting("paypal_currency", "text"))) ."&";
										$querystring .= "no_shipping=". urlencode(stripslashes("1")) ."&";
										
										$querystring .= "cmd=". urlencode(stripslashes("_xclick")) ."&";
										$querystring .= "no_note=". urlencode(stripslashes("1")) ."&";
										$querystring .= "bn=". urlencode(stripslashes("PP-BuyNowBF")) ."&";
										$querystring .= "tax=". urlencode(stripslashes("0")) ."&";
										$querystring .= "rm=". urlencode(stripslashes("0")) ."&";
										$querystring .= "cbt=". urlencode(stripslashes($m['back_to'])) ."&";
										$querystring .= "quantity=". urlencode(stripslashes("1")) ."&";
										
										$querystring .= "return=". urlencode(stripslashes(getTypeUrl("return"))) ."&";
										$querystring .= "cancel_return=". urlencode(stripslashes(getTypeUrl("cancel") ."&uid=". $gu['id'])) ."&";
										$querystring .= "notify_url=". urlencode(getTypeUrl("notify"));
										
										$querystring .= "&custom=". urlencode($activate_code);
										
										mysqli_query($con,"UPDATE login_users SET paypal='$querystring' WHERE username='$username'"); // Update user with paypal query string for if the user cancels his payment to retry
										
										echo "link|||https://www.paypal.com/cgi-bin/webscr". $querystring; // Links the user to paypal
									} elseif((getSetting("social_pay", "text") == "true" && getSetting("enable_stripe", "text") == "true" && getSetting("stripe_cost", "text") != "0" && getSetting("enable_paypal", "text") != "true") || getSetting("social_pay", "text") == "true" && $method == "2") { // Check for Stripe
										$getuid = mysqli_query($con,"SELECT id FROM login_users WHERE username='$username'");
										$gu = mysqli_fetch_array($getuid);
										
										echo "link|||". $script_path ."login.php?stripe&uid=". $gu['id'];
									} elseif(getSetting("social_verification", "text") == "true" && getSetting("activation", "text") == "1") { // Check if activation type is 1, if so, the user has to activate by validating his email
										// Send activation mail, only if paypal is not activated
										$getuid = mysqli_query($con,"SELECT id FROM login_users WHERE username='$username'");
										$gu = mysqli_fetch_array($getuid);
										$val_url = getTypeUrl("activation") . $activate_code;
										
										$subject = getSetting("validation_mail_subject", "text");
										$subject = str_replace("{val_url}", $val_url, $subject);
										$subject = str_replace("{name}", $username, $subject);
										$subject = str_replace("{email}", $email, $subject);
										$subject = str_replace("{date}", date("j-n-Y", $registered_on), $subject);
										
										$message = getSetting("validation_mail", "text");
										$message = str_replace("{val_url}", $val_url, $message);
										$message = str_replace("{name}", $username, $message);
										$message = str_replace("{email}", $email, $message);
										$message = str_replace("{date}", date("j-n-Y", $registered_on), $message);
										$message = nl2br($message);
										$message = html_entity_decode($message);
										
										sendMail($email, $subject, $message, $gu['id']); // Send activation mail
									} elseif(getSetting("send_welcome_mail", "text") == "true") {
										// Send welcome mail, only if no activation mail is send
										$getuid = mysqli_query($con,"SELECT id FROM login_users WHERE username='$username'");
										$gu = mysqli_fetch_array($getuid);
										
										$subject = getSetting("welcome_mail_subject", "text");
										$subject = str_replace("{name}", $username, $subject);
										$subject = str_replace("{email}", $email, $subject);
										$subject = str_replace("{date}", date("j-n-Y", $registered_on), $subject);
										$subject = str_replace("{ip}", $ip, $subject);
										$subject = str_replace("{perm}", getPermName($gu['id']), $subject);
										
										$message = getSetting("welcome_mail", "text");
										$message = str_replace("{name}", $username, $message);
										$message = str_replace("{email}", $email, $message);
										$message = str_replace("{date}", date("j-n-Y", $registered_on), $message);
										$message = str_replace("{ip}", $ip, $message);
										$message = str_replace("{perm}", getPermName($gu['id']), $message);
										$message = nl2br($message);
										$message = html_entity_decode($message);
										
										sendMail($email, $subject, $message, $gu['id']); // Send welcome mail
									} else {
										// Nothing
									}
									
									$inputs2 = mysqli_query($con,"SELECT * FROM login_inputs");
									while($i2 = mysqli_fetch_array($inputs2)) {
										$name = $i2['name'];
										// Check if input is posted, or the if the input is an checkbox
										if(!empty($_POST[$name]) || $i2['type'] == "checkbox" && empty($_POST[$name])) {
											// If the input is an checkbox, and is it not posted, the checkbox is unchecked
											if($i2['type'] == "checkbox") {
												if(!isset($_POST[$name])) {
													$option = "false";
												} else {
													$option = "true";
												}
											} elseif($i2['type'] == "hidden") {
												// Use the value from the input table, because the user might have changed it in his browser
												$option = $i2['value'];
											} else {
												$option = htmlentities(mysqli_real_escape_string($con,$_POST[$name]), ENT_QUOTES);
											}
											
											// Update the input column in the user table
											mysqli_query($con,"UPDATE login_users SET ". $name ."='$option' WHERE username='$username'");
										}
									}
									
									// If PayPal is not enabled or the price is 0 and if stripe is not enabled or the price is 0, the user will be redirected to the login page
									if(getSetting("social_pay", "text") == "false" || (getSetting("enable_paypal", "text") == "false" || getSetting("paypal_cost", "text") == "0")) {
										if(getSetting("social_pay", "text") == "false" || (getSetting("enable_stripe", "text") == "false" || getSetting("stripe_cost", "text") == "0")) {
											echo "link|||". $script_path ."login.php?m=2";
										}
									}
								} else {
									// Error
								}
							} else {
								echo "<h5 class='text-center red'>". getSetting("page_disabled_message", "text") ."</h5>";
							}
						}
					}
				} else {
					echo "<h5 class='text-center red'>". $m['already_logged_in'] ."</h5>";
				}
			}
		}
		
		
		
		// User login
		if(!empty($_POST['login'])) {
			// Check if the user is already logged in
			if(!is_logged_in()) {
				// Check if login type is email or username
				if(getSetting("login_with", "text") == "email") {
					$email = $_POST['email'];
					$logginginwith = $_POST['email'];
				} else {
					$username = $_POST['username'];
					$logginginwith = $_POST['username'];
				}
				
				$password = $_POST['password'];
				$ip = $_SERVER['REMOTE_ADDR'];
				
				// Check if login isn't disabled or if the username or email is an admin
				if(getSetting("disable_login", "text") == "false" || isAdminByNameOrEmail($logginginwith)) {
					// Check if everything required is filled in
					if(getSetting("login_with", "text") == "username" && empty($username)) {
						echo "<h5 class='text-center red'>". $m['fill_in_username'] ."</h5>";
					} elseif(getSetting("login_with", "text") == "email" && empty($email)) {
						echo "<h5 class='text-center red'>". $m['fill_in_email'] ."</h5>";
					} elseif(empty($password)) {
						echo "<h5 class='text-center red'>". $m['fill_in_password'] ."</h5>";
					} else {
						// Check if there is a failed login attempts limit and if failed logins are logged
						if(getSetting("max_failed_attempts", "text") > 0 && getSetting("log_failed_logins", "text") == "true") {
							$logs = mysqli_query($con,"SELECT * FROM login_log WHERE ip='$ip' ORDER BY id DESC");
							
							$failed = 0;
							while($l = mysqli_fetch_array($logs)) {
								if($l['success'] == "1") {
									break; // Stop while loop because a successful login is found
								} elseif(date("j-n-Y", $l['time']) != date("j-n-Y")) {
									break; // Stop while loop because the log is not from today, so it is irrelevant
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
									break; // Stop while loop because the log is not from today so it is irrelevant
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
								echo "<h5 class='text-center red'>". $m['you_are_banned'] ."<br>". $cb['reason'] ."<br><br>". $m['block_expires'] ."<br>". $m['never'] ."</h5>";
							} else {
								echo "<h5 class='text-center red'>". $m['you_are_banned'] ."<br>". $cb['reason'] ."<br><br>". $m['block_expires'] ."<br>". date("d M Y", $cb['until']) ." ". $m['at'] ." ". date("G:i", $cb['until']) ."</h5>";
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
							
							echo "<h5 class='text-center red'>". $m['blocked'] ."</h5>";
						} else {
							require_once('pbkdf2.php'); // Requires password encryption script
							$salt = md5($password); // Create salt
							$pass = pbkdf2($password,$salt); // Encrypt password
							
							// Check if the login type is email or username
							if(getSetting("login_with", "text") == "email") {
								$loginname = mysqli_real_escape_string($con,$_POST['email']);
								// Check if the login is case sensitive
								if(getSetting("case_sensitive", "text") == "true") {
									$check = mysqli_query($con,"SELECT * FROM login_users WHERE BINARY email='$loginname' AND password='$pass'");
								} else {
									$check = mysqli_query($con,"SELECT * FROM login_users WHERE email='$loginname' AND password='$pass'");
								}
								
								$getuid = mysqli_query($con,"SELECT * FROM login_users WHERE email='$loginname'");
							} else {
								$loginname = mysqli_real_escape_string($con,$_POST['username']);
								// Check if the login is case sensitive
								if(getSetting("case_sensitive", "text") == "true") {
									$check = mysqli_query($con,"SELECT * FROM login_users WHERE BINARY username='$loginname' AND password='$pass'");
								} else {
									$check = mysqli_query($con,"SELECT * FROM login_users WHERE username='$loginname' AND password='$pass'");
								}
								
								$getuid = mysqli_query($con,"SELECT * FROM login_users WHERE username='$loginname'");
							}
							
							// Check if the login is correct
							if(mysqli_num_rows($check) == 0) {
								echo "<h5 class='text-center red'>". $m['incorrect_credentials'] ."</h5>";
								
								// Check if log failed logins is enabled, if so log this login try
								if(getSetting("log_failed_logins", "text") == "true" || getSetting("max_failed_attempts", "text") > 0) {
									// Check if a user id is found, if not the username didn't exist
									if(mysqli_num_rows($getuid) > 0) {
										$gu = mysqli_fetch_array($getuid);
										
										addLog("0", $_SERVER['REMOTE_ADDR'], $gu['id'], $loginname, "website");
									} else {
										addLog("0", $_SERVER['REMOTE_ADDR'], 0, $loginname, "website");
									}
								}
							} else {
								$c = mysqli_fetch_array($check);
								$uid = $c['id'];
								
								$bancheck = mysqli_query($con,"SELECT * FROM login_bans WHERE uid='$uid'");
								// Check if the user is banned or if the user isn't active
								if(mysqli_num_rows($bancheck) > 0) {
									echo "<h5 class='text-center red'>". $m['you_are_banned'] ."</h5>";
								} elseif($c['active'] != "1") {
									if(getSetting("enable_paypal", "text") == "true" && getSetting("enable_stripe", "text") == "true") {
										if(!empty($c['paypal'])) {
											echo "<h5 class='text-center red'>". $m['need_paypal_activation'] ."<a href='?retry&uid=". $c['id'] ."'>". $m['clicking_here'] ."</a></h5>";
										} else {
											echo "<h5 class='text-center red'>". $m['need_stripe_activation'] ."<a href='?stripe&uid=". $c['id'] ."'>". $m['clicking_here'] ."</a></h5>";
										}
									} elseif(getSetting("enable_paypal", "text") == "true" && !empty($c['paypal'])) {
										echo "<h5 class='text-center red'>". $m['need_paypal_activation'] ."<a href='?retry&uid=". $c['id'] ."'>". $m['clicking_here'] ."</a></h5>";
									} elseif(getSetting("enable_stripe", "text") == "true") {
										echo "<h5 class='text-center red'>". $m['need_stripe_activation'] ."<a href='?stripe&uid=". $c['id'] ."'>". $m['clicking_here'] ."</a></h5>";
									} elseif(getSetting("activation", "text") == "0") {
										mysqli_query($con,"UPDATE login_users SET active='1' WHERE id='$uid'");
										echo "<h5 class='text-center green'>". $m['activation_success'] ."</h5>";
									} elseif(getSetting("activation", "text") == "1") {
										echo "<h5 class='text-center red'>". $m['need_email_activation'] ."<a href='?resend&uid=". $c['id'] ."'>". $m['clicking_here'] ."</a></h5>";
									} else {
										echo "<h5 class='text-center red'>". $m['need_manual_activation'] ."</h5>";
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
									
									$perm = $c['permission'];
									$getperm = mysqli_query($con,"SELECT * FROM login_permissions WHERE id='$perm'");
									$gp = mysqli_fetch_array($getperm);
									
									if(getSetting("redirect_last_page", "text") == "true" && !empty($_COOKIE['last_url'])) { // Check if remember last page is enabled
										$last_url = $_COOKIE['last_url'];
										setcookie("last_url", "", time() - 3600, "/", getCurrentDomain());
										unset($_COOKIE['last_url']); // Delete last URL cookie to avoid infinite redirections if the user is not allowed to visit the URL
										
										echo "link|||". $last_url;
									} elseif(!empty($gp['on_login'])) { // Check if the user's permission has a logged in redirect URL
										echo "link|||". $gp['on_login'];
									} elseif(getSetting("use_redirect_login", "text") == "true") { // Check if on login redirect is enabled
										if(getSetting("use_redirect_login", "text") != "") { // Extra check if the URL is filled in
											echo "link|||". getSetting("redirect_login", "text");
										} else {
											echo "link|||http://www.". getCurrentDomain();
										}
									} else {
										if(getSetting("message_login", "text") != "") { // Check if there is a custom message filled in, else display default message
											echo "text|||<h5 class='text-center green'>". nl2br(getSetting("message_login", "text")) ."</h5>";
										} else {
											echo "text|||<h5 class='text-center green'>". $m['successful_login'] ."</h5>";
										}
									}
									
									// Check if log successful logins is enabled, if so, log this login try
									if(getSetting("log_successful_logins", "text") == "true") {
										addLog("1", $_SERVER['REMOTE_ADDR'], $uid, $loginname, "website");
									}
								}
							}
						}
					}
				} else {
					if(getSetting("page_disabled_message", "text") == "") {
						echo "<h5 class='text-center red'>". $m['page_disabled_default'] ."</h5>";
					} else {
						echo "<h5 class='text-center red'>". nl2br(getSetting("page_disabled_message", "text")) ."</h5>";
					}
				}
			} else {
				echo "<h5 class='text-center red'>". $m['already_logged_in'] ."</h5>";
			}
		}
	} else {
		echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['token_mismatch'] ."</div>";
	}
}
?>