<?php

require_once('../login.php');

class Helpers{
    
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
    
    public static function format_current_economic_performance_data($data){
        
        $tech = array();
        $cost = array();
        $rate_of_return = array();
        foreach($data as $row){
            //print_r($row);
            array_push($tech, $row['tech_type']);
            array_push($cost, $row['cost']);
            array_push($rate_of_return, $row['rate_of_return']);
        }
        
        $results = array($tech, $cost, $rate_of_return);
        return $results;
    }
    
    public static function format_previous_economic_performance_data($data, $round_id){
        
        $rounds = array();
        
        for($i = 0; $i <= $round_id; $i++){
            array_push($rounds, $i);    
        }
        
        
        
    }
    
    
    
}

Helpers::instance($hn, $un, $pw, $db);

?>