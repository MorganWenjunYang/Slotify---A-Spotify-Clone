<?php
include("../../config.php");

// can't change username
if(!isset($_POST['username'])){
    echo "ERROR: Could not set username";
    exit();
}

// make sure email is submitted and not empty
if(isset($_POST['email']) && $_POST['email']!=""){
    $username = $_POST['username'];
    $email = $_POST['email'];
// make sure email is valid
    if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
        echo "Email is invalid";
        exit;
    }

// check if the email is already in use
    $emailCheck = mysqli_query($con, "SELECT email FROM users WHERE email = '$email' AND username != '$username'");
    if(mysqli_num_rows($emailCheck)>0){
        echo "Email is already in use";
        exit();
    }


    $updateQuery = mysqli_query($con,"UPDATE users SET email = '$email' WHERE username='$username' ");
    echo 'update successful';
}else{
    echo "You must provide an email";

}
?>
