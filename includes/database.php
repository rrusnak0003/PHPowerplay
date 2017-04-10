<?php

require_once('../login.php');

class Database{
    
    //hostname, username, password, database 
    protected static $db;
    protected static $_instance = null;
    
    public static function instance($hn, $un, $pw, $db){
        
        if(is_null( self::$_instance)){
            self::$_instance = new static();
            self::$db  = new mysqli($hn, $un, $pw, $db);
            
            if(self::$db->connect_error) die($db->connect_error);
        }
        
    }
    
    public static function get_current_round(){
        $query = "SELECT MAX(round_id) from economic_performance";
        self::$db->query($query);
        
        if($result){
            return $result->fetch_all()[0][0];
        }else {
            return 0;
        }
    }
    
    public static function create_scenario(){
        //stufff goes here
        return;
    }
    
    public static function get_current_economic_performance($player_id){
        
        $query = "SELECT tech_type, cost, rate_of_return FROM economic_performance where round_id in (
                    SELECT MAX(round_id) FROM `economic_performance`
                  )
                  and player_id=$player_id";
        $result = self::$db->query($query);
        
        if( $result ){
            return $result->fetch_all(MYSQLI_ASSOC);
        }
    }
    
    public static function get_econ_cost_by_tech($tech, $player_id){
        
        $query = "SELECT round_id, tech_type, cost
                  FROM economic_performance
                  WHERE player_id=$player_id and tech_type='$tech'";
                  
        $result = self::$db->query($query);
        
        $cost_points = array();
        if($result){
            $data = $result->fetch_all(MYSQLI_ASSOC);
            //print_r($data);
            
            foreach( $data as $row ){
                array_push( $cost_points, $row['cost']);
            }
            
            return array( 'name' => "$tech - Cost", 'data' => $cost_points);
            
        }
        
        return $cost_points;
        
        
           
    }
    
    public static function get_econ_ror_by_tech($tech, $id){
        
        $query = "SELECT round_id, tech_type, rate_of_return
                  FROM economic_performance
                  WHERE player_id=$id and tech_type='$tech'";
                  
                  
        $result = self::$db->query($query);
        
        $rates = array();
        if($result){
            $data = $result->fetch_all(MYSQLI_ASSOC);
            //print_r($data);
            
            foreach( $data as $row ){
                array_push( $rates, $row['rate_of_return']);
            }
            
            return array( 'name' => "$tech - ROR", 'data' => $rates);
            
        }
        
        return $rates;
        
        
           
    }
    
    public static function get_all_econ_costs($id){
        
        $query = "SELECT DISTINCT tech_type FROM economic_performance";
        
        $result = self::$db->query($query);
        
        $data = array();
        
        if($result){
            $tech_types = $result->fetch_all(MYSQLI_ASSOC);
            
            foreach( $tech_types as $tech_type ){
                array_push($data, self::get_econ_cost_by_tech($tech_type['tech_type'], $id));
            }
            
        }
        
        //print_r($data);
        return $data;
        
    }
    
    public static function get_all_econ_ror($id){
        
        $query = "SELECT DISTINCT tech_type FROM economic_performance";
        
        $result = self::$db->query($query);
        
        $data = array();
        
        if($result){
            $tech_types = $result->fetch_all(MYSQLI_ASSOC);
            
            foreach($tech_types as $tech_type){
                array_push( $data, self::get_econ_ror_by_tech($tech_type['tech_type'], $id));
            }
        }
        
        return $data;
        
    }
    
    public static function get_rounds(){
        
        $query = "SELECT DISTINCT round_id FROM economic_performance";
        
        $result = self::$db->query($query);
        
        $data = array();
        
        if($result){
            foreach($result as $round){
                array_push($data, (int) $round['round_id']);
            }
        }
        
        return $data; 
        
        
    }
    
    public static function get_current_average_economic_performance($player_id){
        
    }
    
    public static function get_current_operational_performance($id){
        
        $query = "SELECT DISTINCT type FROM operational_performance";
        
        $results = self::$db->query($query);
        
        $data = array();
        
        if($results){
            
            $types = $results->fetch_all(MYSQLI_ASSOC);
            
            foreach( $types as $type ){
                array_push($data, self::get_current_operational_performance_by_type($type['type'], $id));
            }
            
            
        }
        
        return $data;
        
    }
    
    public static function get_current_operational_performance_by_type($type, $player_id){
        
        $query = "SELECT * FROM operational_performance 
                  WHERE round_id IN (
                      SELECT MAX(round_id) FROM operational_performance
                  )
                  AND type='$type'
                  AND player_id=$player_id
                  order by zone_id ASC";
                  
        $results = self::$db->query($query);
        
        $data = array('name' => $type, 'data' => array());
        
        if($results){
            
            $performance = $results->fetch_all(MYSQLI_ASSOC);
            
            foreach($performance as $row){
                //print_r( $row );
                array_push($data['data'], $row['reliability']);
            }
            
        }
        
        //print_r( $data );
        
        return $data;
        
        
        
    }
    
    public static function get_average_operational_performance_by_type_per_round($type, $player_id, $round_id){
        
        //echo "round: $round_id <br> player id: $player_id <br> type: $type <br>";
        $query = "SELECT AVG(reliability) average FROM operational_performance
                  WHERE round_id=$round_id
                  AND type='$type'
                  AND player_id=$player_id";
                  
        $results = self::$db->query($query);
        
        //print_r($results);
        
        $data = array('name' => $type, 'data' => array());
        
        if($results){
            
            $average = $results->fetch_all(MYSQLI_ASSOC);
            //print_r($average);
            return $average[0]['average'];
            
        }
        
        //print_r( $data );
        
        return 0;
        
        
        
    }
    
    public static function get_historical_operational_performance($player_id){
        
        $rounds = self::get_rounds();
        
        $types = self::get_reliability_types();
        
        $data = array(); 
        
        foreach($types as $type){
            $temp = array();
            foreach($rounds as $round){
                array_push( $temp, self::get_average_operational_performance_by_type_per_round($type['type'], $player_id, $round));
            }
            array_push($data, array('name' => $type['type'], 'data' => $temp));
        }
        
        return $data;
    }
    
    public static function get_reliability_types(){
        
        $query = "SELECT DISTINCT type FROM operational_performance";
        
        $results = self::$db->query($query);
        
        $data = array();
        
        if( $results ){
            
            $types = $results->fetch_all(MYSQLI_ASSOC);
            
            foreach($types as $type){
                array_push($data, $type);
            }
            
        }
        
        return $data;
        
    }
    
    public static function get_operational_zones(){
        $query = "SELECT DISTINCT zone_id FROM operational_performance"; 
        
        $results = self::$db->query($query);
        
        $data = array();
        
        if($results){
            $zones = $results->fetch_all(MYSQLI_ASSOC);
            foreach( $zones as $zone){
                array_push( $data, (int) $zone['zone_id']);
            }
        }
        
        return $data; 
    }
    
    public static function get_current_environmental_performance(){
        
        $round = self::get_current_round();
        //echo "round: $round <br>";
        
        $co2 = self::get_current_emissions_by_pollutant('carbon_dioxide', $round);
        $co = self::get_current_emissions_by_pollutant('carbon_monoxide', $round);
        $no = self::get_current_emissions_by_pollutant('nitrous_oxide', $round);
        $so2 = self::get_current_emissions_by_pollutant('sulfur_dioxide', $round);
        
        return array($co2, $co, $no, $so2);
        
        
        
                
    }
    
    public static function get_current_emissions_by_pollutant($pollutant, $round){
        
        $query = "SELECT $pollutant FROM environmental_performance where round_id IN (
                    SELECT MAX(round_id) FROM environmental_performance
                  )
                  ORDER BY zone_id ASC";
        //echo $query . "<br>";
        $results = self::$db->query($query);
        //print_r($results);
        $data= array('name' => $pollutant, 'data' => array());
        
        if( $results ){
            $emissions = $results->fetch_all(MYSQLI_ASSOC);
            
            //print_r($emissions);
            
            foreach( $emissions as $emission){
                array_push($data['data'], $emission[$pollutant]);
            }
        }
        
        return $data;
        
        
    }
    
    public static function get_average_environmental_performance_by_pollutant($pollutant, $player_id){
        
        //echo "round: $round_id <br> player id: $player_id <br> type: $type <br>";
        $query = "SELECT AVG($pollutant) average FROM environmental_performance
                  WHERE player_id=$player_id
                  GROUP BY round_id
                  ORDER BY round_id ASC";
                  
        $results = self::$db->query($query);
        
        //print_r($results);
        
        $data = array('name' => $pollutant, 'data' => array());
        if($results){
            
            $averages = $results->fetch_all(MYSQLI_ASSOC);
            //print_r($average);
            foreach($averages as $average){
                array_push($data['data'], $average['average']);
            }
            
        }
        
        return $data;
        
        
        
    }
    

    
    
    public static function get_historical_environmental_performance($player_id){
        
        $co2 = self::get_average_environmental_performance_by_pollutant("carbon_dioxide", $player_id);
        $co = self::get_average_environmental_performance_by_pollutant("carbon_monoxide", $player_id);
        $no = self::get_average_environmental_performance_by_pollutant("nitrous_oxide", $player_id);
        $so2 = self::get_average_environmental_performance_by_pollutant("sulfur_dioxide", $player_id);
        
        return array($co2, $co, $no, $so2);
        
    }
    
    public static function get_player_name($player_id){
        
        $query = "SELECT * FROM users WHERE user_id=$player_id";
        $result = self::$db->query($query);
        
        if($result){
            return $result->fetch_all()[0][2];
        }
        
        
    }
    
    public static function get_player_id_from_name($name){
        $query = "SELECT * FROM users where username='$name'";
        
        $result = self::$db->query($query);
        
        if( $result ){
            return $result->fetch_all()[0][0];
        }
        
        
    }
    
    
    public static function get_fuel_forecast_by_type($type){
        
        //print_r($type);
        $query = "SELECT cost FROM fuel_forecast WHERE fuel='$type' ORDER BY year ASC";
        //echo $query . "<br>";
        $result = self::$db->query($query);
        //print_r($result);
        $data = array();
        //$return_val = array();
        
        if($result){
            $costs = $result->fetch_all(MYSQLI_ASSOC);
            
            foreach($costs as $cost){
                array_push($data, (float) $cost['cost']);
            }
        }
        
        //print_r($data);
        return array('name' => $type, 'data' => $data);
        
        
        
    }
    
    public static function get_fuel_forecast(){
        
        $query = "SELECT DISTINCT fuel FROM fuel_forecast";
        
        $result = self::$db->query($query);
        
        $data = array();
        
        if($result){
            $types = $result->fetch_all(MYSQLI_ASSOC);
            
            foreach($types as $type){
                array_push($data, self::get_fuel_forecast_by_type($type['fuel']));
            }
        }
        
        return json_encode($data);
        
    }
    
    public static function get_fuel_forecast_years(){
        
        $query = "SELECT DISTINCT year FROM fuel_forecast ORDER BY year ASC;";
        
        $result = self::$db->query($query);
        $data = array();
        
        if($result){
            $years = $result->fetch_all(MYSQLI_ASSOC);
            
            foreach($years as $year){
                array_push($data, (int) $year['year']);
            }
        }
        
        return json_encode($data);
        
        
    }
}

Database::instance($hn, $un, $pw, $db);

?>