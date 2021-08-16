<?php
/*====================================================================================================================================================================
* login.php *
On click of the login button or after a successful signup, the user will be directed here.
This file contains all the code relating to the account creation process.
User will be an instance of the class User, calling upon methods appropriate to that class and event.
The HTML error messages are in response to the validation rules that have been set
Session Start is used to temporarily hold the user's account as a session
====================================================================================================================================================================*/

include 'classes.php';
session_start();

// USER INSTANCE
$User = new Users(@$_POST['username'], @$_POST['password']);

// [METHOD CALL]
if(isset($_POST['submit'])){
  $User->LoginValidity();
}
?>

<!-- HTML -->
<!DOCTYPE html>
<html lang="en" dir="ltr">
   <head>
      <meta charset="utf-8">
      <title>Login</title>
      <link rel="stylesheet" href="CSS/formstyle.css" type="text/css">
      <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
   </head>
   <body>
      <div id="box">
         <h1>LOGIN</h1>
         <!-- LOGIN FORM -->
         <form class="login-form" action="<?=$_SERVER['PHP_SELF']?>" method="post" autocomplete="off">
            <p1>Enter username:</p1>
            <br><input type="text" name="username" value="">
            <br></br>
            <p1>Enter password:</p1>
            <br><input type="password" name="password" value="">
            <br></br>
            <button type="submit" name="submit">Login</button>
            <br></br>
            <br>
            <p2>Enter existing account details</p2>
            <br>
            <br>
            <?php
               // ERROR MESSAGES
               if(isset($_GET["error"])){
                 if($_GET["error"] == "emptyinput"){
                   echo "<p3>Fields must not be left empty</p3>";
                 }
                 else if ($_GET["error"] == "invaliddetails") {
                   echo "<p3>Incorrect username or password</p3>";
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
