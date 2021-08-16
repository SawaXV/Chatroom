<?php
/*====================================================================================================================================================================
* classes.php *
File holds the classes that are to be used.
====================================================================================================================================================================*/

class Users{
  // [PROPERTIES]
  public $Username;
  private $Password;

  // [CONSTRUCTOR]
  public function __construct($Username, $Password){
    $this->Username = $Username;
    $this->Password = $Password;
  }

  // [METHODS]
  function LoginValidity(){
    include ('dbh.php');

    // SQL
    $SQL_SelectPassword = mysqli_query($connection, "SELECT Password FROM users WHERE Username = '$this->Username'"); // Get the inputted user's password (If real)
    $SQL_PasswordRow = mysqli_fetch_array($SQL_SelectPassword); // Row password
    $SQL_UpdateOnline = mysqli_query($connection, "UPDATE online SET isOnline = 1 WHERE Username = '$this->Username'"); // Set online status to 1 to indicate they are online

    // If user passes all validation rules, log the user in and redirect them to the chatroom
    if(empty($this->Username) || empty($this->Password)){
      header('location: ../chatserver/login.php?error=emptyinput'); // ERROR - EMPTY FORM
      exit();
    }
    elseif(mysqli_num_rows($SQL_SelectPassword) > 0){
      if(password_verify($this->Password, $SQL_PasswordRow['Password'])){ // VERIFY PASSWORD
        try{
          $SQL_UpdateOnline;
          $_SESSION['Username'] = $this->Username;
          header("location: ../chatserver/chatroom.php"); // DIRECT USER TO CHATROOM
          $connection -> close();
          exit();
        }
        catch(Exception $e) {
          die("Error connecting with database - Online Status"); // ERROR - UPDATING ISONLINE
        }
      }
      else{
        header('location: ../chatserver/login.php?error=invaliddetails'); // ERROR - INCORRECT ACCOUNT INFO.
        exit();
      }
    }
    else{
      header('location: ../chatserver/login.php?error=invaliddetails'); // ERROR - ACCOUNT DOESN'T EXIST
      exit();
    }
  }
  function SignUpValidity(){
    include ("dbh.php");

    // VARIABLES
    $Regex = '/^\S*(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$/'; // At one or more lowercase, uppercase and numeric character
    $passHash = password_hash($this->Password, PASSWORD_DEFAULT); // Hash the password inputted

    // SQL
    $SQL_CheckIfUsernameExists = mysqli_query($connection, "SELECT Username FROM users WHERE Username = '$this->Username'"); // Check if username is already taken
    $SQL_AddToUsers = mysqli_query($connection, "INSERT INTO users(Username, Password) VALUES ('$this->Username', '$passHash')"); // Add the new user to the Users table
    $SQL_AddToOnline = mysqli_query($connection, "INSERT INTO online(UserID, Username) SELECT UserID, Username FROM Users WHERE users.Username = ('$this->Username')"); // Add the new user to the Online table
    $SQL_AddToProfile = mysqli_query($connection, "INSERT INTO profileimage(UserID) SELECT UserID FROM Users WHERE users.Username = ('$this->Username')"); // Add the new user to the profileimage table

    // If user passes all validation rules, sign up the user and redirect them to the login page
    if(empty($this->Username) || empty($this->Password)){
      return header("location: ../chatserver/signup.php?error=emptyinput"); // ERROR - EMPTY FORM
      exit();
    }
    elseif(mysqli_num_rows($SQL_CheckIfUsernameExists) > 0){
      return header("location: ../chatserver/signup.php?error=usernametaken"); // ERROR - USERNAME IS TAKEN
      exit();
    }
    elseif(strlen($this->Password) < 8 || !preg_match($Regex, $this->Password)){
      return header("location: ../chatserver/signup.php?error=invalidpassword"); // ERROR - PASSWORD ISN'T VALID
      exit();
    }
    else{
      try{
        $SQL_AddToUsers;
        try{
          $SQL_AddToOnline;
          try{
            $SQL_AddToProfile;
            header("location: login.php"); // DIRECT USER TO LOGIN PAGE
            $connection -> close();
            exit();
          }
          catch(Exception $e){
            die("Error connecting to database - Adding to Profile"); // ERROR - PROFILEIMAGE TABLE
          }
        }
        catch(Exception $e){
          die("Error connecting to database - Adding to Online"); // ERROR - ONLINE TABLE
        }
      }
      catch(Exception $e){
        die("Error connecting to database - Adding to Users"); // ERROR - USERS TABLE
      }
    }
  }
}

class LoggedUsers extends Users{
  // [PROPERTIES]
  public $UserID;

  // [CONSTRUCTOR]
  public function __construct($Username, $UserID){
    $this->Username = $Username;
    $this->UserID = $UserID;
  }

  // [METHODS]
  public function SendMessage($Message){
    include ('dbh.php');

    // SQL
    $SQL_ImageID = mysqli_query($newConnection, "SELECT ImageID FROM profileimage WHERE UserID = '$this->UserID'"); // Get all users' image ID
    while($row = mysqli_fetch_array($SQL_ImageID)){
      $ImageID[] = $row['ImageID'];
    }
    $SQL_InsertMessage = mysqli_query($newConnection, "INSERT INTO messages(Message) VALUES ('$Message')"); // Insert the message into the Messages table
    $SQL_MessageID = mysqli_insert_id($newConnection); // Fetch the recent message ID by that user
    $SQL_InsertUserMsg = mysqli_query($newConnection, "INSERT INTO usermsg(UserID, MsgID, ImageID) VALUES ('$this->UserID', '$SQL_MessageID', '$ImageID[0]')"); // Insert values into usermsg table to establish relation

    // If user sends a message, pass the message through the appropriate tables to match the message with the correct user
      try{
        $SQL_InsertMessage;
        try{
          $SQL_MessageID;
          try{
            $SQL_InsertUserMsg;
            header("location: ../chatserver/chatroom.php"); // REFRESH CHATROOM
            exit();
          }
          catch(Exception $e){
            die("Error with database - Relation table"); // ERROR - RELATION TABLE NOT INSERTING
          }
        }
        catch(Exception $e){
           die("Error with database - Message ID"); // ERROR - CANNOT RETRIEVE MESSAGE ID
        }
      }
      catch(Exception $e){
        die("Error with database - Sending Message"); // ERROR - MESSAGES TABLE NOT INSERTING
      }
    }

  public function DisplayMessages(){
    include ('dbh.php');

    // SQL - Select all the messages alongside their respective user and their profile image for each new line in the chatlog
    try{
      return mysqli_query($newConnection, "SELECT users.Username, profileimage.Image, messages.Message, messages.Timestamp
      FROM users
      INNER JOIN usermsg ON users.UserID = usermsg.UserID
      INNER JOIN messages ON usermsg.MsgID = messages.MsgID
      INNER JOIN profileimage ON usermsg.ImageID = profileimage.ImageID
      ORDER BY messages.Timestamp");
    }
    catch(Exception $e){
      die("Error with database - Relation not executing"); // ERROR - RELATION TABLE ISN'T RELATING DATA
    }
  }

  public function SetDefaultPFP(){
    include ('dbh.php');

    // SQL
    $SQL_Default = mysqli_query($newConnection, "SELECT Image FROM profileimage WHERE profileimage.UserID = '$this->UserID'"); // Set a default profile picture if Image == null

    // Set a default profile picture when the user logs in and has a null value in the Image column
    while($Row = mysqli_fetch_array($SQL_Default)){
      try{
        if($Row['Image'] == null){
          mysqli_query($newConnection, "UPDATE profileimage SET Image = 'Default.png' WHERE UserID = '$this->UserID'");
        }
      }
      catch(Exception $e){
        die("Error setting default profile picture"); // ERROR - COULDN'T SET DEFAULT PFP
      }
    }
  }

  public function DisplayPFP(){
    include ('dbh.php');

    // Get user's profile image to be displayed
    return mysqli_query($newConnection, "SELECT Image FROM profileimage WHERE profileimage.UserID = '$this->UserID'");
  }

  public function UploadPFP(){
    include ('dbh.php');

    // VARIABLES
    $Location = "Images/".basename($_FILES['image']['name']); // File directory
    $Image = $_FILES['image']['name']; // Establish image name

    // SQL
    $SQL_UpdatePFP = mysqli_query($newConnection, "UPDATE profileimage SET Image = '$Image' WHERE UserID = '$this->UserID'"); // Update user's Image to the one they have uploaded

    // On a confirmed image file, update the user's profile picture by updating the Image column and storing the file into my directory
    if($_FILES['image']['type'] == "image/jpeg" || $_FILES['image']['type'] == "image/png"){
      try{
        $SQL_UpdatePFP;
        try{
          move_uploaded_file($_FILES['image']['tmp_name'], $Location); // Try for whether the file has been directed successfuly to my directory
          header("location: ../chatserver/chatroom.php"); // REFRESH CHATROOM
          exit();
        }
        catch(Exception $e){
          die("Error with directory - Moving file"); // ERROR - MOVING FILE TO MY DIRECTORY
        }
      }
      catch(Exception $e){
        die("Error with database - Updating Profile"); // ERROR - PROFILEIMAGE TABLE NOT UPDATING
      }
    }
    else{
      header("location: ../chatserver/chatroom.php?error=invalidpfp");
      mysqli_query($newConnection, "UPDATE profileimage SET Image = 'Default.png' WHERE UserID = '$this->UserID'");
      exit();
    }
  }

  public function DisplayOnlineUsers(){
    include ('dbh.php');

    // Get all users who have an online status of 1
    return mysqli_query($newConnection, "SELECT Username FROM online WHERE isOnline = 1");
  }

  public function Logout(){
    include ('dbh.php');

    // End the users session and update them to appear offline on logout
    unset($_SESSION['Username']);
    session_destroy(); // Delete the user's session
    header("location: /chatserver/index.php");
    return mysqli_query($newConnection, "UPDATE online SET isOnline = 0 WHERE Username = '$this->Username'"); // Set online status to 0 and redirect them to the login page
    exit();
  }
}
