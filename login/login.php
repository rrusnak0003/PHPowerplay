<?php
include('includes/api.php');
include('head.php');
?>

<div class='container'>
	<noscript>
		<div class='alert alert-danger' role='alert'><?php echo $m['enable_javascript']; ?></div>
	</noscript>
</div>

<div class='container-small'>	
	<?php
	$ip = $_SERVER['REMOTE_ADDR'];
	$checkblock = mysqli_query($con,"SELECT * FROM login_blocks WHERE ip='$ip'");
	$cb = mysqli_fetch_array($checkblock);
	$timenow = time();
	
	// Check if the user is blocked and the block is not permanent
	if(mysqli_num_rows($checkblock) > 0 && $cb['until'] < $timenow && !empty($cb['until']) && $cb['until'] != "0") {
		$logs = mysqli_query($con,"SELECT * FROM login_log WHERE ip='$ip' ORDER BY id DESC");
		while($l = mysqli_fetch_array($logs)) {
			$lid = $l['id'];
			if($l['success'] == "1") {
				break;
			} elseif(date("j-n-Y", $l['time']) != date("j-n-Y")) {
				break;
			} else {
				mysqli_query($con,"DELETE FROM login_log WHERE id='$lid'");
			}
		}
		
		$bid =  $cb['id'];
		mysqli_query($con,"DELETE FROM login_blocks WHERE id='$bid'");
	}
	
	// Check if the user is blocked
	if(mysqli_num_rows($checkblock) > 0 && ($cb['until'] > $timenow || empty($cb['until']) || $cb['until'] == "0")) {
		// Check if the user is permanently blocked
		if(empty($cb['until']) || $cb['until'] == "0") {
			echo "<div id='login_response'><h5 class='text-center red'>". $m['you_are_banned'] ."<br>". $cb['reason'] ."<br><br>". $m['block_expires'] ."<br>". $m['never'] ."</h5></div>";
		} else {
			echo "<div id='login_response'><h5 class='text-center red'>". $m['you_are_banned'] ."<br>". $cb['reason'] ."<br><br>". $m['block_expires'] ."<br>". date("d M Y", $cb['until']) ." ". $m['at'] ." ". date("G:i:s", $cb['until']) ."</h5></div>";
		}
		
		echo "</div>";
		
	// Check if the user is logged in
	} elseif(is_logged_in()) {
		$on_login = explode("|||", getTypeUrl("on_login"));
		if($on_login[0]) {
			header('Location: '. $on_login[1]);
			exit;
		} else {
			echo "<div id='login_response'>". $on_login[1] ."</div>";
		}
	} else {
		
		// Check if the code if filled in
		if(isset($_GET['forgot']) && !empty($_GET['code'])) {
			// Check if the code is found
			$code = mysqli_real_escape_string($con,$_GET['code']);
			$check_code = mysqli_query($con,"SELECT * FROM login_forgot_codes WHERE code='$code'");
			if(mysqli_num_rows($check_code) == 0) {
				header('Location: login.php');
			} else {
		?>
		
			<div class='row row-1 light-dark-top'>
				<h2 class='text-center'><?php echo $m['forgot_title']; ?></h2>
				<div id='forgot_changepass_response'><h5 class='text-center'><?php echo $m['enter_new_password']; ?></h5></div>
			</div>
			
			<form method='post' id='forgot_changepass' class='form-horizontal'>	
				<div class='col-container'>
					<div class='row'>
						<div class='form-group has-feedback'>
							<label class='col-sm-4 control-label'><?php echo $m['password']; ?></label>
							<div class='col-sm-7'>
								<input type='password' class='form-control' name='password'>
								<span class='glyphicon glyphicon-lock form-control-feedback'></span>
							</div>
						</div>
					</div>
					
					<div class='row'>
						<div class='form-group has-feedback'>
							<label class='col-sm-4 control-label'><?php echo $m['password2']; ?></label>
							<div class='col-sm-7'>
								<input type='password' class='form-control' name='password2'>
								<span class='glyphicon glyphicon-lock form-control-feedback'></span>
							</div>
						</div>
					</div>
				</div>
			
				<div class='row light-dark-bottom'>
					<div class='form-group special-form-group text-center'>
						<div class='row'>
							<input type='hidden' name='token' value='<?php echo md5(session_id()); ?>'>
							<input type='hidden' name='code' value='<?php echo $code; ?>'>
							<input type='submit' name='change' value='<?php echo $m['change']; ?>' class='btn btn-primary btn-large'>
						</div>
						<div class='row'>
							<div class='col-sm-12'>
								<a href='login.php'><?php echo $m['back_to_login']; ?></a>
							</div>
						</div>
					</div>
				</div>
			</form>
		
		<?php
			}
		
		} elseif(isset($_GET['forgot'])) {
		?>
		
			<div class='row row-1 light-dark-top'>
				<h2 class='text-center'><?php echo $m['forgot_title']; ?></h2>
				<div id='forgot_pass_response'><h5 class='text-center'><?php echo $m['forgot_info']; ?></h5></div>
			</div>
			
			<form method='post' id='forgot_pass' class='form-horizontal'>	
				<div class='col-container'>
					<div class='row row-1'>
						<div class='form-group has-feedback'>
							<label class='col-sm-3 control-label'><?php echo $m['username']; ?></label>
							<div class='col-sm-8'>
								<input type='text' class='form-control' name='username'>
								<i class='glyphicon glyphicon-user form-control-feedback'></i>
							</div>
						</div>
					</div>
					
					<div class='row'>
						<div class='form-group has-feedback'>
							<label class='col-sm-3 control-label'><?php echo $m['email']; ?></label>
							<div class='col-sm-8'>
								<input type='email' class='form-control' name='email'>
								<i class='glyphicon glyphicon-envelope form-control-feedback'></i>
							</div>
						</div>
					</div>
				</div>
			
				<div class='row light-dark-bottom'>
					<div class='form-group special-form-group text-center'>
						<div class='row'>
							<input type='hidden' name='token' value='<?php echo md5(session_id()); ?>'>
							<input type='submit' name='request' value='<?php echo $m['request_password_change']; ?>' class='btn btn-primary btn-large'>
						</div>
						<div class='row'>
							<div class='col-sm-12'>
								<a href='login.php'><?php echo $m['back_to_login']; ?></a>
							</div>
						</div>
					</div>
				</div>
			</form>
			
		<?php
		// Check if the user has clicked on the 'Forgot your password?'
		} elseif(isset($_GET['activation']) && !empty($_GET['activate_code'])) {
		?>
		
			<div class='row row-0 light-dark-top'>
				<h2 class='text-center'><?php echo $m['activate_title']; ?></h2>
				<div id='forgot_pass_response'><?php activateCode($_GET['activate_code']); ?></div>
				
				<div class='row row-0 row-5 text-center'>
					<div class='col-sm-12'>
						<a href='login.php'><?php echo $m['back_to_login']; ?></a>
					</div>
				</div>
			</div>
			
		<?php
		// Check if a Stripe payment is going to be made
		} elseif(isset($_GET['stripe']) && !empty($_GET['uid']) && getSetting("enable_stripe", "text") == "true") {
			
			$uid = mysqli_real_escape_string($con,$_GET['uid']);
			$check_uid = mysqli_query($con,"SELECT * FROM login_users WHERE id='$uid' AND active='0'");
			if(mysqli_num_rows($check_uid) == 0) {
				header('Location: login.php');
			} else { 
		?>
		
			<div class='row row-1 light-dark-top'>
				<h2 class='text-center'><?php echo $m['stripe_title']; ?></h2>
				<div id='stripe_response'><h5 class='text-center'><?php echo $m['stripe_info']; ?></h5></div>
			</div>
			
			<form method='post' id='stripe' class='form-horizontal'>	
				<div class='col-container'>
					<div class='row row-1'>
						<div class='form-group has-feedback'>
							<label class='col-sm-3 control-label'><?php echo $m['stripe_card']; ?></label>
							<div class='col-sm-8'>
								<input type='text' maxlength='20' class='form-control' name='card'>
								<i class='glyphicon glyphicon-credit-card form-control-feedback'></i>
							</div>
						</div>
					</div>
					
					<div class='row row-1'>
						<div class='form-group has-feedback'>
							<label class='col-sm-3 control-label'><?php echo $m['stripe_month']; ?></label>
							<div class='col-sm-8'>
								<select class='form-control' name='month'>
									<option value='01'>January</option>
									<option value='02'>February</option>
									<option value='03'>March</option>
									<option value='04'>April</option>
									<option value='05'>May</option>
									<option value='06'>June</option>
									<option value='07'>July</option>
									<option value='08'>August</option>
									<option value='09'>September</option>
									<option value='10'>October</option>
									<option value='11'>November</option>
									<option value='12'>December</option>
								</select>
							</div>
						</div>
					</div>
					
					<div class='row row-1'>
						<div class='form-group has-feedback'>
							<label class='col-sm-3 control-label'><?php echo $m['stripe_year']; ?></label>
							<div class='col-sm-8'>
								<input type='number' min='2000' step='1' value='<?php echo date("Y"); ?>' class='form-control' name='year'>
								<i class='glyphicon glyphicon-calendar form-control-feedback'></i>
							</div>
						</div>
					</div>
					
					<div class='row'>
						<div class='form-group has-feedback'>
							<label class='col-sm-3 control-label'><?php echo $m['stripe_cvc']; ?></label>
							<div class='col-sm-8'>
								<input type='number' min='0' step='1' max='9999' class='form-control' name='cvc'>
								<i class='glyphicon glyphicon-asterisk form-control-feedback'></i>
							</div>
						</div>
					</div>
				</div>
			
				<div class='row light-dark-bottom'>
					<div class='form-group special-form-group text-center'>
						<div class='row'>
							<input type='hidden' name='uid' value='<?php echo $_GET['uid']; ?>'>
							<input type='hidden' name='token' value='<?php echo md5(session_id()); ?>'>
							<input type='submit' name='pay' value='<?php echo $m['stripe_pay']; ?>' id='stripe_pay_button' class='btn btn-primary btn-large'>
						</div>
					</div>
				</div>
				
				<div class='col-container'>
					<div class='row row-5 text-center'>
						<a href='https://stripe.com/' target='_blank'><img src='<?php echo $script_path; ?>assets/images/stripe.png'></a>
					</div>
				</div>
			</form>
		
		<?php
			}
		
		// Go to the normal login
		} else {
	
			echo "<div class='row row-1 light-dark-top'>";
			echo "<h2 class='text-center'>". $m['login_title'] ."</h2>";
			
			// Check if login is disabled, if so, the login form will still be shown because only admins can login then
			if(getSetting("disable_login", "text") == "true") {
				if(getSetting("page_disabled_message", "text") == "") {
					echo "<div id='login_response'><h5 class='text-center red'>". $m['page_disabled_default'] ."</h5></div>";
				} else {
					echo "<div id='login_response'><h5 class='text-center red'>". nl2br(getSetting("page_disabled_message", "text")) ."</h5></div>";
				}
			// Check if there are any messages
			} elseif(!empty($_GET['m'])) {
				if($_GET['m'] == "1") {
					if(getSetting("message_notloggedin", "text") != "") {
						echo "<div id='login_response'><h5 class='text-center red'>". nl2br(getSetting("message_notloggedin", "text")) ."</h5></div>";
					} else {
						echo "<div id='login_response'><h5 class='text-center red'>". $m['not_logged_in'] ."</h5></div>";
					}
				}
				if($_GET['m'] == "2") {
					echo "<div id='login_response'><h5 class='text-center green'>". $m['register_success'] ."</h5></div>";
				}
				if($_GET['m'] == "3") {
					if(getSetting("message_logout", "text") != "") {
						echo "<div id='login_response'><h5 class='text-center green'>". nl2br(getSetting("message_logout", "text")) ."</h5></div>";
					} else {
						echo "<div id='login_response'><h5 class='text-center green'>". $m['logout_success'] ."</h5></div>";
					}
				}
				if($_GET['m'] == "4") {
					echo "<div id='login_response'><h5 class='text-center green'>". $m['payment_success'] ."</h5></div>";
				}
				if($_GET['m'] == "5") {
					echo "<div id='login_response'><h5 class='text-center red'>". $m['payment_wrong'] ."</h5></div>";
				}
				if($_GET['m'] == "6") {
					echo "<div id='login_response'><h5 class='text-center green'>". $m['activation_resended'] ."</h5></div>";
				}
			} else {
				echo "<div id='login_response'><h5 class='text-center'>". $m['login_info'] ."</h5></div>";
			}
			
			
			
			// If the user has cancelled his PayPal payment, he can retry the payment without having to register again
			if(isset($_GET['retry']) && !empty($_GET['uid']) && getSetting("enable_paypal", "text") == "true") {
				$userid = mysqli_real_escape_string($con,$_GET['uid']);
				$get_paypal = mysqli_query($con,"SELECT paypal FROM login_users WHERE id='$userid' AND active='0' AND paypal IS NOT NULL");
				if(mysqli_num_rows($get_paypal) > 0) {
					$gp = mysqli_fetch_array($get_paypal);
					header('Location: https://www.paypal.com/cgi-bin/webscr'. $gp['paypal']);
				}
			}
			
			
			
			// Resend activation mail, only possible if activation method is by validating email, and PayPal AND Stripe are not enabled
			if(isset($_GET['resend']) && !empty($_GET['uid']) && getSetting("activation", "text") == "1" && (getSetting("enable_paypal", "text") == "false" || getSetting("enable_stripe", "text") == "false")) {
				$userid = mysqli_real_escape_string($con,$_GET['uid']);
				$get_user = mysqli_query($con,"SELECT * FROM login_users WHERE id='$userid' AND active='0'");
				if(mysqli_num_rows($get_user) > 0) {
					$gu = mysqli_fetch_array($get_user);
					
					// Resend activation mail if the user is found and he is inactive
					$val_url = getTypeUrl("activation") . $gu['activate_code'];
					
					$subject = getSetting("validation_mail_subject", "text");
					$subject = str_replace("{val_url}", $val_url, $subject);
					$subject = str_replace("{name}", $gu['username'], $subject);
					$subject = str_replace("{email}", $gu['email'], $subject);
					$subject = str_replace("{date}", date("j-n-Y", $gu['registered_on']), $subject);
					
					$message = getSetting("validation_mail", "text");
					$message = str_replace("{val_url}", $val_url, $message);
					$message = str_replace("{name}", $gu['username'], $message);
					$message = str_replace("{email}", $gu['email'], $message);
					$message = str_replace("{date}", date("j-n-Y", $gu['registered_on']), $message);
					$message = nl2br($message);
					$message = html_entity_decode($message);
					
					sendMail($gu['email'], $subject, $message, $gu['id']); // Resend activation mail
					
					header('Location: login.php?m=6');
				}
			}
	?>
		
		</div>
		
		<form method='post' id='login' class='form-horizontal'>	
			<div class='col-container'>
				<?php 
				// Check for login type
				if(getSetting("login_with", "text") == "username") {
				?>
				<div class='row row-1'>
					<div class='form-group has-feedback'>
						<label class='col-sm-3 control-label'><?php echo $m['username']; ?></label>
						<div class='col-sm-8'>
							<input type='text' class='form-control' name='username'>
							<i class='glyphicon glyphicon-user form-control-feedback'></i>
						</div>
					</div>
				</div>
				<?php
				} else {
				?>
				<div class='row row-1'>
					<div class='form-group has-feedback'>
						<label class='col-sm-3 control-label'><?php echo $m['email']; ?></label>
						<div class='col-sm-8'>
							<input type='email' class='form-control' name='email'>
							<i class='glyphicon glyphicon-envelope form-control-feedback'></i>
						</div>
					</div>
				</div>
				<?php
				}
				?>
				
				<div class='row'>
					<div class='form-group has-feedback'>
						<label class='col-sm-3 control-label'><?php echo $m['password']; ?></label>
						<div class='col-sm-8'>
							<input type='password' class='form-control' name='password'>
							<span class='glyphicon glyphicon-lock form-control-feedback'></span>
						</div>
					</div>
				</div>
			</div>
		
			<div class='row light-dark-bottom'>
				<div class='form-group special-form-group text-center'>
					<div class='row'>
						<input type='hidden' name='token' value='<?php echo md5(session_id()); ?>'>
						<input type='submit' name='login' value='<?php echo $m['login']; ?>' id='login_button' class='btn btn-primary btn-large'>
					</div>
					<div class='row'>
						<div class='col-sm-12'>
							<a href='?forgot'><?php echo $m['forgot_password']; ?></a>
						</div>
					</div>
					<?php
					// Check if registration isn't disabled
					if(getSetting("disable_register", "text") == "false") {
					?>
					<div class='row'>
						<div class='col-sm-12'>
							<a href='<?php echo $script_path; ?>register.php'><?php echo $m['dont_have_account']; ?></a>
						</div>
					</div>
					<?php
					}
					?>
					<?php
					// Check if any of the social logins is enabled
					if(getSetting("enable_google", "text") == "true" || getSetting("enable_facebook", "text") == "true" || getSetting("enable_twitter", "text") == "true") {
					?>
					<div class='row'>
						<h3><?php echo $m['or_login']; ?></h3>
						
						<div class='col-md-4'></div>
						<div class='col-md-4'>
							<?php
							// If enabled, show login with Google icon
							if(getSetting("enable_google", "text") == "true") {
							?>
							<div class='col-md-4'>
								<a href='social.php?login=google' id='google' title='Google+'><img src='<?php echo $script_path; ?>assets/images/google.png' width='32px' height='32px'></a>
							</div>
							
							<script>
							$("#google").tooltip({
								container: 'body'
							});
							</script>
							<?php
							}
							// If enabled, show login with Facebook icon
							if(getSetting("enable_facebook", "text") == "true") {
							?>
							<div class='col-md-4'>
								<a href='social.php?login=facebook' id='facebook' title='Facebook'><img src='<?php echo $script_path; ?>assets/images/facebook.png' width='32px' height='32px'></a>
							</div>
							
							<script>
							$("#facebook").tooltip({
								container: 'body'
							});
							</script>
							<?php
							}
							// If enabled, show login with Twitter icon
							if(getSetting("enable_twitter", "text") == "true") {
							?>
							<div class='col-md-4'>
								<a href='social.php?login=twitter' id='twitter' title='Twitter'><img src='<?php echo $script_path; ?>assets/images/twitter.png' width='32px' height='32px'></a>
							</div>
							
							<script>
							$("#twitter").tooltip({
								container: 'body'
							});
							</script>
							<?php
							}
							?>
						</div>
					</div>
					<?php
					}
					?>
				</div>
			</div>
		</form>
		<?php
		}
		?>

	<?php
	}
	?>
</div>

<?php
include('footer.php');
?>