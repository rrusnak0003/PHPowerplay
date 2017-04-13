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

    $results = $db->query($query);
    $return_val = array();
    
    $tech_type = array();
    $cost = array();
    $ror = array(); 
    
    if($results){
        $data = $results->fetch_all(MYSQLI_ASSOC);
        
        foreach($data as $row){
            array_push($tech_type, $row['tech_type']);
            array_push($cost, (int) $row['cost']);
            array_push($ror, (int) $row['ror']);
        }
        
        $return_val = array($tech_type, $cost, $ror);
    }
    
    return json_encode($return_val);
    
    
    
    
    
}
function get_average_environmental_performance($db){
    $query = "SELECT zone_name, AVG(carbon_dioxide) co2, AVG(nitrous_oxide) no, 
              AVG(carbon_monoxide) co, AVG(sulfur_dioxide) so2 
              FROM `environmental_performance` join zones on zone_id=zones.id
              group by zone_name
              order by zone_name";
    $results = $db->query($query);
    $return_val = array();
    
    $zones = array();
    $co2 = array();
    $no = array();
    $co = array();
    $so2 = array();
    
    if($results){
        
        $data = $results->fetch_all(MYSQLI_ASSOC);
        
        foreach($data as $row){
            array_push($zones, $row['zone_name']);
            array_push($co2, (int) $row['co2']);
            array_push($no, (int) $row['no']);
            array_push($co, (int) $row['co']);
            array_push($so2, (int) $row['so2']);
        }
        
        $return_val = array($zones, $co2, $no, $co, $so2);
        
        
    }
    
    return json_encode($return_val);
    
}
//$players = get_all_players($db);

//print_r($players);

$average_operational_performance = get_average_operational_performance($db);
print_r($average_operational_performance);

echo "<script> var average_operational_performance=$average_operational_performance; </script>";
echo "<script> var operational_types = ['Commercial', 'Industrial', 'Residential']; </script>";

$average_economic_performance = get_average_economic_performance($db);
print_r($average_economic_performance);

echo "<script> var average_economic_performance=$average_economic_performance; </script>";

$average_environmental_performance = get_average_environmental_performance($db);

print_r($average_environmental_performance);
echo "<script> var average_environmental_performance=$average_environmental_performance; </script>";

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
          <div class="col-lg-6 graph-container">
                <h3> Average Operational Performance </h3>
                <div id="average-operational-performance" class="bar"></div>    
            </div>
          <div class="col-lg-6 graph-container">
                <h3> Average Economic Performance </h3>
                <div id="average-economic-performance" class="bar"></div>    
            </div> 
            <div class="col-lg-12 graph-container">
                <h3> Average Environmental Performance </h3>
                <div id="average-environmental-performance" class="bar"></div>    
            </div>
        </div> <!-- end row -->
      </div> <!-- end container-fluid -->
    </div> <!-- end graph container -->
  
  
  
  
  <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js'></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/chartist/0.10.1/chartist.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/chartist-plugin-legend/0.6.1/chartist-plugin-legend.min.js"></script>
  <script src="../assets/js/admin-reporting.js"></script>
</body>