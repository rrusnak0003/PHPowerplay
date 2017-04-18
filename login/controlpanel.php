<?php
include('includes/api.php');
logged_in();
admin();

include('head.php');
include('menu.php');
?>

	<div class='container container-admin'>
		<div class='col-md-2'>
			<h3><?php echo $m['control_menu']; ?></h3>
			<nav>
				<ul class='nav nav-pills nav-stacked span2'>
					<li><a href='?page=users'><span class='glyphicon glyphicon-user' aria-hidden='true'></span> <?php echo $m['users']; ?></a></li>
				</ul>
			</nav>
		</div>
		
		<div class='col-md-10'>
			<?php
			// Statistics page
			if(empty($_GET['page']) && $_GET['page'] == "statistics") {
			?>
			<div role='tabpanel'>
				<h3><?php echo $m['statistics']; ?></h3>
				<h5><?php echo $m['statistics_info']; ?></h5><br>
				
				<ul class='nav nav-tabs' role='tablist' id='tabs'>
					<li role='presentation' class='active'><a href='#users' aria-controls='users' role='tab' data-toggle='tab'><?php echo $m['users_registration']; ?></a></li>
					<li role='presentation'><a href='#logandblock' aria-controls='logandblock' role='tab' data-toggle='tab'><?php echo $m['log_and_block']; ?></a></li>
					<li role='presentation'><a href='#permissions' aria-controls='permissions' role='tab' data-toggle='tab'><?php echo $m['permissions']; ?></a></li>
					<li role='presentation'><a href='#messages' aria-controls='messages' role='tab' data-toggle='tab'><?php echo $m['messages']; ?></a></li>
					<li role='presentation'><a href='#ipstats' aria-controls='ipstats' role='tab' data-toggle='tab'><?php echo $m['ipstats']; ?></a></li>
				</ul>
				
				<br>
				
				<div class='tab-content'>
					<div role='tabpanel' class='tab-pane active' id='users'>
						<table class='table'>
							<thead>
								<tr>
									<td><b><?php echo $m['users_today']; ?></b></td>
									<td><b><?php echo $m['users_yesterday']; ?></b></td>
									<td><b><?php echo $m['users_this_month']; ?></b></td>
									<td><b><?php echo $m['users_this_year']; ?></b></td>
									<td><b><?php echo $m['users_total']; ?></b></td>
								</tr>
							</thead>
							<tbody>
								<?php
								$day = date("j-n-Y");
								$yesterday = date("j-n-Y", time() - 86400);
								$month = date("n-Y");
								$year = date("Y");
								
								$users_today = 0;
								$users_yesterday = 0;
								$users_this_month = 0;
								$users_this_year = 0;
								
								$users = mysqli_query($con,"SELECT * FROM login_users");
								while($u = mysqli_fetch_array($users)) {
									$time = $u['registered_on'];
									
									if(date("j-n-Y", $time) == $day) {
										$users_today++;
									}
									if(date("j-n-Y", $time) == $yesterday) {
										$users_yesterday++;
									}
									if(date("n-Y", $time) == $month) {
										$users_this_month++;
									}
									if(date("Y", $time) == $year) {
										$users_this_year++;
									}
								}
								?>
									<tr>
										<td><?php echo $users_today; ?></td>
										<td><?php echo $users_yesterday; ?></td>
										<td><?php echo $users_this_month; ?></td>
										<td><?php echo $users_this_year; ?></td>
										<td><?php echo mysqli_num_rows($users); ?></td>
									</tr>
							</tbody>
						</table>
						
						<br><br>
						
						<?php
						$eleven_month_ago = strtotime("-11 month", time());
						$ten_month_ago = strtotime("-10 month", time());
						$nine_month_ago = strtotime("-9 month", time());
						$eight_month_ago = strtotime("-8 month", time());
						$seven_month_ago = strtotime("-7 month", time());
						$six_month_ago = strtotime("-6 month", time());
						$five_month_ago = strtotime("-5 month", time());
						$four_month_ago = strtotime("-4 month", time());
						$three_month_ago = strtotime("-3 month", time());
						$two_month_ago = strtotime("-2 month", time());
						$one_month_ago = strtotime("-1 month", time());
						$this_month = time();
						?>
						
						<script>
						var uchart;
						
						var uchartData = [
							{
								"date": "<?php echo date("M Y", $eleven_month_ago); ?>",
								"users": <?php echo getUsersByDate("M Y", $eleven_month_ago); ?>
							},
							{
								"date": "<?php echo date("M Y", $ten_month_ago); ?>",
								"users": <?php echo getUsersByDate("M Y", $ten_month_ago); ?>
							},
							{
								"date": "<?php echo date("M Y", $nine_month_ago); ?>",
								"users": <?php echo getUsersByDate("M Y", $nine_month_ago); ?>
							},
							{
								"date": "<?php echo date("M Y", $eight_month_ago); ?>",
								"users": <?php echo getUsersByDate("M Y", $eight_month_ago); ?>
							},
							{
								"date": "<?php echo date("M Y", $seven_month_ago); ?>",
								"users": <?php echo getUsersByDate("M Y", $seven_month_ago); ?>
							},
							{
								"date": "<?php echo date("M Y", $six_month_ago); ?>",
								"users": <?php echo getUsersByDate("M Y", $six_month_ago); ?>
							},
							{
								"date": "<?php echo date("M Y", $five_month_ago); ?>",
								"users": <?php echo getUsersByDate("M Y", $five_month_ago); ?>
							},
							{
								"date": "<?php echo date("M Y", $four_month_ago); ?>",
								"users": <?php echo getUsersByDate("M Y", $four_month_ago); ?>
							},
							{
								"date": "<?php echo date("M Y", $three_month_ago); ?>",
								"users": <?php echo getUsersByDate("M Y", $three_month_ago); ?>
							},
							{
								"date": "<?php echo date("M Y", $two_month_ago); ?>",
								"users": <?php echo getUsersByDate("M Y", $two_month_ago); ?>
							},
							{
								"date": "<?php echo date("M Y", $one_month_ago); ?>",
								"users": <?php echo getUsersByDate("M Y", $one_month_ago); ?>
							},
							{
								"date": "<?php echo date("M Y", $this_month); ?>",
								"users": <?php echo getUsersByDate("M Y", $this_month); ?>
							},
						];
						
						AmCharts.ready(function () {
							// SERIAL CHART
							uchart = new AmCharts.AmSerialChart();
							uchart.dataProvider = uchartData;
							uchart.categoryField = "date";
							uchart.startDuration = 0.5;

							// AXES
							// category
							var ucategoryAxis = uchart.categoryAxis;
							ucategoryAxis.labelRotation = 0;
							ucategoryAxis.gridPosition = "start";

							// value
							// in case you don't want to change default settings of value axis,
							// you don't need to create it, as one value axis is created automatically.

							// GRAPH
							var ugraph = new AmCharts.AmGraph();
							ugraph.valueField = "users";
							ugraph.balloonText = "[[category]]: <b>[[value]]</b> <?php echo $m['graph_users_registered']; ?>";
							ugraph.type = "column";
							ugraph.lineAlpha = 0;
							ugraph.fillAlphas = 0.8;
							ugraph.lineColor = "#428BCA";
							uchart.addGraph(ugraph);

							// CURSOR
							var uchartCursor = new AmCharts.ChartCursor();
							uchartCursor.cursorAlpha = 0;
							uchartCursor.zoomable = false;
							uchartCursor.categoryBalloonEnabled = false;
							uchart.addChartCursor(uchartCursor);

							uchart.creditsPosition = "top-right";
							
							uchart.write("users_chart");
						});
						</script>
						
						<div id='users_chart' style='width: 100%; height: 400px;'></div>
					</div>
					
					
					
					<div role='tabpanel' class='tab-pane' id='logandblock'>
						<table class='table'>
							<thead>
								<tr>
									<td><b><?php echo $m['logins_today']; ?></b></td>
									<td><b><?php echo $m['logins_total']; ?></b></td>
									<td><b><?php echo $m['blocks_today']; ?></b></td>
									<td><b><?php echo $m['blocks_total']; ?></b></td>
								</tr>
							</thead>
							<tbody>
								<?php
								$logins_today = 0;
								
								$logins = mysqli_query($con,"SELECT * FROM login_log WHERE success='1'");
								while($l = mysqli_fetch_array($logins)) {
									if(date("j-n-Y", $l['time']) == $day) {
										$logins_today++;
									}
								}
								
								
								$blocks_today = 0;
								
								$blocks = mysqli_query($con,"SELECT * FROM login_blocks");
								while($b = mysqli_fetch_array($blocks)) {
									if(date("j-n-Y", $b['time']) == $day) {
										$blocks_today++;
									}
								}
								?>
									<tr>
										<td><?php echo $logins_today; ?></td>
										<td><?php echo mysqli_num_rows($logins); ?></td>
										<td><?php echo $blocks_today; ?></td>
										<td><?php echo mysqli_num_rows($blocks); ?></td>
									</tr>
							</tbody>
						</table>
						
						<br><br>
						
						<?php
						$seven_days_ago = strtotime("-7 days", time());
						$six_days_ago = strtotime("-6 days", time());
						$five_days_ago = strtotime("-5 days", time());
						$four_days_ago = strtotime("-4 days", time());
						$three_days_ago = strtotime("-3 days", time());
						$two_days_ago = strtotime("-2 days", time());
						$one_day_ago = strtotime("-1 day", time());
						$now = time();
						
						
						
						$logins_seven_days_ago = 0;
						$logins_six_days_ago = 0;
						$logins_five_days_ago = 0;
						$logins_four_days_ago = 0;
						$logins_three_days_ago = 0;
						$logins_two_days_ago = 0;
						$logins_one_day_ago = 0;
						$logins_now = 0;
						
						$login = mysqli_query($con,"SELECT * FROM login_log WHERE success='1' AND time BETWEEN ". $seven_days_ago ." AND ". $now);
						while($log = mysqli_fetch_array($login)) {
							if(date("j-n-Y", $log['time']) == date("j-n-Y", $seven_days_ago)) {
								$logins_seven_days_ago++;
							}
							if(date("j-n-Y", $log['time']) == date("j-n-Y", $six_days_ago)) {
								$logins_six_days_ago++;
							}
							if(date("j-n-Y", $log['time']) == date("j-n-Y", $five_days_ago)) {
								$logins_five_days_ago++;
							}
							if(date("j-n-Y", $log['time']) == date("j-n-Y", $four_days_ago)) {
								$logins_four_days_ago++;
							}
							if(date("j-n-Y", $log['time']) == date("j-n-Y", $three_days_ago)) {
								$logins_three_days_ago++;
							}
							if(date("j-n-Y", $log['time']) == date("j-n-Y", $two_days_ago)) {
								$logins_two_days_ago++;
							}
							if(date("j-n-Y", $log['time']) == date("j-n-Y", $one_day_ago)) {
								$logins_one_day_ago++;
							}
							if(date("j-n-Y", $log['time']) == date("j-n-Y", $now)) {
								$logins_now++;
							}
						}
						
						
						
						$blocks_seven_days_ago = 0;
						$blocks_six_days_ago = 0;
						$blocks_five_days_ago = 0;
						$blocks_four_days_ago = 0;
						$blocks_three_days_ago = 0;
						$blocks_two_days_ago = 0;
						$blocks_one_day_ago = 0;
						$blocks_now = 0;
						
						$block = mysqli_query($con,"SELECT * FROM login_blocks WHERE time BETWEEN ". $seven_days_ago ." AND ". $now);
						while($blo = mysqli_fetch_array($block)) {
							if(date("j-n-Y", $blo['time']) == date("j-n-Y", $seven_days_ago)) {
								$blocks_seven_days_ago++;
							}
							if(date("j-n-Y", $blo['time']) == date("j-n-Y", $six_days_ago)) {
								$blocks_six_days_ago++;
							}
							if(date("j-n-Y", $blo['time']) == date("j-n-Y", $five_days_ago)) {
								$blocks_five_days_ago++;
							}
							if(date("j-n-Y", $blo['time']) == date("j-n-Y", $four_days_ago)) {
								$blocks_four_days_ago++;
							}
							if(date("j-n-Y", $blo['time']) == date("j-n-Y", $three_days_ago)) {
								$blocks_three_days_ago++;
							}
							if(date("j-n-Y", $blo['time']) == date("j-n-Y", $two_days_ago)) {
								$blocks_two_days_ago++;
							}
							if(date("j-n-Y", $blo['time']) == date("j-n-Y", $one_day_ago)) {
								$blocks_one_day_ago++;
							}
							if(date("j-n-Y", $blo['time']) == date("j-n-Y", $now)) {
								$blocks_now++;
							}
						}
						?>
						
						<script>
						var lchart;
						
						var lchartData = [
							{
							"date": "<?php echo date("M j", $seven_days_ago); ?>",
							"logins": <?php echo $logins_seven_days_ago; ?>
							},
							{
							"date": "<?php echo date("M j", $six_days_ago); ?>",
							"logins": <?php echo $logins_six_days_ago; ?>
							},
							{
							"date": "<?php echo date("M j", $five_days_ago); ?>",
							"logins": <?php echo $logins_five_days_ago; ?>
							},
							{
							"date": "<?php echo date("M j", $four_days_ago); ?>",
							"logins": <?php echo $logins_four_days_ago; ?>
							},
							{
							"date": "<?php echo date("M j", $three_days_ago); ?>",
							"logins": <?php echo $logins_three_days_ago; ?>
							},
							{
							"date": "<?php echo date("M j", $two_days_ago); ?>",
							"logins": <?php echo $logins_two_days_ago; ?>
							},
							{
							"date": "<?php echo date("M j", $one_day_ago); ?>",
							"logins": <?php echo $logins_one_day_ago; ?>
							},
							{
							"date": "<?php echo date("M j", $now); ?>",
							"logins": <?php echo $logins_now; ?>
							}
						];
						
						AmCharts.ready(function() {
							lchart = new AmCharts.AmSerialChart();
							lchart.dataProvider = lchartData;
							lchart.categoryField = "date";
							lchart.startDuration = 0;
							
							var lgraph = new AmCharts.AmGraph();
							lgraph.valueField = "logins";
							lgraph.type = "line";
							lgraph.fillAlphas = 0;
							lgraph.lineColor = "#428BCA";
							lgraph.bullet = "round";
							lgraph.balloonText = "[[category]]: <b>[[value]]</b> <?php echo $m['graph_logins']; ?>";
							lchart.addGraph(lgraph);
							
							lchart.creditsPosition = "top-right";
						});
						</script>
						
						<div id='logins_chart' style='width: 100%; height: 400px;'></div>
						
						<br><br>
						
						<script>
						var bchart;
						
						var bchartData = [
							{
							"date": "<?php echo date("M j", $seven_days_ago); ?>",
							"blocks": <?php echo $blocks_seven_days_ago; ?>
							},
							{
							"date": "<?php echo date("M j", $six_days_ago); ?>",
							"blocks": <?php echo $blocks_six_days_ago; ?>
							},
							{
							"date": "<?php echo date("M j", $five_days_ago); ?>",
							"blocks": <?php echo $blocks_five_days_ago; ?>
							},
							{
							"date": "<?php echo date("M j", $four_days_ago); ?>",
							"blocks": <?php echo $blocks_four_days_ago; ?>
							},
							{
							"date": "<?php echo date("M j", $three_days_ago); ?>",
							"blocks": <?php echo $blocks_three_days_ago; ?>
							},
							{
							"date": "<?php echo date("M j", $two_days_ago); ?>",
							"blocks": <?php echo $blocks_two_days_ago; ?>
							},
							{
							"date": "<?php echo date("M j", $one_day_ago); ?>",
							"blocks": <?php echo $blocks_one_day_ago; ?>
							},
							{
							"date": "<?php echo date("M j", $now); ?>",
							"blocks": <?php echo $blocks_now; ?>
							}
						];
						
						AmCharts.ready(function() {
							bchart = new AmCharts.AmSerialChart();
							bchart.dataProvider = bchartData;
							bchart.categoryField = "date";
							bchart.startDuration = 0;
							
							var bgraph = new AmCharts.AmGraph();
							bgraph.valueField = "blocks";
							bgraph.type = "line";
							bgraph.fillAlphas = 0;
							bgraph.lineColor = "#428BCA";
							bgraph.bullet = "round";
							bgraph.balloonText = "[[category]]: <b>[[value]]</b> <?php echo $m['graph_blocks']; ?>";
							bchart.addGraph(bgraph);
							
							bchart.creditsPosition = "top-right";
						});
						</script>
						
						<div id='blocks_chart' style='width: 100%; height: 400px;'></div>
					</div>
					
					
					
					<div role='tabpanel' class='tab-pane' id='permissions'>
						<table class='table'>
							<thead>
								<tr>
									<td><b><?php echo $m['permission']; ?></b></td>
									<td><b><?php echo $m['users']; ?></b></td>
								</tr>
							</thead>
							<tbody>
								<?php
								$permissions = mysqli_query($con,"SELECT * FROM login_permissions ORDER BY level DESC");
								while($p = mysqli_fetch_array($permissions)) {
									$permid = $p['id'];
									$usertotal = mysqli_query($con,"SELECT * FROM login_users WHERE permission='$permid'");
								?>
									<tr>
										<td><?php echo $p['name']; ?></td>
										<td><?php echo mysqli_num_rows($usertotal); ?></td>
									</tr>
								<?php
								}
								?>
							</tbody>
						</table>
						
						<br><br>
						
						<script>
						var pchart;
						
						var pchartData = [
							<?php
							$perms = mysqli_query($con,"SELECT * FROM login_permissions ORDER BY level DESC");
							while($perm = mysqli_fetch_array($perms)) {
								$permid = $perm['id'];
								$usertotal = mysqli_query($con,"SELECT * FROM login_users WHERE permission='$permid'");
							?>
							{
								"rank": "<?php echo $perm['name']; ?>",
								"users": <?php echo mysqli_num_rows($usertotal); ?>
							},
							<?php
							}
							?>
						];
						
						AmCharts.ready(function () {
							// SERIAL CHART
							pchart = new AmCharts.AmSerialChart();
							pchart.dataProvider = pchartData;
							pchart.categoryField = "rank";
							pchart.startDuration = 1;

							// AXES
							// category
							var pcategoryAxis = pchart.categoryAxis;
							pcategoryAxis.labelRotation = 0;
							pcategoryAxis.gridPosition = "start";

							// value
							// in case you don't want to change default settings of value axis,
							// you don't need to create it, as one value axis is created automatically.

							// GRAPH
							var pgraph = new AmCharts.AmGraph();
							pgraph.valueField = "users";
							pgraph.balloonText = "[[category]]: <b>[[value]]</b> <?php echo $m['graph_users']; ?>";
							pgraph.type = "column";
							pgraph.lineAlpha = 0;
							pgraph.fillAlphas = 0.8;
							pgraph.lineColor = "#428BCA";
							pchart.addGraph(pgraph);

							// CURSOR
							var pchartCursor = new AmCharts.ChartCursor();
							pchartCursor.cursorAlpha = 0;
							pchartCursor.zoomable = false;
							pchartCursor.categoryBalloonEnabled = false;
							pchart.addChartCursor(pchartCursor);

							pchart.creditsPosition = "top-right";
						});
						</script>
						
						<div id='perms_chart' style='width: 100%; height: 400px;'></div>
					</div>
					
					
					
					<div role='tabpanel' class='tab-pane' id='messages'>
						<table class='table'>
							<thead>
								<tr>
									<td><b><?php echo $m['messages_today']; ?></b></td>
									<td><b><?php echo $m['messages_yesterday']; ?></b></td>
									<td><b><?php echo $m['messages_this_month']; ?></b></td>
									<td><b><?php echo $m['messages_this_year']; ?></b></td>
									<td><b><?php echo $m['messages_total']; ?></b></td>
								</tr>
							</thead>
							<tbody>
								<?php
								$day = date("j-n-Y");
								$yesterday = date("j-n-Y", time() - 86400);
								$month = date("n-Y");
								$year = date("Y");
								
								$messages_today = 0;
								$messages_yesterday = 0;
								$messages_this_month = 0;
								$messages_this_year = 0;
								
								$messages = mysqli_query($con,"SELECT * FROM login_messages");
								while($msg = mysqli_fetch_array($messages)) {
									$time = $msg['time'];
									
									if(date("j-n-Y", $time) == $day) {
										$messages_today++;
									}
									if(date("j-n-Y", $time) == $yesterday) {
										$messages_yesterday++;
									}
									if(date("n-Y", $time) == $month) {
										$messages_this_month++;
									}
									if(date("Y", $time) == $year) {
										$messages_this_year++;
									}
								}
								?>
									<tr>
										<td><?php echo $messages_today; ?></td>
										<td><?php echo $messages_yesterday; ?></td>
										<td><?php echo $messages_this_month; ?></td>
										<td><?php echo $messages_this_year; ?></td>
										<td><?php echo mysqli_num_rows($messages); ?></td>
									</tr>
							</tbody>
						</table>
						
						<br><br>
						
						<?php
						$eleven_month_ago = strtotime("-11 month", time());
						$ten_month_ago = strtotime("-10 month", time());
						$nine_month_ago = strtotime("-9 month", time());
						$eight_month_ago = strtotime("-8 month", time());
						$seven_month_ago = strtotime("-7 month", time());
						$six_month_ago = strtotime("-6 month", time());
						$five_month_ago = strtotime("-5 month", time());
						$four_month_ago = strtotime("-4 month", time());
						$three_month_ago = strtotime("-3 month", time());
						$two_month_ago = strtotime("-2 month", time());
						$one_month_ago = strtotime("-1 month", time());
						$this_month = time();
						?>
						
						<script>
						var msgchart;
						
						var msgchartData = [
							{
								"date": "<?php echo date("M Y", $eleven_month_ago); ?>",
								"messages": <?php echo getMessagesByDate("M Y", $eleven_month_ago); ?>
							},
							{
								"date": "<?php echo date("M Y", $ten_month_ago); ?>",
								"messages": <?php echo getMessagesByDate("M Y", $ten_month_ago); ?>
							},
							{
								"date": "<?php echo date("M Y", $nine_month_ago); ?>",
								"messages": <?php echo getMessagesByDate("M Y", $nine_month_ago); ?>
							},
							{
								"date": "<?php echo date("M Y", $eight_month_ago); ?>",
								"messages": <?php echo getMessagesByDate("M Y", $eight_month_ago); ?>
							},
							{
								"date": "<?php echo date("M Y", $seven_month_ago); ?>",
								"messages": <?php echo getMessagesByDate("M Y", $seven_month_ago); ?>
							},
							{
								"date": "<?php echo date("M Y", $six_month_ago); ?>",
								"messages": <?php echo getMessagesByDate("M Y", $six_month_ago); ?>
							},
							{
								"date": "<?php echo date("M Y", $five_month_ago); ?>",
								"messages": <?php echo getMessagesByDate("M Y", $five_month_ago); ?>
							},
							{
								"date": "<?php echo date("M Y", $four_month_ago); ?>",
								"messages": <?php echo getMessagesByDate("M Y", $four_month_ago); ?>
							},
							{
								"date": "<?php echo date("M Y", $three_month_ago); ?>",
								"messages": <?php echo getMessagesByDate("M Y", $three_month_ago); ?>
							},
							{
								"date": "<?php echo date("M Y", $two_month_ago); ?>",
								"messages": <?php echo getMessagesByDate("M Y", $two_month_ago); ?>
							},
							{
								"date": "<?php echo date("M Y", $one_month_ago); ?>",
								"messages": <?php echo getMessagesByDate("M Y", $one_month_ago); ?>
							},
							{
								"date": "<?php echo date("M Y", $this_month); ?>",
								"messages": <?php echo getMessagesByDate("M Y", $this_month); ?>
							},
						];
						
						AmCharts.ready(function () {
							// SERIAL CHART
							msgchart = new AmCharts.AmSerialChart();
							msgchart.dataProvider = msgchartData;
							msgchart.categoryField = "date";
							msgchart.startDuration = 0.5;

							// AXES
							// category
							var msgcategoryAxis = msgchart.categoryAxis;
							msgcategoryAxis.labelRotation = 0;
							msgcategoryAxis.gridPosition = "start";

							// value
							// in case you don't want to change default settings of value axis,
							// you don't need to create it, as one value axis is created automatically.

							// GRAPH
							var msggraph = new AmCharts.AmGraph();
							msggraph.valueField = "messages";
							msggraph.balloonText = "[[category]]: <b>[[value]]</b> <?php echo $m['graph_messages_send']; ?>";
							msggraph.type = "column";
							msggraph.lineAlpha = 0;
							msggraph.fillAlphas = 0.8;
							msggraph.lineColor = "#428BCA";
							msgchart.addGraph(msggraph);

							// CURSOR
							var msgchartCursor = new AmCharts.ChartCursor();
							msgchartCursor.cursorAlpha = 0;
							msgchartCursor.zoomable = false;
							msgchartCursor.categoryBalloonEnabled = false;
							msgchart.addChartCursor(msgchartCursor);

							msgchart.creditsPosition = "top-right";
						});
						</script>
						
						<div id='messages_chart' style='width: 100%; height: 400px;'></div>
					</div>
					
					
					
					<div role='tabpanel' class='tab-pane' id='ipstats'>
						<?php
						if(!empty($_GET['ip'])) {
						$ip = mysqli_real_escape_string($con,$_GET['ip']);
						$logcheck = mysqli_query($con,"SELECT * FROM login_log WHERE ip='$ip'");
						$usercheck = mysqli_query($con,"SELECT * FROM login_users WHERE ip='$ip' ORDER BY id DESC");
						?>
							<?php
							echo "<a href='?page=statistics&select=ip'><span class='glyphicon glyphicon-arrow-left' aria-hidden='true'></span> &nbsp;". $m['search_another_ip'] ."</a><br><br>";
							
							if(mysqli_num_rows($logcheck) == 0 && mysqli_num_rows($usercheck) == 0) {
								echo "<div class='alert alert-danger' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a>". $m['ip_not_found'] ."</div>";
							} else {
								$goodlogs = mysqli_query($con,"SELECT * FROM login_log WHERE ip='$ip' AND success='1'");
								$badlogs = mysqli_query($con,"SELECT * FROM login_log WHERE ip='$ip' AND success='0'");
								
								echo "<h4>". $ip ."</h4><br>";
								
								echo "<strong>". $m['users_on_this_ip'] ."</strong>". mysqli_num_rows($usercheck) ."<br><br>";
								echo "<div class='row'>";
									while($uc = mysqli_fetch_array($usercheck)) {
										echo "<div class='col-md-2'>";
										echo "<a href='?page=users&uid=". $uc['id'] ."'>". $uc['username'] ."</a>";
										echo "</div>";
									}
								echo "</div>";
								
								echo "<br><br>";
								echo "<font color='green'>". $m['good_logs'] . mysqli_num_rows($goodlogs) ."</font><br>";
								echo "<font color='red'>". $m['bad_logs'] . mysqli_num_rows($badlogs) ."</font>";
								
								echo "<br><br><br>";
								echo "<a href='http://www.ip-tracker.org/locator/ip-lookup.php?ip=". $ip ."' target='_blank'><span class='glyphicon glyphicon-share-alt' aria-hidden='true'></span> &nbsp;". $m['more_info'] ."</a>";
							}
							?>
						<?php
						} else {
						?>
							<form method='get'>
								<div class='row row-1'>
									<div class='row'>
										<h4><?php echo $m['search_ip']; ?></h4>
									</div>
									
									<div class='form-group col-md-4 special-col'>
										<input type='hidden' name='page' value='statistics'>
										<input type='text' name='ip' class='form-control' placeholder='<?php echo $m['ip']; ?>'>
									</div>
								</div>
								
								<div class='row'>
									<div class='form-group'>
										<input type='submit' value='<?php echo $m['search_ip']; ?>' class='btn btn-primary'>
									</div>
								</div>
							</form>
						<?php
						}
						?>
					</div>
				</div>
			</div>
			
			<script>
			$('#tabs li:eq(0) a').click(function (e) {
				e.preventDefault();
				$(this).tab('show');
				uchart.write("users_chart");
			});
			
			$('#tabs li:eq(1) a').click(function (e) {
				e.preventDefault();
				$(this).tab('show');
				lchart.write("logins_chart");
				bchart.write("blocks_chart");
			});
			
			$('#tabs li:eq(2) a').click(function (e) {
				e.preventDefault();
				$(this).tab('show');
				pchart.write("perms_chart");
			});
			
			$('#tabs li:eq(3) a').click(function (e) {
				e.preventDefault();
				$(this).tab('show');
				msgchart.write("messages_chart");
			});
			
			<?php
			if(isset($_GET['ip'])) {
			?>
			// If ip is not empty show ip tab
			$('#tabs li:eq(4) a').tab('show');
			<?php
			}
			?>
			
			<?php
			if(!empty($_GET['select']) && $_GET['select'] == "ip") {
			?>
			// If select is ip show ip tab
			$('#tabs li:eq(4) a').tab('show');
			<?php
			}
			?>
			</script>
			<?php
			// Tools page
			} elseif(empty($_GET['page']) && $_GET['page'] == "tools") {
			?>
			<div role='tabpanel'>
				<h3><?php echo $m['tools']; ?></h3>
				<h5><?php echo $m['tools_info']; ?></h5><br>
				
				<ul class='nav nav-tabs' role='tablist' id='tabs'>
					<li role='presentation' class='active'><a href='#message' aria-controls='message' role='tab' data-toggle='tab'><?php echo $m['mass_message']; ?></a></li>
					<li role='presentation'><a href='#clean' aria-controls='clean' role='tab' data-toggle='tab'><?php echo $m['clean_tools']; ?></a></li>
					<li role='presentation'><a href='#backup' aria-controls='backup' role='tab' data-toggle='tab'><?php echo $m['backup']; ?></a></li>
				</ul>
				
				<br>
				
				<div class='tab-content'>
					<div role='tabpanel' class='tab-pane active' id='message'>
						<div id='response_mass_message'></div>
						<form method='post' id='mass_message'>
							<div class='row row-1'>
								<div class='form-group'>
									<label class='col-sm-4 control-label'><?php echo $m['to']; ?></label>
									<div class='col-sm-8'>
										<select class='form-control' name='sendto'>
											<option value='everyone'><?php echo $m['everyone']; ?></option>
											<?php
											$perms = mysqli_query($con,"SELECT * FROM login_permissions ORDER BY level DESC");
											while($p = mysqli_fetch_array($perms)) {
											?>
											<option value='<?php echo $p['id']; ?>'><?php echo $p['name']; ?></option>
											<?php
											}
											?>
										</select>
									</div>
								</div>
							</div>
							
							<div class='row row-1'>
								<div class='form-group'>
									<label class='col-sm-4 control-label'><?php echo $m['subject']; ?></label>
									<div class='col-sm-8'>
										<input type='text' class='form-control' name='subject'>
									</div>
								</div>
							</div>
							
							<div class='row row-2'>
								<div class='form-group'>
									<label class='col-sm-4 control-label'><?php echo $m['message']; ?></label>
									<div class='col-sm-8'>
										<textarea class='form-control' name='message' rows='5'></textarea>
									</div>
								</div>
							</div>
							
							<div class='row'>
								<div class='form-group'>
									<input type='hidden' name='token' value='<?php echo md5(session_id()); ?>'>
									<input type='submit' value='<?php echo $m['send']; ?>' class='btn btn-primary'>
								</div>
							</div>
						</form>
					</div>
					
					
					
					<div role='tabpanel' class='tab-pane ' id='clean'>
						<div id='clean_response'><div class='alert alert-warning' role='alert'><a href='#' class='close' data-dismiss='alert'>&times;</a><?php echo $m['clean_warning']; ?></div></div>
						
						<div class='row row-2'>
							<form method='post' id='clean_messages'>
								<div class='row'>
									<h4><?php echo $m['clean_messages']; ?></h4>
								</div>
								
								<div class='row'>
									<div class='form-group'>
										<div class='row'>
											<div class='col-sm-4'>
												<?php echo $m['clean_messages_info']; ?>
											</div>
											<div class='col-sm-7'>
												<input type='date' name='from' class='form-control' value='<?php echo date("Y-m-d"); ?>' id='from_messages' oninput='countItems("<?php echo md5(session_id()); ?>", "messages");'>
											</div>
											<div class='col-sm-1'>
												<a class='date_info' title='<?php echo $m['date_info']; ?>'><span class='glyphicon glyphicon-exclamation-sign' style='font-size: 24px;'></span></a>
											</div>
										</div>
									</div>
								</div>
								
								<div class='row'>
									<div class='form-group'>
										<div class='row'>
											<div class='col-sm-4'>
												<?php echo $m['this_will_delete']; ?>
											</div>
											<div class='col-sm-8'>
												<div id='message_response'></div>
												
												<script>
												$(document).ready(function() {
													countItems("<?php echo md5(session_id()); ?>", "messages");
												});
												</script>
											</div>
										</div>
									</div>
								</div>
								
								<div class='row row-1'>
									<div class='form-group'>
										<div class='row'>
											<div class='col-sm-4'>
												<?php echo $m['admin_pass']; ?>
											</div>
											<div class='col-sm-8'>
												<input type='password' name='password' class='form-control'>
											</div>
										</div>
									</div>
								</div>
								
								<div class='row'>
									<div class='form-group'>
										<input type='hidden' name='token' value='<?php echo md5(session_id()); ?>'>
										<input type='submit' name='clean' value='<?php echo $m['clean_messages']; ?>' class='btn btn-primary'>
									</div>
								</div>
							</form>
						</div>
						
						<hr>
						
						<div class='row row-2 row-6'>
							<form method='post' id='clean_logs'>
								<div class='row'>
									<h4><?php echo $m['clean_logs']; ?></h4>
								</div>
								
								<div class='row'>
									<div class='form-group'>
										<div class='row'>
											<div class='col-sm-4'>
												<?php echo $m['clean_logs_info']; ?>
											</div>
											<div class='col-sm-7'>
												<input type='date' name='from' class='form-control' value='<?php echo date("Y-m-d"); ?>' id='from_logs' oninput='countItems("<?php echo md5(session_id()); ?>", "logs");'>
											</div>
											<div class='col-sm-1'>
												<a class='date_info' title='<?php echo $m['date_info']; ?>'><span class='glyphicon glyphicon-exclamation-sign' style='font-size: 24px;'></span></a>
											</div>
										</div>
									</div>
								</div>
								
								<div class='row'>
									<div class='form-group'>
										<div class='row'>
											<div class='col-sm-4'>
												<?php echo $m['this_will_delete']; ?>
											</div>
											<div class='col-sm-8'>
												<div id='logs_response'></div>
												
												<script>
												$(document).ready(function() {
													countItems("<?php echo md5(session_id()); ?>", "logs");
												});
												</script>
											</div>
										</div>
									</div>
								</div>
								
								<div class='row row-1'>
									<div class='form-group'>
										<div class='row'>
											<div class='col-sm-4'>
												<?php echo $m['admin_pass']; ?>
											</div>
											<div class='col-sm-8'>
												<input type='password' name='password' class='form-control'>
											</div>
										</div>
									</div>
								</div>
								
								<div class='row'>
									<div class='form-group'>
										<input type='hidden' name='token' value='<?php echo md5(session_id()); ?>'>
										<input type='submit' name='clean' value='<?php echo $m['clean_logs']; ?>' class='btn btn-primary'>
									</div>
								</div>
							</form>
						</div>
						
						<hr>
						
						<div class='row row-4 row-6'>
							<form method='post' id='clean_users'>
								<div class='row'>
									<h4><?php echo $m['clean_users']; ?></h4>
								</div>
								
								<div class='row'>
									<div class='form-group'>
										<div class='row'>
											<div class='col-sm-4'>
												<?php echo $m['clean_users_info']; ?>
											</div>
											<div class='col-sm-7'>
												<input type='date' name='from' class='form-control' value='<?php echo date("Y-m-d"); ?>' id='from_users' oninput='countItems("<?php echo md5(session_id()); ?>", "users");'>
											</div>
											<div class='col-sm-1'>
												<a class='date_info' title='<?php echo $m['date_info']; ?>'><span class='glyphicon glyphicon-exclamation-sign' style='font-size: 24px;'></span></a>
											</div>
										</div>
									</div>
								</div>
								
								<div class='row'>
									<div class='form-group'>
										<div class='row'>
											<div class='col-sm-4'>
												<?php echo $m['this_will_delete']; ?>
											</div>
											<div class='col-sm-8'>
												<div id='users_response'></div>
												
												<script>
												$(document).ready(function() {
													countItems("<?php echo md5(session_id()); ?>", "users");
												});
												</script>
											</div>
										</div>
									</div>
								</div>
								
								<div class='row'>
									<div class='form-group'>
										<div class='row'>
											<div class='col-sm-4'>
												<?php echo $m['based_on']; ?>
											</div>
											<div class='col-sm-8'>
												<select name='based' class='form-control' id='based' onchange='countItems("<?php echo md5(session_id()); ?>", "users");'>
													<option value='activity'><?php echo $m['based_on_activity']; ?></option>
													<option value='registration'><?php echo $m['based_on_registration']; ?></option>
												</select>
											</div>
										</div>
									</div>
								</div>
								
								<div class='row row-1'>
									<div class='form-group'>
										<div class='row'>
											<div class='col-sm-4'>
												<?php echo $m['admin_pass']; ?>
											</div>
											<div class='col-sm-8'>
												<input type='password' name='password' class='form-control'>
											</div>
										</div>
									</div>
								</div>
								
								<div class='row'>
									<div class='form-group'>
										<input type='hidden' name='token' value='<?php echo md5(session_id()); ?>'>
										<input type='submit' name='clean' value='<?php echo $m['clean_users']; ?>' class='btn btn-primary'>
									</div>
								</div>
							</form>
						</div>
						
						
						<div class='row'>
							<button type='button' class='btn btn-danger' onclick='openModal("delete_inactive");'><?php echo $m['delete_inactive_users']; ?></button><br><br>
							<button type='button' class='btn btn-danger' onclick='openModal("delete_never_loggedin");'><?php echo $m['delete_users_never_logged_in']; ?></button>
						</div>
						
						
						<script>
						$(".date_info").tooltip({
							container: 'body'
						});
						</script>
						
						
						
						<div class='modal fade bs-example-modal-sm' id='delete_inactive' tabindex='-1' role='dialog' aria-labelledby='DeleteModal' aria-hidden='true'>
							<div class='modal-dialog' id='delete_modal_content'>
								<div class='modal-content'>
									<div class='modal-header'>
										<button class='close' data-dismiss='modal' type='button'>
											<span aria-hidden="true">×</span>
											<span class="sr-only">Close</span>
										</button>
										<h4 id='mySmallModalLabel' class='modal-title'><?php echo $m['delete_inactive_users']; ?></h4>
									</div>
									<div class='modal-body'>
										<div id='delete_inactive_response'></div>
										
										<script>
										$(document).ready(function() {
											countItems("<?php echo md5(session_id()); ?>", "delete_inactive");
										});
										</script>
										
										<form method='post' id='delete_inactive_form'>
											<div class='row row-1'>
												<div class='form-group'>
													<label class='control-label'><?php echo $m['admin_pass']; ?></label>
													<input type='password' name='password' class='form-control'>
												</div>
											</div>
											
											<div class='row'>
												<div class='form-group'>
													<input type='hidden' name='token' value='<?php echo md5(session_id()); ?>'>
													<input type='submit' name='delete' value='<?php echo $m['delete']; ?>' class='btn btn-danger'>
												</div>
											</div>
										</form>
									</div>
								</div>
							</div>
						</div>
						
						
						<div class='modal fade bs-example-modal-sm' id='delete_never_loggedin' tabindex='-1' role='dialog' aria-labelledby='DeleteModal' aria-hidden='true'>
							<div class='modal-dialog' id='delete_modal_content'>
								<div class='modal-content'>
									<div class='modal-header'>
										<button class='close' data-dismiss='modal' type='button'>
											<span aria-hidden="true">×</span>
											<span class="sr-only">Close</span>
										</button>
										<h4 id='mySmallModalLabel' class='modal-title'><?php echo $m['delete_users_never_logged_in']; ?></h4>
									</div>
									<div class='modal-body'>
										<div id='delete_never_loggedin_response'></div>
										
										<script>
										$(document).ready(function() {
											countItems("<?php echo md5(session_id()); ?>", "delete_never_loggedin");
										});
										</script>
										
										<form method='post' id='delete_never_loggedin_form'>
											<div class='row row-1'>
												<div class='form-group'>
													<label class='control-label'><?php echo $m['admin_pass']; ?></label>
													<input type='password' name='password' class='form-control'>
												</div>
											</div>
											
											<div class='row'>
												<div class='form-group'>
													<input type='hidden' name='token' value='<?php echo md5(session_id()); ?>'>
													<input type='submit' name='delete' value='<?php echo $m['delete']; ?>' class='btn btn-danger'>
												</div>
											</div>
										</form>
									</div>
								</div>
							</div>
						</div>
					</div>
					
					
					
					<div role='tabpanel' class='tab-pane' id='backup'>
						<div class='row'>
							<form method='post' id='create_backup'>
								<div class='row'>
									<div class='form-group'>
										<input type='hidden' name='token' value='<?php echo md5(session_id()); ?>'>
										<input type='submit' value='<?php echo $m['create_backup']; ?>' class='btn btn-primary'>
									</div>
								</div>
							</form>
						</div>
						
						<div class='row'>
							<div id='create_backup_response'></div>
						</div>
					</div>
				</div>
			</div>
			<?php
			// Logs page
			} elseif(empty($_GET['page']) && $_GET['page'] == "logs") {
			?>
			<h3><?php echo $m['logs']; ?></h3>
			<h5><?php echo $m['logs_info']; ?></h5><br>
			
			<?php
			$log = mysqli_query($con,"SELECT * FROM login_log ORDER BY id DESC");
			
			if(mysqli_num_rows($log) > 0) {
			?>
				<table class='table table-striped table-hover' id='ltable'>
					<thead>
						<tr>
							<td><b><?php echo $m['id']; ?></b></td>
							<td><b><?php echo $m['status']; ?></b></td>
							<td><b><?php echo $m['date']; ?></b></td>
							<td><b><?php echo $m['type']; ?></b></td>
							<td><b><a id='ip' title='<?php echo $m['ip_info']; ?>'><?php echo $m['ip']; ?></a></b></td>
							<td><b><?php echo $m['username']; ?></b></td>
						</tr>
					</thead>
					<tbody>
						<?php
						while($l = mysqli_fetch_array($log)) {
							$lip = $l['ip'];
							$blocked = mysqli_query($con,"SELECT * FROM login_blocks WHERE ip='$lip'");
							
							if($l['success'] == "1") {
							?>
								<tr>
									<td><?php echo $l['id']; ?></td>
									<td><?php echo "<font color='green'>". $m['success'] ."</font>"; ?></td>
									<td><?php echo date("j M Y", $l['time']) ." at ". date("G:i", $l['time']); ?></td>
									<td><?php 
									if(!empty($l['type'])) {
										if($l['type'] == "google") {
											echo "<img src='". $script_path ."assets/images/google.png' width='14px' heigth='14px'> Google+";
										} elseif($l['type'] == "facebook") {
											echo "<img src='". $script_path ."assets/images/facebook.png' width='14px' heigth='14px'> Facebook";
										} elseif($l['type'] == "twitter") {
											echo "<img src='". $script_path ."assets/images/twitter.png' width='14px' heigth='14px'> Twitter";
										} else {
											echo "<span class='glyphicon glyphicon-globe' aria-hidden='true'></span> Website";
										}
									} else {
										echo "<span class='glyphicon glyphicon-globe' aria-hidden='true'></span> Website";
									}
									?></td>
									<td><?php if(mysqli_num_rows($blocked) == 0) { echo "<a href='?page=statistics&ip=". $l['ip'] ."'>". $l['ip'] ."</a>"; } else { echo "<a href='?page=statistics&ip=". $l['ip'] ."' style='color: red;'>". $l['ip'] ."</a>"; } ?></td>
									<td><?php if(userValue($l['uid'], "username") != "") { echo "<a href='?page=users&uid=". $l['uid'] ."'>". userValue($l['uid'], "username") ."</a>"; } else { echo $l['try']; }; ?></td>
								</tr>
							<?php
							} else {
							?>
								<tr>
									<td><?php echo $l['id']; ?></td>
									<td><?php echo "<font color='red'>". $m['failed'] ."</font>"; ?></td>
									<td><?php echo date("j M Y", $l['time']) ." at ". date("G:i", $l['time']); ?></td>
									<td><?php 
									if(!empty($l['type'])) {
										if($l['type'] == "google") {
											echo "<img src='". $script_path ."assets/images/google.png' width='14px' heigth='14px'> Google+";
										} elseif($l['type'] == "facebook") {
											echo "<img src='". $script_path ."assets/images/facebook.png' width='14px' heigth='14px'> Facebook";
										} elseif($l['type'] == "twitter") {
											echo "<img src='". $script_path ."assets/images/twitter.png' width='14px' heigth='14px'> Twitter";
										} else {
											echo "<span class='glyphicon glyphicon-globe' aria-hidden='true'></span> Website";
										}
									} else {
										echo "<span class='glyphicon glyphicon-globe' aria-hidden='true'></span> Website";
									}
									?></td>
									<td><?php if(mysqli_num_rows($blocked) == 0) { echo "<a href='?page=statistics&ip=". $l['ip'] ."'>". $l['ip'] ."</a>"; } else { echo "<a href='?page=statistics&ip=". $l['ip'] ."' style='color: red;'>". $l['ip'] ."</a>"; } ?></td>
									<td><?php if(userValue($l['uid'], "username") != "") { echo "<a href='?page=users&uid=". $l['uid'] ."'>". userValue($l['uid'], "username") ."</a>"; } else { echo $l['try']; }; ?></td>
								</tr>
							<?php
							}
						}
						?>
					</tbody>
				</table>
				
				<script>
				$(document).ready(function() {
					$('#ltable').dataTable( {
						"paging":   true,
						"ordering": true,
						"info":     false,
						"order": [[ 0, "desc" ]],
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
					
					$("#ip").tooltip({
						container: 'body'
					});
				});
				</script>
			<?php
			} else {
				echo "<div class='alert alert-info' role='alert'>". $m['no_logs_found'] ."</div>";
			}
			?>
			<?php
			// Blocked IPs page
			} elseif(empty($_GET['page']) && $_GET['page'] == "blocked") {
			?>
			<h3><?php echo $m['blocked_ip']; ?></h3>
			<h5><?php echo $m['blocked_ip_info']; ?></h5><br>
			
			<?php
			$timenow = time();
			$blocks = mysqli_query($con,"SELECT * FROM login_blocks WHERE until > ". $timenow ." OR until='0' OR until='' ORDER BY id DESC");
			
			// Check if there are any blocked IPs
			if(mysqli_num_rows($blocks) > 0) {
			?>
				<div id='delete_block_response'></div>
				<div class='row'>
					<form method='post' id='delete_block'>
						<table class='table table-striped table-hover' id='btable'>
							<thead>
								<tr>
									<td><input type='checkbox' id='selectall'></td>
									<td><b><?php echo $m['id']; ?></b></td>
									<td><b><?php echo $m['blocked_on']; ?></b></td>
									<td><b><?php echo $m['ip']; ?></b></td>
									<td><b><?php echo $m['blocked_until']; ?></b></td>
									<td><b><?php echo $m['reason']; ?></b></td>
								</tr>
							</thead>
							<tbody>
								<?php
								while($b = mysqli_fetch_array($blocks)) {
								?>
									<tr>
										<td><input type='checkbox' class='check' name='<?php echo $b['id']; ?>'></td>
										<td><?php echo $b['id']; ?></td>
										<td><?php echo date("j M Y", $b['time']) ." at ". date("G:i", $b['time']); ?></td>
										<td><?php echo "<a href='?page=statistics&ip=". $b['ip'] ."'>". $b['ip'] ."</a>"; ?></td>
										<td><?php if(empty($b['until']) || $b['until'] == "0") { echo "Forever"; } else { echo date("d M Y", $b['until']) ." ". $m['at'] ." ". date("G:i", $b['until']); } ?></td>
										<?php
										if(empty($b['reason'])) {
										?>
											<td><?php echo "<a class='reason' title='". $m['no_reason'] ."'>". $m['reason'] ."</a>"; ?></td>
										<?php
										} else {
										?>
											<td><?php echo "<a class='reason' title='". $b['reason'] ."'>". $m['reason'] ."</a>"; ?></td>
										<?php
										}
										?>
									</tr>
								<?php
								}
								?>
							</tbody>
						</table>
						
						<div class='row' id='actions' style='margin-bottom: -49px;'>
							<div class='form-group'>
								<input type='hidden' name='token' value='<?php echo md5(session_id()); ?>'>
								<input type='submit' name='delete' value='<?php echo $m['delete_blocks']; ?>' class='btn btn-danger' style='position: relative; top: -60px;'>
							</div>
						</div>
					</form>
				</div>
				
				<script>
				$(document).ready(function() {
					$('#btable').dataTable( {
						"paging":   true,
						"ordering": true,
						"info":     false,
						"order": [[ 1, "desc" ]],
						"columnDefs": [ { "orderable": false, "targets": 0 } ],
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
					
					
					
					$(".reason").tooltip({
						container: 'body'
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
				});
				</script>
			<?php
			} else {
				echo "<div class='alert alert-info' role='alert'>". $m['no_blocks_found'] ."</div>";
			}
			?>
			
			<h3><?php echo $m['add_block']; ?></h3>
			<div id='add_block_response'></div>
			<form method='post' id='add_block'>
				<div class='row row-1 row-5'>
					<div class='form-group'>
						<label class='col-sm-4 control-label'><?php echo $m['ip']; ?></label>
						<div class='col-sm-8'>
							<input type='text' name='ip' class='form-control'>
						</div>
					</div>
				</div>
				
				<div class='row row-1'>
					<div class='form-group'>
						<label class='col-sm-4 control-label'><?php echo $m['reason']; ?></label>
						<div class='col-sm-8'>
							<input type='text' name='reason' class='form-control'>
						</div>
					</div>
				</div>
				
				<div class='row row-2'>
					<div class='form-group'>
						<label class='col-sm-4 control-label'><?php echo $m['blocked_time']; ?></label>
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
								checkFormat();
							});
							
							$("#blocked_time").tooltip({
								container: 'body'
							});
							
							<?php if(getSetting("blocked_format", "text") != "") { ?>
							$('select[name=blocked_format]').val("<?php echo getSetting("blocked_format", "text"); ?>");
							<?php } ?>
							</script>
						</div>
					</div>
				</div>
				
				<div class='row'>
					<div class='form-group'>
						<input type='hidden' name='token' value='<?php echo md5(session_id()); ?>'>
						<input type='submit' name='add' value='<?php echo $m['add']; ?>' class='btn btn-primary'>
					</div>
				</div>
			</form>
			
			<?php
			// User page
			} else {
			?>
			<h3><?php echo $m['users']; ?></h3>
			<h5><?php echo $m['users_info']; ?></h5>
			
			<?php
			if(!empty($_GET['uid'])) {
				$uid = mysqli_real_escape_string($con,$_GET['uid']);
				
				$user = mysqli_query($con,"SELECT * FROM login_users WHERE id='$uid'");
				$u = mysqli_fetch_array($user);
				?>
				<form method='post' id='adminprofile'>	
					<div class='row'>
						<div class='col-md-12'>
							<div class='row row-1'>
								<h3><?php echo $m['info_user']; ?></h3>
							</div>
							
							<div class='row row-2'>
								<!--<div class='col-md-4'>
									<?php
									// Get avatar
									if(empty($u['avatar'])) {
									?>
									<img src='<?php echo $script_path; ?>assets/images/no_image.png' class='img-thumbnail' style='width: 250px; height: 250px;'>
									<?php
									} else {
									?>
									<img src='<?php echo $script_path ."uploads/". $u['avatar']; ?>' class='img-thumbnail' style='width: 250px; height: 250px;'>
									<?php
									}
									?>
								</div>-->
								
								<div class='col-md-8'>
									<div class='form-group'>
										<label class='col-sm-4 control-label'><?php echo $m['username']; ?></label>
										<div class='col-sm-8'>
											<?php echo $u['username']; ?> 
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
											?>
										</div>
									</div>
									
									<br>
									
									<div class='form-group'>
										<label class='col-sm-4 control-label'><?php echo $m['email']; ?></label>
										<div class='col-sm-8'>
											<?php echo $u['email']; ?>
										</div>
									</div>
									
									<br>
									
									<div class='form-group'>
										<label class='col-sm-4 control-label'><?php echo $m['registered_on']; ?></label>
										<div class='col-sm-8'>
											<?php echo date("j F Y", $u['registered_on']); ?>
										</div>
									</div>
									
									<br>
									
									<div class='form-group'>
										<label class='col-sm-4 control-label'><?php echo $m['last_login']; ?></label>
										<div class='col-sm-8'>
											<?php if(!empty($u['last_login'])) { echo date("j F Y", $u['last_login']) ." ". $m['at'] ." ". date("G:i", $u['last_login']); } else { echo "-"; } ?>
										</div>
									</div>
									
									<br>
									
									<div class='form-group'>
										<label class='col-sm-4 control-label'><?php echo $m['ip']; ?></label>
										<div class='col-sm-8'>
											<?php 
											if(!empty($u['ip'])) {
												echo "<a href='?page=statistics&ip=". $u['ip'] ."'>". $u['ip'] ."</a>"; 
											} else {
												echo "-";
											}
											?>
										</div>
									</div>
									
									<br>
									
									<div class='form-group'>
										<label class='col-sm-4 control-label'><?php echo $m['permission']; ?></label>
										<div class='col-sm-8'>
											<?php echo "<a href='settings.php?page=permissions&id=". $u['permission'] ."'>". getPermName($u['id']) ."</a>"; ?>
										</div>
									</div>
									
									<br>
									
									<div class='form-group'>
										<label class='col-sm-4 control-label'><?php echo $m['user_logins']; ?></label>
										<div class='col-sm-8'>
											<?php 
											$user_logins = mysqli_query($con,"SELECT * FROM login_log WHERE success='1' AND uid='$uid'");
											echo mysqli_num_rows($user_logins);
											?>
										</div>
									</div>
									
									<br>
									
									<!--<div class='form-group'>
										<label class='col-sm-4 control-label'><?php echo $m['register_method']; ?></label>
										<div class='col-sm-8'>
											<?php 
											if(!empty($u['type'])) {
												if($u['type'] == "google") {
													echo "<a href='https://plus.google.com/". $u['sid'] ."' target='_blank'><img src='". $script_path ."assets/images/google.png' width='14px' heigth='14px'> Google+</a>";
												} elseif($u['type'] == "facebook") {
													echo "<a href='https://www.facebook.com/app_scoped_user_id/". $u['sid'] ."' target='_blank'><img src='". $script_path ."assets/images/facebook.png' width='14px' heigth='14px'> Facebook</a>";
												} elseif($u['type'] == "twitter") {
													echo "<a href='https://twitter.com/intent/user?user_id=". $u['sid'] ."' target='_blank'><img src='". $script_path ."assets/images/twitter.png' width='14px' heigth='14px'> Twitter</a>";
												} elseif($u['type'] == "admin") {
													echo "<span class='glyphicon glyphicon-user' aria-hidden='true'></span> Admin";
												} else {
													echo "<span class='glyphicon glyphicon-globe' aria-hidden='true'></span> Website";
												}
											} else {
												echo "<span class='glyphicon glyphicon-globe' aria-hidden='true'></span> Website";
											}
											?>
										</div>
									</div>-->
								</div>
							</div>
							
							
							
							<div class='row'>
								<h3><?php echo $m['edit_user']; ?></h3>
							</div>
							
							<div id='adminprofile_response'></div>
							
							<div class='row'>
								<div class='form-group'>
									<label class='col-sm-4 control-label'><?php echo $m['username']; ?>*</label>
									<div class='col-sm-8'>
										<input type='username' class='form-control' name='username' value='<?php echo $u['username']; ?>'>
									</div>
								</div>
							</div>
							
							<div class='row'>
								<div class='form-group'>
									<label class='col-sm-4 control-label'><?php echo $m['email']; ?>*</label>
									<div class='col-sm-8'>
										<input type='email' class='form-control' name='email' value='<?php echo userValue($u['id'], "email"); ?>'>
									</div>
								</div>
							</div>
							
							<div class='row'>
								<div class='form-group'>
									<label class='col-sm-4 control-label'><?php echo $m['permission']; ?>*</label>
									<div class='col-sm-8'>
										<select name='permission' class='form-control'>
											<?php
											$permissions = mysqli_query($con,"SELECT * FROM login_permissions ORDER BY level DESC");
											while($p = mysqli_fetch_array($permissions)) {
											?>
											<option value='<?php echo $p['id']; ?>'><?php echo $p['name']; ?></option>
											<?php
											}
											?>
										</select>
										
										<script>
										// Select the current permission
										$('select[name=permission]').val("<?php echo $u['permission']; ?>");
										</script>
									</div>
								</div>
							</div>
							
							<div class='row'>
								<?php
								// Get the extra inputs filled in with the data FROM login_the current user
								getExtraInputs(true, $u['id']);
								?>
							</div>
						</div>
					</div>
					
					<div class='row'>
						<div class='col-md-12'>
							<div class='form-group'>
								<input type='hidden' name='token' value='<?php echo md5(session_id()); ?>'>
								<input type='hidden' name='uid' value='<?php echo $u['id']; ?>'>
								<input type='submit' name='save' value='<?php echo $m['save']; ?>' class='btn btn-primary'>
							</div>
						</div>
					</div>
				</form>
				
				
				
				<div class='row row-1 row-7'>
					<h3><?php echo $m['change_password']; ?></h3>
				</div>
				
				<div id='adminpassword_response'></div>
				<form method='post' id='adminchangepass'>	
					<div class='row row-2'>
						<div class='col-md-12'>
							<div class='form-group'>
								<label class='col-sm-4 control-label'><?php echo $m['newpass']; ?></label>
								<div class='col-sm-8'>
									<input type='password' class='form-control' name='newpass'>
								</div>
							</div>
							
							<br><br>
							
							<div class='form-group'>
								<label class='col-sm-4 control-label'><?php echo $m['newpass2']; ?></label>
								<div class='col-sm-8'>
									<input type='password' class='form-control' name='newpass2'>
								</div>
							</div>
						</div>
					</div>
					
					<div class='row'>
						<div class='col-md-12'>
							<div class='form-group'>
								<input type='hidden' name='token' value='<?php echo md5(session_id()); ?>'>
								<input type='hidden' name='uid' value='<?php echo $u['id']; ?>'>
								<input type='submit' name='change' value='<?php echo $m['change']; ?>' class='btn btn-primary'>
							</div>
						</div>
					</div>
				</form>
				<?php
			} else {
				$users = mysqli_query($con,"SELECT * FROM login_users ORDER BY id");
				
				if(mysqli_num_rows($users) > 0) {
				?>
				<div class='row row-5'>
					<button type='button' class='btn btn-primary' data-toggle='modal' data-target='#add_user'><span class='glyphicon glyphicon-plus-sign'></span>&nbsp; <?php echo $m['add_user']; ?></button>
				</div>
				
				<br>
					<table class='table table-striped table-hover' id='utable'>
						<thead>
							<tr>
								<td><input type='checkbox' id='selectall'></td>
								<td><b><?php echo $m['id']; ?></b></td>
								<td><b><?php echo $m['username']; ?></b></td>
								<td><b><?php echo $m['email']; ?></b></td>
								<td><b><?php echo $m['registered_on']; ?></b></td>
								<td><b><?php echo $m['permission']; ?></b></td>
								<td><b><?php echo $m['active']; ?></b></td>
							</tr>
						</thead>
						<tbody>
							<?php
							while($u = mysqli_fetch_array($users)) {
								$userid = $u['id'];
								$checkban = mysqli_query($con,"SELECT * FROM login_bans WHERE uid='$userid'");
							?>
								<tr<?php if(mysqli_num_rows($checkban) > 0) { echo " class='danger'"; } ?>>
									<td><input type='checkbox' class='check' name='<?php echo $u['id']; ?>'></td>
									<td><?php echo $u['id']; ?></td>
									<td><?php echo "<a href='?page=users&uid=". $u['id'] ."'>". $u['username'] ."</a> "; ?></td>
									<td><?php echo $u['email']; ?></td>
									<td><?php echo date("j F Y", $u['registered_on']); ?></td>
									<td><?php echo getPermName($u['id']); ?></td>
									<td><?php if($u['active'] == "1") { echo $m['yes']; } else { echo $m['no']; } ?></td>
								</tr>
							<?php
							}
							?>
						</tbody>
					</table>
					
					<div class='form-group' id='actions'>
						<h3><?php echo $m['options_for_selected']; ?></h3>
						<h5><?php echo $m['options_for_selected_info']; ?></h5><br>
						
						<input type='hidden' name='token' value='<?php echo md5(session_id()); ?>'>
						
						<button class='btn btn-primary' onclick='openModal("change_permission");'><?php echo $m['change_permission']; ?></button>
						
						<br><br>
						
						<!--<button class='btn btn-primary' onclick='openModal("ban_user");'><?php echo $m['ban_user']; ?></button>
						<button class='btn btn-primary' onclick='openModal("unban_user");'><?php echo $m['unban_user']; ?></button>
						
						<br><br>-->
						
						<button class='btn btn-primary' onclick='openModal("activate_user");'><?php echo $m['activate_user']; ?></button>
						<button class='btn btn-primary' onclick='openModal("deactivate_user");'><?php echo $m['deactivate_user']; ?></button>
						
						<br><br>
						
						<button class='btn btn-danger' onclick='openModal("delete_user");'><?php echo $m['delete_user']; ?></button>
					</div>
					
					<script>
					$(document).ready(function() {
						$('#utable').dataTable( {
							"paging":   true,
							"ordering": true,
							"info":     false,
							"order": [[ 1, "asc" ]],
							"columnDefs": [ { "orderable": false, "targets": 0 } ],
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
								$('#actions').fadeIn(200);
							} else {
								$('.check').each(function() {
									this.checked = false;                    
								});       
								$('#actions').fadeOut(200);	
							}
						});
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
					
					
					
					
					<div class='modal fade bs-example-modal-sm' id='add_user' tabindex='-1' role='dialog' aria-labelledby='add_user' aria-hidden='true'>
						<div class='modal-dialog'>
							<div class='modal-content'>
								<div class='modal-header'>
									<button class='close' data-dismiss='modal' type='button'>
										<span aria-hidden="true">×</span>
										<span class="sr-only"><?php echo $m['close']; ?></span>
									</button>
									<h4 id='mySmallModalLabel' class='modal-title'><?php echo $m['add_user']; ?></h4>
								</div>
								<div class='modal-body'>
									<div id='add_user_response'></div>
									<form method='post' id='add_user_form'>
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
										
										<div class='form-group'>
											<label class='col-sm-4 control-label'><?php echo $m['permission']; ?>*</label>
											<div class='col-sm-8 row-1'>
												<select class='form-control' name='permission'>
												<?php
												$permissions = mysqli_query($con,"SELECT * FROM login_permissions ORDER BY level DESC");
												while($p = mysqli_fetch_array($permissions)) {
													?>
													<option value='<?php echo $p['id']; ?>'><?php echo $p['name']; ?></option>
													<?php
												}
												?>
												</select>
												
												<script>
												// Select the default permission
												$('select[name=permission]').val("<?php echo getSetting("default_permission", "text"); ?>");
												</script>
											</div>
										</div>
										
										<?php
										// Get the extra inputs, empty
										getExtraInputs();
										?>
										
										<div class='form-group'>
											<input type='hidden' name='token' value='<?php echo md5(session_id()); ?>'>
											<input type='submit' name='add' value='<?php echo $m['add']; ?>' class='btn btn-primary'>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
					
					
					
					<div class='modal fade bs-example-modal-sm' id='change_permission' tabindex='-1' role='dialog' aria-labelledby='change_permission' aria-hidden='true'>
						<div class='modal-dialog'>
							<div class='modal-content'>
								<div class='modal-header'>
									<button class='close' data-dismiss='modal' type='button'>
										<span aria-hidden="true">×</span>
										<span class="sr-only"><?php echo $m['close']; ?></span>
									</button>
									<h4 id='mySmallModalLabel' class='modal-title'><?php echo $m['change_permission']; ?></h4>
								</div>
								<div class='modal-body'>
									<div id='change_permission_response'></div>
									<form method='post' id='change_permission_form'>
										<h4><?php echo $m['change_to']; ?></h4><br>
										
										<div class='form-group'>
											<label class='col-sm-4 control-label'><?php echo $m['permission']; ?></label>
											<div class='col-sm-8' id='perm_level_div'>
												<select class='form-control' name='permission'>
													<?php
													$permissions = mysqli_query($con,"SELECT * FROM login_permissions ORDER BY level DESC");
													while($p = mysqli_fetch_array($permissions)) {
													?>
														<option value='<?php echo $p['id']; ?>'><?php echo $p['name']; ?></option>
													<?php
													}
													?>
												</select>
											</div>
										</div>
										
										<br><br><br>
										
										<div class='form-group'>
											<input type='hidden' name='token' value='<?php echo md5(session_id()); ?>'>
											<input type='submit' name='change' value='<?php echo $m['change']; ?>' class='btn btn-primary'>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
					
					
					
					<div class='modal fade bs-example-modal-sm' id='ban_user' tabindex='-1' role='dialog' aria-labelledby='ban_user' aria-hidden='true'>
						<div class='modal-dialog'>
							<div class='modal-content'>
								<div class='modal-header'>
									<button class='close' data-dismiss='modal' type='button'>
										<span aria-hidden="true">×</span>
										<span class="sr-only"><?php echo $m['close']; ?></span>
									</button>
									<h4 id='mySmallModalLabel' class='modal-title'><?php echo $m['ban_user']; ?></h4>
								</div>
								<div class='modal-body'>
									<div id='ban_user_response'></div>
									<form method='post' id='ban_user_form'>
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
					
					
					
					<div class='modal fade bs-example-modal-sm' id='unban_user' tabindex='-1' role='dialog' aria-labelledby='unban_user' aria-hidden='true'>
						<div class='modal-dialog'>
							<div class='modal-content'>
								<div class='modal-header'>
									<button class='close' data-dismiss='modal' type='button'>
										<span aria-hidden="true">×</span>
										<span class="sr-only"><?php echo $m['close']; ?></span>
									</button>
									<h4 id='mySmallModalLabel' class='modal-title'><?php echo $m['unban_user']; ?></h4>
								</div>
								<div class='modal-body'>
									<div id='unban_user_response'></div>
									<form method='post' id='unban_user_form'>
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
					
					
					
					<div class='modal fade bs-example-modal-sm' id='activate_user' tabindex='-1' role='dialog' aria-labelledby='activate_user' aria-hidden='true'>
						<div class='modal-dialog'>
							<div class='modal-content'>
								<div class='modal-header'>
									<button class='close' data-dismiss='modal' type='button'>
										<span aria-hidden="true">×</span>
										<span class="sr-only"><?php echo $m['close']; ?></span>
									</button>
									<h4 id='mySmallModalLabel' class='modal-title'><?php echo $m['activate_user']; ?></h4>
								</div>
								<div class='modal-body'>
									<div id='activate_user_response'></div>
									<form method='post' id='activate_user_form'>
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
					
					
					
					<div class='modal fade bs-example-modal-sm' id='deactivate_user' tabindex='-1' role='dialog' aria-labelledby='deactivate_user' aria-hidden='true'>
						<div class='modal-dialog'>
							<div class='modal-content'>
								<div class='modal-header'>
									<button class='close' data-dismiss='modal' type='button'>
										<span aria-hidden="true">×</span>
										<span class="sr-only"><?php echo $m['close']; ?></span>
									</button>
									<h4 id='mySmallModalLabel' class='modal-title'><?php echo $m['deactivate_user']; ?></h4>
								</div>
								<div class='modal-body'>
									<div id='deactivate_user_response'></div>
									<form method='post' id='deactivate_user_form'>
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
					
					
					
					<div class='modal fade bs-example-modal-sm' id='delete_user' tabindex='-1' role='dialog' aria-labelledby='delete_user' aria-hidden='true'>
						<div class='modal-dialog'>
							<div class='modal-content'>
								<div class='modal-header'>
									<button class='close' data-dismiss='modal' type='button'>
										<span aria-hidden="true">×</span>
										<span class="sr-only"><?php echo $m['close']; ?></span>
									</button>
									<h4 id='mySmallModalLabel' class='modal-title'><?php echo $m['delete_user']; ?></h4>
								</div>
								<div class='modal-body'>
									<div id='delete_user_response'></div>
									<form method='post' id='delete_user_form'>
										<h4><?php echo $m['are_you_sure_delete']; ?></h4><br>
										
										<div class='form-group'>
											<input type='hidden' name='token' value='<?php echo md5(session_id()); ?>'>
											<input type='submit' name='yes' value='<?php echo $m['yes']; ?>' class='btn btn-danger'>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				<?php
				} else {
					echo "<div class='alert alert-info' role='alert'>". $m['no_users_found'] ."</div>";
				}
			}
				?>
			<?php
			}
			?>
		</div>
	</div>

<?php
include('footer.php');
?>