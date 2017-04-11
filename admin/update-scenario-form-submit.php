<?php

require_once("../includes/database.php");

$fixcost = $_GET['fixed_cost'];
$varcost = $_GET['variable_cost'];
$capcost = $_GET['capital_cost'];
$nomcap = $_GET['nominal_capacity'];
$mincap = $_GET['min_capacity'];
$maxcap = $_GET['max_capacity'];
$coemissions = $_GET['co_emissions'];

$fixcost5yr = $_GET['fixed_cost5yr'];
$varcost5yr = $_GET['variable_cost5yr'];
$capcost5yr = $_GET['capital_cost5yr'];
$nomcap5yr = $_GET['nominal_capacity5yr'];
$mincap5yr = $_GET['min_capacity5yr'];
$maxcap5yr = $_GET['max_capacity5yr'];
$coemissions5yr = $_GET['co_emissions5yr'];




function findTech($db, $year){



    $query = "select * from technology_cost where technology='Onshore Wind' AND year =$year";

    $tech = $db->query($query);

    /*
    ** users is a mysqli_result object
    ** so if num_rows of the object is 0 there isn't a match
    */
    if($tech->num_rows == 0){
        return true;
    }
    else {
        return false;
    }

}



function editScenario($db){



    if(findTech($db, 2016)){
        $query = "INSERT INTO technology_cost (year, technology, technical, overnight_capital, fixed_cost, variable_cost, min_capacity, nominal_capacity, carbon_dioxide)
                  VALUES (2016, 'Onshore Wind', 'Onshore Wind (WN)', 0, $fixcost,$varcost, $mincap, $nomcap, $coemissions )";
        $insert = $db->query($query);
    }
    else {
        $query = "UPDATE technology_cost 
                SET year = 2016, technology = 'Onshore_Wind', technical = 'Onshore Wind (WN)', overnight_capital = 0, fixed_cost = $fixcost, variable_cost = $varcost, min_capacity = $mincap, nominal_capacity =  $nomcap, carbon_dioxide= $coemissions
                WHERE year = 2016 AND  technology='Onshore Wind'";
        $insert = $db->query($query);
    }


}


?>