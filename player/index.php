<?php 
    require_once('../includes/database.php');
    require_once('../includes/helper_functions.php');
    if(isset($_GET['player_id'])){
        $id = $_GET['player_id'];
        //echo $_GET['player_id'];
        $name = Database::get_player_name($_GET['player_id']);
        $current_economic_performance = Helpers::format_current_economic_performance_data(Database::get_current_economic_performance($id));
        echo "<script> var current_economic_data=" . json_encode($current_economic_performance) . ";</script>";
        
        $coal_cost = Database::get_econ_cost_by_tech('Coal');
        print_r($coal_cost);
        $all_econ_costs = json_encode(Database::get_all_econ_costs());
        print_r($all_econ_costs);
        echo "<script> var all_economic_data = " . $all_econ_costs . ";</script>";
        $all_econ_ror = json_encode(Database::get_all_econ_ror());
        echo "<script> var all_economic_ror = " . $all_econ_ror . ";</script>";
        print_r($all_econ_ror);
        $rounds = json_encode(Database::get_rounds());
        print_r($rounds);
        echo "<script> var rounds = " . $rounds . "</script>";
        
    }
    else {
        echo "you did not provide a user id!";
        //print_r($_GET);
        
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
  <div class="player-container">
      
    <h1 id="username"> <?php echo $name; ?> </h1>  
    <h6> Game data for <?php echo $name ?> </h6>
    
    <div id="player-data-container">
        <div class="container-fluid">
        <div class="row">
            <div class="col-lg-4 graph-container">
                <h3> Economic Performance - Current Round </h3>
                <div id="current-econ-performance-bar" class="bar"></div>    
            </div>

            
            <div class="col-lg-4 graph-container">
                <h3> Economic Performance - Previous Rounds </h3>
                <div id="previous-econ-performance-line-1" class="line"></div> 
                
            </div>
            
            <div class="col-lg-4 graph-container">
                <h3> Economic Performance - Previous Rounds </h3>
                <div id="previous-econ-performance-line" class="line"></div> 
                
            </div>
        
        </div>
    </div>
        
    </div>
    
      
  </div>
  


  <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js'></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/chartist/0.10.1/chartist.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/chartist-plugin-legend/0.6.1/chartist-plugin-legend.min.js"></script>
  <script src="../assets/js/player-reporting.js"></script>
  

</body>



</html>


