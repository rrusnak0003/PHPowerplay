<?php
define('ROOT', dirname(__FILE__));

if(!isset($con)) {
	require(ROOT .'/../config.php');
}
if(!isset($titles)) {
	require(ROOT .'/../lang/'. $language .'.php');
}
if(!function_exists('getSetting')) {
	require(ROOT .'/functions.php');
}

// Starts session and enables it on whole domain
@session_set_cookie_params(0, '/', getCurrentDomain()); 
@session_start();

// Set timezone
if(getSetting("timezone", "text") != "") {
	date_default_timezone_set(getSetting("timezone", "text"));
}


// Automatic function to require someone to be logged in on a page
function logged_in() {
	global $script_path;
	
	// If session is not found or IP has changed or the uid is not found the user is not logged in
	if(empty($_SESSION['uid']) || empty($_SESSION['ip']) || $_SESSION['ip'] != $_SERVER['REMOTE_ADDR'] || !checkUid($_SESSION['uid']) || checkIpBlock($_SERVER['REMOTE_ADDR'])) {
		setcookie("last_url", getCurrentUrl(), 0, "/", getCurrentDomain()); // Save last url in a cookie
		
		if(getSetting("use_redirect_notloggedin", "text") == "true") {
			header('Location: '. getSetting("redirect_notloggedin", "text"));
		} else {
			if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != "off") {
				header('Location: https://www.'. getCurrentDomain() . $script_path .'login.php?m=1');
			} else {
				header('Location: http://www.'. getCurrentDomain() . $script_path .'login.php?m=1');
			}
		}
		exit;
	}
}



// Function to check if a user is logged in
function is_logged_in() {
	// If session is not found or IP has changed or the uid is not found the user is not logged in
	if(empty($_SESSION['uid']) || empty($_SESSION['ip']) || $_SESSION['ip'] != $_SERVER['REMOTE_ADDR'] || !checkUid($_SESSION['uid']) || checkIpBlock($_SERVER['REMOTE_ADDR'])) {
		return false;
	} else {
		return true;
	}
}



// Check if visitor is logged in, if so update last active time and last action
if(is_logged_in()) {
	if(strpos($_SERVER['REQUEST_URI'], "posthandler.php") == false && strpos($_SERVER['REQUEST_URI'], "paypal.php") == false) {
		$last_active = time();
		$last_action = $_SERVER['REQUEST_URI'];
		
		mysqli_query($con,"UPDATE login_users SET last_active='$last_active', last_action='$last_action' WHERE id='". $_SESSION['uid'] ."'");
	}
}



// Automatic function to require someone to be admin
function admin() {
	global $con;
	global $script_path;
	
	$uid = mysqli_real_escape_string($con, $_SESSION['uid']);
	$getuser = mysqli_query($con,"SELECT * FROM login_users WHERE id='$uid'");
	$gu = mysqli_fetch_array($getuser);
	
	// Check if the permission id is 1, if not the user is not admin
	// NOTE: this means that you shouldn't change the id of the admin rank (or the name)
	if($gu['permission'] != "1") {
		if(getSetting("use_redirect_nopermission", "text") == "true") {
			header('Location: '. getSetting("redirect_nopermission", "text"));
		} else {
			if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != "off") {
				header('Location: https://www.'. getCurrentDomain() . $script_path .'profile.php?m=1');
			} else {
				header('Location: http://www.'. getCurrentDomain() . $script_path .'profile.php?m=1');
			}
		}
		exit;
	}
}



// Function to check if someone is admin
function is_admin() {
	global $con;
	
	$uid = mysqli_real_escape_string($con, $_SESSION['uid']);
	$getuser = mysqli_query($con,"SELECT * FROM login_users WHERE id='$uid'");
	$gu = mysqli_fetch_array($getuser);
	
	// Check if the permission id is 1, if not the user is not admin
	// NOTE: this means that you shouldn't change the id of the admin rank (or the name)
	if($gu['permission'] != "1") {
		return false;
	} else {
		return true;
	}
}



// Automatic function to require a minimum level for a page
function minLevel($level) {
	global $con;
	global $script_path;
	$uid = mysqli_real_escape_string($con, $_SESSION['uid']);
	
	// If permission level of the user is lower than the minimum the user should be redirected to a no permission page
	if(getPermLevel($uid) < $level) {
		if(getSetting("use_redirect_nopermission", "text") == "true") {
			header('Location: '. getSetting("redirect_nopermission", "text"));
		} else {
			if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != "off") {
				header('Location: https://www.'. getCurrentDomain() . $script_path .'profile.php?m=1');
			} else {
				header('Location: http://www.'. getCurrentDomain() . $script_path .'profile.php?m=1');
			}
		}
		exit;
	}
}



// Function to check if a user has the minimum required level
function is_minLevel($level) {
	global $con;
	$uid = mysqli_real_escape_string($con, $_SESSION['uid']);
	
	// If permission level of the user is lower than the minimum the function returns false
	if(getPermLevel($uid) < $level) {
		return false;
	} else {
		return true;
	}
}



// Automatic function to set a maximum level for a page
function maxLevel($level) {
	global $con;
	global $script_path;
	$uid = mysqli_real_escape_string($con, $_SESSION['uid']);
	
	// If permission level of the user is higher than the maximum level the user should be redirected to a no permission page
	if(getPermLevel($uid) > $level) {
		if(getSetting("use_redirect_nopermission", "text") == "true") {
			header('Location: '. getSetting("redirect_nopermission", "text"));
		} else {
			if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != "off") {
				header('Location: https://www.'. getCurrentDomain() . $script_path .'profile.php?m=1');
			} else {
				header('Location: http://www.'. getCurrentDomain() . $script_path .'profile.php?m=1');
			}
		}
		exit;
	}
}



// Function to check if a user has a lower level than the maximum allowed level
function is_maxLevel($level) {
	global $con;
	$uid = mysqli_real_escape_string($con, $_SESSION['uid']);
	
	// If permission level of the user is higher than the maximum level the function returns false
	if(getPermLevel($uid) > $level) {
		return false;
	} else {
		return true;
	}
}



// Automatic function to allow only certain specified levels on a page
function allowedLevels($levels) {
	global $con;
	global $script_path;
	
	$uid = mysqli_real_escape_string($con, $_SESSION['uid']);
	$getuser = mysqli_query($con,"SELECT * FROM login_users WHERE id='$uid'");
	$gu = mysqli_fetch_array($getuser);
	
	$permid = $gu['permission'];
	$gperm = mysqli_query($con,"SELECT * FROM login_permissions WHERE id='$permid'");
	$gp = mysqli_fetch_array($gperm);
	
	$level = explode(",", $levels); // Separate the levels FROM login_the commas
	
	$found = 0;
	// Check if the level of the user is one of the specified levels
	foreach($level as $l) {
		$l = trim($l);
		if($l == $gp['level']) {
			$found++; // Count 1 to $found if the levels match
		}
	}
	
	// If user does not have one of the specified levels, he will be redirected to a no permission link
	if($found == 0) {
		if(getSetting("use_redirect_nopermission", "text") == "true") {
			header('Location: '. getSetting("redirect_nopermission", "text"));
		} else {
			if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != "off") {
				header('Location: https://www.'. getCurrentDomain() . $script_path .'profile.php?m=1');
			} else {
				header('Location: http://www.'. getCurrentDomain() . $script_path .'profile.php?m=1');
			}
		}
		exit;
	}
}



// Function to check if a user has one of the specified levels
function is_allowedLevel($levels) {
	global $con;
	
	$uid = mysqli_real_escape_string($con, $_SESSION['uid']);
	$getuser = mysqli_query($con,"SELECT * FROM login_users WHERE id='$uid'");
	$gu = mysqli_fetch_array($getuser);
	
	$permid = $gu['permission'];
	$gperm = mysqli_query($con,"SELECT * FROM login_permissions WHERE id='$permid'");
	$gp = mysqli_fetch_array($gperm);
	
	$level = explode(",", $levels); // Separate the levels FROM login_the commas
	
	$found = 0;
	// Check if the user has one of the specified levels
	foreach($level as $l) {
		$l = trim($l);
		if($l == $gp['level']) {
			$found++; // Count 1 to $found if the levels match
		}
	}
	
	// If user does not have one of the specified level the function returns false
	if($found == 0) {
		return false;
	} else {
		return true;
	}
}



// Function to rank someone up
function rankUp($uid = null, $levels = 1, $savemode = true) {
	global $con;
	global $m;
	
	// If uid is null, use the current user his uid, else use the specified uid
	if($uid == null) {
		$uid = $_SESSION['uid'];
	} else {
		$uid = mysqli_real_escape_string($con, $uid);
	}
	
	$user = mysqli_query($con,"SELECT * FROM login_users WHERE id='$uid'");
	// Check if the uid exists
	if(mysqli_num_rows($user) > 0) {
		$u = mysqli_fetch_array($user);
		$permid = $u['permission'];
		
		$perm = mysqli_query($con,"SELECT * FROM login_permissions WHERE id='$permid'");
		$p = mysqli_fetch_array($perm);
		
		if($p['name'] == "Admin" && $savemode == true) {
			// Can't level higher because he is admin and savemode is on
			return false;
		} else {
			$newlevel = $p['level'] + $levels;
			$newperm = mysqli_query($con,"SELECT * FROM login_permissions WHERE level='$newlevel'");
			
			// Check if new permission is found
			if(mysqli_num_rows($newperm) > 0) {
				$np = mysqli_fetch_array($newperm);
				if($np['name'] == "Admin" && $savemode == true) {
					// Can't level to admin when savemode is on
					return false;
				} else {
					// Rankup successful
					$newid = $np['id'];
					mysqli_query($con,"UPDATE login_users SET permission='$newid' WHERE id='$uid'");
					
					return true;
				}
			} else {
				// New permission not found
				return false;
			}
		}
	} else {
		// User is not found
		return false;
	}
}



// Function to rank someone down
function rankDown($uid = null, $levels = 1, $savemode = true) {
	global $con;
	global $m;
	
	// If uid is null, use the current user his uid, else use the specified uid
	if($uid == null) {
		$uid = $_SESSION['uid'];
	} else {
		$uid = mysqli_real_escape_string($con, $uid);
	}
	
	$user = mysqli_query($con,"SELECT * FROM login_users WHERE id='$uid'");
	// Check if the uid exists
	if(mysqli_num_rows($user) > 0) {
		$u = mysqli_fetch_array($user);
		$permid = $u['permission'];
		
		$perm = mysqli_query($con,"SELECT * FROM login_permissions WHERE id='$permid'");
		$p = mysqli_fetch_array($perm);
		
		if($p['name'] == "Admin" && $savemode == true) {
			// Can't level lower because he is admin and savemode is on
			return false;
		} elseif($p['level'] == "1") {
			// Can't level lower because lowest level is 1
			return false;
		} else {
			$newlevel = $p['level'] - $levels;
			$newperm = mysqli_query($con,"SELECT * FROM login_permissions WHERE level='$newlevel'");
			
			// Check if new permission exists
			if(mysqli_num_rows($newperm) > 0) {
				// Rankdown successful
				$np = mysqli_fetch_array($newperm);
				$newid = $np['id'];
				
				mysqli_query($con,"UPDATE login_users SET permission='$newid' WHERE id='$uid'");
				
				return true;
			} else {
				// New permission not found
				return false;
			}
		}
	} else {
		// User is not found
		return false;
	}
}



// Function to rank someone to a specified level
function rankTo($uid = null, $to, $savemode = true) {
	global $con;
	global $m;
	
	// If uid is null, use the current user his uid, else use the specified uid
	if($uid == null) {
		$uid = $_SESSION['uid'];
	} else {
		$uid = mysqli_real_escape_string($con, $uid);
	}
	
	$user = mysqli_query($con,"SELECT * FROM login_users WHERE id='$uid'");
	// Check if the uid exists
	if(mysqli_num_rows($user) > 0) {
		$u = mysqli_fetch_array($user);
		$permid = $u['permission'];
		
		$perm = mysqli_query($con,"SELECT * FROM login_permissions WHERE id='$permid'");
		$p = mysqli_fetch_array($perm);
		
		if($p['name'] == "Admin" && $savemode == true) {
			// Can't level because he is admin and savemode is on
			return false;
		} else {
			$newlevel = mysqli_real_escape_string($con,$to);
			$newperm = mysqli_query($con,"SELECT * FROM login_permissions WHERE level='$newlevel'");
			
			// Check if new permission exists
			if(mysqli_num_rows($newperm) > 0) {
				// Rankto successful
				$np = mysqli_fetch_array($newperm);
				$newid = $np['id'];
				
				mysqli_query($con,"UPDATE login_users SET permission='$newid' WHERE id='$uid'");
				
				return true;
			} else {
				// New permission not found
				return false;
			}
		}
	} else {
		// User is not found
		return false;
	}
}



// Function to get information FROM login_a user
function userValue($uid = null, $value) {
	global $con;
	
	// If uid is null, use the current user his uid, else use the specified uid
	if($uid == null) {
		if(!empty($_SESSION['uid'])) {
			$uid = $_SESSION['uid'];
		} else {
			$uid = "";
		}
	} else {
		$uid = mysqli_real_escape_string($con, $uid);
	}
	
	$guser = mysqli_query($con,"SELECT * FROM login_users WHERE id='$uid'");
	// Check if uid exists
	if(mysqli_num_rows($guser) > 0) {
		$gu = mysqli_fetch_array($guser);
		
		// If value is not empty return the value, else return false
		if(!empty($gu[$value])) {
			return $gu[$value];
		} else {
			return false;
		}
	} else {
		return false;
	}
}



// Function to get the permission level of a user
function getPermLevel($uid = null) {
	global $con;
	
	// If uid is null, use the current user his uid, else use the specified uid
	if($uid == null) {
		$uid = $_SESSION['uid'];
	} else {
		$uid = mysqli_real_escape_string($con, $uid);
	}
	
	$guser = mysqli_query($con,"SELECT * FROM login_users WHERE id='$uid'");
	// Check if uid exists, is so return the level of the user, else return false
	if(mysqli_num_rows($guser) > 0) {
		$gu = mysqli_fetch_array($guser);
		
		$permid = $gu['permission'];
		$gperm = mysqli_query($con,"SELECT * FROM login_permissions WHERE id='$permid'");
		$gp = mysqli_fetch_array($gperm);
		
		return $gp['level'];
	} else {
		return false;
	}
}



// Function to get the permission name of a user
function getPermName($uid = null) {
	global $con;
	
	// If uid is null, use the current user his uid, else use the specified uid
	if($uid == null) {
		$uid = $_SESSION['uid'];
	} else {
		$uid = mysqli_real_escape_string($con, $uid);
	}
	
	$guser = mysqli_query($con,"SELECT * FROM login_users WHERE id='$uid'");
	// Check if uid exists, is so return the name of the permission of the user, else return false
	if(mysqli_num_rows($guser) > 0) {
		$gu = mysqli_fetch_array($guser);
		
		$permid = $gu['permission'];
		$gperm = mysqli_query($con,"SELECT * FROM login_permissions WHERE id='$permid'");
		$gp = mysqli_fetch_array($gperm);
		
		return $gp['name'];
	} else {
		return false;
	}
}



// Function to check if a login combination is correct
function checkLogin($type = "username", $input, $password) {
	global $con;
	
	require_once('pbkdf2.php'); // Require password hash script
	$pass = mysqli_real_escape_string($con,$password);
	$salt = md5($pass); // Create salt
	$password = pbkdf2($pass, $salt); // Create hashed password
	
	$input = mysqli_real_escape_string($con,$input);
	
	// Check if login type is email or username
	if($type == "email") {
		$check = mysqli_query($con,"SELECT * FROM login_users WHERE email='$input' AND password='$password'");
	} else {
		$check = mysqli_query($con,"SELECT * FROM login_users WHERE username='$input' AND password='$password'");
	}
	
	// If combination gives a result, the login is correct and the function returns true
	// NOTE: if you want to use this for a custom login page, you have to create the sessions yourself (search in posthandler.php for login to see what's required)
	if(mysqli_num_rows($check) == 1) {
		return true;
	} else {
		return false;
	}
}



function is_online($uid = null) {
	global $con;
	
	// If uid is null, use the current user his uid, else use the specified uid
	if($uid == null) {
		$uid = $_SESSION['uid'];
	} else {
		$uid = mysqli_real_escape_string($con, $uid);
	}
	
	$guser = mysqli_query($con,"SELECT * FROM login_users WHERE id='$uid'");
	// Check if uid exists, is so check if the user is counted as online, else return false
	if(mysqli_num_rows($guser) > 0) {
		$gu = mysqli_fetch_array($guser);
		
		$online_time = formatToSeconds(getSetting("online_time", "text"), "minutes");
		$last_active = $gu['last_active'];
		
		if($last_active + $online_time > time()) {
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
}
?>