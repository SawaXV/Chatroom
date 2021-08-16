<?php
/*====================================================================================================================================================================
* signup.php *
On click of the signup button, the user will be directed here.
This file contains all the code relating to the account creation process.
User will be an instance of the class User, calling upon methods appropriate to that class and event.
The HTML error messages are in response to the validation rules that have been set
====================================================================================================================================================================*/

include 'classes.php';

// USER INSTANCE
$User = new Users(@trim($_POST['username']), @$_POST['password']);

// [METHOD CALL]
if(isset($_POST['submit'])){
  $User->SignUpValidity();
  exit();
}
?>

<!-- HTML -->
<!DOCTYPE html>
<html lang="en" dir="ltr">
   <head>
      <meta charset="utf-8">
      <title>Sign up</title>
      <link rel="stylesheet" href="CSS/formstyle.css" type="text/css">
      <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
   </head>
   <body>
      <div id="box">
         <h1>Sign Up</h1>
         <!-- SIGNUP FORM -->
         <form class="signup-form" action="<?=$_SERVER['PHP_SELF']?>" method="post" autocomplete="off">
            <p1>Enter a username:</p1>
            <br><input type="text" name="username">
            <br></br>
            <p1>Enter a password:</p1>
            <br><input type="password" name="password">
            <br></br>
            <button type="submit" name="submit">Sign Up</button>
            <br>
            <p2>Password: <br> * 8 <u>characters</u> or more<br> * One or more <u>uppercase</u> and <u>lowercase character(s)</u><br> * One or more <u>number(s)</u></p2>
            <br>
            <br>
            <?php
               // ERROR MESSAGES
               if(isset($_GET["error"])){
                 if($_GET["error"] == "emptyinput"){
                   echo "<p3>Fields must not be left empty</p3>";
                 }
                 else if ($_GET["error"] == "usernametaken") {
                   echo "<p3>Username is taken</p3>";
                 }
                 else if ($_GET["error"] == "invalidpassword") {
                   echo "<p3>Password must be at least 8 characters and have one uppercase and lowercase letter and a number</p3>";
                 }
               }
               ?>
         </form>
      </div>
      <!-- Box animation -->
      <script type="text/javascript">
         $("#box").animate({top:'25%'}, 500);
      </script>
   </body>
</html>
