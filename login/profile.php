<?php
include('includes/api.php');
logged_in();

include('head.php');


// Check if id or u is set, and if public profiles are enabled
if((!empty($_GET['id']) || !empty($_GET['u'])) && getSetting("public_profiles", "text") == "true") {

// Determine how to search the user
if(!empty($_GET['id'])) {
	$id = mysqli_real_escape_string($con,$_GET['id']);
	$user = mysqli_query($con,"SELECT * FROM login_users WHERE id='$id'");
} else {
	$u = htmlentities(mysqli_real_escape_string($con,$_GET['u']), ENT_QUOTES);
	$user = mysqli_query($con,"SELECT * FROM login_users WHERE username='$u'");
}

// Check if the user exists
if(mysqli_num_rows($user) == 0) {
?>
<div class='container container-profile'>
	<div class='row'>
		<h2><?php echo $m['profile_title']; ?></h2>
		<h5 class='red'><?php echo $m['user_not_found']; ?></h5>
	</div>
</div>
<?php
} else {
$u = mysqli_fetch_array($user);
$uid = $u['id'];
?>
<div class='container container-profile'>
	<div class='row'>
		<h2><?php echo $m['profile_title']; ?> of <?php echo $u['username']; ?></h2>
	</div>
	
	<div class='row'>
		<div class='col-sm-3'>
			<div class='row'>
				<?php
				// Get avatar of current user
				if(empty($u['avatar'])) {
				?>
				<img src='<?php echo $script_path; ?>assets/images/no_image.png' class='img-thumbnail' style='width: 250px; height: 250px;' id='avatar'>
				<?php
				} else {
				?>
				<img src='<?php echo $script_path ."uploads/". $u['avatar']; ?>' class='img-thumbnail' style='width: 250px; height: 250px;' id='avatar'>
				<?php
				}
				?>
			</div>
			
			<?php
			// Check if users are allowed to send messages
			if(getSetting("send_messages", "text") == "true") {
			?>
			<div class='row'>
				<button type='button' class='btn btn-primary' onclick='answerMessage("send_message", "<?php echo $uid; ?>");'><span class='glyphicon glyphicon-share-alt' aria-hidden='true'></span> <?php echo $m['send_message']; ?></button>
			</div>
			<?php
			}
			?>
		</div>
		
		<div class='col-sm-9'>
			<table class='table borderless'>
				<thead>
					<tr>
						<td><?php echo $m['username']; ?></td>
						<td><?php echo $u['username']; ?> 
						<?php
						if(is_online($uid)) {
						?>
						<span class='label label-success'>online</span>
						<?php
						} else {
						?>
						<span class='label label-danger'>offline</span>
						<?php
						}
						?></td>
					</tr>
					
					<tr>
						<td><?php echo $m['email']; ?></td>
						<td><?php echo $u['email']; ?></td>
					</tr>
					
					<tr>
						<td><?php echo $m['registered_on']; ?></td>
						<td><?php echo date("j F Y", $u['registered_on']); ?></td>
					</tr>
					
					<tr>
						<td><?php echo $m['last_login']; ?></td>
						<td><?php if(!empty($u['last_login'])) { echo date("j F Y", $u['last_login']) ." ". $m['at'] ." ". date("G:i", $u['last_login']); } else { echo "-"; } ?></td>
					</tr>
					
					<tr>
						<td><?php echo $m['permission']; ?></td>
						<td><?php echo getPermName($uid); ?></td>
					</tr>
					
					<?php
					$extra_inputs = mysqli_query($con,"SELECT * FROM login_inputs WHERE public='true' AND type<>'hidden' ORDER BY place DESC"); // Get extra inputs
					if(mysqli_num_rows($extra_inputs) > 0) { // Check if there are any inputs
						while($ei = mysqli_fetch_array($extra_inputs)) {
						$name = $ei['name'];
						
						if(!empty($ei['public_name'])) {
							$public_name = $ei['public_name'];
						} else {
							$public_name = $ei['name'];
						}
						
						if($ei['type'] == "checkbox") { // Check if the input type is checkbox
						
						if($u[$name] == "true") { // Change true in Yes, and false in No
							$value = $m['yes'];
						} else {
							$value = $m['no'];
						}
						?>
						<tr>
							<td><?php echo $public_name; ?></td>
							<td><?php echo $value; ?></td>
						</tr>
						<?php
						} else {
						?>
						<tr>
							<td><?php echo $public_name; ?></td>
							<td><?php echo nl2br($u[$name]); ?></td>
						</tr>
						<?php
						}
						}
					}
					?>
				</thead>
			</table>
		</div>
	</div>
</div>
<?php
}
} else {
?>

<div class='container container-profile'>
	<div class='row'>
		<h2><?php echo $m['profile_title']; ?></h2>
	</div>
	
	<?php
	// Check for messages
	if(!empty($_GET['m'])) {
		if($_GET['m'] == "1") {
			if(getSetting("message_nopermission", "text") != "") {
				echo "<div class='alert alert-danger' role='danger'><a href='#' class='close' data-dismiss='alert'>&times;</a>". nl2br(getSetting("message_nopermission", "text")) ."</div>";
			} else {
				echo "<div class='alert alert-danger' role='danger'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['no_permission'] ."</div>";
			}
		}
	}
	
	// Check if profile page is disabled
	if(getSetting("disable_profile", "text") == "true") {
		if(getSetting("page_disabled_message", "text") == "") {
			echo "<div class='alert alert-info' role='alert'>". $m['page_disabled_default'] ."</div>";
		} else {
			echo "<div class='alert alert-info' role='alert'>". nl2br(getSetting("page_disabled_message", "text")) ."</div>";
		}
	} else {
	
	$uid = $_SESSION['uid'];
	?>
	
	<div role='tabpanel'>
		<div class='row'>
			<ul class='nav nav-tabs' role='tablist' id='tabs'>
				<li role='presentation' class='active'><a href='#info' aria-controls='info' role='tab' data-toggle='tab' class='realtab'><?php echo $m['info']; ?></a></li>
				<li role='presentation'><a href='#edit' aria-controls='edit' role='tab' data-toggle='tab'><?php echo $m['edit']; ?></a></li>
				<?php
				// Show messages tab if it is allowed
				if(getSetting("send_messages", "text") == "true") {
				$messages = mysqli_query($con,"SELECT * FROM login_messages WHERE sendto='$uid' AND opened='0'");
				?>
				<li role='presentation'><a href='#messages' aria-controls='messages' role='tab' data-toggle='tab'><?php echo $m['messages'] ." <span class='badge'>". mysqli_num_rows($messages) ."</span>"; ?></a></li>
				<?php
				}
				// Show password change tab if it is allowed and the password is not empty
				if(getSetting("password_change", "text") == "true" && userValue($uid, "password") != "") {
				?>
				<li role='presentation'><a href='#change_password' aria-controls='change_password' role='tab' data-toggle='tab'><?php echo $m['change_password']; ?></a></li>
				<?php
				}
				// Show set password tab if the password from the user is empty
				if(userValue($uid, "password") == "") {
				?>
				<li role='presentation'><a href='#set_password' aria-controls='set_password' role='tab' data-toggle='tab'><?php echo $m['set_password']; ?></a></li>
				<?php
				}
				// Show admin panel tab if the user is admin
				if(is_admin()) {
				?>
				<li><a href='<?php echo $script_path; ?>controlpanel.php'><?php echo $m['admin_panel']; ?></a></li>
				<?php
				}
				?>
				<li><a href='logout.php'><?php echo $m['logout']; ?></a></li>
			</ul>
		</div>
		
		
		
		<div class='tab-content'>
			<div role='tabpanel' class='tab-pane active' id='info'>
				<div class='row'>
					<div id='avatar_response'></div>
					<h3><?php echo $m['info']; ?></h3>
				</div>
				
				<div class='row'>
					<!--<div class='col-md-3'>
						<div class='row row-2'>
							<?php
							// Get avatar of  currect user
							if(userValue($_SESSION['uid'], "avatar") == "") {
							?>
							<img src='<?php echo $script_path; ?>assets/images/no_image.png' class='img-thumbnail' style='width: 250px; height: 250px;' id='avatar'>
							<?php
							} else {
							?>
							<img src='<?php echo $script_path ."uploads/". userValue($_SESSION['uid'], "avatar"); ?>' class='img-thumbnail' style='width: 250px; height: 250px;' id='avatar'>
							<?php
							}
							?>
						</div>
						
						<div class='row'>
							<form method='post' id='change_avatar' enctype='multipart/form-data'>
								<div class='row'>
									<div class='form-group'>
										<input type='file' name='file' id='file'>
									</div>
								</div>
								
								<div class='row row-0'>
									<div class='form-group'>
										<input type='hidden' name='token' value='<?php echo md5(session_id()); ?>'>
										<input type='hidden' name='change_avatar' value='Change'>
										<input type='submit' name='save' value='<?php echo $m['save']; ?>' class='btn btn-primary'>
									</div>
								</div>
							</form>
						</div>
					</div>-->
					
					<div class='row'>
						<div class='col-md-9'>
							<div class='col-md-3'><?php echo "<h4>". $m['username'] .":</h4><br>"; ?></div>
							<div class='col-md-6'><?php echo "<h4>". userValue($_SESSION['uid'], "username") ."</h4><br>"; ?></div>
						</div>
						
						<div class='col-md-9'>
							<div class='col-md-3'><?php echo "<h4>". $m['email'] .":</h4><br>"; ?></div>
							<div class='col-md-6'><?php echo "<h4>". userValue($_SESSION['uid'], "email") ."</h4><br>"; ?></div>
						</div>
						
						<div class='col-md-9'>
							<div class='col-md-3'><?php echo "<h4>". $m['registered_on'] .":</h4><br>"; ?></div>
							<div class='col-md-6'><?php echo "<h4>". date("j F Y", userValue($_SESSION['uid'], "registered_on")) ."</h4><br>"; ?></div>
						</div>	
						
						<div class='col-md-9'>
							<div class='col-md-3'><?php echo "<h4>". $m['last_login'] .":</h4><br>"; ?></div>
							<div class='col-md-6'><?php echo "<h4>". date("j F Y", userValue($_SESSION['uid'], "last_login")) ." ". $m['at'] ." ". date("G:i", userValue($_SESSION['uid'], "last_login")) ."</h4><br>"; ?></div>
						</div>
					</div>
				</div>
			</div>
			
			
			
			<div role='tabpanel' class='tab-pane' id='edit'>
				<div class='row row-1'>
					<h3><?php echo $m['edit']; ?></h3>
				</div>
				
				<div id='profile_response'></div>
				<form method='post' id='profile'>	
					<div class='row'>
						<div class='col-md-12'>
							<div class='row row-1'>
								<div class='form-group'>
									<label class='col-sm-4 control-label'><?php echo $m['username']; ?>*</label>
									<div class='col-sm-8'>
										<input type='text' class='form-control' name='username' value='<?php echo userValue($_SESSION['uid'], "username"); ?>'<?php if(getSetting("username_change", "text") == "false") { echo " disabled"; } ?>>
									</div>
								</div>
							</div>
							
							<div class='row row-1'>
								<div class='form-group'>
									<label class='col-sm-4 control-label'><?php echo $m['email']; ?>*</label>
									<div class='col-sm-8'>
										<input type='email' class='form-control' name='email' value='<?php echo userValue($_SESSION['uid'], "email"); ?>'<?php if(getSetting("email_change", "text") == "false") { echo " disabled"; } ?>>
									</div>
								</div>
							</div>
							
							<?php
							// Get extra inputs, filled in with the data of the current user
							getExtraInputs(true, $_SESSION['uid']);
							?>
						</div>
					</div>
					
					<div class='row row-5'>
						<div class='col-md-12'>
							<div class='form-group'>
								<input type='hidden' name='token' value='<?php echo md5(session_id()); ?>'>
								<input type='submit' name='save' value='<?php echo $m['save']; ?>' class='btn btn-primary'>
							</div>
						</div>
					</div>
				</form>
			</div>
			
			
			
			<div role='tabpanel' class='tab-pane' id='messages'>
				<?php
				// Another check if it is allowed to send and read messages
				if(getSetting("send_messages", "text") == "true") {
				?>
					<?php
					if(!empty($_GET['mid'])) {
					?>
						<div class='row row-2'>
							<a href='?page=messages'><span class='glyphicon glyphicon-arrow-left' aria-hidden='true'></span> &nbsp;<?php echo $m['back_to_messages']; ?></a>
						</div>
						
						<?php
						$mid = mysqli_real_escape_string($con,$_GET['mid']);
						$message = mysqli_query($con,"SELECT * FROM login_messages WHERE sendto='$uid' AND id='$mid'");
						
						if(mysqli_num_rows($message) > 0) {
						
						$msg = mysqli_fetch_array($message);
						
						if($msg['opened'] == "0") {
							mysqli_query($con,"UPDATE login_messages SET opened='1' WHERE sendto='$uid' AND id='$mid'");
						}
						?>
						<div class='panel panel-primary'>
							<div class='panel-heading'>
								<h3 class='panel-title'><?php echo $msg['subject']; ?></h3>
							</div>
							<div class='panel-body'>
								<?php echo nl2br($msg['message']); ?>
							</div>
							<div class='panel-footer'>
								<?php 
								echo $m['from'] .": ". userValue($msg['sendfrom'], "username");
								echo "<br>";
								echo $m['date'] .": ". date("j M Y", $msg['time']) ." at ". date("G:i", $msg['time']);
								echo "<br><br>";
								$onclick = 'answerMessage("send_message", "'. $msg['sendfrom'] .'");';
								echo "<button type='button' class='btn btn-primary' onclick='". $onclick ."'><span class='glyphicon glyphicon-share-alt' aria-hidden='true'></span> ". $m['answer'] ."</button>";
								?>
							</div>
						</div>
						<?php
						} else {
							echo "<div class='alert alert-danger' role='alert'>". $m['message_not_found'] ."</div>";
						}
						?>
					<?php
					} else {
					?>
						<div class='row'>
							<h3><?php echo $m['messages']; ?></h3>
						</div>
						
						<?php
						$message = mysqli_query($con,"SELECT * FROM login_messages WHERE sendto='$uid' ORDER BY id DESC");
						
						if(mysqli_num_rows($message) > 0) {
						?>
						<div id='messages_response'></div>
						<table class='table table-hover' id='mtable'>
							<thead>
								<tr>
									<td><input type='checkbox' id='selectall'></td>
									<td><b><?php echo $m['from']; ?></b></td>
									<td><b><?php echo $m['subject']; ?></b></td>
									<td><b><?php echo $m['date']; ?></b></td>
								</tr>
							</thead>
							<tbody>
								<?php
								while($msg = mysqli_fetch_array($message)) {
									if($msg['opened'] == "0") {
									?>
										<tr class='active'>
											<td><input type='checkbox' class='check' name='<?php echo $msg['id']; ?>'></td>
											<td style='cursor: pointer;' onclick='window.location.href = "?mid=<?php echo $msg['id']; ?>";'><?php echo userValue($msg['sendfrom'], "username"); ?></td>
											<td style='cursor: pointer;' onclick='window.location.href = "?mid=<?php echo $msg['id']; ?>";'><?php echo $msg['subject']; ?></td>
											<td style='cursor: pointer;' onclick='window.location.href = "?mid=<?php echo $msg['id']; ?>";'><?php echo date("j M Y", $msg['time']) ." at ". date("G:i", $msg['time']); ?></td>
										</tr>
									<?php
									} else {
									?>
										<tr>
											<td><input type='checkbox' class='check' name='<?php echo $msg['id']; ?>'></td>
											<td style='cursor: pointer;' onclick='window.location.href = "?mid=<?php echo $msg['id']; ?>";'><?php echo userValue($msg['sendfrom'], "username"); ?></td>
											<td style='cursor: pointer;' onclick='window.location.href = "?mid=<?php echo $msg['id']; ?>";'><?php echo $msg['subject']; ?></td>
											<td style='cursor: pointer;' onclick='window.location.href = "?mid=<?php echo $msg['id']; ?>";'><?php echo date("j M Y", $msg['time']) ." at ". date("G:i", $msg['time']); ?></td>
										</tr>
									<?php
									}
								}
								?>
							</tbody>
						</table>
						
						<div class='row row-0'>
							<div class='form-group' id='actions' style='margin-bottom: -34px;'>
								<button type='button' class='btn btn-primary' onclick='openMessages("<?php echo md5(session_id()); ?>");' style='position: relative; top: -60px;'><?php echo $m['mark_opened']; ?></button>
								<button type='button' class='btn btn-danger' onclick='openModal("delete_messages");' style='position: relative; top: -60px;'><?php echo $m['delete']; ?></button>
							</div>
						</div>
						
						<script>
						$(document).ready(function() {
							$('#mtable').dataTable( {
								"paging":   true,
								"ordering": false,
								"info":     false,
								"language": {
									"lengthMenu": "<?php echo $m['show']; ?> _MENU_ <?php echo $m['records']; ?>",
									"zeroRecords": "<?php echo $m['nothing_found']; ?>",
									"paginate": {
										"next": "<?php echo $m['next']; ?>",
										"previous": "<?php echo $m['previous']; ?>"
									}
								}
							});
						});
						
						
						
						$('#selectall').click(function(event) {
							if(this.checked) {
								$('.check').each(function() {
									this.checked = true;             
								});
								$('#actions').fadeIn(200);
							} else {
								$('.check').each(function() {
									this.checked = false;                    
								});       
								$('#actions').fadeOut(200);	
							}
						});
						
						$(".check").change(function() {
							if ($('.check').filter(':checked').length > 0) {
								$('#actions').fadeIn(200);
							} else {
								$('#actions').fadeOut(200);
							}
						});


						if($('.check').prop('checked')) {
							$('#actions').css('display', 'inline');
						} else {
							$('#actions').css('display', 'none');
						}
						</script>
						<?php
						} else {
							echo "<div class='alert alert-info' role='alert'>". $m['no_messages_found'] ."</div>";
						}
						?>
						
						<div class='row row-2 row-5'>
							<button type='button' class='btn btn-primary' data-toggle='modal' data-target='#send_message'><?php echo $m['new_message']; ?></button>
						</div>
					<?php
					}
					?>
				<?php
				}
				?>
			</div>
			
			
			
			<div role='tabpanel' class='tab-pane' id='change_password'>	
				<?php
				// Another check if it is allowed to change your password and the password field is not empty
				if(getSetting("password_change", "text") == "true" && userValue($uid, "password") != "") {
				?>
				<div class='row'>
					<h3><?php echo $m['change_password']; ?></h3>
					<div id='password_response'></div>
				</div>
				
				<form method='post' id='changepass'>	
					<div class='row'>
						<div class='col-md-12'>
							<div class='row row-1'>
								<div class='form-group'>
									<label class='col-sm-4 control-label'><?php echo $m['oldpass']; ?></label>
									<div class='col-sm-8'>
										<input type='password' class='form-control' name='oldpass'>
									</div>
								</div>
							</div>
							
							<div class='row row-1'>
								<div class='form-group'>
									<label class='col-sm-4 control-label'><?php echo $m['newpass']; ?></label>
									<div class='col-sm-8'>
										<input type='password' class='form-control' name='newpass'>
									</div>
								</div>
							</div>
							
							<div class='row row-2'>
								<div class='form-group'>
									<label class='col-sm-4 control-label'><?php echo $m['newpass2']; ?></label>
									<div class='col-sm-8'>
										<input type='password' class='form-control' name='newpass2'>
									</div>
								</div>
							</div>
						</div>
					</div>
					
					<div class='row'>
						<div class='col-md-12'>
							<div class='form-group'>
								<input type='hidden' name='token' value='<?php echo md5(session_id()); ?>'>
								<input type='submit' name='change' value='<?php echo $m['change']; ?>' class='btn btn-primary'>
							</div>
						</div>
					</div>
				</form>
				<?php
				}
				?>
			</div>
			
			
			
			<div role='tabpanel' class='tab-pane' id='set_password'>	
				<?php
				// Another check if the password field is empty
				if(userValue($uid, "password") == "") {
				?>
				<div class='row'>
					<h3><?php echo $m['set_password']; ?></h3>
					<div id='password_response'><div class='alert alert-info' role='alert'><?php echo $m['set_password_info']; ?></div></div>
				</div>
				
				<form method='post' id='setpass'>	
					<div class='row'>
						<div class='col-md-12'>
							<div class='row row-1'>
								<div class='form-group'>
									<label class='col-sm-4 control-label'><?php echo $m['newpass']; ?></label>
									<div class='col-sm-8'>
										<input type='password' class='form-control' name='newpass'>
									</div>
								</div>
							</div>
							
							<div class='row row-2'>
								<div class='form-group'>
									<label class='col-sm-4 control-label'><?php echo $m['newpass2']; ?></label>
									<div class='col-sm-8'>
										<input type='password' class='form-control' name='newpass2'>
									</div>
								</div>
							</div>
						</div>
					</div>
					
					<div class='row'>
						<div class='col-md-12'>
							<div class='form-group'>
								<input type='hidden' name='token' value='<?php echo md5(session_id()); ?>'>
								<input type='submit' name='set' value='<?php echo $m['set_password']; ?>' class='btn btn-primary'>
							</div>
						</div>
					</div>
				</form>
				<?php
				}
				} // End of the else on line 32
				?>
			</div>
		</div>
	</div>
</div>



<div class='modal fade bs-example-modal-sm' id='delete_messages' tabindex='-1' role='dialog' aria-labelledby='delete_messages' aria-hidden='true'>
	<div class='modal-dialog'>
		<div class='modal-content'>
			<div class='modal-header'>
				<button class='close' data-dismiss='modal' type='button'>
					<span aria-hidden="true">×</span>
					<span class="sr-only"><?php echo $m['close']; ?></span>
				</button>
				<h4 id='mySmallModalLabel' class='modal-title'><?php echo $m['delete_messages']; ?></h4>
			</div>
			<div class='modal-body'>
				<div id='delete_messages_response'></div>
				<form method='post' id='delete_messages_form'>
					<h4><?php echo $m['are_you_sure']; ?></h4><br>
					
					<div class='form-group'>
						<input type='hidden' name='token' value='<?php echo md5(session_id()); ?>'>
						<input type='submit' name='yes' value='<?php echo $m['yes']; ?>' class='btn btn-primary'>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>



<script>
<?php if(!empty($_GET['mid'])) { ?>
// If message id is not empty show message tab
$('#tabs li:eq(2) a').tab('show');
<?php } ?>

<?php if(!empty($_GET['page']) && $_GET['page'] == "messages") { ?>
// If page is messages show message tab
$('#tabs li:eq(2) a').tab('show');
<?php } ?>
</script>

<?php
}
?>



<?php
// Check if users are allowed to send messages
if(getSetting("send_messages", "text") == "true") {
?>
<div class='modal fade bs-example-modal-sm' id='send_message' tabindex='-1' role='dialog' aria-labelledby='send_message' aria-hidden='true'>
	<div class='modal-dialog'>
		<div class='modal-content'>
			<div class='modal-header'>
				<button class='close' data-dismiss='modal' type='button'>
					<span aria-hidden="true">×</span>
					<span class="sr-only"><?php echo $m['close']; ?></span>
				</button>
				<h4 id='mySmallModalLabel' class='modal-title'><?php echo $m['send_message']; ?></h4>
			</div>
			<div class='modal-body'>
				<div id='send_message_response'></div>
				<form method='post' id='send_message_form'>
					<div class='form-group'>
						<label><?php echo $m['to']; ?>:</label><br>
						<select class='selectpicker' name='sendto[]' data-live-search='true' date-size='auto' multiple>
							<?php
							$users = mysqli_query($con,"SELECT * FROM login_users ORDER BY username");
							
							while($u = mysqli_fetch_array($users)) {
							?>
							<option value='<?php echo $u['id']; ?>'><?php echo $u['username']; ?></option>
							<?php
							}
							?>
						</select>
					</div>
					
					<script>
					$('.selectpicker').selectpicker();
					</script>
					
					<br>
					
					<div class='form-group'>
						<label><?php echo $m['subject']; ?>:</label><br>
						<input type='text' class='form-control' name='subject' id='subject' maxlength='100'>
					</div>
					
					<br>
					
					<div class='form-group'>
						<label><?php echo $m['message']; ?>:</label><br>
						<textarea class='form-control' name='message' id='message' rows='5'></textarea>
					</div>
					
					<br><br>
					
					<div class='form-group'>
						<input type='hidden' name='token' value='<?php echo md5(session_id()); ?>'>
						<input type='submit' name='send' id='sendmsg' value='<?php echo $m['send']; ?>' class='btn btn-primary'>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<?php
}
?>



<?php
include('footer.php');
?>