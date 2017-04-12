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
echo("fixcost" + $fixcost);



function findTech($db, $year){



    $query = "select * from technology_cost where technology='Onshore Wind' AND year =$year";

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
if(findTech($db, 2016)){
    $query = "INSERT INTO technology_cost (year, technology, technical, overnight_capital, fixed_cost, variable_cost, min_capacity, nominal_capacity, max_capacity,  carbon_dioxide)
                  VALUES (2016, 'Onshore Wind', 'Onshore Wind (WN)', $capcost, $fixcost,$varcost, $mincap, $nomcap,$maxcap, $coemissions )";
    $insert = $db->query($query);

}
else {
    $query = "UPDATE technology_cost 
                SET year = 2016, technology = 'Onshore Wind', technical = 'Onshore Wind (WN)', overnight_capital = $capcost, fixed_cost = $fixcost, variable_cost = $varcost, min_capacity = $mincap, max_capacity = $maxcap, nominal_capacity =  $nomcap, carbon_dioxide= $coemissions
                WHERE year = 2016 AND  technology='Onshore Wind'";
    $insert = $db->query($query);

}


if(findTech($db, 2021)){
    $query = "INSERT INTO technology_cost (year, technology, technical, overnight_capital, fixed_cost, variable_cost, min_capacity, nominal_capacity, max_capacity,  carbon_dioxide)
                  VALUES (2021, 'Onshore Wind', 'Onshore Wind (WN)', $capcost5yr, $fixcost5yr,$varcost5yr, $mincap5yr, $nomcap5yr,$maxcap5yr, $coemissions5yr )";
    $insert = $db->query($query);

}
else {
    $query = "UPDATE technology_cost 
                SET year = 2021, technology = 'Onshore Wind', technical = 'Onshore Wind (WN)', overnight_capital = $capcost5yr, fixed_cost = $fixcost5yr, variable_cost = $varcost5yr, min_capacity = $mincap5yr, max_capacity = $maxcap5yr, nominal_capacity =  $nomcap5yr, carbon_dioxide= $coemissions5yr
                WHERE year = 2021 AND  technology='Onshore Wind'";
    $insert = $db->query($query);

}



header('Location: ./update-scenario-form.php');
?>