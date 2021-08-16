<?php
/*====================================================================================================================================================================
* dbh.php *
This file contains the main variables Connection and NewConnection which are connect queries that will be used in other files for query execution.
Database queries are also included here which will create the tables.
====================================================================================================================================================================*/

// [CONNECTIONS]
// Initial user access
$serverName = "localhost";
$Username = "root";
$Password = "";
$dbName = "chatroomdb";
$connection = mysqli_connect($serverName, $Username, $Password, $dbName); // Allows for execution of queries pre-chatroom
if(!$connection){
  echo "Cannot connect to database - " . mysqli_connect_error();
}

// Logged user access (Read/Write)
$loggedUser = "siteUser";
$newPassword = "iFDqje7e9St69L02"; // Randomly generated password to access the SQL account
$newConnection = mysqli_connect($serverName, $loggedUser, $newPassword, $dbName); // Allows for execution of queries post-chatroom
if(!$newConnection){
  echo "Cannot connect to database - " . mysqli_connect_error();
}

// [TABLES]
// Creation
$usersTable = "CREATE TABLE Users(
  UserID int NOT NULL PRIMARY KEY AUTO_INCREMENT,
  Username varchar(30) NOT NULL,
  Password varchar(255) NOT NULL
);";

$messagesTable = "CREATE TABLE Messages(
  MsgID int NOT NULL PRIMARY KEY AUTO_INCREMENT,
  Message varchar(300),
  Timestamp timestamp NOT NULL
);";

$usermsgTable = "CREATE TABLE UserMsg(
  UserID INT,
  MsgID INT,
  ImageID INT,
  PRIMARY KEY (UserID, MsgID, ImageID),
  FOREIGN KEY (UserID) REFERENCES users(UserID),
  FOREIGN KEY (MsgID) REFERENCES messages(MsgID),
  FOREIGN KEY (ImageID) REFERENCES profileimage(ImageID)
);";

$profileimageTable = "CREATE TABLE ProfileImage(
  ImageID INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  UserID INT,
  Image varchar(),
  FOREIGN KEY (UserID) REFERENCES users(UserID)
);";

$onlineTable = "CREATE TABLE online (
      isOnline INT NOT NULL,
      UserID INT NOT NULL,
      Username VARCHAR(300) NOT NULL,
      FOREIGN KEY (UserID) REFERENCES users(UserID),
      PRIMARY KEY (isOnline, UserID)
);";

// Execution
try{
  mysqli_query($connection, $usersTable);
  try{
    mysqli_query($connection, $messagesTable);
    try{
      mysqli_query($connection, $usermsgTable);
      try{
        mysqli_query($connection, $profileimageTable);
        try{
          mysqli_query($connection, $onlineTable);
        }
        catch(Exception $e){
          echo "Could not create Online table";
        }
      }
      catch(Exception $e){
        echo "Could not create ProfileImage table";
      }
    }
    catch(Exception $e){
      echo "Could not create UserMsg table";
    }
  }
  catch(Exception $e){
    echo "Could not create Messages table";
  }
}
catch(Exception $e){
  echo "Could not create Users table";
}


?>
