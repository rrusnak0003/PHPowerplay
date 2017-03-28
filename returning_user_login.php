<?php 
    /**
    *** @param $role - the role of the user 
    */ 
    function redirect($role){
        
        if($role == "admin"){
            header("Location: admin/index.php");
        }
        else if($role == "analyst"){
            header("Location: analyst/index.php");
        }
        else if($role =="player"){
            header("Location: player/index.php");
        }
        else{
            echo "hmmmmmmm that role isn't recognized<br>";
        }
    }
    function verifyUser($db){
        
        $username = $_GET['returning_user_name'];
        $password = md5($_GET['returning_user_password']);
        $query = "SELECT * FROM users WHERE username='$username' and password='$password'";
        //echo $query . "<br>";
        $users = $db->query($query);
        
        if($users->num_rows == 1){
            $user = $users->fetch_array(MYSQLI_NUM);
            //print_r($user);
            //so only one record exists in users 
            //$user[1] has the role of the user logging in 
            echo $user[1] . "<br>";
            redirect($user[1]);
        }
        else {
            echo "user not found";
        }
        
    }
    require('login.php');
    
    $db = new mysqli($hn, $un, $pw, $db);
    
    if($db->connect_error) die($db->connect_error);
    
    verifyUser($db);
?>