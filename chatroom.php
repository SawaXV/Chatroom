<?php
/*====================================================================================================================================================================
* chatroom.php *
On successful login the user will be directed here.
This file contains all the code relating to the bulk of the project, the chatroom itself.
User will now be an instance of the class LoggedUser, calling upon methods appropriate to that class and event.
!! For the actual output of Messages and Online Users, see the HTML below !!
====================================================================================================================================================================*/

session_start();
include 'dbh.php';
include 'classes.php';

// VARIABLES
$LoggedUser = $_SESSION['Username'];

// SQL
$SQL_UserID = mysqli_query($newConnection, "SELECT UserID FROM users WHERE Username = '$LoggedUser'");
while ($Row = mysqli_fetch_array($SQL_UserID)) {
  $userID[] = $Row['UserID'];
}

// USER INSTANCE
$User = new LoggedUsers($LoggedUser, $userID[0]);

// [METHOD CALL]
// SEND MESSAGE
$Message = @$_POST["chatbox"];
if(isset($_POST['submit'])){
  if(empty($Message)){
    header("location: /chatserver/chatroom.php"); // Empty message was sent, do nothing
    exit();
  }
  else{
    $User->SendMessage($Message);
  }
}

// DISPLAY MESSAGES
$Messages = $User->DisplayMessages();

// DEFAULT PFP
$User->SetDefaultPFP();

// DISPLAY PFP
$SidePFP = $User->DisplayPFP();

// UPLOAD PFP
if(isset($_POST['profilepic'])){
  header("location: /chatserver/chatroom.php?action=changeprofilepicture");
  exit();
}
if(isset($_POST['confirm'])){
  $User->UploadPFP();
}

// ONLINE USERS
$List = $User->DisplayOnlineUsers();

// LOGOUT
if(isset($_POST['logout'])){
  $User->Logout();
}
?>

<!-- HTML -->
<!DOCTYPE html>
<html lang="en" dir="ltr">
   <head>
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
      <link rel="stylesheet" href="CSS/chatroomstyle.css" type="text/css">
      <link rel="shortcut icon" href="#">
      <title>Chatroom</title>
   </head>
   <body>
      <!-- CHATLOG -->
      <div id="log">
         <p id="messages">
            <?php
            // Fetch messages and output them for each line
               while($MessageRow = mysqli_fetch_array($Messages)){
                 echo "<img id='iconPFP' src='Images/".$MessageRow['Image']."'>";
                 echo "<p id='time'>[{$MessageRow['Timestamp']}]";
                 echo "<p id='name'>{$MessageRow['Username']}<p>";
                 echo "<p id='message'>{$MessageRow['Message']}</p>";
               }
            ?>
         </p>
      </div>
      <!-- REFRESH-->
      <script type="text/javascript">


         // Set refresh timer (Chatlog)
         var Refresh = setInterval('window.location.reload()', 7000);
         $('#log').on('refresh', function () {
         	if ($(this).val().length == '') {
         		clearInterval(Refresh);
         		Refresh = setInterval('window.location.reload()', 7000);
         	} else {
         		clearInterval(Refresh);
         	}
         });
         // Scroll on refresh
         document.addEventListener('DOMContentLoaded', function () {
         	var messages = document.querySelector('#log');
         	messages.scrollTop = messages.scrollHeight;
         });
         // Set refresh timer (Online)
         RefreshList();
         function RefreshList() {
           setInterval(function() {
             $('#onlinelist').load('chatroom.php, #onlinelist');
           }, 7000);
         }



      </script>
      <!-- USER TAB -->
      <div id="usertab">
         <p id="usertitle">User tab</p>
         <div>
            <form action="<?=$_SERVER['PHP_SELF']?>" method="post">
               <button id="profilepic" type="submit" name="profilepic">Change profile picture</button>
            </form>
            <form id="change" action="<?=$_SERVER['PHP_SELF']?>" method="post" enctype="multipart/form-data">
               <?php
                  // Execute method if user submitted an image
                  if(isset($_GET['action'])){
                   if($_GET['action'] == "changeprofilepicture"){
                      echo "<input type='file' name='image' accept='image/*'> <button id='confirm' type='submit' name='confirm'>Confirm</button>";
                    }
                  }
                ?>
            </form>
            <?php
               // Get the user's image file to be displayed as a preview
               while($imageRow = mysqli_fetch_array($SidePFP)){
                 echo "<div>";
                 echo "<img id='sidePFP' src='Images/".$imageRow['Image']."'";
                 echo "</div>";
               }
               // ERRORS
               if(isset($_GET["error"])){
                 if($_GET["error"] == "invalidpfp"){
                   echo "<p id='usererror'>Image must be either a .png or .jpg</p>";
                 }
             }
                ?>
            <p id="sidename"><?=$User->Username?></p>
         </div>
      </div>
      <!-- ONLINE TAB -->
      <div id="onlinelist">
         <p id="onlinetitle">Online users</p>
         <?php
            // Output a list of the online users
            while($list = mysqli_fetch_array($List)){
              echo "<p id='onlineuser'>❯❯ {$list['Username']}</p>";
            }
             ?>
         <form action="<?=$_SERVER['PHP_SELF']?>" method="post">
            <button id="logout" type="submit" name="logout">Logout</button>
         </form>
      </div>
      <!-- MESSAGE INPUT BOX -->
      <div id="chatboxoutline">
         <form action="<?=$_SERVER['PHP_SELF']?>" method="post">
            <textarea id="text" rows="8" cols="80" method="post" name="chatbox"></textarea>
            <p id="notice">Make sure to click "Logout" when leaving!!</p> <!-- !! Inform user on logging out to set online status to 0 -->
            <button id="submit" type="submit" name="submit">Send ❯❯</button>
         </form>
      </div>
   </body>
</html>
