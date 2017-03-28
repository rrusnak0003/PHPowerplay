<!DOCTYPE html>
<html >
<head>
  <meta charset="UTF-8">
  <title>PowerPlay</title>
  
  
  <link rel='stylesheet prefetch' href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha.6/css/bootstrap.min.css'>

  <link rel="stylesheet" href="assets/css/powerplay.css">

  
</head>

<body>
  <div id="login-wrapper">
  <div id="login-form">
    
    <form action="#" id="returning_user_form">
      <h2> Login </h2>
      <input type="text" name="returning_user_name" placeholder="user name" required/>
      <br>
      <input type="text" name="returning_user_password" placeholder="password" required>
      <br>
      <button class="btn btn-success" type="submit">Submit</button>
      
    </form>
    
    <form action="#" id="new_user_form">
      <h2> New User? </h2>
      <input type="text" name="new_user_name" placeholder="user name" required/>
      <br>
      <input type="password" name="new_user_password" placeholder="password" required/>
      <br>
      <input type="password" name="new_user_confirm_password" placeholder="confirm password" required/> 
      <br>
      <input type="radio" value="player" name="role" checked> Player </input>
      <input type="radio" value="analyst" name="role"> Analyst </input>
      <input type="radio" value="admin" name="role"> Admin </input>
      <br>
      <button class="btn btn-success" type="submit">Submit</button>
    </form>
    
    
  </div>
</div>
  
  
</body>
</html>