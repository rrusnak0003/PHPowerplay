<?php 
    require_once('../includes/database.php');
    require_once('../includes/helper_functions.php');
    if(isset($_GET['player_id'])){
        $id = $_GET['player_id'];
        //echo $_GET['player_id'];
        $name = Database::get_player_name($_GET['player_id']);
        $current_economic_performance = Helpers::format_current_economic_performance_data(Database::get_current_economic_performance($id));
        //print_r($current_economic_performance);
        echo "<script> var current_economic_data=" . json_encode($current_economic_performance) . ";</script>";
        
        //$coal_cost = Database::get_econ_cost_by_tech('Coal');
        //print_r($coal_cost);
        $all_econ_costs = json_encode(Database::get_all_econ_costs($id));
        //print_r($all_econ_costs);
        echo "<script> var all_economic_data = " . $all_econ_costs . ";</script>";
        $all_econ_ror = json_encode(Database::get_all_econ_ror($id));
        echo "<script> var all_economic_ror = " . $all_econ_ror . ";</script>";
        //print_r($all_econ_ror);
        $rounds = json_encode(Database::get_rounds());
        //print_r($rounds);
        echo "<script> var rounds = " . $rounds . "</script>";
        
        $current_operational_performance = json_encode(Database::get_current_operational_performance($id));
        $zones = json_encode(Database::get_operational_zones());
        //print_r( $current_operational_performance);
        //print_r($zones);
        
        echo "<script> var zones = $zones; </script>";
        echo "<script> var current_operational_performance = $current_operational_performance; </script>";
        
        $historical_operational_performance = json_encode(Database::get_historical_operational_performance($id));
        
        //print_r($historical_operational_performance);
        
        echo "<script> var historical_operational_performance = " . $historical_operational_performance . "; </script>";
        
        $current_environmental_performance = json_encode(Database::get_current_environmental_performance());
        
        //print_r( $current_environmental_performance);
        
        echo "<script> var current_environmental_performance = $current_environmental_performance; </script>";
        
        
        $historical_environmental_performance = json_encode(Database::get_historical_environmental_performance($id));
        
        print_r($historical_environmental_performance);
        
        echo "<script> var historical_environmental_performance = $historical_environmental_performance; </script>";
    
    
        $fuel_forecast = Database::get_fuel_forecast();
        
        print_r($fuel_forecast);
        echo "<script> var fuel_forecast=$fuel_forecast</script>";
        
        $years = Database::get_fuel_forecast_years();
        print_r($years);
        echo "<script> var years=$years</script>";
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
        <div class="row graph-row"> <!-- beginning of economic reporting graphs -->
            <div class="col-lg-4 graph-container">
                <h3> Economic Performance - Current Round </h3>
                <div id="current-econ-performance-bar" class="bar"></div>    
            </div>

            
            <div class="col-lg-4 graph-container">
                <h3> Economic Performance Log - Cost by Technology </h3>
                <div id="previous-econ-performance-line-1" class="line"></div> 
                
            </div>
            
            <div class="col-lg-4 graph-container">
                <h3> Economic Performance Log - Rate of Return by Technology </h3>
                <div id="previous-econ-performance-line" class="line"></div> 
                
            </div>
        
        </div> <!-- end of economic reporting graphs -->
        
        <div class="row graph-row"> <!-- beginning of operational performance graphs -->
            <div class="col-lg-6 graph-container">
                <h3> Operational Performance - Current Round </h3>
                <div id="current-operational-performance-line" class="bar"></div>    
            </div>
            
            <div class="col-lg-6 graph-container">
                <h3> Average Operational Performance By Type Log </h3>
                <div id="previous-operational-performance-line" class="bar"></div>    
            </div>
            
        </div> <!-- end of operational performance graphs -->
        
        <div class="row graph-row"> <!-- beginning of environmental performance graphs -->
            <div class="col-lg-6 graph-container">
                <h3> Environmental Performance - Current Round </h3>
                <div id="current-environmental-performance-line" class="bar"></div>    
            </div>
            
            <div class="col-lg-6 graph-container">
                <h3> Environmental Performance - All Rounds </h3>
                <div id="previous-environmental-performance-line" class="bar"></div>    
            </div>
        </div> <!-- end of environmental performance graphs -->
        
        <div class="row graph-row"> <!-- beginning of fuel forecast and production commit -->
            <div class="col-lg-6 graph-container">
                <h3> fuel forecast </h3>
                <div id="fuel-forecast" class="line"></div>    
            </div>
            
            <div class="col-lg-6 graph-container">
                <h3> production commit </h3>
                <div id="previous-environmental-performance-line" class="bar"></div>    
            </div>
        </div> <!-- end of fuel forecast and production commit -->
    </div>
        
    </div>
    
      
  </div>
  


  <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js'></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/chartist/0.10.1/chartist.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/chartist-plugin-legend/0.6.1/chartist-plugin-legend.min.js"></script>
  <script src="../assets/js/player-reporting.js"></script>
  

</body>



</html>


