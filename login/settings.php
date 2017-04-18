<?php
include('includes/api.php');
logged_in();
admin();

include('head.php');
include('menu.php');
?>

	<div class='container container-admin'>
		<div class='col-md-2'>
			<h3><?php echo $m['settings_menu']; ?></h3>
			<nav>
				<ul class='nav nav-pills nav-stacked span2'>
					<li><a href='?page=main'><span class='glyphicon glyphicon-dashboard' aria-hidden='true'></span> <?php echo $m['main_menu']; ?></a></li>
					<li><a href='?page=permissions'><span class='glyphicon glyphicon-asterisk' aria-hidden='true'></span> <?php echo $m['permissions_menu']; ?></a></li>
				</ul>
			</nav>
		</div>
		
		<div class='col-md-10'>

		<?php
		// Redirect settings page
		if(!empty($_GET['page']) && $_GET['page'] == 'links') {
		?>
			<div class='row row-1'>
				<h3><?php echo $m['links']; ?></h3>
				<h5><?php echo $m['link_info']; ?></h5>
			</div>
			
			<div id='redirect_response'></div>
			
			<form method='post' id='redirect'>
				<div class='row row-1'>
					<div class='form-group'>
						<label class='col-sm-4 control-label'><a id='redirect_login' title='<?php echo $m['redirect_login_title']; ?>'><?php echo $m['redirect_login']; ?></a> <a id='on_login_warning' title='<?php echo $m['on_login_warning']; ?>'><span class='glyphicon glyphicon-exclamation-sign'></span></a></label>
						<div class='col-sm-8'>
							<div class='checkbox'>
								<label><input type='checkbox' name='use_redirect_login' <?php if(getSetting("use_redirect_login", "checkbox")) { echo "checked"; } ?>> <?php echo $m['enable']; ?></label>
							</div>
							<input type='text' class='form-control' name='redirect_login'<?php echo getSetting("redirect_login", "value"); ?> placeholder='<?php echo $m['url_example']; ?>'>
						</div>
					</div>
				</div>
				
				<div class='row row-1'>
					<div class='form-group'>
						<label class='col-sm-4 control-label'><a id='redirect_logout' title='<?php echo $m['redirect_logout_title']; ?>'><?php echo $m['redirect_logout']; ?></a></label>
						<div class='col-sm-8'>
							<div class='checkbox'>
								<label><input type='checkbox' name='use_redirect_logout' <?php if(getSetting("use_redirect_logout", "checkbox")) { echo "checked"; } ?>> <?php echo $m['enable']; ?></label>
							</div>
							<input type='text' class='form-control' name='redirect_logout'<?php echo getSetting("redirect_logout", "value"); ?> placeholder='<?php echo $m['url_example']; ?>'>
						</div>
					</div>
				</div>
				
				<div class='row row-1'>
					<div class='form-group'>
						<label class='col-sm-4 control-label'><a id='redirect_nopermission' title='<?php echo $m['redirect_nopermission_title']; ?>'><?php echo $m['redirect_nopermission']; ?></a></label>
						<div class='col-sm-8'>
							<div class='checkbox'>
								<label><input type='checkbox' name='use_redirect_nopermission' <?php if(getSetting("use_redirect_nopermission", "checkbox")) { echo "checked"; } ?>> <?php echo $m['enable']; ?></label>
							</div>
							<input type='text' class='form-control' name='redirect_nopermission'<?php echo getSetting("redirect_nopermission", "value"); ?> placeholder='<?php echo $m['url_example']; ?>'>
						</div>
					</div>
				</div>
				
				<div class='row row-2'>
					<div class='form-group'>
						<label class='col-sm-4 control-label'><a id='redirect_notloggedin' title='<?php echo $m['redirect_notloggedin_title']; ?>'><?php echo $m['redirect_notloggedin']; ?></a> <a id='need_redirect' title='<?php echo $m['need_redirect']; ?>'><span class='glyphicon glyphicon-exclamation-sign'></span></a></label>
						<div class='col-sm-8'>
							<div class='checkbox'>
								<label><input type='checkbox' name='use_redirect_notloggedin' <?php if(getSetting("use_redirect_notloggedin", "checkbox")) { echo "checked"; } ?> disabled> <?php echo $m['enable']; ?></label>
							</div>
							<input type='text' class='form-control' name='redirect_notloggedin'<?php echo getSetting("redirect_notloggedin", "value"); ?> placeholder='<?php echo $m['url_example']; ?>'>
						</div>
					</div>
				</div>
				
				<div class='row'>
					<div class='form-group'>
						<input type='hidden' name='token' value='<?php echo md5(session_id()); ?>'>
						<input type='submit' name='save' value='<?php echo $m['save']; ?>' class='btn btn-primary'>
					</div>
				</div>
			</form>
		<?php
		// Mail settings page
		} elseif(!empty($_GET['page']) && $_GET['page'] == "mails") {
		?>
			<div class='row row-1'>
				<h3><?php echo $m['mails']; ?></h3>
				<h5><?php echo $m['mails_info']; ?></h5>
			</div>
			
			<div id='mail_response'></div>
			<form method='post' id='mail'>
				<div class='row'>
					<div class='form-group'>
						<label class='col-sm-4 control-label'><a id='mailtype' title='<?php echo $m['mailtype_title']; ?>'><?php echo $m['mailtype']; ?></a></label>
						<div class='col-sm-8'>
							<select name='mailtype' class='form-control' id='selectmailtype'>
								<option value='local'>Local (PHPMailer)</option>
								<option value='smtp'>SMTP (PHPMailer)</option>
							</select>
						</div>
					</div>
				</div>
				
				<div id='smtp_settings'>
					<div class='row'>
						<div class='form-group'>
							<label class='col-sm-4 control-label'><a id='smtp_hostname' title='<?php echo $m['smtp_hostname_title']; ?>'><?php echo $m['smtp_hostname']; ?></a></label>
							<div class='col-sm-8'>
								<input type='text' class='form-control' name='smtp_hostname'<?php echo getSetting("smtp_hostname", "value"); ?>>
							</div>
						</div>
					</div>
					
					<div class='row'>
						<div class='form-group'>
							<label class='col-sm-4 control-label'><?php echo $m['smtp_username']; ?></label>
							<div class='col-sm-8'>
								<input type='text' class='form-control' name='smtp_username'<?php echo getSetting("smtp_username", "value"); ?>>
							</div>
						</div>
					</div>
					
					<div class='row'>
						<div class='form-group'>
							<label class='col-sm-4 control-label'><?php echo $m['smtp_password']; ?></label>
							<div class='col-sm-8'>
								<input type='password' class='form-control' name='smtp_password'<?php echo getSetting("smtp_password", "value"); ?>>
							</div>
						</div>
					</div>
					
					<div class='row'>
						<div class='form-group'>
							<label class='col-sm-4 control-label'><a id='smtp_port' title='<?php echo $m['smtp_port_title']; ?>'><?php echo $m['smtp_port']; ?></a></label>
							<div class='col-sm-2'>
								<input type='number' class='form-control' min='0' name='smtp_port'<?php echo getSetting("smtp_port", "value"); ?>>
							</div>
						</div>
					</div>
					
					<div class='row'>
						<div class='form-group'>
							<label class='col-sm-4 control-label'><?php echo $m['smtp_ssl']; ?></label>
							<div class='col-sm-8'>
								<div class='checkbox'>
									<label><input type='checkbox' name='smtp_ssl'<?php if(getSetting("smtp_ssl", "checkbox")) { echo " checked"; } ?>> <?php echo $m['use_ssl']; ?></label>
								</div>
							</div>
						</div>
					</div>
				</div>
				
				<script>
				// Select the current mail type
				$('#selectmailtype').val("<?php echo getSetting("mailtype", "text"); ?>");
				
				// Show smtp settings if the mail type is smtp
				$("#selectmailtype").change(function() {
					if($(this).val() == "smtp") {
						$('#smtp_settings').css('display', 'block');
					} else {
						$('#smtp_settings').css('display', 'none');
					}
				});

				// Same as above, but this is for on page load
				if($("#selectmailtype").val() == "smtp") {
					$('#smtp_settings').css('display', 'block');
				} else {
					$('#smtp_settings').css('display', 'none');
				}
				</script>
				
				
				
				<div class='row row-8'>
					<div class='form-group'>
						<label class='col-sm-4 control-label'><a id='welcome_mail' title='<?php echo $m['welcome_mail_title']; ?>'><?php echo $m['welcome_mail']; ?></a><br><br><?php echo $m['welcome_tags']; ?></label>
						<div class='col-sm-8'>
							<input type='text' class='form-control' name='welcome_mail_subject'<?php echo getSetting("welcome_mail_subject", "value"); ?>>
							<br>
							<textarea class='form-control' name='welcome_mail' rows='10'><?php echo getSetting("welcome_mail", "text"); ?></textarea>
						</div>
					</div>
				</div>
				
				<div class='row row-6'>
					<div class='form-group'>
						<label class='col-sm-4 control-label'><a id='validation_mail' title='<?php echo $m['validation_mail_title']; ?>'><?php echo $m['validation_mail']; ?></a><br><br><?php echo $m['validation_tags']; ?></label>
						<div class='col-sm-8'>
							<input type='text' class='form-control' name='validation_mail_subject'<?php echo getSetting("validation_mail_subject", "value"); ?>>
							<br>
							<textarea class='form-control' name='validation_mail' rows='10'><?php echo getSetting("validation_mail", "text"); ?></textarea>
						</div>
					</div>
				</div>
				
				<div class='row row-6'>
					<div class='form-group'>
						<label class='col-sm-4 control-label'><a id='reset_mail' title='<?php echo $m['reset_mail_title']; ?>'><?php echo $m['reset_mail']; ?></a><br><br><?php echo $m['reset_tags']; ?></label>
						<div class='col-sm-8'>
							<input type='text' class='form-control' name='reset_mail_subject'<?php echo getSetting("reset_mail_subject", "value"); ?>>
							<br>
							<textarea class='form-control' name='reset_mail' rows='10'><?php echo getSetting("reset_mail", "text"); ?></textarea>
						</div>
					</div>
				</div>
				
				<div class='row row-6'>
					<div class='form-group'>
						<input type='hidden' name='token' value='<?php echo md5(session_id()); ?>'>
						<input type='submit' name='save' value='<?php echo $m['save']; ?>' class='btn btn-primary'>
					</div>
				</div>
			</form>
		<?php
		// Login settings page
		} elseif(!empty($_GET['page']) && $_GET['page'] == "login") {
		?>
			<h3><?php echo $m['login_settings']; ?></h3>
			<h5><?php echo $m['login_settings_info']; ?></h5><br>
			
			<div id='login_response'>
			<?php
			// Check if max failed attempts is on and log failed logins is off
			if(getSetting("max_failed_attempts", "text") > 0 && getSetting("log_failed_logins", "text") == "false") {
				echo "<div class='alert alert-info' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['enable_log'] ."</div>";
			}
			?>
			</div>
			
			<form method='post' id='login_form'>
				<div class='row row-2'>
					<div class='form-group'>
						<label class='col-sm-4 control-label'><a id='login_log' title='<?php echo $m['login_log']; ?>'><?php echo $m['log']; ?></a></label>
						<div class='col-sm-8'>
							<div class='checkbox'>
								<label><input type='checkbox' name='log_successful_logins' <?php if(getSetting("log_successful_logins", "checkbox")) { echo "checked"; } ?>> <?php echo $m['log_successful_logins']; ?></label>
							</div>
							<div class='checkbox' id='checkbox_log_failed_logins'>
								<label><input type='checkbox' id='log_failed_logins' name='log_failed_logins'<?php if(getSetting("log_failed_logins", "checkbox")) { echo " checked"; } ?>> <?php echo $m['log_failed_logins']; ?></label>
							</div>
						</div>
					</div>
				</div>
				
				<div class='row'>
					<div class='form-group'>
						<label class='col-sm-4 control-label'><a id='max_failed_attempts' title='<?php echo $m['max_failed_attempts_title']; ?>'><?php echo $m['max_failed_attempts']; ?></a></label>
						<div class='col-sm-2'>
							<input type='number' min='0' id='input_max_failed_attempts' onchange='loginCheck();' class='form-control' name='max_failed_attempts'<?php echo getSetting("max_failed_attempts", "value"); ?>>
						</div>
					</div>
				</div>
				
				<div class='row row-2'>
					<div class='form-group'>
						<label class='col-sm-4 control-label'><a id='blocked_time' title='<?php echo $m['blocked_time_title']; ?>'><?php echo $m['blocked_time']; ?></a></label>
						<div class='col-sm-2'>
							<input type='number' min='0' step='1' class='form-control' id='blocked_amount' name='blocked_amount'<?php echo getSetting("blocked_amount", "value"); ?>>
						</div>
						<div class='col-sm-4'>
							<select name='blocked_format' id='blocked_format' onchange='checkFormat();' class='form-control'>
								<option value='minutes'><?php echo $m['minutes']; ?></option>
								<option value='hours'><?php echo $m['hours']; ?></option>
								<option value='days'><?php echo $m['days']; ?></option>
								<option value='months'><?php echo $m['months']; ?></option>
								<option value='years'><?php echo $m['years']; ?></option>
								<option value='forever'><?php echo $m['forever']; ?></option>
							</select>
							
							<script>
							$(document).ready(function() {
								// Check the currect format, if the format is forever, the amount input should be disabled
								checkFormat();
							});
							
							<?php if(getSetting("blocked_format", "text") != "") { ?>
							$('select[name=blocked_format]').val("<?php echo getSetting("blocked_format", "text"); ?>");
							<?php } ?>
							</script>
						</div>
					</div>
				</div>
				
				<div class='row row-2'>
					<div class='form-group'>
						<label class='col-sm-4 control-label'><a id='redirect_last_page' title='<?php echo $m['redirect_last_page_title']; ?>'><?php echo $m['redirect_last_page']; ?></a></label>
						<div class='col-sm-8'>
							<div class='checkbox'>
								<label><input type='checkbox' name='redirect_last_page' <?php if(getSetting("redirect_last_page", "checkbox")) { echo "checked"; } ?>> <?php echo $m['redirect_last_page']; ?></label>
							</div>
						</div>
					</div>
					<div class='form-group'>
						<label class='col-sm-4 control-label'><a id='case_sensitive' title='<?php echo $m['case_sensitive_title']; ?>'><?php echo $m['case_sensitive']; ?></a></label>
						<div class='col-sm-8'>
							<div class='checkbox'>
								<label><input type='checkbox' name='case_sensitive' <?php if(getSetting("case_sensitive", "checkbox")) { echo "checked"; } ?>> <?php echo $m['case_sensitive']; ?></label>
							</div>
						</div>
					</div>
				</div>
				
				<div class='row'>
					<div class='form-group'>
						<input type='hidden' name='token' value='<?php echo md5(session_id()); ?>'>
						<input type='submit' name='save' value='<?php echo $m['save']; ?>' class='btn btn-primary'>
					</div>
				</div>
			</form>
		<?php
		// Registration settings page
		} elseif(!empty($_GET['page']) && $_GET['page'] == "registration") {
		?>
			<div class='row row-1'>
				<h3><?php echo $m['registration_settings']; ?></h3>
				<h5><?php echo $m['registration_settings_info']; ?></h5>
			</div>
			
			<?php
			if(!empty($_GET['id'])) {
				$id = mysqli_real_escape_string($con,$_GET['id']);
				$getinput = mysqli_query($con,"SELECT * FROM login_inputs WHERE id='$id'");
				if(mysqli_num_rows($getinput) == 1) {
					$gi = mysqli_fetch_array($getinput);
					?>
					<div id='edit_response'></div>
					<form method='post' id='save_input'>
						<div class='row'>
							<div class='form-group'>
								<label class='col-sm-4 control-label'><a id='input_name' title='<?php echo $m['name_title']; ?>'><?php echo $m['name']; ?></a> <a id='no_spaces_allowed' title='<?php echo $m['no_spaces_allowed']; ?>'><span class='glyphicon glyphicon-exclamation-sign'></span></a></label>
								<div class='col-sm-8'>
									<input type='text' class='form-control' name='input_name' value='<?php echo $gi['name']; ?>'>
								</div>
							</div>
						</div>
						
						<div class='row' id='public_name'>
							<div class='form-group'>
								<label class='col-sm-4 control-label'><a id='input_public_name' title='<?php echo $m['public_name_title']; ?>'><?php echo $m['public_name']; ?></a></label>
								<div class='col-sm-8'>
									<input type='text' class='form-control' name='input_public_name' value='<?php echo $gi['public_name']; ?>'>
								</div>
							</div>
						</div>
						
						<div class='row'>
							<div class='form-group'>
								<label class='col-sm-4 control-label'><a id='input_type' title='<?php echo $m['type_title']; ?>'><?php echo $m['type']; ?></a></label>
								<div class='col-sm-8'>
									<select class='form-control' name='input_type' id='type' onchange='check();'>
										<option value='text'>text</option>
										<option value='textarea'>textarea</option>
										<option value='checkbox'>checkbox</option>
										<option value='select'>select</option>
										<option value='hidden'>hidden</option>
										<option value='number'>(HTML5) number</option>
										<option value='range'>(HTML5) range</option>
										<option value='date'>(HTML5) date</option>
										<option value='url'>(HTML5) url</option>
										<option value='color'>(HTML5) color</option>
										<option value='email'>(HTML5) email</option>
									</select>
									
									<script>
										// Select the current input type
										$('select[name=input_type]').val("<?php echo $gi['type']; ?>");
									</script>
								</div>
							</div>
						</div>
						
						
						<!-- begin extra inputs -->
						<div class='row' id='maxlength'>
							<div class='form-group'>
								<label class='col-sm-4 control-label'><a id='input_maxlength' title='<?php echo $m['maxlength_title']; ?>'><?php echo $m['input_maxlength']; ?></a></label>
								<div class='col-sm-8'>
									<input type='number' class='form-control' name='input_maxlength' min='0'<?php if(!empty($gi['maxlength'])) { echo " value='". $gi['maxlength'] ."'"; } else { echo " value='0'"; } ?>>
								</div>
							</div>
						</div>
						
						<div class='row' id='rows' style='display: none;'>
							<div class='form-group'>
								<label class='col-sm-4 control-label'><a id='input_rows' title='<?php echo $m['rows_title']; ?>'><?php echo $m['input_rows']; ?></a></label>
								<div class='col-sm-8'>
									<input type='number' class='form-control' name='input_rows' min='0'<?php if(!empty($gi['rows'])) { echo " value='". $gi['rows'] ."'"; } else { echo " value='0'"; } ?>>
								</div>
							</div>
						</div>
						
						<div class='row' id='min' style='display: none;'>
							<div class='form-group'>
								<label class='col-sm-4 control-label'><a id='input_min' title='<?php echo $m['min_title']; ?>'><?php echo $m['input_min']; ?></a></label>
								<div class='col-sm-8'>
									<input type='number' class='form-control' name='input_min' min='0'<?php if(!empty($gi['min'])) { echo " value='". $gi['min'] ."'"; } else { echo " value='0'"; } ?>>
								</div>
							</div>
						</div>
						
						<div class='row' id='max' style='display: none;'>
							<div class='form-group'>
								<label class='col-sm-4 control-label'><a id='input_max' title='<?php echo $m['max_title']; ?>'><?php echo $m['input_max']; ?></a></label>
								<div class='col-sm-8'>
									<input type='number' class='form-control' name='input_max' min='0'<?php if(!empty($gi['max'])) { echo " value='". $gi['max'] ."'"; } else { echo " value='0'"; } ?>>
								</div>
							</div>
						</div>
						
						<div class='row' id='step' style='display: none;'>
							<div class='form-group'>
								<label class='col-sm-4 control-label'><a id='input_step' title='<?php echo $m['step_title']; ?>'><?php echo $m['input_step']; ?></a></label>
								<div class='col-sm-8'>
									<input type='number' class='form-control' name='input_step' min='0'<?php if(!empty($gi['step'])) { echo " value='". $gi['step'] ."'"; } else { echo " value='0'"; } ?>>
								</div>
							</div>
						</div>
						
						<div class='row' id='checked' style='display: none;'>
							<div class='form-group'>
								<label class='col-sm-4 control-label'><a id='input_checked' title='<?php echo $m['checked_title']; ?>'><?php echo $m['input_checked']; ?></a></label>
								<div class='col-sm-2'>
									<div class='checkbox'>
										<label><input type='checkbox' name='input_checked'<?php if($gi['checked'] == "true") { echo " checked"; } ?>> <?php echo $m['input_checked']; ?></label>
									</div>
								</div>
							</div>
						</div>
						<!-- end extra inputs -->
						
						
						<div class='row' id='placeholder'>
							<div class='form-group'>
								<label class='col-sm-4 control-label'><a id='input_placeholder' title='<?php echo $m['placeholder_title']; ?>'><?php echo $m['placeholder']; ?></a></label>
								<div class='col-sm-8'>
									<input type='text' class='form-control' name='input_placeholder'<?php if(!empty($gi['placeholder'])) { echo " value='". $gi['placeholder'] ."'"; } ?>>
								</div>
							</div>
						</div>
						
						<div class='row' id='value'>
							<div class='form-group'>
								<label class='col-sm-4 control-label'><a id='input_value' title='<?php echo $m['value_title']; ?>'><?php echo $m['input_value']; ?></a></label>
								<div class='col-sm-8'>
									<input type='text' class='form-control' name='input_value'<?php if(!empty($gi['value'])) { echo " value='". $gi['value'] ."'"; } ?>>
								</div>
							</div>
						</div>
						
						<div class='row' id='required'>
							<div class='form-group'>
								<label class='col-sm-4 control-label'><a id='input_required' title='<?php echo $m['required_title']; ?>'><?php echo $m['required']; ?></a></label>
								<div class='col-sm-2'>
									<div class='checkbox'>
										<label><input type='checkbox' name='input_required'<?php if($gi['required'] == "true") { echo " checked"; } ?>> <?php echo $m['required']; ?></label>
									</div>
								</div>
							</div>
						</div>
						
						<div class='row' id='error'>
							<div class='form-group'>
								<label class='col-sm-4 control-label'><a id='input_error' title='<?php echo $m['input_error_title']; ?>'><?php echo $m['input_error']; ?></a></label>
								<div class='col-sm-8'>
									<textarea class='form-control' name='input_error' rows='3'><?php if(!empty($gi['input_error'])) { echo $gi['input_error']; } ?></textarea>
								</div>
							</div>
						</div>
						
						<div class='row' id='options'>
							<div class='form-group'>
								<label class='col-sm-4 control-label'><a id='select_options' title='<?php echo $m['select_options_title']; ?>'><?php echo $m['select_options']; ?></a></label>
								<?php
								$pairs = explode("|||", $gi['options']); // Make pairs of names with values
								?>
								<div class='col-sm-4' id='names'>
									<label class='control-label'><?php echo $m['name']; ?></label>
									<?php
									if(count($pairs) > 1) {
										foreach($pairs as $pair) {
										$pa = explode("***", $pair); // Split name and value
										?>
										<input type='text' name='name[]' value='<?php echo $pa[0]; ?>' class='form-control'>
										<?php
										}
									} else {
									?>
									<input type='text' name='name[]' class='form-control'>
									<?php
									}
									?>
								</div>
								<div class='col-sm-4' id='values'>
									<label class='control-label'><?php echo $m['value']; ?></label>
									<?php
									if(count($pairs) > 1) {
										foreach($pairs as $pair) {
										$pa = explode("***", $pair); // Split name and value
										?>
										<input type='text' name='value[]' value='<?php echo $pa[1]; ?>' class='form-control'>
										<?php
										}
									} else {
									?>
									<input type='text' name='value[]' class='form-control'>
									<?php
									}
									?>
								</div>
								
								<div class='col-sm-4'></div>
								<div class='col-sm-4 row-5'>
									<button type='button' name='add_option' onclick='addOption();' class='btn btn-primary'><?php echo $m['add_option']; ?></button>
								</div>
							</div>
						</div>
						
						<div class='row' id='public'>
							<div class='form-group'>
								<label class='col-sm-4 control-label'><a id='input_public' title='<?php echo $m['input_public_title']; ?>'><?php echo $m['input_public']; ?></a></label>
								<div class='col-sm-4'>
									<div class='checkbox'>
										<label><input type='checkbox' name='input_public'<?php if($gi['public'] == "true") { echo " checked"; } ?>> <?php echo $m['input_public']; ?></label>
									</div>
								</div>
							</div>
						</div>
						
						<div class='row'>
							<div class='form-group'>
								<input type='hidden' name='token' value='<?php echo md5(session_id()); ?>'>
								<input type='hidden' name='id' value='<?php echo $id; ?>'>
								<input type='submit' name='save' value='<?php echo $m['save']; ?>' class='btn btn-primary'>
							</div>
						</div>
					</form>
				<?php
				} else {
					header('Location: ?page=registration');
				}
			} else {
			?>
			
			<div id='registration_response'></div>
			
			<form method='post' id='registration_form'>
				<div class='row'>
					<div class='form-group'>
						<label class='col-sm-4 control-label'><a id='on_registration' title='<?php echo $m['on_registration_title']; ?>'><?php echo $m['on_registration']; ?></a></label>
						<div class='col-sm-8'>
							<div class='checkbox'>
								<label><input type='checkbox' id='recaptcha_enabled' name='recaptcha' <?php if(getSetting("recaptcha", "checkbox")) { echo "checked"; } ?>> <?php echo $m['require_recaptcha']; ?></label>
							</div>
							<div id='recaptcha' style='display: none;'>
							<a href='https://www.google.com/recaptcha/admin#whyrecaptcha' target='_blank'>Create reCAPTCHA key</a><br><br>
							<strong>Public key</strong> <input type='text' name='publickey'<?php echo getSetting("publickey", "value"); ?> class='form-control'><br>
							<strong>Private key</strong> <input type='text' name='privatekey'<?php echo getSetting("privatekey", "value"); ?> class='form-control'><br>
							</div>
							<div class='checkbox'>
								<label><input type='checkbox' name='send_welcome_mail' <?php if(getSetting("send_welcome_mail", "checkbox")) { echo "checked"; } ?>> <?php echo $m['send_welcome_mail']; ?></label>
							</div>
						</div>
					</div>
				</div>
				
				<div class='row row-4'>
					<div class='form-group'>
						<label class='col-sm-4 control-label'><a id='max_ip' title='<?php echo $m['max_ip_title']; ?>'><?php echo $m['max_ip']; ?></a></label>
						<div class='col-sm-2'>
							<input type='number' min='0' class='form-control' name='max_ip'<?php echo getSetting("max_ip", "value"); ?>>
						</div>
					</div>
				</div>
				
				<div class='row row-2'>
					<div class='form-group'>
						<label class='col-sm-4 control-label'><a id='activation' title='<?php echo $m['activation_title']; ?>'><?php echo $m['activation']; ?></a></label>
						<div class='col-sm-6'>
							<select name='activation' class='form-control'>
								<option value='0'><?php echo $m['no_activation']; ?></option>
								<option value='1'><?php echo $m['email_activation']; ?></option>
								<option value='2'><?php echo $m['manual_activation']; ?></option>
							</select>
							
							<?php if(getSetting("activation", "text") != "") { ?>
							<script>
							// Select the current activation type
							$('select[name=activation]').val("<?php echo getSetting("activation", "text"); ?>");
							</script>
							<?php } ?>
						</div>
					</div>
				</div>
				
				<div class='row'>
					<div class='form-group'>
						<label class='col-sm-4 control-label'><a id='username_length' title='<?php echo $m['username_length_title']; ?>'><?php echo $m['username_length']; ?></a></label>
						<div class='col-sm-1'>
							<?php echo $m['minimum']; ?>
						</div>
						<div class='col-sm-2'>
							<input type='number' min='0' class='form-control' name='username_minlength'<?php echo getSetting("username_minlength", "value"); ?>>
						</div>
						
						<div class='col-sm-1'>
							<?php echo $m['maximum']; ?>
						</div>
						<div class='col-sm-2'>
							<input type='number' min='0' class='form-control' name='username_maxlength'<?php echo getSetting("username_maxlength", "value"); ?>>
						</div>
					</div>
				</div>
				
				<div class='row row-2'>
					<div class='form-group'>
						<label class='col-sm-4 control-label'><a id='password_length' title='<?php echo $m['password_length_title']; ?>'><?php echo $m['password_length']; ?></a></label>
						<div class='col-sm-1'>
							<?php echo $m['minimum']; ?>
						</div>
						<div class='col-sm-2'>
							<input type='number' min='0' class='form-control' name='password_minlength'<?php echo getSetting("password_minlength", "value"); ?>>
						</div>
						
						<div class='col-sm-1'>
							<?php echo $m['maximum']; ?>
						</div>
						<div class='col-sm-2'>
							<input type='number' min='0' class='form-control' name='password_maxlength'<?php echo getSetting("password_maxlength", "value"); ?>>
						</div>
					</div>
				</div>
				
				<div class='row row-4'>
					<div class='form-group'>
						<input type='hidden' name='token' value='<?php echo md5(session_id()); ?>'>
						<input type='submit' name='save' value='<?php echo $m['save']; ?>' class='btn btn-primary'>
					</div>
				</div>
			</form>
			
			
			
			<div class='row row-8'>
				<h3><?php echo $m['extra_inputs']; ?></h3>
				
				<div id='move_response'></div>
				<div id='inputs'>
					<?php
					$inputs = mysqli_query($con,"SELECT * FROM login_inputs ORDER BY place DESC");
							
					if(mysqli_num_rows($inputs) > 0) {
					?>
						<table class='table' id='input_table'>
							<thead>
								<tr>
									<td><strong><?php echo $m['place']; ?></strong></td>
									<td><strong><?php echo $m['name']; ?></strong></td>
									<td><strong><?php echo $m['type']; ?></strong></td>
									<td><strong><?php echo $m['required']; ?></strong></td>
									<td><strong><?php echo $m['actions']; ?></strong></td>
								</tr>
							</thead>
							<tbody>
							<?php
							while($i = mysqli_fetch_array($inputs)) {
							?>
								<tr>
									<td><?php echo $i['place']; ?></td>
									<td><?php echo $i['name']; ?></td>
									<td><?php echo $i['type']; ?></td>
									<td><?php echo $i['required']; ?></td>
									<td>
										<a class='edit' title='<?php echo $m['edit']; ?>' href='?page=registration&id=<?php echo $i['id']; ?>'><button type='button' class='btn btn-success'><span class='glyphicon glyphicon-pencil'></span></button></a>
										<a class='up' title='<?php echo $m['up']; ?>'><button type='button' onclick='moveInput("<?php echo md5(session_id()); ?>", "up", <?php echo $i['id']; ?>);' class='btn btn-primary'><span class='glyphicon glyphicon-chevron-up'></span></button></a>
										<a class='down' title='<?php echo $m['down']; ?>'><button type='button' onclick='moveInput("<?php echo md5(session_id()); ?>", "down", <?php echo $i['id']; ?>);' class='btn btn-primary'><span class='glyphicon glyphicon-chevron-down'></span></button></a>
										<a class='delete' title='<?php echo $m['delete']; ?>'><button type='button' class='btn btn-danger' onclick='sureDeleteInput("<?php echo md5(session_id()); ?>", <?php echo $i['id']; ?>, "<?php echo $m['delete']; ?>");'><span class='glyphicon glyphicon-trash'></span></button></a>
									</td>
								</tr>
							<?php
							}
							?>
							</tbody>
						</table>
					<?php
					} else {
						echo "<div class='alert alert-info' role='alert'>". $m['no_inputs_found'] ."</div>";
					}
					?>
				</div>
			</div>
			
			<div class='row'>
				<button type='button' class='btn btn-primary' data-toggle='modal' data-target='#add_modal'><span class='glyphicon glyphicon-plus-sign'></span>&nbsp; <?php echo $m['add_input']; ?></button>
			</div>
			
			
			
			<div class='modal fade bs-example-modal-sm' id='delete_modal' tabindex='-1' role='dialog' aria-labelledby='DeleteModal' aria-hidden='true'>
				<div class='modal-dialog' id='delete_modal_content'>
					<div class='modal-content'>
						<div class='modal-header'>
							<button class='close' data-dismiss='modal' type='button'>
								<span aria-hidden="true">×</span>
								<span class="sr-only">Close</span>
							</button>
							<h4 id='mySmallModalLabel' class='modal-title'><?php echo $m['warning']; ?></h4>
						</div>
						<div class='modal-body'>
							<div id='delete_response'></div>
							<?php echo $m['delete_input']; ?>
							<div id='delete_sure'><button type='submit' class='btn btn-danger'><?php echo $m['delete']; ?></button></div>
						</div>
					</div>
				</div>
			</div>
			
			<div class='modal fade bs-example-modal-sm' id='add_modal' tabindex='-1' role='dialog' aria-labelledby='AddModal' aria-hidden='true'>
				<div class='modal-dialog'>
					<div class='modal-content'>
						<div class='modal-header'>
							<button class='close' data-dismiss='modal' type='button'>
								<span aria-hidden="true">×</span>
								<span class="sr-only"><?php echo $m['close']; ?></span>
							</button>
							<h4 id='mySmallModalLabel' class='modal-title'><?php echo $m['add_input']; ?></h4>
						</div>
						<div class='modal-body'>
							<div id='input_response'></div>
							<form method='post' id='add_input'>
								<div class='row'>
									<div class='form-group'>
										<label class='col-sm-4 control-label'><a id='input_name' title='<?php echo $m['name_title']; ?>'><?php echo $m['name']; ?></a> <a id='no_spaces_allowed' title='<?php echo $m['no_spaces_allowed']; ?>'><span class='glyphicon glyphicon-exclamation-sign'></span></a></label>
										<div class='col-sm-8'>
											<input type='text' class='form-control' name='input_name'>
										</div>
									</div>
								</div>
								
								<div class='row' id='public_name'>
									<div class='form-group'>
										<label class='col-sm-4 control-label'><a id='input_public_name' title='<?php echo $m['public_name_title']; ?>'><?php echo $m['public_name']; ?></a></label>
										<div class='col-sm-8'>
											<input type='text' class='form-control' name='input_public_name'>
										</div>
									</div>
								</div>
								
								<div class='row'>
									<div class='form-group'>
										<label class='col-sm-4 control-label'><a id='input_type' title='<?php echo $m['type_title']; ?>'><?php echo $m['type']; ?></a></label>
										<div class='col-sm-8'>
											<select class='form-control' name='input_type' id='type' onchange='check();'>
												<option value='text'>text</option>
												<option value='textarea'>textarea</option>
												<option value='checkbox'>checkbox</option>
												<option value='select'>select</option>
												<option value='hidden'>hidden</option>
												<option value='number'>(HTML5) number</option>
												<option value='range'>(HTML5) range</option>
												<option value='date'>(HTML5) date</option>
												<option value='url'>(HTML5) url</option>
												<option value='color'>(HTML5) color</option>
												<option value='email'>(HTML5) email</option>
											</select>
										</div>
									</div>
								</div>
								
								
								<!-- begin extra inputs -->
								<div class='row' id='maxlength'>
									<div class='form-group'>
										<label class='col-sm-4 control-label'><a id='input_maxlength' title='<?php echo $m['maxlength_title']; ?>'><?php echo $m['input_maxlength']; ?></a></label>
										<div class='col-sm-8'>
											<input type='number' class='form-control' name='input_maxlength' min='0' value='0'>
										</div>
									</div>
								</div>
								
								<div class='row' id='rows' style='display: none;'>
									<div class='form-group'>
										<label class='col-sm-4 control-label'><a id='input_rows' title='<?php echo $m['rows_title']; ?>'><?php echo $m['input_rows']; ?></a></label>
										<div class='col-sm-8'>
											<input type='number' class='form-control' name='input_rows' min='0' value='0'>
										</div>
									</div>
								</div>
								
								<div class='row' id='min' style='display: none;'>
									<div class='form-group'>
										<label class='col-sm-4 control-label'><a id='input_min' title='<?php echo $m['min_title']; ?>'><?php echo $m['input_min']; ?></a></label>
										<div class='col-sm-8'>
											<input type='number' class='form-control' name='input_min' min='0' value='0'>
										</div>
									</div>
								</div>
								
								<div class='row' id='max' style='display: none;'>
									<div class='form-group'>
										<label class='col-sm-4 control-label'><a id='input_max' title='<?php echo $m['max_title']; ?>'><?php echo $m['input_max']; ?></a></label>
										<div class='col-sm-8'>
											<input type='number' class='form-control' name='input_max' min='0' value='0'>
										</div>
									</div>
								</div>
								
								<div class='row' id='step' style='display: none;'>
									<div class='form-group'>
										<label class='col-sm-4 control-label'><a id='input_step' title='<?php echo $m['step_title']; ?>'><?php echo $m['input_step']; ?></a></label>
										<div class='col-sm-8'>
											<input type='number' class='form-control' name='input_step' min='0' value='0'>
										</div>
									</div>
								</div>
								
								<div class='row' id='checked' style='display: none;'>
									<div class='form-group'>
										<label class='col-sm-4 control-label'><a id='input_checked' title='<?php echo $m['checked_title']; ?>'><?php echo $m['input_checked']; ?></a></label>
										<div class='col-sm-2'>
											<div class='checkbox'>
												<label><input type='checkbox' name='input_checked'> <?php echo $m['input_checked']; ?></label>
											</div>
										</div>
									</div>
								</div>
								<!-- end extra inputs -->
								
								
								<div class='row' id='placeholder'>
									<div class='form-group'>
										<label class='col-sm-4 control-label'><a id='input_placeholder' title='<?php echo $m['placeholder_title']; ?>'><?php echo $m['placeholder']; ?></a></label>
										<div class='col-sm-8'>
											<input type='text' class='form-control' name='input_placeholder'>
										</div>
									</div>
								</div>
								
								<div class='row' id='value'>
									<div class='form-group'>
										<label class='col-sm-4 control-label'><a id='input_value' title='<?php echo $m['value_title']; ?>'><?php echo $m['input_value']; ?></a></label>
										<div class='col-sm-8'>
											<input type='text' class='form-control' name='input_value'>
										</div>
									</div>
								</div>
								
								<div class='row' id='required'>
									<div class='form-group'>
										<label class='col-sm-4 control-label'><a id='input_required' title='<?php echo $m['required_title']; ?>'><?php echo $m['required']; ?></a></label>
										<div class='col-sm-2'>
											<div class='checkbox'>
												<label><input type='checkbox' name='input_required'> <?php echo $m['required']; ?></label>
											</div>
										</div>
									</div>
								</div>
								
								<div class='row' id='error'>
									<div class='form-group'>
										<label class='col-sm-4 control-label'><a id='input_error' title='<?php echo $m['input_error_title']; ?>'><?php echo $m['input_error']; ?></a></label>
										<div class='col-sm-8'>
											<textarea class='form-control' name='input_error' rows='3'></textarea>
										</div>
									</div>
								</div>
								
								<div class='row' id='options'>
									<div class='form-group'>
										<label class='col-sm-4 control-label'><a id='select_options' title='<?php echo $m['select_options_title']; ?>'><?php echo $m['select_options']; ?></a></label>
										<div class='col-sm-4' id='names'>
											<label class='control-label'>Name</label>
											<input type='text' name='name[]' class='form-control'>
										</div>
										<div class='col-sm-4' id='values'>
											<label class='control-label'>Value</label>
											<input type='text' name='value[]' class='form-control'>
										</div>
										
										<div class='col-sm-4'></div>
										<div class='col-sm-4 row-5'>
											<button type='button' name='add_option' onclick='addOption();' class='btn btn-primary'>Add option</button>
										</div>
									</div>
								</div>
								
								<div class='row' id='public'>
									<div class='form-group'>
										<label class='col-sm-4 control-label'><a id='input_public' title='<?php echo $m['input_public_title']; ?>'><?php echo $m['input_public']; ?></a></label>
										<div class='col-sm-4'>
											<div class='checkbox'>
												<label><input type='checkbox' name='input_public' checked> <?php echo $m['input_public']; ?></label>
											</div>
										</div>
									</div>
								</div>
								
								<div class='row row-5'>
									<div class='form-group'>
										<input type='hidden' name='token' value='<?php echo md5(session_id()); ?>'>
										<input type='submit' name='add' value='<?php echo $m['add']; ?>' class='btn btn-primary'>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
			
			
			<script>
			$("input[type='checkbox'][name='recaptcha']").change(function() {
				if(this.checked) {
					$('#recaptcha').css('display', 'inline');
					$('#extraspace').css('display', 'inline');
				} else {
					$('#recaptcha').css('display', 'none');
					$('#extraspace').css('display', 'none');
				}
			});


			if($('#recaptcha_enabled').prop('checked')) {
				$('#recaptcha').css('display', 'inline');
				$('#extraspace').css('display', 'inline');
			} else {
				$('#recaptcha').css('display', 'none');
				$('#extraspace').css('display', 'none');
			}
			</script>
			
			<?php
			}
			?>
			
			<script>
			// This function shows and hides the extra attributes for the inputs, depends on the input type
			function check() {
				var type = $("#type").val();
				
				$('#maxlength').css('display', 'none');
				$('#rows').css('display', 'none');
				$('#min').css('display', 'none');
				$('#max').css('display', 'none');
				$('#step').css('display', 'none');
				$('#checked').css('display', 'none');
				$('#options').css('display', 'none');
				
				$('#placeholder').css('display', 'block');
				$('#value').css('display', 'block');
				$('#required').css('display', 'block');
				$('#error').css('display', 'block');
				$('#public').css('display', 'block');
				
				$('input[name=input_value]').attr("type", "text");
				
				if(type == "text") {
					$('#maxlength').css('display', 'block');
				}
				if(type == "textarea") {
					$('#maxlength').css('display', 'block');
					$('#rows').css('display', 'block');
				}
				if(type == "checkbox") {
					$('#checked').css('display', 'block');
					$('#value').css('display', 'none');
				}
				if(type == "select") {
					$('#options').css('display', 'block');
					$('#placeholder').css('display', 'none');
					$('#value').css('display', 'none');
				}
				if(type == "hidden") {
					$('#placeholder').css('display', 'none');
					$('#required').css('display', 'none');
					$('#error').css('display', 'none');
					$('#public_name').css('display', 'none');
					$('#public').css('display', 'none');
				}
				if(type == "number") {
					$('#min').css('display', 'block');
					$('#max').css('display', 'block');
					$('#step').css('display', 'block');
				}
				if(type == "range") {
					$('#placeholder').css('display', 'none');
					$('#min').css('display', 'block');
					$('#max').css('display', 'block');
					$('#step').css('display', 'block');
				}
				if(type == "date") {
					$('#min').css('display', 'block');
					$('#max').css('display', 'block');
				}
				if(type == "url") {
					$('#maxlength').css('display', 'block');
				}
				if(type == "email") {
					$('#maxlength').css('display', 'block');
				}
				if(type == "color") {
					$('input[name=input_value]').attr("type", "color");
				}
			};
			
			check();
			</script>
			
		<?php
		// Messages settings page
		} elseif(empty($_GET['page']) && $_GET['page'] == "messages") {
		?>
			<div class='row row-1'>
				<h3><?php echo $m['messages']; ?></h3>
				<h5><?php echo $m['messages_info']; ?></h5>
			</div>
			
			<div id='message_response'></div>
			<form method='post' id='message'>
				<div class='row row-3'>
					<div class='form-group'>
						<label class='col-sm-4 control-label'><a id='message_login' title='<?php echo $m['message_login_title']; ?>'><?php echo $m['message_login']; ?></a></label>
						<div class='col-sm-8'>
							<textarea class='form-control' name='message_login' rows='5'><?php echo getSetting("message_login", "text"); ?></textarea>
						</div>
					</div>
				</div>
				
				<div class='row row-3'>
					<div class='form-group'>
						<label class='col-sm-4 control-label'><a id='message_logout' title='<?php echo $m['message_logout_title']; ?>'><?php echo $m['message_logout']; ?></a></label>
						<div class='col-sm-8'>
							<textarea class='form-control' name='message_logout' rows='5'><?php echo getSetting("message_logout", "text"); ?></textarea>
						</div>
					</div>
				</div>
				
				<div class='row row-3'>
					<div class='form-group'>
						<label class='col-sm-4 control-label'><a id='message_nopermission' title='<?php echo $m['message_nopermission_title']; ?>'><?php echo $m['message_nopermission']; ?></a></label>
						<div class='col-sm-8'>
							<textarea class='form-control' name='message_nopermission' rows='5'><?php echo getSetting("message_nopermission", "text"); ?></textarea>
						</div>
					</div>
				</div>
				
				<div class='row row-2'>
					<div class='form-group'>
						<label class='col-sm-4 control-label'><a id='message_notloggedin' title='<?php echo $m['message_notloggedin_title']; ?>'><?php echo $m['message_notloggedin']; ?></a></label>
						<div class='col-sm-8'>
							<textarea class='form-control' name='message_notloggedin' rows='5'><?php echo getSetting("message_notloggedin", "text"); ?></textarea>
						</div>
					</div>
				</div>
				
				<div class='row'>
					<div class='form-group'>
						<input type='hidden' name='token' value='<?php echo md5(session_id()); ?>'>
						<input type='submit' name='save' value='<?php echo $m['save']; ?>' class='btn btn-primary'>
					</div>
				</div>
			</form>
				
		<?php
		// Coupons settings page
		} elseif(empty($_GET['page']) && $_GET['page'] == "coupons") {
		?>
			<div class='row row-1'>
				<h3><?php echo $m['coupons']; ?></h3>
				<h5><?php echo $m['coupons_info']; ?></h5>
			</div>
			
			<div class="alert alert-info" role="alert">
				<?php echo $m['plans_not_finished']; ?>
			</div>
		<?php
		// Social login settings page
		} elseif(empty($_GET['page']) && $_GET['page'] == "social") {
		?>
			<div class='row row-1'>
				<h3><?php echo $m['social']; ?></h3>
				<h5><?php echo $m['social_info']; ?></h5>
			</div>
			
			<div id='social_response'></div>
			<form method='post' id='social_settings'>
				<div class='row row-4'>
					<div class='row row'>
						<div class='form-group'>
							<label class='col-sm-4 control-label'><a id='social_main' title='<?php echo $m['social_main_title']; ?>'><?php echo $m['social_main']; ?></a></label>
							<div class='col-sm-8'>
								<div class='checkbox'>
									<label><input type='checkbox' name='social_verification'<?php if(getSetting("social_verification", "checkbox")) { echo " checked"; } ?>> <?php echo $m['social_verification']; ?></label>
								</div>
								<div class='checkbox'>
									<label><input type='checkbox' name='social_pay'<?php if(getSetting("social_pay", "checkbox")) { echo " checked"; } ?>> <?php echo $m['social_pay']; ?></label>
								</div>
							</div>
						</div>
					</div>
				</div>
				
				
				
				<div class='row row-4'>
					<div class='row'>
						<div class='form-group'>
							<label class='col-sm-4 control-label'><a id='enable_google' title='<?php echo $m['enable_google_title']; ?>'><?php echo $m['enable_google']; ?></a></label>
							<div class='col-sm-8'>
								<div class='checkbox'>
									<label><input type='checkbox' name='enable_google'<?php if(getSetting("enable_google", "checkbox")) { echo " checked"; } ?>> <?php echo $m['enable_google']; ?></label>
								</div>
								<a href='http://www.effeiets.nl/login/doc-online.php?tutorial=create-google-api' target='_blank'>https://www.effeiets.nl/login/doc-online.php?tutorial=create-google-api</a>
							</div>
						</div>
					</div>
					
					<div class='row' id='google_options' style='dispay: none;'>
						<div class='row'>
							<div class='form-group'>
								<label class='col-sm-4 control-label'><a id='client_id' title='<?php echo $m['client_id_title']; ?>'><?php echo $m['client_id']; ?></a></label>
								<div class='col-sm-8'>
									<input type='text' class='form-control' name='client_id'<?php echo getSetting("client_id", "value"); ?>>
								</div>
							</div>
						</div>
						
						<div class='row'>
							<div class='form-group'>
								<label class='col-sm-4 control-label'><a id='client_secret' title='<?php echo $m['client_secret_title']; ?>'><?php echo $m['client_secret']; ?></a></label>
								<div class='col-sm-8'>
									<input type='text' class='form-control' name='client_secret'<?php echo getSetting("client_secret", "value"); ?>>
								</div>
							</div>
						</div>
						
						<div class='row'>
							<div class='form-group'>
								<label class='col-sm-4 control-label'><a id='api_key' title='<?php echo $m['api_key_title']; ?>'><?php echo $m['api_key']; ?></a></label>
								<div class='col-sm-8'>
									<input type='text' class='form-control' name='api_key'<?php echo getSetting("api_key", "value"); ?>>
								</div>
							</div>
						</div>
						
						<div class='row row-2'>
							<div class='form-group'>
								<label class='col-sm-4 control-label'><a id='google_redirect' title='<?php echo $m['google_redirect_title']; ?>'><?php echo $m['google_redirect']; ?></a></label>
								<div class='col-sm-8'>
									<code><?php echo getTypeUrl("google_redirect"); ?></code>
								</div>
							</div>
						</div>
					</div>
					
					<script>
					$("input[type='checkbox'][name='enable_google']").change(function() {
						if(this.checked) {
							$('#google_options').css('display', 'block');
						} else {
							$('#google_options').css('display', 'none');
						}
					});


					if($("input[type='checkbox'][name='enable_google']").prop('checked')) {
						$('#google_options').css('display', 'block');
					} else {
						$('#google_options').css('display', 'none');
					}
					</script>
				</div>
				
				
				
				<div class='row row-4'>
					<div class='row row-1'>
						<div class='form-group'>
							<label class='col-sm-4 control-label'><a id='enable_facebook' title='<?php echo $m['enable_facebook_title']; ?>'><?php echo $m['enable_facebook']; ?></a></label>
							<div class='col-sm-4'>
								<div class='checkbox'>
									<label><input type='checkbox' name='enable_facebook'<?php if(getSetting("enable_facebook", "checkbox")) { echo " checked"; } ?>> <?php echo $m['enable_facebook']; ?></label>
								</div>
								<a href='http://developers.facebook.com/apps' target='_blank'>http://developers.facebook.com/apps</a>
							</div>
						</div>
					</div>
					
					<div class='row' id='facebook_options' style='dispay: none;'>
						<div class='row'>
							<div class='form-group'>
								<label class='col-sm-4 control-label'><a id='fb_appid' title='<?php echo $m['fb_appid_title']; ?>'><?php echo $m['fb_appid']; ?></a></label>
								<div class='col-sm-8'>
									<input type='text' class='form-control' name='fb_appid'<?php echo getSetting("fb_appid", "value"); ?>>
								</div>
							</div>
						</div>
						
						<div class='row row-2'>
							<div class='form-group'>
								<label class='col-sm-4 control-label'><a id='fb_appsecret' title='<?php echo $m['fb_appsecret_title']; ?>'><?php echo $m['fb_appsecret']; ?></a></label>
								<div class='col-sm-8'>
									<input type='text' class='form-control' name='fb_appsecret'<?php echo getSetting("fb_appsecret", "value"); ?>>
								</div>
							</div>
						</div>
					</div>
					
					<script>
					$("input[type='checkbox'][name='enable_facebook']").change(function() {
						if(this.checked) {
							$('#facebook_options').css('display', 'block');
						} else {
							$('#facebook_options').css('display', 'none');
						}
					});


					if($("input[type='checkbox'][name='enable_facebook']").prop('checked')) {
						$('#facebook_options').css('display', 'block');
					} else {
						$('#facebook_options').css('display', 'none');
					}
					</script>
				</div>
				
				
				
				<div class='row row-2'>
					<div class='row'>
						<div class='form-group'>
							<label class='col-sm-4 control-label'><a id='enable_twitter' title='<?php echo $m['enable_twitter_title']; ?>'><?php echo $m['enable_twitter']; ?></a></label>
							<div class='col-sm-4'>
								<div class='checkbox'>
									<label><input type='checkbox' name='enable_twitter'<?php if(getSetting("enable_twitter", "checkbox")) { echo " checked"; } ?>> <?php echo $m['enable_twitter']; ?></label>
								</div>
								<a href='https://apps.twitter.com/app/new' target='_blank'>https://apps.twitter.com/app/new</a>
							</div>
						</div>
					</div>
					
					<div class='row' id='twitter_options' style='dispay: none;'>
						<div class='row'>
							<div class='form-group'>
								<label class='col-sm-4 control-label'><a id='consumer_key' title='<?php echo $m['consumer_key_title']; ?>'><?php echo $m['consumer_key']; ?></a></label>
								<div class='col-sm-8'>
									<input type='text' class='form-control' name='consumer_key'<?php echo getSetting("consumer_key", "value"); ?>>
								</div>
							</div>
						</div>
						
						<div class='row'>
							<div class='form-group'>
								<label class='col-sm-4 control-label'><a id='consumer_secret' title='<?php echo $m['consumer_secret_title']; ?>'><?php echo $m['consumer_secret']; ?></a></label>
								<div class='col-sm-8'>
									<input type='text' class='form-control' name='consumer_secret'<?php echo getSetting("consumer_secret", "value"); ?>>
								</div>
							</div>
						</div>
						
						<div class='row'>
							<div class='form-group'>
								<label class='col-sm-4 control-label'><a id='twitter_callback' title='<?php echo $m['twitter_callback_title']; ?>'><?php echo $m['twitter_callback']; ?></a></label>
								<div class='col-sm-8'>
									<code><?php echo getTypeUrl("twitter_callback"); ?></code>
								</div>
							</div>
						</div>
					</div>
					
					<script>
					$("input[type='checkbox'][name='enable_twitter']").change(function() {
						if(this.checked) {
							$('#twitter_options').css('display', 'block');
						} else {
							$('#twitter_options').css('display', 'none');
						}
					});


					if($("input[type='checkbox'][name='enable_twitter']").prop('checked')) {
						$('#twitter_options').css('display', 'block');
					} else {
						$('#twitter_options').css('display', 'none');
					}
					</script>
				</div>
				
				<div class='row'>
					<div class='form-group'>
						<input type='hidden' name='token' value='<?php echo md5(session_id()); ?>'>
						<input type='submit' name='save' value='<?php echo $m['save']; ?>' class='btn btn-primary'>
					</div>
				</div>
			</form>
		<?php
		// Plans settings page
		} elseif(!empty($_GET['page']) && $_GET['page'] == "plans") {
		?>
			<div class='row row-1'>
				<h3><?php echo $m['plans']; ?></h3>
				<h5><?php echo $m['plans_info']; ?></h5>
			</div>
			
			<div class="alert alert-info" role="alert">
				<?php echo $m['plans_not_finished']; ?>
			</div>
			
			<div class='row row-2'>
				<div class='col-md-12'>
					<h4><?php echo $m['payment_gateways']; ?></h4>
				</div>
				
				<div class='col-md-6'>
					<div class='panel panel-<?php if(getSetting("enable_paypal", "text") == "true") { echo "success"; } else { echo "danger"; } ?>'>
						<div class='panel-body'>
							
							
							<a href='?page=paypal'><button type='button' class='btn btn-default pull-right'><span class='glyphicon glyphicon-cog' aria-hidden='true'></span></button></a>
						</div>
					</div>
				</div>
				
				<div class='col-md-6'>
					<div class='panel panel-<?php if(getSetting("enable_stripe", "text") == "true") { echo "success"; } else { echo "danger"; } ?>'>
						<div class='panel-body'>
							
							
							<a href='?page=stripe'><button type='button' class='btn btn-default pull-right'><span class='glyphicon glyphicon-cog' aria-hidden='true'></span></button></a>
						</div>
					</div>
				</div>
			</div>
		<?php
		// Paypal settings page
		} elseif(!empty($_GET['page']) && $_GET['page'] == "paypal") {
		?>
			<div class='row'>
				<h3><?php echo $m['paypal']; ?></h3>
				<h5><?php echo $m['paypal_info']; ?></h5>
			</div>
			
			<div class='row row-2'>
				<a href='?page=plans'><span class='glyphicon glyphicon-arrow-left' aria-hidden='true'></span> <?php echo $m['back_to_plans']; ?></a>
			</div>
			
			<div id='paypal_response'></div>
			<form method='post' id='paypal'>
				<div class='row'>
					<div class='form-group'>
						<label class='col-sm-4 control-label'><a id='enable_paypal' title='<?php echo $m['paypal_title']; ?>'><?php echo $m['enable_paypal']; ?></a></label>
						<div class='col-sm-8'>
							<div class='checkbox'>
								<label><input type='checkbox' name='enable_paypal'<?php if(getSetting("enable_paypal", "checkbox")) { echo " checked"; } ?>> <?php echo $m['enable_paypal']; ?></label>
							</div>
						</div>
					</div>
				</div>
				
				<div class='row row-2'>
					<div class='form-group'>
						<label class='col-sm-4 control-label'><a id='paypal_email' title='<?php echo $m['paypal_email_title']; ?>'><?php echo $m['paypal_email']; ?></a></label>
						<div class='col-sm-8'>
							<input type='email' class='form-control' name='paypal_email'<?php echo getSetting("paypal_email", "value"); ?>>
						</div>
					</div>
				</div>
				
				<div class='row row-2'>
					<div class='form-group'>
						<label class='col-sm-4 control-label'><a id='paypal_cost' title='<?php echo $m['paypal_cost_title']; ?>'><?php echo $m['paypal_cost']; ?></a></label>
						<div class='col-sm-2'>
							<select name='paypal_currency' class='form-control'>
								<option value='EUR'>&euro; &nbsp; EUR</option>
								<option value='USD'>$ &nbsp; USD</option>
								<option value='GBP'>&pound; &nbsp; GBP</option>
							</select>
							
							<?php if(getSetting("paypal_currency", "text") != "") { ?>
							<script>
							// Select the current currency
							$('select[name=paypal_currency]').val("<?php echo getSetting("paypal_currency", "text"); ?>");
							</script>
							<?php } ?>
						</div>
						<div class='col-sm-6'>
							<input type='number' class='form-control' min='0' step='0.01' name='paypal_cost'<?php if(getSetting("paypal_cost", "value") != false) { echo getSetting("paypal_cost", "value"); } else { echo " value='0.00'"; }; ?>>
						</div>
					</div>
				</div>
				
				<div class='row'>
					<div class='form-group'>
						<input type='hidden' name='token' value='<?php echo md5(session_id()); ?>'>
						<input type='submit' name='save' value='<?php echo $m['save']; ?>' class='btn btn-primary'>
					</div>
				</div>
			</form>
		<?php
		// Stripe settings page
		} elseif(!empty($_GET['page']) && $_GET['page'] == "stripe") {
		?>
			<div class='row'>
				<h3><?php echo $m['stripe']; ?></h3>
				<h5><?php echo $m['stripe_settings_info']; ?></h5>
			</div>
			
			<div class='row row-2'>
				<a href='?page=plans'><span class='glyphicon glyphicon-arrow-left' aria-hidden='true'></span> <?php echo $m['back_to_plans']; ?></a>
			</div>
			
			<div id='stripe_response'></div>
			<form method='post' id='stripe_settings'>
				<div class='row'>
					<div class='form-group'>
						<label class='col-sm-4 control-label'><a id='enable_stripe' title='<?php echo $m['enable_stripe_title']; ?>'><?php echo $m['enable_stripe']; ?></a></label>
						<div class='col-sm-2'>
							<div class='checkbox'>
								<label><input type='checkbox' name='enable_stripe'<?php if(getSetting("enable_stripe", "checkbox")) { echo " checked"; } ?>> <?php echo $m['enable_stripe']; ?></label>
							</div>
						</div>
					</div>
				</div>
				
				<div class='row row-2'>
					<div class='form-group'>
						<label class='col-sm-4 control-label'><a id='stripe_key' title='<?php echo $m['stripe_key_title']; ?>'><?php echo $m['stripe_key']; ?></a></label>
						<div class='col-sm-8'>
							<input type='text' class='form-control' name='stripe_key'<?php echo getSetting("stripe_key", "value"); ?>>
						</div>
					</div>
				</div>
				
				<div class='row row-2'>
					<div class='form-group'>
						<label class='col-sm-4 control-label'><a id='stripe_cost' title='<?php echo $m['stripe_cost_title']; ?>'><?php echo $m['stripe_cost']; ?></a></label>
						<div class='col-sm-2'>
							<select name='stripe_currency' class='form-control'>
								<option value='EUR'>&euro; &nbsp; EUR</option>
								<option value='USD'>$ &nbsp; USD</option>
								<option value='GBP'>&pound; &nbsp; GBP</option>
							</select>
							
							<?php if(getSetting("stripe_currency", "text") != "") { ?>
							<script>
							// Select the current currency
							$('select[name=stripe_currency]').val("<?php echo getSetting("stripe_currency", "text"); ?>");
							</script>
							<?php } ?>
						</div>
						<div class='col-sm-6'>
							<input type='number' class='form-control' min='0' step='0.01' name='stripe_cost'<?php if(getSetting("stripe_cost", "value") != false) { echo getSetting("stripe_cost", "value"); } else { echo " value='0.00'"; }; ?>>
						</div>
					</div>
				</div>
				
				<div class='row'>
					<div class='form-group'>
						<input type='hidden' name='token' value='<?php echo md5(session_id()); ?>'>
						<input type='submit' name='save' value='<?php echo $m['save']; ?>' class='btn btn-primary'>
					</div>
				</div>
			</form>
		<?php
		// Permission settings page
		} elseif(!empty($_GET['page']) && $_GET['page'] == "permissions") {
		?>
			<div class='row'>
				<h3><?php echo $m['permissions']; ?></h3>
				<h5><?php echo $m['permissions_info']; ?></h5>
			</div>
			
			<?php
			if(!empty($_GET['id'])) {
				$id = mysqli_real_escape_string($con,$_GET['id']);
				$getperm = mysqli_query($con,"SELECT * FROM login_permissions WHERE id='$id'");
				if(mysqli_num_rows($getperm) == 1) {
					$gp = mysqli_fetch_array($getperm);
					?>
					<div id='edit_response'></div>
					<form method='post' id='save_perm'>
						<div class='row'>
							<h4><?php echo $m['editing'] ." ". $gp['name']; ?></h4>
						</div>
						
						<div class='row row-1'>
							<div class='form-group'>
								<label class='col-sm-4 control-label'><a id='perm_name' title='<?php echo $m['perm_name_title']; ?>'><?php echo $m['perm_name']; ?></a></label>
								<div class='col-sm-8'>
									<?php
									if($gp['name'] == "Admin") {
									?>
									<input type='text' class='form-control' name='perm_name' value='<?php echo $gp['name']; ?>' disabled>
									<?php
									} else {
									?>
									<input type='text' class='form-control' name='perm_name' value='<?php echo $gp['name']; ?>'>
									<?php
									}
									?>
								</div>
							</div>
						</div>
						
						<div class='row'>
							<h4><?php echo $m['links']; ?></h4>
						</div>
						
						<div class='row row-1'>
							<div class='form-group'>
								<label class='col-sm-4 control-label'><a id='redirect_login' title='<?php echo $m['redirect_login_title'] ."". $m['leave_empty']; ?>'><?php echo $m['redirect_login']; ?></a> <a id='on_login_warning' title='<?php echo $m['on_login_warning']; ?>'><span class='glyphicon glyphicon-exclamation-sign'></span></a></label>
								<div class='col-sm-8'>
									<input type='text' class='form-control' name='on_login' value='<?php echo $gp['on_login']; ?>' placeholder='<?php echo $m['url_example']; ?>'>
								</div>
							</div>
						</div>
						
						<div class='row row-1'>
							<div class='form-group'>
								<label class='col-sm-4 control-label'><a id='redirect_logout' title='<?php echo $m['redirect_logout_title'] ."". $m['leave_empty']; ?>'><?php echo $m['redirect_logout']; ?></a></label>
								<div class='col-sm-8'>
									<input type='text' class='form-control' name='on_logout' value='<?php echo $gp['on_logout']; ?>' placeholder='<?php echo $m['url_example']; ?>'>
								</div>
							</div>
						</div>
						
						<div class='row row-2'>
							<div class='form-group'>
								<label class='col-sm-4 control-label'><a id='redirect_nopermission' title='<?php echo $m['redirect_nopermission_title'] ."". $m['leave_empty']; ?>'><?php echo $m['redirect_nopermission']; ?></a></label>
								<div class='col-sm-8'>
									<input type='text' class='form-control' name='no_permission' value='<?php echo $gp['no_permission']; ?>' placeholder='<?php echo $m['url_example']; ?>'>
								</div>
							</div>
						</div>
						
						<div class='row'>
							<div class='form-group'>
								<input type='hidden' name='token' value='<?php echo md5(session_id()); ?>'>
								<input type='hidden' name='id' value='<?php echo $id; ?>'>
								<input type='submit' name='save' value='<?php echo $m['save']; ?>' class='btn btn-primary'>
							</div>
						</div>
					</form>
					
					
					
					<div class='row row-8'>
						<h3><?php echo $m['users_perm']; ?></h3>
					</div>
					
					<?php
					$permid = $gp['id'];
					$users = mysqli_query($con,"SELECT * FROM login_users WHERE permission='$permid'");
					
					if(mysqli_num_rows($users) > 0) {
					?>
					<div id='usertable'>
						<div id='usertabledata'>
							<form method='post' id='move_user'>
								<div class='row'>
									<table class='table table-striped table-hover' id='putable'>
										<thead>
											<tr>
												<td><strong><input type='checkbox' id='selectall'></strong></td>
												<td><strong><?php echo $m['username']; ?></strong></td>
												<td><strong><?php echo $m['permission']; ?></strong></td>
												<td><strong><?php echo $m['email']; ?></strong></td>
												<td><strong><?php echo $m['registered_on']; ?></strong></td>
											</tr>
										</thead>
										<tbody>
											<?php
											while($u = mysqli_fetch_array($users)) {
											?>
											<tr>
												<td><input type='checkbox' class='check' name='<?php echo $u['id']; ?>'></td>
												<td><?php echo "<a href='controlpanel.php?page=users&uid=". $u['id'] ."'>". $u['username'] ."</a>"; ?></td>
												<td><?php echo getPermName($u['id']); ?></td>
												<td><?php echo $u['email']; ?></td>
												<td><?php echo date("j F Y", $u['registered_on']); ?></td>
											</tr>
											<?php
											}
											?>
										</tbody>
									</table>
								</div>
								
								<div class='row'>
									<div class='col-md-8 special-col'>
										<div class='row'>
											<h3><?php echo $m['move_users']; ?></h3>
										</div>
										
										<div id='move_response'></div>
										<div class='row'>
											<div class='form-group'>
												<select class='form-control' name='perm'>
												<?php
												$permissions = mysqli_query($con,"SELECT * FROM login_permissions ORDER BY level DESC");
											
												while($p = mysqli_fetch_array($permissions)) {
													if($p['name'] != $gp['name']) {
														?>
														<option value='<?php echo $p['id']; ?>'><?php echo $p['name']; ?></option>
														<?php
													}
												}
												?>
												</select>
											</div>
										</div>
										
										<div class='row'>
											<div class='form-group'>
												<input type='hidden' name='token' value='<?php echo md5(session_id()); ?>'>
												<input type='hidden' name='pid' id='pid' value='<?php echo $gp['id']; ?>'>
												<input type='submit' name='move' value='<?php echo $m['move']; ?>' class='btn btn-primary'>
											</div>
										</div>
									</div>
								</div>
							</form>
						</div>
					</div>
					
					<script>
					$(document).ready(function() {
						var putable = $('#putable').dataTable( {
							"paging":   true,
							"ordering": false,
							"info":     false,
							"language": {
								"search": "<?php echo $m['search']; ?>",
								"lengthMenu": "<?php echo $m['show']; ?> _MENU_ <?php echo $m['records']; ?>",
								"zeroRecords": "<?php echo $m['nothing_found']; ?>",
								"paginate": {
									"next": "<?php echo $m['next']; ?>",
									"previous": "<?php echo $m['previous']; ?>"
								}
							}
						});
						
						
						
						$('#selectall').click(function(event) {
							if(this.checked) {
								$('.check').each(function() {
									this.checked = true;             
								});
							} else {
								$('.check').each(function() {
									this.checked = false;                    
								});        
							}
						});
					});
					</script>
					<?php
					} else {
						echo $m['no_users_found'];
					}
					?>
					
					
					
					<?php
				} else {
					header('Location: ?page=permissions');
				}
			} else {
				?>
				
				<div id='perm_response'></div>
				<div class='row row-2'>
					<div id='perm_table'>
						<table class='table' id='ptable'>
							<thead>
								<tr>
									<td><strong><a id='level_warning' title='<?php echo $m['level_warning']; ?>'><?php echo $m['level']; ?></a></strong></td>
									<td><strong><?php echo $m['name']; ?></strong></td>
									<td><strong><?php echo $m['users']; ?></strong></td>
									<td><strong><?php echo $m['actions']; ?></strong></td>
								</tr>
							</thead>
							<tbody>
								<?php
								$permissions = mysqli_query($con,"SELECT * FROM login_permissions ORDER BY level DESC");
								
								while($p = mysqli_fetch_array($permissions)) {
									$id = $p['id'];
									$countusers = mysqli_query($con,"SELECT id FROM login_users WHERE permission='$id'");
									$users = mysqli_num_rows($countusers);
									
									if($p['name'] == "Admin") {
									?>
										<tr class='danger'>
											<td><?php echo $p['level']; ?></td>
											<td><?php echo $p['name']; ?> <a id='admin_info' title='<?php echo $m['admin_info']; ?>'><span class='glyphicon glyphicon-exclamation-sign'></span></a></td>
											<td><?php echo $users; ?></td>
											<td>
												<a class='edit' title='<?php echo $m['edit']; ?>' href='?page=permissions&id=<?php echo $p['id']; ?>'><button type='button' class='btn btn-success'><span class='glyphicon glyphicon-pencil'></span></button></a>
												<a class='up' title='<?php echo $m['cant_move_admin']; ?>'><button type='button' class='btn btn-primary disabled'><span class='glyphicon glyphicon-chevron-up'></span></button></a>
												<a class='down' title='<?php echo $m['cant_move_admin']; ?>'><button type='button' class='btn btn-primary disabled'><span class='glyphicon glyphicon-chevron-down'></span></button></a>
												<a class='delete' title='<?php echo $m['cant_del_admin']; ?>'><button type='button' class='btn btn-danger disabled'><span class='glyphicon glyphicon-trash'></span></button></a>
											</td>
										</tr>
									<?php
									} elseif($p['id'] == getSetting("default_permission", "text")) {
									?>
										<tr class='info'>
											<td><?php echo $p['level']; ?></td>
											<td><?php echo $p['name']; ?></td>
											<td><?php echo $users; ?></td>
											<td>
												<a class='edit' title='<?php echo $m['edit']; ?>' href='?page=permissions&id=<?php echo $p['id']; ?>'><button type='button' class='btn btn-success'><span class='glyphicon glyphicon-pencil'></span></button></a>
												<a class='up' title='<?php echo $m['up']; ?>'><button type='button' onclick='actionForm("<?php echo md5(session_id()); ?>", "up", <?php echo $p['id']; ?>);' class='btn btn-primary'><span class='glyphicon glyphicon-chevron-up'></span></button></a>
												<a class='down' title='<?php echo $m['down']; ?>'><button type='button' onclick='actionForm("<?php echo md5(session_id()); ?>", "down", <?php echo $p['id']; ?>);' class='btn btn-primary'><span class='glyphicon glyphicon-chevron-down'></span></button></a>
												<a class='delete' title='<?php echo $m['cant_del_default']; ?>'><button type='button' class='btn btn-danger disabled'><span class='glyphicon glyphicon-trash'></span></button></a>
											</td>
										</tr>
									<?php
									} else {
									?>
										<tr>
											<td><?php echo $p['level']; ?></td>
											<td><?php echo $p['name']; ?></td>
											<td><?php echo $users; ?></td>
											<td>
												<a class='edit' title='<?php echo $m['edit']; ?>' href='?page=permissions&id=<?php echo $p['id']; ?>'><button type='button' class='btn btn-success'><span class='glyphicon glyphicon-pencil'></span></button></a>
												<a class='up' title='<?php echo $m['up']; ?>'><button type='button' onclick='actionForm("<?php echo md5(session_id()); ?>", "up", <?php echo $p['id']; ?>);' class='btn btn-primary'><span class='glyphicon glyphicon-chevron-up'></span></button></a>
												<a class='down' title='<?php echo $m['down']; ?>'><button type='button' onclick='actionForm("<?php echo md5(session_id()); ?>", "down", <?php echo $p['id']; ?>);' class='btn btn-primary'><span class='glyphicon glyphicon-chevron-down'></span></button></a>
												<a class='delete' title='<?php echo $m['delete']; ?>'><button type='button' class='btn btn-danger' onclick='sureDeletePerm("<?php echo md5(session_id()); ?>", <?php echo $p['id']; ?>, "<?php echo $m['delete']; ?>");'><span class='glyphicon glyphicon-trash'></span></button></a>
											</td>
										</tr>
									<?php
									}
								}
								?>
							</tbody>
						</table>
					</div>
				</div>
				
				<div class='modal fade bs-example-modal-sm' id='delete_modal' tabindex='-1' role='dialog' aria-labelledby='DeleteModal' aria-hidden='true'>
					<div class='modal-dialog' id='delete_modal_content'>
						<div class='modal-content'>
							<div class='modal-header'>
								<button class='close' data-dismiss='modal' type='button'>
									<span aria-hidden="true">×</span>
									<span class="sr-only">Close</span>
								</button>
								<h4 id='mySmallModalLabel' class='modal-title'><?php echo $m['warning']; ?></h4>
							</div>
							<div class='modal-body'>
								<div id='delete_response'></div>
								<?php echo $m['delete_permission']; ?>
								<div id='delete_sure'><button type='submit' class='btn btn-danger'><?php echo $m['delete']; ?></button></div>
							</div>
						</div>
					</div>
				</div>
				
				
				
				<div class='modal fade bs-example-modal-sm' id='create_modal' tabindex='-1' role='dialog' aria-labelledby='CreateModal' aria-hidden='true'>
					<div class='modal-dialog'>
						<div class='modal-content'>
							<div class='modal-header'>
								<button class='close' data-dismiss='modal' type='button'>
									<span aria-hidden="true">×</span>
									<span class="sr-only"><?php echo $m['close']; ?></span>
								</button>
								<h4 id='mySmallModalLabel' class='modal-title'><?php echo $m['create_permission']; ?></h4>
							</div>
							<div class='modal-body'>
								<div id='create_response'></div>
								<form method='post' id='create_perm'>
									<div class='row'>
										<h4><?php echo $m['main_settings']; ?></h4>
									</div>
									
									<div class='row row-1'>
										<div class='form-group'>
											<label class='col-sm-4 control-label'><a id='perm_name' title='<?php echo $m['perm_name_title']; ?>'><?php echo $m['perm_name']; ?></a></label>
											<div class='col-sm-8'>
												<input type='text' class='form-control' name='perm_name'>
											</div>
										</div>
									</div>
									
									<div class='row row-2'>
										<div class='form-group'>
											<label class='col-sm-4 control-label'><a id='perm_level' title='<?php echo $m['perm_level_title']; ?>'><?php echo $m['perm_level']; ?></a></label>
											<div class='col-sm-8' id='perm_level_div'>
												<select class='form-control' name='perm_level' id='perm_level_select'>
												<?php
												$permissions = mysqli_query($con,"SELECT * FROM login_permissions ORDER BY level DESC");
											
												while($p = mysqli_fetch_array($permissions)) {
													if($p['name'] != "Admin") {
														?>
														<option value='<?php echo $p['id']; ?>'><?php echo $p['name']; ?></option>
														<?php
													}
												}
												?>
												</select>
											</div>
										</div>
									</div>
									
									
									
									<div class='row'>
										<h4><?php echo $m['links']; ?></h4>
									</div>
									
									<div class='row row-1'>
										<div class='form-group'>
											<label class='col-sm-4 control-label'><a id='redirect_login' title='<?php echo $m['redirect_login_title'] ."". $m['leave_empty']; ?>'><?php echo $m['redirect_login']; ?></a> <a id='on_login_warning' title='<?php echo $m['on_login_warning']; ?>'><span class='glyphicon glyphicon-exclamation-sign'></span></a></label>
											<div class='col-sm-8'>
												<input type='url' class='form-control' name='on_login' placeholder='<?php echo $m['url_example']; ?>'>
											</div>
										</div>
									</div>
									
									<div class='row row-1'>
										<div class='form-group'>
											<label class='col-sm-4 control-label'><a id='redirect_logout' title='<?php echo $m['redirect_logout_title'] ."". $m['leave_empty']; ?>'><?php echo $m['redirect_logout']; ?></a></label>
											<div class='col-sm-8'>
												<input type='url' class='form-control' name='on_logout' placeholder='<?php echo $m['url_example']; ?>'>
											</div>
										</div>
									</div>
									
									<div class='row row-2'>
										<div class='form-group'>
											<label class='col-sm-4 control-label'><a id='redirect_nopermission' title='<?php echo $m['redirect_nopermission_title'] ."". $m['leave_empty']; ?>'><?php echo $m['redirect_nopermission']; ?></a></label>
											<div class='col-sm-8'>
												<input type='url' class='form-control' name='no_permission' placeholder='<?php echo $m['url_example']; ?>'>
											</div>
										</div>
									</div>
									
									<div class='row'>
										<div class='form-group'>
											<input type='hidden' name='token' value='<?php echo md5(session_id()); ?>'>
											<input type='submit' name='create' value='<?php echo $m['create']; ?>' class='btn btn-primary'>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				
				
				
				<div class='row'>
					<button type='button' class='btn btn-primary' data-toggle='modal' data-target='#create_modal'><span class='glyphicon glyphicon-plus-sign'></span>&nbsp; <?php echo $m['create_permission']; ?></button>
				</div>
			<?php
			}
			?>
			
		<?php
		// Main settings page
		} else {
		?>
			
			<div class='row row-1'>
				<h3><?php echo $m['main_settings']; ?></h3>
				<h5><?php echo $m['main_settings_info']; ?></h5>
			</div>
			
			<div id='main_response'></div>
			<div id='main_form'>
				<div class='row'>
					<form method='post' id='main_settings'>
						<div class='row row-2'>
							<div class='form-group'>
								<label class='col-sm-4 control-label'><a id='disable_title' title='<?php echo $m['disable_title']; ?>'><?php echo $m['disable']; ?></a></label>
								<div class='col-sm-8'>
									<div class='checkbox'>
										<label><input type='checkbox' name='disable_register' <?php if(getSetting("disable_register", "checkbox")) { echo "checked"; } ?>> <?php echo $m['disable_register']; ?></label>
									</div>
									<div class='checkbox'>
										<label><input type='checkbox' name='disable_login' <?php if(getSetting("disable_login", "checkbox")) { echo "checked"; } ?>> <?php echo $m['disable_login']; ?></label>
									</div>
									<!--<div class='checkbox'>
										<label><input type='checkbox' name='disable_profile' <?php if(getSetting("disable_profile", "checkbox")) { echo "checked"; } ?>> <?php echo $m['disable_profile']; ?></label>
									</div>-->
									<textarea name='page_disabled_message' rows='5' class='form-control' placeholder='<?php echo $m['page_disabled_message']; ?>'><?php echo getSetting("page_disabled_message", "text"); ?></textarea>
								</div>
							</div>
						</div>
						
						<div class='row row-2'>
							<div class='form-group'>
								<label class='col-sm-4 control-label'><a id='allow_title' title='<?php echo $m['allow_title']; ?>'><?php echo $m['allow']; ?></a></label>
								<div class='col-sm-8'>
									<!--<div class='checkbox'>
										<label><input type='checkbox' name='public_profiles' <?php if(getSetting("public_profiles", "checkbox")) { echo "checked"; } ?>> <?php echo $m['public_profiles']; ?></label>
									</div>-->
									<div class='checkbox'>
										<label><input type='checkbox' name='username_change' <?php if(getSetting("username_change", "checkbox")) { echo "checked"; } ?>> <?php echo $m['username_change']; ?></label>
									</div>
									<div class='checkbox'>
										<label><input type='checkbox' name='email_change' <?php if(getSetting("email_change", "checkbox")) { echo "checked"; } ?>> <?php echo $m['email_change']; ?></label>
									</div>
									<div class='checkbox'>
										<label><input type='checkbox' name='password_change' <?php if(getSetting("password_change", "checkbox")) { echo "checked"; } ?>> <?php echo $m['password_change']; ?></label>
									</div>
									<!--<div class='checkbox'>
										<label><input type='checkbox' name='send_messages' <?php if(getSetting("send_messages", "checkbox")) { echo "checked"; } ?>> <?php echo $m['send_messages']; ?></label>
									</div>-->
								</div>
							</div>
						</div>
						
						<div class='row'>
							<div class='form-group'>
								<label class='col-sm-4 control-label'>
									<a id='timezone_title' title='<?php echo $m['timezone_title']; ?>'><?php echo $m['timezone']; ?></a> 
								</label>
								<div class='col-sm-8'>
									<select name='timezone' class='form-control'>
										<?php
										$timezones = timezone_identifiers_list();
										foreach($timezones as $t) {
										?>
											<option value='<?php echo $t; ?>'><?php echo $t; ?></option>
										<?php
										}
										?>
									</select>
									
									<script>
									// Select the current login type
									$('select[name=timezone]').val("<?php if(getSetting("timezone", "text") != "") { echo getSetting("timezone", "text"); } else { echo date_default_timezone_get(); } ?>");
									</script>
								</div>
							</div>
						</div>
						
						<div class='row'>
							<div class='form-group'>
								<label class='col-sm-4 control-label'><a id='default_permission_title' title='<?php echo $m['default_permission_title']; ?>'><?php echo $m['default_permission']; ?></a></label>
								<div class='col-sm-8'>
									<select name='default_permission' class='form-control'>
										<?php
										$permissions = mysqli_query($con,"SELECT * FROM login_permissions ORDER BY level DESC");
										
										while($p = mysqli_fetch_array($permissions)) {
											if($p['id'] != getSetting("default_permission", "text")) {
											?>
											<option value='<?php echo $p['id']; ?>'><?php echo $p['name']; ?></option>
											<?php
											} else {
											?>
											<option value='<?php echo $p['id']; ?>'><?php echo $p['name']; ?></option>
											
											<script>
											// Select the default permission
											$('select[name=default_permission]').val(<?php echo $p['id']; ?>);
											</script>
											<?php
											}
										}
										?>
									</select>
								</div>
							</div>
						</div>
						
						<div class='row'>
							<div class='form-group'>
								<label class='col-sm-4 control-label'><a id='login_with_title' title='<?php echo $m['login_with_title']; ?>'><?php echo $m['login_with']; ?></a></label>
								<div class='col-sm-8'>
									<select name='login_with' class='form-control'>
										<option value='username'><?php echo $m['username']; ?></option>
										<option value='email'><?php echo $m['email']; ?></option>
									</select>
									
									<?php if(getSetting("login_with", "text") != "") { ?>
									<script>
									// Select the current login type
									$('select[name=login_with]').val("<?php echo getSetting("login_with", "text"); ?>");
									</script>
									<?php } ?>
								</div>
							</div>
						</div>
						
						<div class='row'>
							<div class='form-group'>
								<label class='col-sm-4 control-label'><a id='admin_email_title' title='<?php echo $m['admin_email_title']; ?>'><?php echo $m['admin_email']; ?></a></label>
								<div class='col-sm-8'>
									<input type='email' class='form-control' name='admin_email'<?php echo getSetting("admin_email", "value"); ?> placeholder='<?php echo $m['email_example']; ?>'>
								</div>
							</div>
						</div>
						
						<div class='row'>
							<div class='form-group'>
								<label class='col-sm-4 control-label'><a id='email_name_title' title='<?php echo $m['email_name_title']; ?>'><?php echo $m['email_name']; ?></a></label>
								<div class='col-sm-8'>
									<input type='text' class='form-control' name='email_name'<?php echo getSetting("email_name", "value"); ?>>
								</div>
							</div>
						</div>
						
						<!--<div class='row row-2'>
							<div class='form-group'>
								<label class='col-sm-4 control-label'><a id='online_time_title' title='<?php echo $m['online_time_title']; ?>'><?php echo $m['online_time']; ?></a></label>
								<div class='col-sm-8'>
									<input type='number' min='0' step='1' class='form-control' name='online_time'<?php echo getSetting("online_time", "value"); ?>>
								</div>
							</div>
						</div>-->
						
						<div class='row'>
							<div class='form-group'>
								<input type='hidden' name='token' value='<?php echo md5(session_id()); ?>'>
								<input type='submit' name='save' value='<?php echo $m['save']; ?>' class='btn btn-primary'>
							</div>
						</div>
					</form>
				</div>
			</div>
			
		<?php
		}
		?>
		</div>
	</div>


	<!-- Extra script for the tooltips -->
	<script type='text/javascript' src='<?php echo $script_path; ?>assets/js/settings.js'></script>

<?php
include('footer.php');
?>