<?php 
    
    require_once("includes/database.php");
    
    function isValidUserName($db){
        
        $username = $_GET['new_user_name'];
        
        $query = "select * from users where username='$username'";
        
        $users = $db->query($query);
        
        /*
        ** users is a mysqli_result object 
        ** so if num_rows of the object is 0 there isn't a match
        */
        if($user->num_rows == 0){
            return true;
        }
        else {
            return false;
        }
        
    }
    
    /**
    *** @param $role - the role of the user 
    */ 
    function redirect($role, $id){
        
        if($role == "admin"){
            header("Location: admin/index.php?player_id=$id");
        }
        else if($role == "analyst"){
            header("Location: analyst/index.php?player_id=$id");
        }
        else if($role =="player"){
            header("Location: player/index.php?player_id=$id");
        }
        else{
            echo "hmmmmmmm that role isn't recognized<br>";
        }
    }
    
    /**
     ** insert the user into the db if is a valid user name and passwords match 
     */
    function insertUser($db){
        
        $username = $_GET['new_user_name'];
        $password = $_GET['new_user_password'];
        $confirm_password = $_GET['new_user_confirm_password'];
        $email = $_GET['new_user_email_address'];
        $role = $_GET['role'];
        
        
        
        if(isValidUserName($db) && $password == $confirm_password){
            $query = "INSERT INTO users (role, username, password, email_address)
                      VALUES ('$role', '$username', MD5('$password'), '$email')";
            $insert = $db->query($query);
            
            if(!$insert){
                echo "Something has gone terribly wrong...<br>";
                print_r($insert);
            }
            else{
                $id = Database::get_player_id_from_name($username);
                redirect($role, $id);
            }
            
        }
        else{
            header("Location: index.php");
        }
        
    }
    
    require('login.php');
    
    $db = new mysqli($hn, $un, $pw, $db);
    
    if($db->connect_error) die($db->connect_error);
    
    insertUser($db);
    
    
    
?>

