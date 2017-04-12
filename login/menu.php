
	<nav class='navbar navbar-inverse' role='navigation'>
		<div class='container'>
			<div class='navbar-header'>
				<button type='button' class='navbar-toggle collapsed' data-toggle='collapse' data-target='#bs-example-navbar-collapse-1'>
					<span class='sr-only'>Toggle navigation</span>
					<span class='icon-bar'></span>
					<span class='icon-bar'></span>
					<span class='icon-bar'></span>
				</button>
				<a class='navbar-brand' href='/'><span class='glyphicon glyphicon-home' aria-hidden='true'></span> Home</a>
			</div>
			
			<div class='collapse navbar-collapse' id='bs-example-navbar-collapse-1'>
				<ul class='nav navbar-nav'>
					<li><a href='controlpanel.php?page=users'><span class='glyphicon glyphicon-tasks' aria-hidden='true'></span> <?php echo $m['control_panel']; ?></a></li>
					<li><a href='settings.php?page=main'><span class='glyphicon glyphicon-cog' aria-hidden='true'></span> <?php echo $m['settings']; ?></a></li>
				</ul>
				<ul class='nav navbar-nav pull-right'>
					<li class='dropdown'>
						<a href='#' class='dropdown-toggle' data-toggle='dropdown'><?php echo $m['welcome'] ." ". userValue($_SESSION['uid'], "username"); ?> <span class='caret'></span></a>
						<ul class='dropdown-menu' role='menu'>
							<li><a href='profile.php'><span class='glyphicon glyphicon-user' aria-hidden='true'></span> <?php echo $m['profile']; ?></a></li>
							<li class='divider'></li>
							<li><a href='logout.php'><span class='glyphicon glyphicon-log-out' aria-hidden='true'></span> <?php echo $m['logout']; ?></a></li>
						</ul>
					</li>
				</ul>
			</div>
		</div>
	</nav>
	
	<div class='container'>
		<!-- Check if javascript is enabled -->
		<noscript>
			<div class='alert alert-danger' role='alert'><?php echo $m['enable_javascript']; ?></div>
		</noscript>
	</div>
	