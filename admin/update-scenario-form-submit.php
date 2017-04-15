<?php

require_once("../includes/database.php");
session_start();

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
echo("fixcost" + $fixcost);



function findTech($db, $year){



    $query = "select * from technology_cost where technology='Solar' AND year =$year";

    $tech = $db->query($query);


    if($tech->num_rows == 0){
        return true;
    }
    else {
        return false;
    }

}












require('../login.php');

$db = new mysqli($hn, $un, $pw, $db);

if($db->connect_error)
{

    die($db->connect_error);

}
if(findTech($db, 2016) && ($mincap<$maxcap) ){
    $query = "INSERT INTO technology_cost (year, technology, technical, overnight_capital, fixed_cost, variable_cost, min_capacity, nominal_capacity, max_capacity,  carbon_dioxide)
                  VALUES (2016, 'Solar', 'Photovoltaic - Fixed', $capcost, $fixcost,$varcost, $mincap, $nomcap,$maxcap, $coemissions )";
    $insert = $db->query($query);

}
elseif ($mincap<$maxcap){
    $query = "UPDATE technology_cost 
                SET year = 2016, technology = 'Solar', technical = 'Onshore Wind (WN)', overnight_capital = $capcost, fixed_cost = $fixcost, variable_cost = $varcost, min_capacity = $mincap, max_capacity = $maxcap, nominal_capacity =  $nomcap, carbon_dioxide= $coemissions
                WHERE year = 2016 AND  technology='Solar'";
    $insert = $db->query($query);

}
else{
    $_SESSION['error'] = 'Error: Changes not made, make sure maximum > minimum capacity';

}


if(findTech($db, 2021) && ($mincap5yr<$maxcap5yr)){
    $query = "INSERT INTO technology_cost (year, technology, technical, overnight_capital, fixed_cost, variable_cost, min_capacity, nominal_capacity, max_capacity,  carbon_dioxide)
                  VALUES (2021, 'Solar', 'Photovoltaic - Fixed', $capcost5yr, $fixcost5yr,$varcost5yr, $mincap5yr, $nomcap5yr,$maxcap5yr, $coemissions5yr )";
    $insert = $db->query($query);

}
elseif ($mincap5yr<$maxcap5yr){
    $query = "UPDATE technology_cost 
                SET year = 2021, technology = 'Solar', technical = 'Photovoltaic - Fixed', overnight_capital = $capcost5yr, fixed_cost = $fixcost5yr, variable_cost = $varcost5yr, min_capacity = $mincap5yr, max_capacity = $maxcap5yr, nominal_capacity =  $nomcap5yr, carbon_dioxide= $coemissions5yr
                WHERE year = 2021 AND  technology='Solar'";
    $insert = $db->query($query);

}
else{
    $_SESSION['error'] = 'Error: Changes not made to five year variables, make sure maximum > minimum capacity';
}

header('Location: ./update-scenario-form.php');
?>