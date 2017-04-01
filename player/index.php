<?php 
    require_once('../includes/database.php');
    require_once('../includes/helper_functions.php');
    if(isset($_GET['player_id'])){
        $id = $_GET['player_id'];
        echo $_GET['player_id'];
        $name = Database::get_player_name($_GET['player_id']);
        $current_economic_performance = Helpers::format_operational_performance_data(Database::get_current_economic_performance($id));
        echo "<script> var current_economic_data=" . json_encode($current_economic_performance) . ";</script>";
        
    }
    else {
        echo "you did not provide a user id!";
        print_r($_GET);
        
    }

?>

<!DOCTYPE html>
<html >
<head>
  <meta charset="UTF-8">
  <title>PowerPlay</title>
  
  <link rel="icon" href=" /home/ubuntu/workspace/favicon.ico" type="image/x-icon"/>
  <link rel="shortcut icon" href="/home/ubuntu/workspace/favicon.ico" type="image/x-icon"/>
  
  
  <link rel='stylesheet prefetch' href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha.6/css/bootstrap.min.css'>

  <link rel="stylesheet" href="../assets/css/powerplay.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chartist/0.10.1/chartist.min.css">
  
</head>
<body>
  <h1> <?php echo $name; ?> </h1>  

<div class="container-fluid">
    <div class="row">
        <div class="col-6">
            <h3> Economic Performance - Current Round </h3>
            <div id="current-econ-performance-bar"></div>    
        </div>
        
    </div>
</div>


  <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js'></script>
  <script src='../assets/js/chartist-plugin-axistitle.min.js'></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/chartist/0.10.1/chartist.min.js"></script>
  <script src="../assets/js/player-reporting.js"></script>
  

</body>



</html>


