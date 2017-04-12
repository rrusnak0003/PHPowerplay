// Function to move permissions
function actionForm(token, action, id) {
	// Send post request to the posthandler and update the response div
	$.ajax({
		data: "token=" + token + "&action=" + action + "&id=" + id + "&move_perm=Move",
		type: "post",
		url: "includes/posthandler.php",
		success: function(response) { 
			$('#perm_response').html(response);
		}
	});
	
	// Update table a little bit later to make sure the request is processed, sometimes the table won't refresh properly
	setTimeout(function(){
		$("." + action).tooltip("hide");
		$('#perm_table').load("settings.php?page=permissions #ptable");
		$('#perm_level_div').load("settings.php?page=permissions #perm_level_select");
	}, 100);
	
	return false;
};



// Create permission form
$("#create_perm").submit(function() {
	// Send data to the posthandler
	$.ajax({
		data: $(this).serialize() + "&create_perm=Create",
		type: "post",
		url: "includes/posthandler.php",
		success: function(response) { 
			// If response is 1, the permission is created and the permission table should be refreshed and the modal should be hided
			if(response === "1") {
				$('#perm_table').load("settings.php?page=permissions #ptable");
				$('#perm_level_div').load("settings.php?page=permissions #perm_level_select"); // Refresh permissions
				$('#create_perm')[0].reset(); // Empty form
				$('#create_modal').modal("hide");
			} else {
				$('#create_response').html(response);
			}
		}
	});
	
	return false;
});



// Save permission form
$("#save_perm").submit(function() {
	// Send data to the posthandler
	$.ajax({
		data: $(this).serialize() + "&save_perm=Save",
		type: "post",
		url: "includes/posthandler.php",
		success: function(response) { 
			// If response is 1, the permission is saved and the user should be redirected to the permission page
			if(response === "1") {
				window.location.href = "settings.php?page=permissions";
			} else {
				$('#edit_response').html(response);
			}
		}
	});
	
	return false;
});



// Confirmation modal
function sureDeletePerm(token, id, m_delete) {
	$('#delete_modal').modal("show");
	
	var token = '"' + token + '"';
	
	$('#delete_sure').html("<button type='submit' class='btn btn-danger' onclick='deletePerm(" + token + ", " + id + ");'>" + m_delete + "</button>");
}



// Function to delete a permission
function deletePerm(token, id) {
	// Send data to the posthandler
	$.ajax({
		data: "token=" + token + "&id=" + id + "&delete_perm=Delete",
		type: "post",
		url: "includes/posthandler.php",
		success: function(response) { 
			// If response is 1, the permission is deleted and the permission table should be updated
			if(response === "1") {
				$('#perm_table').load("settings.php?page=permissions #ptable");
				$('#perm_level_div').load("settings.php?page=permissions #perm_level_select"); // Update permissions
				$('#delete_modal').modal("hide"); // Hide confirmation modal
			} else {
				$('#delete_response').html(response);
			}
		}
	});
	
	return false;
};



// Form to move a user from one permission to another
$("#move_user").submit(function() {
	// Get data out of DataTable
	var table = $('#putable').DataTable();
	var data = table.$('input').serialize();
	
	// Send data to the posthandler
	$.ajax({
		data: $(this).serialize() + "&" + data + "&move_user=Move",
		type: "post",
		url: "includes/posthandler.php",
		success: function(response) { 
			$('#move_response').html(response);
		}
	});
	
	return false;
});



// Main settings form
$("#main_settings").submit(function() {
	// Send data to the posthandler
	$.ajax({
		data: $(this).serialize() + "&main_settings=Save",
		type: "post",
		url: "includes/posthandler.php",
		success: function(response) { 
			$('#main_response').html(response);
		}
	});
	
	return false;
});



// Registration settings form
$("#registration_form").submit(function() {
	// Send data to the posthandler
	$.ajax({
		data: $(this).serialize() + "&registration_settings=Save",
		type: "post",
		url: "includes/posthandler.php",
		success: function(response) { 
			$('#registration_response').html(response);
		}
	});
	
	return false;
});



// Function to move an input
function moveInput(token, action, id) {
	// Send data to the posthandler
	$.ajax({
		data: "token=" + token + "&action=" + action + "&id=" + id + "&move_input=Move",
		type: "post",
		url: "includes/posthandler.php",
		success: function(response) { 
			$('#move_response').html(response);
		}
	});
	
	// Update input table a little bit later to make sure the request is processed
	setTimeout(function(){
		$("." + action).tooltip("hide");
		$('#inputs').load("settings.php?page=registration #input_table");
	}, 100);
	
	return false;
};



// Add input form
$("#add_input").submit(function() {
	// Send data to the posthandler
	$.ajax({
		data: $(this).serialize() + "&add_input=Add",
		type: "post",
		url: "includes/posthandler.php",
		success: function(response) { 
			// If response is 1, the input has been added and the input table should be updated
			if(response === "1") {
				$('#inputs').load("settings.php?page=registration #input_table");
				$('#add_input')[0].reset(); // Empty form
				$('#add_modal').modal("hide"); // Hides the add input modal
			} else {
				$('#input_response').html(response);
			}
			
		}
	});
	
	return false;
});



// Save input form
$("#save_input").submit(function() {
	// Send data to the posthandler
	$.ajax({
		data: $(this).serialize() + "&save_input=Save",
		type: "post",
		url: "includes/posthandler.php",
		success: function(response) { 
			// If response is 1, the input is saved and the user should be redirected to the registration page
			if(response === "1") {
				window.location.href = "settings.php?page=registration";
			} else {
				$('#edit_response').html(response);
			}
		}
	});
	
	return false;
});



// Function to open the confirmation modal
function sureDeleteInput(token, id, m_delete) {
	$('#delete_modal').modal("show");
	
	var token = '"' + token + '"';
	
	$('#delete_sure').html("<button type='submit' class='btn btn-danger' onclick='deleteInput(" + token + ", " + id + ");'>" + m_delete + "</button>");
}



// Function to delete a input
function deleteInput(token, id) {
	// Send data to the posthandler
	$.ajax({
		data: "token=" + token + "&id=" + id + "&delete_input=Delete",
		type: "post",
		url: "includes/posthandler.php",
		success: function(response) { 
			// If response is 1, the input is deleted and the input table should be refreshed
			if(response === "1") {
				$('#inputs').load("settings.php?page=registration #input_table");
				$('#delete_modal').modal("hide");
			} else {
				$('#delete_response').html(response);
			}
		}
	});
	
	return false;
};



// Login settings form
$("#login_form").submit(function() {
	// Send data to the posthandler
	$.ajax({
		data: $(this).serialize() + "&login_settings=Save",
		type: "post",
		url: "includes/posthandler.php",
		success: function(response) { 
			$('#login_response').html(response);
		}
	});
	
	return false;
});



// Redirect settings form
$("#redirect").submit(function() {
	// Send data to the posthandler
	$.ajax({
		data: $(this).serialize() + "&save_redirect=Save",
		type: "post",
		url: "includes/posthandler.php",
		success: function(response) { 
			$('#redirect_response').html(response);
		}
	});
	
	return false;
});



// Messages settings form
$("#message").submit(function() {
	// Send data to the posthandler
	$.ajax({
		data: $(this).serialize() + "&save_messages=Save",
		type: "post",
		url: "includes/posthandler.php",
		success: function(response) { 
			$('#message_response').html(response);
		}
	});
	
	return false;
});



// Registration form
$("#register").submit(function() {
	$("#register_button").prop("disabled", true); // Disable the register button to prevent the user from requesting too often while the current request is not done
	
	// Send data to the posthandler
	$.ajax({
		data: $(this).serialize() + "&register=Register",
		type: "post",
		url: "includes/posthandler.php",
		success: function(response) { 
			// Check if the response contains |||
			if(response.indexOf('|||') != -1) {
				var split = response.split('|||'); // Split the response
				
				// If the first part of the response is link, the registration is successful and the user should be redirected to the given link
				if(split[0] == "link") {
					window.location.href = split[1];
				} else {
					$("#register_button").prop("disabled", false); // Enable the register button
					$('#register_response').html(response); // Update response
					grecaptcha.reset(); // Reload reCAPTCHA
				}
			} else {
				$("#register_button").prop("disabled", false); // Enable the register button
				$('#register_response').html(response); // Update response
				grecaptcha.reset(); // Reload reCAPTCHA
			}
		}
	});
	
	return false;
});



// Social registration form
$("#social_register").submit(function() {
	$("#social_register_button").prop("disabled", true); // Disable the register button to prevent the user from requesting too often while the current request is not done
	
	// Send data to the posthandler
	$.ajax({
		data: $(this).serialize() + "&social_register=Register",
		type: "post",
		url: "includes/posthandler.php",
		success: function(response) { 
			// Check if the response contains |||
			if(response.indexOf('|||') != -1) {
				var split = response.split('|||'); // Split the response
				
				// If the first part of the response is link, the social registration is successful and the user should be redirected to the given link
				if(split[0] == "link") {
					window.location.href = split[1];
				} else {
					$("#social_register_button").prop("disabled", false); // Enable the register button
					$('#social_response').html(response); // Update response
				}
			} else {
				$("#social_register_button").prop("disabled", false); // Enable the register button
				$('#social_response').html(response); // Update response
			}
		}
	});
	
	return false;
});



// Login form
$("#login").submit(function() {
	$("#login_button").prop("disabled", true); // Disable the login button to prevent the user from requesting too often while the current request is not done
	
	// Send data to the posthandler
	$.ajax({
		data: $(this).serialize() + "&login=Login",
		type: "post",
		url: "includes/posthandler.php",
		success: function(response) { 
			// Check if the response contains |||
			if(response.indexOf('|||') != -1) {
				var split = response.split('|||'); // Split the response
				
				// If the first part of the response is link, the user is logged in and should be redirected with the given link
				if(split[0] == "link") {
					window.location.href = split[1];
				} else {
					$("#login_button").prop("disabled", false); // Enable login button
					$('#login_response').html(split[1]); // Update response
				}
			} else {
				$("#login_button").prop("disabled", false); // Enable login button
				$('#login_response').html(response); // Update response
			}
		}
	});
	
	return false;
});



// Function to check if the value of max failed attempts is more than 0, if so, check the log failed login checkbox
function loginCheck() {
	if($("#input_max_failed_attempts").val() != 0) {
		$("#log_failed_logins").prop('checked', true);
	}
}



// Mail settings form
$("#mail").submit(function() {
	// Send data to the posthandler
	$.ajax({
		data: $(this).serialize() + "&mail=Save",
		type: "post",
		url: "includes/posthandler.php",
		success: function(response) { 
			$('#mail_response').html(response);
		}
	});
	
	return false;
});



// Profile settings form
$("#profile").submit(function() {
	// Send data to the posthandler
	$.ajax({
		data: $(this).serialize() + "&profile=Save",
		type: "post",
		url: "includes/posthandler.php",
		success: function(response) { 
			$('#profile_response').html(response);
		}
	});
	
	return false;
});



// Change password form
$("#changepass").submit(function() {
	// Send data to the posthandler
	$.ajax({
		data: $(this).serialize() + "&changepass=Change",
		type: "post",
		url: "includes/posthandler.php",
		success: function(response) { 
			$('#password_response').html(response);
		}
	});
	
	return false;
});



// Change password form
$("#setpass").submit(function() {
	// Send data to the posthandler
	$.ajax({
		data: $(this).serialize() + "&setpass=Set",
		type: "post",
		url: "includes/posthandler.php",
		success: function(response) { 
			$('#password_response').html(response);
		}
	});
	
	return false;
});



// Profile settings changed by admin form
$("#adminprofile").submit(function() {
	// Send data to the posthandler
	$.ajax({
		data: $(this).serialize() + "&adminprofile=Save",
		type: "post",
		url: "includes/posthandler.php",
		success: function(response) { 
			$('#adminprofile_response').html(response);
		}
	});
	
	return false;
});



// Change password by admin form
$("#adminchangepass").submit(function() {
	// Send data to the posthandler
	$.ajax({
		data: $(this).serialize() + "&adminchangepass=Change",
		type: "post",
		url: "includes/posthandler.php",
		success: function(response) { 
			$('#adminpassword_response').html(response);
		}
	});
	
	return false;
});



// Add IP block form
$("#add_block").submit(function() {
	// Send data to the posthandler
	$.ajax({
		data: $(this).serialize() + "&add_block=Add",
		type: "post",
		url: "includes/posthandler.php",
		success: function(response) { 
			$('#add_block_response').html(response);
		}
	});
	
	return false;
});



// Delete IP block form
$("#delete_block").submit(function() {
	// Send data to the posthandler
	$.ajax({
		data: $(this).serialize() + "&delete_block=Delete",
		type: "post",
		url: "includes/posthandler.php",
		success: function(response) { 
			$('#delete_block_response').html(response);
		}
	});
	
	return false;
});



// Function to open a modal by id
function openModal(id) {
	$('#' + id).modal('show');
}



// Add user form
$("#add_user_form").submit(function() {
	// Send data to the posthandler
	$.ajax({
		data: $(this).serialize() + "&add_user=Add",
		type: "post",
		url: "includes/posthandler.php",
		success: function(response) { 
			$('#add_user_response').html(response);
			
			// When the modal hides, refresh the page
			$('#add_user').on('hide.bs.modal', function (e) {
				window.location.href = "?page=users";
			});
		}
	});
	
	return false;
});


// Change permission form
$("#change_permission_form").submit(function() {
	// Get data from DataTable
	var table = $('#utable').DataTable();
	var data = table.$('input').serialize();
	
	// Send data to the posthandler
	$.ajax({
		data: $(this).serialize() + "&" + data + "&change_permission=Change",
		type: "post",
		url: "includes/posthandler.php",
		success: function(response) { 
			$('#change_permission_response').html(response);
			
			// When the modal hides, refresh the page
			$('#change_permission').on('hide.bs.modal', function (e) {
				window.location.href = "?page=users";
			});
		}
	});
	
	return false;
});



// Ban user form
$("#ban_user_form").submit(function() {
	// Get data from DataTable
	var table = $('#utable').DataTable();
	var data = table.$('input').serialize();
	
	// Send data to the posthandler
	$.ajax({
		data: $(this).serialize() + "&" + data + "&ban_user=Ban",
		type: "post",
		url: "includes/posthandler.php",
		success: function(response) { 
			$('#ban_user_response').html(response);
			
			// When the modal hides, refresh the page
			$('#ban_user').on('hide.bs.modal', function (e) {
				window.location.href = "?page=users";
			});
		}
	});
	
	return false;
});



// Unban user form
$("#unban_user_form").submit(function() {
	// Get data from DataTable
	var table = $('#utable').DataTable();
	var data = table.$('input').serialize();
	
	// Send data to the posthandler
	$.ajax({
		data: $(this).serialize() + "&" + data + "&unban_user=Unban",
		type: "post",
		url: "includes/posthandler.php",
		success: function(response) { 
			$('#unban_user_response').html(response);
			
			// When the modal hides, refresh the page
			$('#unban_user').on('hide.bs.modal', function (e) {
				window.location.href = "?page=users";
			});
		}
	});
	
	return false;
});



// Activate user form
$("#activate_user_form").submit(function() {
	// Get data from DataTable
	var table = $('#utable').DataTable();
	var data = table.$('input').serialize();
	
	// Send data to the posthandler
	$.ajax({
		data: $(this).serialize() + "&" + data + "&activate_user=Activate",
		type: "post",
		url: "includes/posthandler.php",
		success: function(response) { 
			$('#activate_user_response').html(response);
			
			// When the modal hides, refresh the page
			$('#activate_user').on('hide.bs.modal', function (e) {
				window.location.href = "?page=users";
			});
		}
	});
	
	return false;
});



// Deactivate user form
$("#deactivate_user_form").submit(function() {
	// Get data from DataTable
	var table = $('#utable').DataTable();
	var data = table.$('input').serialize();
	
	// Send data to the posthandler
	$.ajax({
		data: $(this).serialize() + "&" + data + "&deactivate_user=Deactivate",
		type: "post",
		url: "includes/posthandler.php",
		success: function(response) { 
			$('#deactivate_user_response').html(response);
			
			// When the modal hides, refresh the page
			$('#deactivate_user').on('hide.bs.modal', function (e) {
				window.location.href = "?page=users";
			});
		}
	});
	
	return false;
});



// Delete user form
$("#delete_user_form").submit(function() {
	// Get data from DataTable
	var table = $('#utable').DataTable();
	var data = table.$('input').serialize();
	
	// Send data to the posthandler
	$.ajax({
		data: $(this).serialize() + "&" + data + "&delete_user=Delete",
		type: "post",
		url: "includes/posthandler.php",
		success: function(response) { 
			$('#delete_user_response').html(response);
			
			// When the modal hides, refresh the page
			$('#delete_user').on('hide.bs.modal', function (e) {
				window.location.href = "?page=users";
			});
		}
	});
	
	return false;
});



// Function to mark messages as opened
function openMessages(token) {
	// Get data from DataTable
	var table = $('#mtable').DataTable();
	var data = table.$('input').serialize();
	
	// Send data to the posthandler
	$.ajax({
		data: data + "&token=" + token + "&open_messages=Open",
		type: "post",
		url: "includes/posthandler.php",
		success: function(response) { 
			$('#messages_response').html(response);
		}
	});
	
	return false;
}



// Function to auto select a receiver
function answerMessage(id, uid) {
	$('#' + id).modal('show'); // Open send message modal
	$('.selectpicker').selectpicker('val', uid); // Select user to receive the message
}



// Delete messages form
$("#delete_messages_form").submit(function() {
	// Get data from DataTable
	var table = $('#mtable').DataTable();
	var data = table.$('input').serialize();
	
	// Send data to the posthandler
	$.ajax({
		data: $(this).serialize() + "&" + data + "&delete_messages=Delete",
		type: "post",
		url: "includes/posthandler.php",
		success: function(response) { 
			$('#delete_messages_response').html(response);
			
			// When the modal hides, refresh the page
			$('#delete_messages').on('hide.bs.modal', function (e) {
				window.location.href = "?page=messages";
			});
		}
	});
	
	return false;
});



// Send message form
$("#send_message_form").submit(function() {
	// Send data to the posthandler
	$.ajax({
		data: $(this).serialize() + "&send_message=Send",
		type: "post",
		url: "includes/posthandler.php",
		success: function(response) { 
			var split = response.split('|||'); // Split the response
			
			// If the first part of the response is 1, the message is send 
			if(split[0] == "1") {
				$('#send_message_response').html(split[1]); // Update response
				$('#subject').val(""); // Empty subject input
				$('#message').val(""); // Empty message textarea
				$('#sendmsg').prop("disabled", true); // Disable send button
				
				// When the modal hides, refresh the page
				$('#send_message').on('hide.bs.modal', function (e) {
					location.reload(); 
				});
			} else {
				$('#send_message_response').html(split[1]);
			}
		}
	});
	
	return false;
});



// Create backup form
$("#create_backup").submit(function() {
	// Send data to the posthandler
	$.ajax({
		data: $(this).serialize() + "&create_backup=Backup",
		type: "post",
		url: "includes/posthandler.php",
		success: function(response) { 
			$('#create_backup_response').html(response);
		}
	});
	
	return false;
});



// Mass message form
$("#mass_message").submit(function() {
	// Send data to the posthandler
	$.ajax({
		data: $(this).serialize() + "&mass_message=Send",
		type: "post",
		url: "includes/posthandler.php",
		success: function(response) { 
			$('#response_mass_message').html(response);
		}
	});
	
	return false;
});



// Clean message form
$("#clean_messages").submit(function() {
	// Send data to the posthandler
	$.ajax({
		data: $(this).serialize() + "&clean_messages=Clean",
		type: "post",
		url: "includes/posthandler.php",
		success: function(response) { 
			$('#clean_response').html(response);
		}
	});
	
	return false;
});



// Clean logs form
$("#clean_logs").submit(function() {
	// Send data to the posthandler
	$.ajax({
		data: $(this).serialize() + "&clean_logs=Clean",
		type: "post",
		url: "includes/posthandler.php",
		success: function(response) { 
			$('#clean_response').html(response);
		}
	});
	
	return false;
});



// Clean users form
$("#clean_users").submit(function() {
	// Send data to the posthandler
	$.ajax({
		data: $(this).serialize() + "&clean_users=Clean",
		type: "post",
		url: "includes/posthandler.php",
		success: function(response) { 
			$('#clean_response').html(response);
		}
	});
	
	return false;
});



// Change avatar form
$("#change_avatar").submit(function() {
	// Send data to the posthandler
	$.ajax({
		url: "includes/posthandler.php",
		type: "post",
		data: new FormData(this),
		contentType: false,
		cache: false,
		processData:false,
		success: function(response) {
			$("#avatar_response").html(response);
		}
	});
	
	return false;
});



// Check if the file changes, to show a preview of the image
$("#file").change(function() {
	var file = this.files[0];
	var imagefile = file.type;
	var imagesize = file.size;
	var match = ["image/jpeg", "image/png", "image/jpg", "image/gif"];
	
	// Check if the file extension is allowed, and if the file isn't too big
	if(!((imagefile == match[0]) || (imagefile == match[1]) || (imagefile == match[2]) || (imagefile == match[3])) || imagesize > 2097152) {
		$('#avatar').attr('src', 'assets/images/no_image.png'); // Show no image because the given file is not allowed
		$("#file").css("color", "red"); // Make the color of the file red
	} else {
		var reader = new FileReader();
		reader.onload = imageIsLoaded;
		reader.readAsDataURL(this.files[0]);
	}
});



// Function to update f
function imageIsLoaded(e) {
	$("#file").css("color", "green"); // Make the color of the file green
	$('#avatar').attr('src', e.target.result); // Show a preview of the image
};



// Social login settings form
$("#social_settings").submit(function() {
	// Send data to the posthandler
	$.ajax({
		data: $(this).serialize() + "&social_settings=Save",
		type: "post",
		url: "includes/posthandler.php",
		success: function(response) { 
			$('#social_response').html(response);
		}
	});
	
	return false;
});



// Add plan form
$("#add_plan").submit(function() {
	// Send data to the posthandler
	$.ajax({
		data: $(this).serialize() + "&add_plan=Add",
		type: "post",
		url: "includes/posthandler.php",
		success: function(response) { 
			// If response is 1, the plan is created and the page should be refreshed
			if(response === "1") {
				location.reload();
			} else {
				$('#add_plan_response').html(response);
			}
		}
	});
	
	return false;
});



// Edit plan form
$("#edit_plan").submit(function() {
	// Send data to the posthandler
	$.ajax({
		data: $(this).serialize() + "&edit_plan=Edit",
		type: "post",
		url: "includes/posthandler.php",
		success: function(response) { 
			// If response is 1, the plan is created and the page should be refreshed
			if(response === "1") {
				window.location.href = "settings.php?page=plans";
			} else {
				$('#edit_plan_response').html(response);
			}
		}
	});
	
	return false;
});



// Confirmation modal
function sureDeletePlan(token, id, m_delete) {
	$('#delete_plan').modal("show");
	
	var token = '"' + token + '"';
	
	$('#delete_sure').html("<button type='submit' class='btn btn-danger' onclick='deletePerm(" + token + ", " + id + ");'>" + m_delete + "</button>");
}



// Function to delete a plan
function deletePerm(token, id) {
	// Send data to the posthandler
	$.ajax({
		data: "token=" + token + "&id=" + id + "&delete_perm=Delete",
		type: "post",
		url: "includes/posthandler.php",
		success: function(response) { 
			// If response is 1, the permission is deleted and the permission table should be updated
			if(response === "1") {
				$('#perm_table').load("settings.php?page=permissions #ptable");
				$('#perm_level_div').load("settings.php?page=permissions #perm_level_select"); // Update permissions
				$('#delete_modal').modal("hide"); // Hide confirmation modal
			} else {
				$('#delete_response').html(response);
			}
		}
	});
	
	return false;
};



// Paypal settings form
$("#paypal").submit(function() {
	// Send data to the posthandler
	$.ajax({
		data: $(this).serialize() + "&paypal=Save",
		type: "post",
		url: "includes/posthandler.php",
		success: function(response) { 
			$('#paypal_response').html(response);
		}
	});
	
	return false;
});



// Stripe settings form
$("#stripe_settings").submit(function() {
	// Send data to the posthandler
	$.ajax({
		data: $(this).serialize() + "&stripe_settings=Save",
		type: "post",
		url: "includes/posthandler.php",
		success: function(response) { 
			$('#stripe_response').html(response);
		}
	});
	
	return false;
});



// Function to count how much items there would be deleted
function countItems(token, item) {
	if(item == "messages") {
		var from;
		from = $("#from_messages").val();
		
		// Send data to the posthandler
		$.ajax({
			data: "token=" + token + "&from=" + from + "&item=messages&count_items=Count",
			type: "post",
			url: "includes/posthandler.php",
			success: function(response) { 
				$('#message_response').html(response);
			}
		});
	} else if(item == "logs") {
		var from;
		from = $("#from_logs").val();
		
		// Send data to the posthandler
		$.ajax({
			data: "token=" + token + "&from=" + from + "&item=logs&count_items=Count",
			type: "post",
			url: "includes/posthandler.php",
			success: function(response) { 
				$('#logs_response').html(response);
			}
		});
	} else if(item == "users") {
		var from;
		var based;
		
		from = $("#from_users").val();
		based = $("#based").val();
		
		// Send data to the posthandler
		$.ajax({
			data: "token=" + token + "&from=" + from + "&based=" + based + "&item=users&count_items=Count",
			type: "post",
			url: "includes/posthandler.php",
			success: function(response) { 
				$('#users_response').html(response);
			}
		});
	} else if(item == "delete_inactive") {
		// Send data to the posthandler
		$.ajax({
			data: "token=" + token + "&from=0&item=delete_inactive&count_items=Count",
			type: "post",
			url: "includes/posthandler.php",
			success: function(response) { 
				$('#delete_inactive_response').html(response);
			}
		});
	} else if(item == "delete_never_loggedin") {
		// Send data to the posthandler
		$.ajax({
			data: "token=" + token + "&from=0&item=delete_never_loggedin&count_items=Count",
			type: "post",
			url: "includes/posthandler.php",
			success: function(response) { 
				$('#delete_never_loggedin_response').html(response);
			}
		});
	} else {
		// Item not found
	}
}



// Delete all inactive user form
$("#delete_inactive_form").submit(function() {
	// Send data to the posthandler
	$.ajax({
		data: $(this).serialize() + "&delete_inactive=Delete",
		type: "post",
		url: "includes/posthandler.php",
		success: function(response) { 
			$('#delete_inactive_response').html(response);
		}
	});
	
	return false;
});



// Delete all users who never logged in form
$("#delete_never_loggedin_form").submit(function() {
	// Send data to the posthandler
	$.ajax({
		data: $(this).serialize() + "&delete_never_loggedin=Delete",
		type: "post",
		url: "includes/posthandler.php",
		success: function(response) { 
			$('#delete_never_loggedin_response').html(response);
		}
	});
	
	return false;
});



// Function to check the format
function checkFormat() {
	var val;
	val = $("#blocked_format").val(); // Get the format
	
	if(val == "forever") {
		$('#blocked_amount').prop("disabled", true); // Disable the amount
	} else {
		$('#blocked_amount').prop("disabled", false); // Enable the amount
	}
}



// Forgot password form
$("#forgot_pass").submit(function() {
	// Send data to the posthandler
	$.ajax({
		data: $(this).serialize() + "&forgot_pass=Forgotten",
		type: "post",
		url: "includes/posthandler.php",
		success: function(response) { 
			$('#forgot_pass_response').html(response);
		}
	});
	
	return false;
});



// Change forgotten password form
$("#forgot_changepass").submit(function() {
	// Send data to the posthandler
	$.ajax({
		data: $(this).serialize() + "&forgot_changepass=Forgotten",
		type: "post",
		url: "includes/posthandler.php",
		success: function(response) { 
			$('#forgot_changepass_response').html(response);
		}
	});
	
	return false;
});



// Check database connection form
$("#check_database").submit(function() {
	// Send data to the posthandler
	$.ajax({
		data: $(this).serialize() + "&check_database=Check",
		type: "post",
		url: "includes/posthandler.php",
		success: function(response) { 
			var split = response.split('|||'); // Split the response
			
			// If the first part of the response is 1, hide the database form
			if(split[0] == "1") {
				$('#database_response').html(split[1]);
				$('#check_database').css("display", "none");
			} else {
				$('#database_response').html(split[1]);
			}
		}
	});
	
	return false;
});



// Stripe payment form
$("#stripe").submit(function() {
	$("#stripe_pay_button").prop("disabled", true); // Disable the complete payment button to prevent the user from requesting too often while the current request is not done
	
	// Send data to the posthandler
	$.ajax({
		data: $(this).serialize() + "&stripe_pay=Pay",
		type: "post",
		url: "includes/posthandler.php",
		success: function(response) {
			var split = response.split('|||'); // Split the response
			
			// If the first part of the response is 1, don't enable the payment button to prevent the user from double paying
			if(split[0] == "1") {
				$('#stripe_response').html(split[1]);
			} else {
				$('#stripe_response').html(split[1]);
				$("#stripe_pay_button").prop("disabled", false); // Enable it again
			}
		}
	});
	
	return false;
});



function addOption() {
	$("#names").append("<input type='text' name='name[]' class='form-control'>");
	$("#values").append("<input type='text' name='value[]' class='form-control'>");
}