<?php
// Include the version display if the page is not login.php or register.php or profile.php or social.php
if($title != "login.php" && $title != "register.php" && $title != "profile.php" && $title != "social.php") {
?>	
<br>
<div class='text-center'>
	v1.4
</div>
<?php
}
?>
<br><br>

<script src='<?php echo $script_path; ?>assets/js/functions.js'></script>
</body>
</html>