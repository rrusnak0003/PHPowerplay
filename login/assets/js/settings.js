// Tooltips on ready
$(document).ready(function() {
	// Main
	$("#security").tooltip({
		container: 'body'
	});
	
	// Links
	$("#on_login_warning").tooltip({
		container: 'body'
	});
	$("#redirect_login").tooltip({
		container: 'body'
	});
	$("#redirect_logout").tooltip({
		container: 'body'
	});
	$("#redirect_nopermission").tooltip({
		container: 'body'
	});
	$("#redirect_notloggedin").tooltip({
		container: 'body'
	});
	
	// Messages
	$("#message_login").tooltip({
		container: 'body'
	});
	$("#message_logout").tooltip({
		container: 'body'
	});
	$("#message_nopermission").tooltip({
		container: 'body'
	});
	$("#message_notloggedin").tooltip({
		container: 'body'
	});
	$("#need_redirect").tooltip({
		container: 'body'
	});
	
	// Permissions
	$(".edit").tooltip({
		container: 'body'
	});
	$(".up").tooltip({
		container: 'body'
	});
	$(".down").tooltip({
		container: 'body'
	});
	$(".delete").tooltip({
		container: 'body'
	});
	$("#perm_name").tooltip({
		container: 'body'
	});
	$("#perm_level").tooltip({
		container: 'body'
	});
	$("#admin_info").tooltip({
		container: 'body'
	});
	$("#level_warning").tooltip({
		container: 'body'
	});
		
	// Mails
	$("#welcome_mail").tooltip({
		container: 'body'
	});
	$("#validation_mail").tooltip({
		container: 'body'
	});
	$("#reset_mail").tooltip({
		container: 'body'
	});
	$("#mailtype").tooltip({
		container: 'body'
	});
	$("#smtp_hostname").tooltip({
		container: 'body'
	});
	$("#smtp_port").tooltip({
		container: 'body'
	});
	
	// Main settings
	$("#disable_title").tooltip({
		container: 'body'
	});
	$("#default_permission_title").tooltip({
		container: 'body'
	});
	$("#allow_title").tooltip({
		container: 'body'
	});
	$("#login_with_title").tooltip({
		container: 'body'
	});
	$("#admin_email_title").tooltip({
		container: 'body'
	});
	$("#email_name_title").tooltip({
		container: 'body'
	});
	$("#online_time_title").tooltip({
		container: 'body'
	});
	$("#timezone_title").tooltip({
		container: 'body'
	});
	
	// Login settings
	$("#login_log").tooltip({
		container: 'body'
	});
	$("#max_failed_attempts").tooltip({
		container: 'body'
	});
	$("#redirect_last_page").tooltip({
		container: 'body'
	});
	$("#blocked_time").tooltip({
		container: 'body'
	});
	$("#case_sensitive").tooltip({
		container: 'body'
	});
	$("#username_length").tooltip({
		container: 'body'
	});
	$("#password_length").tooltip({
		container: 'body'
	});
	
	// Registration settings
	$("#on_registration").tooltip({
		container: 'body'
	});
	$("#max_ip").tooltip({
		container: 'body'
	});
	$("#input_name").tooltip({
		container: 'body'
	});
	$("#input_public_name").tooltip({
		container: 'body'
	});
	$("#input_type").tooltip({
		container: 'body'
	});
	$("#input_maxlength").tooltip({
		container: 'body'
	});
	$("#input_rows").tooltip({
		container: 'body'
	});
	$("#input_min").tooltip({
		container: 'body'
	});
	$("#input_max").tooltip({
		container: 'body'
	});
	$("#input_step").tooltip({
		container: 'body'
	});
	$("#input_checked").tooltip({
		container: 'body'
	});
	$("#input_placeholder").tooltip({
		container: 'body'
	});
	$("#input_value").tooltip({
		container: 'body'
	});
	$("#input_required").tooltip({
		container: 'body'
	});
	$("#no_spaces_allowed").tooltip({
		container: 'body'
	});
	$("#input_error").tooltip({
		container: 'body'
	});
	$("#select_options").tooltip({
		container: 'body'
	});
	$("#activation").tooltip({
		container: 'body'
	});
	$("#input_public").tooltip({
		container: 'body'
	});
	
	// Social login settings
	$("#social_main").tooltip({
		container: 'body'
	});
	$("#enable_google").tooltip({
		container: 'body'
	});
	$("#client_id").tooltip({
		container: 'body'
	});
	$("#client_secret").tooltip({
		container: 'body'
	});
	$("#api_key").tooltip({
		container: 'body'
	});
	$("#google_redirect").tooltip({
		container: 'body'
	});
	$("#enable_facebook").tooltip({
		container: 'body'
	});
	$("#fb_appid").tooltip({
		container: 'body'
	});
	$("#fb_appsecret").tooltip({
		container: 'body'
	});
	$("#enable_twitter").tooltip({
		container: 'body'
	});
	$("#consumer_key").tooltip({
		container: 'body'
	});
	$("#consumer_secret").tooltip({
		container: 'body'
	});
	$("#twitter_callback").tooltip({
		container: 'body'
	});
	
	// Paypal settings
	$("#enable_paypal").tooltip({
		container: 'body'
	});
	$("#paypal_email").tooltip({
		container: 'body'
	});
	$("#paypal_ipn").tooltip({
		container: 'body'
	});
	$("#paypal_cost").tooltip({
		container: 'body'
	});
	
	// Stripe settings
	$("#enable_stripe").tooltip({
		container: 'body'
	});
	$("#stripe_key").tooltip({
		container: 'body'
	});
	$("#stripe_cost").tooltip({
		container: 'body'
	});
});



// Tooltips after AJAX
$(document).ajaxComplete(function(){
	// Main
	$("#security").tooltip({
		container: 'body'
	});
	
	// Links
	$("#on_login_warning").tooltip({
		container: 'body'
	});
	$("#redirect_login").tooltip({
		container: 'body'
	});
	$("#redirect_logout").tooltip({
		container: 'body'
	});
	$("#redirect_nopermission").tooltip({
		container: 'body'
	});
	$("#redirect_notloggedin").tooltip({
		container: 'body'
	});
	
	// Messages
	$("#message_login").tooltip({
		container: 'body'
	});
	$("#message_logout").tooltip({
		container: 'body'
	});
	$("#message_nopermission").tooltip({
		container: 'body'
	});
	$("#message_notloggedin").tooltip({
		container: 'body'
	});
	$("#need_redirect").tooltip({
		container: 'body'
	});
	
	// Permissions
	$(".edit").tooltip({
		container: 'body'
	});
	$(".up").tooltip({
		container: 'body'
	});
	$(".down").tooltip({
		container: 'body'
	});
	$(".delete").tooltip({
		container: 'body'
	});
	$("#perm_name").tooltip({
		container: 'body'
	});
	$("#perm_level").tooltip({
		container: 'body'
	});
	$("#admin_info").tooltip({
		container: 'body'
	});
	$("#level_warning").tooltip({
		container: 'body'
	});
		
	// Mails
	$("#welcome_mail").tooltip({
		container: 'body'
	});
	$("#validation_mail").tooltip({
		container: 'body'
	});
	$("#reset_mail").tooltip({
		container: 'body'
	});
	$("#mailtype").tooltip({
		container: 'body'
	});
	$("#smtp_hostname").tooltip({
		container: 'body'
	});
	$("#smtp_port").tooltip({
		container: 'body'
	});
	
	// Main settings
	$("#disable_title").tooltip({
		container: 'body'
	});
	$("#default_permission_title").tooltip({
		container: 'body'
	});
	$("#allow_title").tooltip({
		container: 'body'
	});
	$("#login_with_title").tooltip({
		container: 'body'
	});
	$("#admin_email_title").tooltip({
		container: 'body'
	});
	$("#email_name_title").tooltip({
		container: 'body'
	});
	$("#online_time_title").tooltip({
		container: 'body'
	});
	$("#timezone_title").tooltip({
		container: 'body'
	});
	
	// Login settings
	$("#login_log").tooltip({
		container: 'body'
	});
	$("#max_failed_attempts").tooltip({
		container: 'body'
	});
	$("#redirect_last_page").tooltip({
		container: 'body'
	});
	$("#blocked_time").tooltip({
		container: 'body'
	});
	$("#case_sensitive").tooltip({
		container: 'body'
	});
	$("#username_length").tooltip({
		container: 'body'
	});
	$("#password_length").tooltip({
		container: 'body'
	});
	
	// Registration settings
	$("#on_registration").tooltip({
		container: 'body'
	});
	$("#max_ip").tooltip({
		container: 'body'
	});
	$("#input_name").tooltip({
		container: 'body'
	});
	$("#input_public_name").tooltip({
		container: 'body'
	});
	$("#input_type").tooltip({
		container: 'body'
	});
	$("#input_maxlength").tooltip({
		container: 'body'
	});
	$("#input_rows").tooltip({
		container: 'body'
	});
	$("#input_min").tooltip({
		container: 'body'
	});
	$("#input_max").tooltip({
		container: 'body'
	});
	$("#input_step").tooltip({
		container: 'body'
	});
	$("#input_checked").tooltip({
		container: 'body'
	});
	$("#input_placeholder").tooltip({
		container: 'body'
	});
	$("#input_value").tooltip({
		container: 'body'
	});
	$("#input_required").tooltip({
		container: 'body'
	});
	$("#no_spaces_allowed").tooltip({
		container: 'body'
	});
	$("#input_error").tooltip({
		container: 'body'
	});
	$("#select_options").tooltip({
		container: 'body'
	});
	$("#activation").tooltip({
		container: 'body'
	});
	$("#input_public").tooltip({
		container: 'body'
	});
	
	// Social login settings
	$("#social_main").tooltip({
		container: 'body'
	});
	$("#enable_google").tooltip({
		container: 'body'
	});
	$("#client_id").tooltip({
		container: 'body'
	});
	$("#client_secret").tooltip({
		container: 'body'
	});
	$("#api_key").tooltip({
		container: 'body'
	});
	$("#google_redirect").tooltip({
		container: 'body'
	});
	$("#enable_facebook").tooltip({
		container: 'body'
	});
	$("#fb_appid").tooltip({
		container: 'body'
	});
	$("#fb_appsecret").tooltip({
		container: 'body'
	});
	$("#enable_twitter").tooltip({
		container: 'body'
	});
	$("#consumer_key").tooltip({
		container: 'body'
	});
	$("#consumer_secret").tooltip({
		container: 'body'
	});
	$("#twitter_callback").tooltip({
		container: 'body'
	});
	
	// Paypal settings
	$("#enable_paypal").tooltip({
		container: 'body'
	});
	$("#paypal_email").tooltip({
		container: 'body'
	});
	$("#paypal_ipn").tooltip({
		container: 'body'
	});
	$("#paypal_cost").tooltip({
		container: 'body'
	});
	
	// Stripe settings
	$("#enable_stripe").tooltip({
		container: 'body'
	});
	$("#stripe_key").tooltip({
		container: 'body'
	});
	$("#stripe_cost").tooltip({
		container: 'body'
	});
});



$('#delete_modal').on('hidden.bs.modal', function () {
	$('#delete_response').html("");
});