<?php
  ob_start();

  session_start();

  $timezone = date_default_timezone_set('Asia/Shanghai');

  $con = mysqli_connect('localhost','root','','slotify');  # sever/ default username/ default password/ database name

  if(mysqli_connect_error()){
    echo 'Failed to connect: '.mysqli_connect_error(); # dot append string together
  }

?>
