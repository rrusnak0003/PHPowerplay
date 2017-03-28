# PHPowerplay

![alt text](https://www.amli.com/blog/wp-content/uploads/2013/06/MemeGeneratorNet.jpg "Logo Title Text 1")

Who thought Python would be tough? This is a rewrite of PowerPlay in good ol' fashioned LAMP

## Break down of File Structure
* /index.php - holds the login page 
    * login page has a form for new users and returning users 
    * returning users submit user name and password 
    * new users create a username and password, confirm password, and specify whether they are an admin, player, or analyst
* /returning_user_login.php
    * check to make sure the credentials are correct  
    * if the credentials are correct 
        * and the user is an admin
            * redirect to /admin/index.php
        * and the user is an analyst
            * redirect to /analyst/index.php
        * and the user is a player 
            * redirect to /player/index.php
    * if the credentials are incorrect 
        * redirect to index.php
* /new_user_login.php
    * check to make sure user name doesn't exist - if it does redirect to /index.php
    * check to make sure password and confirm password match - if they don't redirect to index.php 
    * if everything checks out insert that user into the DB 
    * if the user registered as an admin - redirect to /admin/index.php
    * if the user registered as an analyst - redirect to /analyst/index.php
    * if the user registered as a player - redirect to /player/index.php
* /admin/index.php - holds the admin panel
* /analyst/index.php - holds the analyst page 
* /player/index.php - holds the player page 
* /includes/ - holds helper php files 
* /tests/ - if you couldn't tell - your tests go here 

## Things to Know 

* Node and NPM
    * This project uses Node and NPM - install Node and then install NPM (Node Package Manager)
    * Specifically, it uses grunt-sass to compile powerplay.scss into powerplay.css 
* CSS 
    * SCSS - Syntactically Correct Style Sheets 
    * SCSS is a CSS Preprocessor that adds some additional features to CSS 
        * you don't have to use the advanced features to use SCSS 
    * To add a SCSS file to the project 
        * create yourfile.scss in assets/scss/
        * add this to powerplay.scss
            * @import "yourfile"; 
        * then run Grunt and that CSS will be added to powerplay.css - the master css file for the project 
    * if you'd prefer to avoid the whole grunt and scss thing make your own css files in assets/css 
        * just be aware you cannot modify powerplay.css without using scss
        * powerplay.css gets created and update via grunt so any changes made directly to powerplay.css will be overwritten
    * Grunting 
        * if you make a change to a .scss file run this command 
            * grunt OR grunt -v from the command line 
* JS will eventually work the same way - or not depending on how close we get to the deadline 