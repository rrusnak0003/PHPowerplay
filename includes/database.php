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