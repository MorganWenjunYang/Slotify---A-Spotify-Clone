<?php
include("../../config.php");

if(isset($_POST['playlistId'])){

    $playlistId = $_POST['playlistId'];
    $playlistQuery = mysqli_query($con,"DELETE FROM playlists Where id='$playlistId'");
    $songQuery = mysqli_query($con,"DELETE FROM playlistSongs Where playlistId='$playlistId'");

}
else{
    echo "Name or username parameters not passed into file";
}

?>
