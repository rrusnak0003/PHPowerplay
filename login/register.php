<?php
include('head.php');
?>

<div class='container'>
	<!-- Check if javascript is enabled -->
	<noscript>
		<div class='alert alert-danger' role='alert'><?php echo $m['enable_javascript']; ?></div>
	</noscript>
</div>

<div class='container-small'>
	<?php
	$ip = mysqli_real_escape_string($con,$_SERVER['REMOTE_ADDR']);
	$checkblock = mysqli_query($con,"SELECT * FROM login_blocks WHERE ip='$ip'");
	$cb = mysqli_fetch_array($checkblock);
	$timenow = time();
	
	// Check if the user is blocked
	if(mysqli_num_rows($checkblock) > 0 && ($cb['until'] > $timenow || empty($cb['until']) || $cb['until'] == "0")) {
		// Check if the user is permanently blocked
		if(empty($cb['until']) || $cb['until'] == "0") {
			echo "<div id='login_response'><h5 class='text-center red'>". $m['you_are_banned'] ."<br>". $cb['reason'] ."<br><br>". $m['block_expires'] ."<br>". $m['never'] ."</h5></div>";
		} else {
			echo "<div id='login_response'><h5 class='text-center red'>". $m['you_are_banned'] ."<br>". $cb['reason'] ."<br><br>". $m['block_expires'] ."<br>". date("d M Y", $cb['until']) ." ". $m['at'] ." ". date("G:i:s", $cb['until']) ."</h5></div>";
		}
		
		echo "</div>";
	} else {
	?>
	
	<div class='row row-1 light-dark-top'>
		<h1 class='text-center'><?php echo $m['register_title']; ?></h1>
	
	<?php
	// Check if the registration page is disabled
	if(getSetting("disable_register", "text") == "true") {
		if(getSetting("page_disabled_message", "text") == "") {
			echo "<div id='register_response'><h5 class='text-center red'>". $m['page_disabled_default'] ."</h5></div>";
		} else {
			echo "<div id='register_response'><h5 class='text-center red'>". nl2br(getSetting("page_disabled_message", "text")) ."</h5></div>";
		}
		
		echo "</div>";
	} else {
	?>
		
		<div id='register_response'><h5 class='text-center'><?php echo $m['register_info']; ?></h5></div>
	</div>
	
	<form method='post' id='register'>	
		<div class='row'>
			<div class='col-md-12'>
				<div class='form-group'>
					<label class='col-sm-4 control-label'><?php echo $m['username']; ?>*</label>
					<div class='col-sm-8 row-1'>
						<input type='text' class='form-control' name='username'>
					</div>
				</div>
				
				<div class='form-group'>
					<label class='col-sm-4 control-label'><?php echo $m['email']; ?>*</label>
					<div class='col-sm-8 row-1'>
						<input type='email' class='form-control' name='email'>
					</div>
				</div>
				
				<div class='form-group'>
					<label class='col-sm-4 control-label'><?php echo $m['password']; ?>*</label>
					<div class='col-sm-8 row-1'>
						<input type='password' class='form-control' name='password'>
					</div>
				</div>
				
				<div class='form-group'>
					<label class='col-sm-4 control-label'><?php echo $m['password2']; ?>*</label>
					<div class='col-sm-8 row-1'>
						<input type='password' class='form-control' name='password2'>
					</div>
				</div>
				
				<?php
				// Get the extra inputs, empty
				getExtraInputs();
				?>
				
				<?php
				// Check for multiple payment gateways
				if(getSetting("enable_paypal", "text") == "true" && getSetting("enable_stripe", "text") == "true") {
				?>
				<div class='row row-6'>
					<div class='form-group'>
						<label class='col-sm-4 control-label'><?php echo $m['payment_method']; ?>*</label>
						<div class='col-sm-8'>
							<div class='radio'>
								<label>
									<input type='radio' name='pay_method' value='Paypal' checked> <img src='<?php echo $script_path; ?>assets/images/paypal.png' width='100px' height='26px'>
								</label>
							</div>
							<div class='radio'>
								<label>
									<input type='radio' name='pay_method' value='Stripe'> <img src='<?php echo $script_path; ?>assets/images/stripe_logo.png' width='100px' height='34px'>
								</label>
							</div>
						</div>
					</div>
				</div>
				<?php
				}
				?>
				
				<?php
				// Check if reCAPTCHA is enabled
				if(getSetting("recaptcha", "text") == "true") {
					require_once('includes/recaptchalib.php');
					$publickey = getSetting("publickey", "text");
					
					if($language == "dutch") {
						$lang = "nl";
					} else {
						$lang = "en";
					}
					?>
					
					<div class='row row-6'>
						<div class='form-group'>
							<label class='col-sm-4 control-label'><?php echo $m['recaptcha']; ?>*</label>
							<div class='col-sm-8' id='recaptcha_div'>
								<div class='g-recaptcha' data-sitekey='<?php echo $publickey;?>'></div>
								<script type='text/javascript' src='https://www.google.com/recaptcha/api.js?hl=<?php echo $lang; ?>'></script>
							</div>
						</div>
					</div>
					<?php
				}
				?>
			</div>
		</div>
		
		<div class='row light-dark-bottom'>
			<div class='form-group special-form-group text-center'>
				<div class='row'>
					<input type='hidden' name='token' value='<?php echo md5(session_id()); ?>'>
					<input type='submit' name='register' value='<?php echo $m['register']; ?>' id='register_button' class='btn btn-primary btn-large'>
				</div>
				<div class='row'>
					<a href='<?php echo $script_path; ?>login.php'><?php echo $m['already_registered']; ?></a>
				</div>
			</div>
		</div>
	</form>
	
	<?php
	}
	}
	?>
	
</div>

<?php
include('footer.php');
?>