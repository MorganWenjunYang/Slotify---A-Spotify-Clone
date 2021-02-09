<?php
include("includes/config.php");
include("includes/classes/Artist.php");
include("includes/classes/Album.php");
include("includes/classes/Song.php");
include("includes/classes/User.php");
include("includes/classes/Playlist.php");
//session_destroy(); // manually log out ; refresh then the session info will be gone
if(isset($_SESSION['userLoggedIn'])){
    $userLoggedIn = new User($con, $_SESSION['userLoggedIn']); // inherit session
    $username = $userLoggedIn->getUsername();
    echo "<script>userLoggedIn = '$username';</script>";
}
else{
    header("Location: register.php");
}

?>

<html>
<head>
    <title>Welcome to Slotify</title>
    <link rel="stylesheet" type="text/css" href='assets/css/style.css'>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- make sure you put jquery above scrip.js -->
    <script src='assets/js/script.js'></script>
</head>
<body>

    <!-- <script>
        var audioElement = new Audio();
        audioElement.setTrack('assets/music/bensound-funkyelement.mp3');
        audioElement.audio.play();
    </script> -->
    <div id='mainContainer'>
        <div id="topContainer">
            <?php include("includes/navBarContainer.php");?>

            <div id='mainViewContainer'>
                <div id='mainContent'>
