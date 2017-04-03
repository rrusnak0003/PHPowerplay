<?php

class Database{
    
    //hostname, username, password, database 
    protected static $db;
    protected static $_instance = null;
    
    public static function instance(){
        
        if(is_null( self::$_instance)){
            self::$_instance = new static();
            self::$db  = new mysqli('localhost', 'dheesch', '', 'powerplay-test');
            
            if(self::$db->connect_error) die($db->connect_error);
        }
        
    }
    
    public static function get_current_round(){
        $query = "SELECT MAX(round_id) from economic_performance";
        self::$db->query($query);
        
        if($result){
            return $result->fetch_all()[0][0];
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
                  and player_id=1";
        $result = self::$db->query($query);
        
        if( $result ){
            return $result->fetch_all(MYSQLI_ASSOC);
        }
    }
    
    public static function get_econ_cost_by_tech($tech){
        
        $query = "SELECT round_id, tech_type, cost
                  FROM economic_performance
                  WHERE player_id=1 and tech_type='$tech'";
                  
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
    
    public static function get_econ_ror_by_tech($tech){
        
        $query = "SELECT round_id, tech_type, rate_of_return
                  FROM economic_performance
                  WHERE player_id=1 and tech_type='$tech'";
                  
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
    
    public static function get_all_econ_costs(){
        
        $query = "SELECT DISTINCT tech_type FROM economic_performance";
        
        $result = self::$db->query($query);
        
        $data = array();
        
        if($result){
            $tech_types = $result->fetch_all(MYSQLI_ASSOC);
            
            foreach( $tech_types as $tech_type ){
                array_push($data, self::get_econ_cost_by_tech($tech_type['tech_type']));
            }
            
        }
        
        //print_r($data);
        return $data;
        
    }
    
    public static function get_all_econ_ror(){
        
        $query = "SELECT DISTINCT tech_type FROM economic_performance";
        
        $result = self::$db->query($query);
        
        $data = array();
        
        if($result){
            $tech_types = $result->fetch_all(MYSQLI_ASSOC);
            
            foreach($tech_types as $tech_type){
                array_push( $data, self::get_econ_ror_by_tech($tech_type['tech_type']));
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
    
    public static function get_current_operational_performance(){
        return;
    }
    
    public static function get_current_environmental_performance(){
        
    }
    
    public static function get_historical_economic_performance(){
        
    }
    
    public static function get_historical_operational_performance(){
        
    }
    
    public static function get_historical_environmental_performance(){
        
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
    
    
    
    
}

Database::instance();

?>