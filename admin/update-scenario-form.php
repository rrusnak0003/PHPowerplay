<?php
require_once("../includes/database.php");

require('../login.php');

$db = new mysqli($hn, $un, $pw, $db);

if($db->connect_error) die($db->connect_error);




$query = "select * from technology_cost where technology='Onshore Wind' AND year =2016 limit 1";
$result = $db->query($query);
$row = $result->fetch_array(MYSQLI_BOTH);
$test= $db->fetch_array;


$current_fixcost = $row['fixed_cost'];
$current_varcost = $row['variable_cost'];
$current_capcost = $row['overnight_capital'];
$current_nomcap = $row['nominal_capacity'];
$current_mincap = $row['min_capacity'];
$current_maxcap = $row['max_capacity'];
$current_coemissions = $row['carbon_dioxide'];

$query = "select * from technology_cost where technology='Onshore Wind' AND year =2021 limit 1";
$result = $db->query($query);
$row = $result->fetch_array(MYSQLI_BOTH);
$test= $db->fetch_array;

$current_fixcost5y = $row['fixed_cost'];
$current_varcost5y = $row['variable_cost'];
$current_capcost5y = $row['overnight_capital'];
$current_nomcap5y = $row['nominal_capacity'];
$current_mincap5y = $row['min_capacity'];
$current_maxcap5y = $row['max_capacity'];
$current_coemissions5y = $row['carbon_dioxide'];

?>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<style>.form-control {
        min-width: 0;
        width: 200px;

    }</style>



<form method="GET" action="update-scenario-form-submit.php" id="editscenarioform">
    <div class="panel panel-default">
        <div class="panel-heading"> Wind Variables </div>
        <div class="panel-body">
            <div class="form-group">
                <label for="fixedcost"> Fixed cost </label>
                <input  type="number" id="fixedcost" class="form-control" name="fixed_cost"  required></input>
                <small  class="form-text text-muted">Current value: <?php echo ($current_fixcost); ?></small>
            </div>
            <div class="form-group">
                <label for="varcost"> Variable cost</label>
                <input  type="number" id="varcost" class="form-control" name="variable_cost"  required></input>
                <small  class="form-text text-muted">Current value: <?php echo ($current_varcost); ?></small>
            </div>


            <div class="form-group">
                <label> Capital cost</label>
                <input  type="number" class="form-control" name="capital_cost"  required></input>
                <small  class="form-text text-muted">Current value: <?php echo ($current_capcost); ?></small>
            </div>

            <div class="form-group">
                <label> Nominal capacity</label>
                <input  type="number" class="form-control" name="nominal_capacity"  required></input>
                <small  class="form-text text-muted">Current value: <?php echo ($current_nomcap); ?></small>
            </div>

            <div class="form-group">
                <label> Minimum capacity </label>
                <input  type="number" class="form-control" name="min_capacity"  required></input>
                <small  class="form-text text-muted">Current value: <?php echo ($current_mincap); ?></small>
            </div>

            <div class="form-group">
                <label> Maximum capacity </label>
                <input  type="number" class="form-control" name="max_capacity"  required></input>
                <small  class="form-text text-muted">Current value: <?php echo ($current_maxcap); ?></small>
            </div>

            <div class="form-group">
                <label> CO2 emissions </label>
                <input  type="number" class="form-control" name="co_emissions"  required></input>
                <small  class="form-text text-muted">Current value: <?php echo ($current_coemissions); ?></small>
            </div>
        </div>
    </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading"> Wind Variables 5 Years from present</div>
        <div class="panel-body">

            <div class="form-group">
                <label> Fixed cost  </label>
                <input  type="number" class="form-control" name="fixed_cost5yr"  required></input>
                <small  class="form-text text-muted">Current value: <?php echo ($current_fixcost5y); ?></small>
            </div>
            <div class="form-group">
                <label> Variable cost </label>
                <input  type="number" class="form-control" name="variable_cost5yr"  required></input>
                <small  class="form-text text-muted">Current value: <?php echo ($current_varcost5y); ?></small>
            </div>
            <div class="form-group">
                <label> Capital cost </label>
                <input  type="number" class="form-control" name="capital_cost5yr"  required></input>
                <small  class="form-text text-muted">Current value: <?php echo ($current_capcost5y); ?></small>
            </div>
            <div class="form-group">
                <label> Nominal capacity </label>
                <input  type="number" class="form-control" name="nominal_capacity5yr"  required></input>
                <small  class="form-text text-muted">Current value: <?php echo ($current_nomcap5y); ?></small>
            </div>
            <div class="form-group">
                <label> Minimum capacity  </label>
                <input  type="number" class="form-control" name="min_capacity5yr"  required></input>
                <small  class="form-text text-muted">Current value: <?php echo ($current_mincap5y); ?></small>
            </div>
            <div class="form-group">
                <label> Maximum capacity  </label>
                <input  type="number" class="form-control" name="max_capacity5yr"  required></input>
                <small  class="form-text text-muted">Current value: <?php echo ($current_maxcap5y); ?></small>
            </div>
            <div class="form-group">
                <label> CO2 emissions  </label>
                <input  type="number" class="form-control" name="co_emissions5yr"  required></input>
                <small  class="form-text text-muted">Current value: <?php echo ($current_coemissions5y); ?></small>
            </div>
        </div>
    </div>
    </div>
    </br>
    <button class="btn btn-success" type="submit">Submit</button>

</form>

