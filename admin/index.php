<?php
require_once( '../login.php');
require_once('../includes/database.php');
require_once('../includes/helper_functions.php');

$fuel_forecast = Database::get_fuel_forecast();
        
//print_r($fuel_forecast);
echo "<script> var fuel_forecast=$fuel_forecast</script>";
        
$years = Database::get_fuel_forecast_years();
//print_r($years);
echo "<script> var years=$years</script>";
        
$monthly_commit_data = Database::get_yearly_production_commit_by_type();
//print_r($monthly_commit_data);
echo "<script> var monthly_commit_data=$monthly_commit_data </script>";

$db  = new mysqli($hn, $un, $pw, $db);

function get_all_players($db){
 $query = "SELECT user_id, username FROM users WHERE role='player'";
 
 $results = $db->query($query);
 
 if($results){
     $players = $results->fetch_all(MYSQLI_ASSOC);
 }
     foreach( $players as $player ){
      //print_r($player);
      //echo $player['id'];
      $url = "/player/index.php?player_id=" . $player['user_id'];
      echo "<a href=$url>" . $player['username'] . "</a><br>";
     }
 return $players;
}


function get_average_operational_performance($db){
    $query = "SELECT type, AVG(reliability) reliability FROM operational_performance
              GROUP BY type
              ORDER BY type";
    
    $results = $db->query($query);
    $return_val = array();
    if($results){
        $data = $results->fetch_all(MYSQLI_ASSOC);
        
        foreach($data as $row){
            $temp = array('name' => $row['type'], 'data' => array((float)$row['reliability']));
            array_push($return_val, (int) $row['reliability']);
        }
        
    }
    //$return_val = array( 1, 2, 3);
    return json_encode($return_val);
            
    
}

function get_average_economic_performance($db){
    
    $query = "SELECT tech_type, AVG(cost) cost, AVG(rate_of_return) ror FROM `economic_performance`
              group by tech_type
              order by tech_type";

    
    
}
//$players = get_all_players($db);

//print_r($players);

$average_operational_performance = get_average_operational_performance($db);
print_r($average_operational_performance);

echo "<script> var average_operational_performance=$average_operational_performance; </script>";
echo "<script> var operational_types = ['Commercial', 'Industrial', 'Residential']; </script>";


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

  <h1> Admin Panel </h1>
  <?php get_all_players($db); ?>
  
  <div id="player-data-container">
    <div class="container-fluid">
        <div class="row graph-row">
          <div class="col-lg-4 graph-container">
                <h3> Average Operational Performance </h3>
                <div id="average-operational-performance" class="bar"></div>    
            </div>
        </div> <!-- end row -->
      </div> <!-- end container-fluid -->
    </div> <!-- end graph container -->
  
  
  
  
  <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js'></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/chartist/0.10.1/chartist.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/chartist-plugin-legend/0.6.1/chartist-plugin-legend.min.js"></script>
  <script src="../assets/js/admin-reporting.js"></script>
</body>