<?php

function sanitizeFormPassword($inputText){
  $inputText = strip_tags($inputText);
  return $inputText;
}

function sanitizeFormUsername($inputText){
  $inputText = strip_tags($inputText);
  $inputText =  str_replace(' ','',$inputText);
  return $inputText;
}

function sanitizeFormString($inputText){
  $inputText = strip_tags($inputText);
  $inputText =  str_replace(' ','',$inputText);
  $inputText = ucfirst(strtolower($inputText));# uppercase the first character
  return $inputText;
}



if(isset($_POST['registerButton'])){
  //register button is pressed
  $username = sanitizeFormUsername($_POST['username']);
  $firstName = sanitizeFormString($_POST['firstName']);
  $lastName = sanitizeFormString($_POST['lastName']);
  $email = sanitizeFormString($_POST['email']);
  $emailConfirm = sanitizeFormString($_POST['emailConfirm']);
  $password = sanitizeFormPassword($_POST['password']);
  $passwordConfirm = sanitizeFormPassword($_POST['passwordConfirm']);

  $wasSuccessful = $account -> register($username, $firstName, $lastName, $email, $emailConfirm, $password, $passwordConfirm);
  // send whether register is successful to a variable

  if($wasSuccessful == true) {
      $_SESSION['userLoggedIn'] = $username;
      header("Location: index.php");
  }

}

?>
