<?php
include("includes/includedFiles.php");

if(isset($_GET['term'])){
    $term=urldecode($_GET['term']);
}
else{
    $term = '';
}
?>


<div class="searchContainer">
    <h4>Serach for an artist, album or songs</h4>
    <input type="text" class="searchInput" value="<?php echo $term;?>" placeholder="Start Typing ..." onfocus="var temp_value=this.value;
this.value=''; this.value=temp_value" spellcheck="false">
    <!-- on focus: make sure everytime you refresh the page, the cursor will be set to the end of the string -->
</div>


<script>

$(".searchInput").focus(); // make sure everytime you refresh the page, the focus will be set on the search input

$(function(){


    $(".searchInput").keyup(function(){
        clearTimeout(timer); // when you start typing, the timer will be reset

        timer = setTimeout(function(){ // when you finish typing, the action will be activated after timeout
            var val = $(".searchInput").val();
            openPage("search.php?term=" + val);
        },2000)
    })

})
</script>


<?php
 if($term== "") {
     exit(); // return nothing when searh bar is empty
 }
?>

<div class='tracklistContainer borderbottom'>
	<h2>SONGS</h2>
	<ul class='tracklist'>
		<?php
            $songsQuery = mysqli_query($con," SELECT id FROM songs WHERE title LIKE '$term%' LIMIT 10");
            if(mysqli_num_rows($songsQuery)==0){
                echo "<span class='noResults'>No songs found matching " . $term . "</span>";
            }


			$songIdArray = array();


			$i = 1; // keep note of order of track
			while($row = mysqli_fetch_array($songsQuery)){
                if($i>15){
                    break;
                }
                array_push($songIdArray, $row['id']);

				$albumSong = new Song($con, $row['id']);
				$albumArtist = $albumSong->getArtist();

				echo "<li class='tracklistRow'>
						<div class='trackCount'>
							<img class='play' src='assets/images/icons/play-white.png' onclick='setTrack(\"".$albumSong->getId()."\", tempPlaylist, true)'>
							<span class = 'trackNumber'>$i</span>
						</div>

						<div class='trackInfo'>
							<span class='trackName'>".$albumSong->getTitle()."</span>
							<span class='trackArtist'>".$albumArtist->getName()."</span>
						</div>

						<div class='trackOptions'>
							<input type='hidden' class='songId' value='".$albumSong->getId()."'>
							<img class='optionButton' src='assets/images/icons/more.png' onclick='showOptionsMenu(this)'>
						</div>

						<div class='trackDuration'>
							<span class='duration'>". $albumSong->getDuration() ."</span>
						</div>
					</li>";
				$i = $i + 1;
			}
		?>

		<script>
			var tempSongIds = '<?php echo json_encode($songIdArray);?>';
			tempPlaylist = JSON.parse(tempSongIds);
		</script>
	</ul>
</div>

<div class="artistContainer borderbottom">
    <h2>ARTISTS</h2>
    <?php
        $artistsQuery = mysqli_query($con," SELECT id FROM artists WHERE name LIKE '$term%' LIMIT 10");
        if(mysqli_num_rows($artistsQuery)==0){
            echo "<span class='noResults'>No artists found matching " . $term . "</span>";
        }
        while($row = mysqli_fetch_array($artistsQuery)){
            $artistFound = new Artist($con, $row['id']);
            echo "<div class = 'searchResultRow'>
                <div class = 'artistName'>
                    <span role='link' tabindex='0' onclick='openPage(\"artist.php?id=".$artistFound->getId()."\")'>
                    "
                    .$artistFound->getName().
                    "
                    </span>

                </div>


            </div>";
        }
    ?>

</div>


<div class='gridViewContainer'>
	<h2>ALBUMS</h2>
    <?php
        $albumQuery = mysqli_query($con, "SELECT * from albums WHERE title LIKE '$term% LIMIT 10'");
        if(mysqli_num_rows($albumQuery)==0){
            echo "<span class='noResults'>No albums found matching " . $term . "</span>";
        }
        while($row = mysqli_fetch_array($albumQuery)){
                echo "<div class='gridViewItem'>

                <span role='link' tabindex='0' onclick='openPage(\"album.php?id=". $row['id'] . "\")'>
                            <img src='" . $row['artworkPath'] . "'>
                            <div class = 'gridViewInfo'>"
                                . $row['title'] .
                            "</div>
                        </a>
                    </div>";
        }
    ?>

</div>

<nav class="optionsMenu">
	<input type='hidden' class='songId'>
	<?php echo Playlist::getPlaylistDropdown($con, $userLoggedIn->getUserName());?>
</nav>
