<!--========================================================================================================================================================
* index.php *
Page only serves as an introduction, main function belongs to other files.
 ========================================================================================================================================================-->
 
<!DOCTYPE html>
<html lang="en" dir="ltr">
   <head>
      <meta charset="utf-8">
      <link rel="stylesheet" href="CSS/indexstyle.css" type="text/css">
      <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script> <!-- Ajax script -->
      <title>chatroom</title>
   </head>
   <body>
      <div id="box">
         <h1>CHATROOM WEBSITE</h1>
         <!-- PAGE BUTTONS -->
         <p>
            <a href="signup.php" id="sign">Sign Up</a> <br>
            <a href="login.php" id="login">Login</a>
         </p>
      </div>
      <!-- Box animation -->
      <script type="text/javascript">
         $("#box").animate({top:'25%'}, 1000);
      </script>
   </body>
</html>
