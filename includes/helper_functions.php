<?php

class Helpers{
    
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
    
    public static function format_operational_performance_data($data){
        
        $tech = array();
        $cost = array();
        $rate_of_return = array();
        foreach($data as $row){
            print_r($row);
            array_push($tech, $row['tech_type']);
            array_push($cost, $row['cost']);
            array_push($rate_of_return, $row['rate_of_return']);
        }
        
        $results = array($tech, $cost, $rate_of_return);
        return $results;
    }
    
    
    
}

Helpers::instance();

?>