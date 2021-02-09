<?php

// the process:
// when you first request page from address box in the browser:
// the header and footer are loaded
// then we use ajax (openpage) to get the main content
// the if block is run and includes all the file we need
// and go back to load index.php
// but this time without header and footer
// only the main content

if(isset($_SERVER['HTTP_X_REQUESTED_WITH'])){
    // activated when request from ajax
    include("includes/config.php");
    include("includes/classes/User.php");
    include("includes/classes/Artist.php");
    include("includes/classes/Album.php");
    include("includes/classes/Song.php");
    include("includes/classes/Playlist.php");

    # when page is separtaely loaded through ajax, it doesn't know the logged in user
    # however in script.js we have encoded every url with the user
    # thus we can easily retrieve it here in php through $_GET

    if(isset($_GET['userLoggedIn'])){
        $userLoggedIn = new User($con, $_GET['userLoggedIn']);
    }
    else{
        echo "Username variable was not passed into page. Check the openPage JS function";
        exit();
    }
}
else{
    include("includes/header.php");
    include("includes/footer.php");

    // the order has been changed so we have to request for the main content again
    $url = $_SERVER['REQUEST_URI'];
    echo "<script>openPage('$url')</script>";

    exit();


}

?>
