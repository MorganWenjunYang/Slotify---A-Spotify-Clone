<?php

// generate random playlist of 10 songs
$songQuery = mysqli_query($con,"SELECT id FROM songs ORDER BY RAND() LIMIT 10");
$resultArray = array();
while($row = mysqli_fetch_array($songQuery)){
    array_push($resultArray,$row['id']);
}
$jsonArray = json_encode($resultArray);
// output of array of id of 10 songs
?>

<script>

$(document).ready(function(){
    var newPlaylist = <?php echo $jsonArray;?>;
    audioElement = new Audio();
    setTrack(newPlaylist[0],newPlaylist,false);

    updateVolumeProgressBar(audioElement.audio);
    // preventing controls from highlighting on mouse drag
    $('#nowPlayingBarContainer').on('mousedown touchstart mousemove touchmove', function(e){
        e.preventDefault(); // prevent the default behavior
    }) // on any of these 4 events; touchxxxx only works on touch screen



    // PLAYBACK
    $(".playbackBar .progressBar").mousedown(function(){
        mouseDown = true;
        console.log('mouse down');
    });

    $(".playbackBar .progressBar").mousemove(function(e){
        // e: pass whatever calls it into the function: mouse in this case
        if (mouseDown==true){
            //set time of song depending on position of mouse
            timeFromOffset(e, this); // this is ".playbackBar .progressBar"

        }
    });
    $(".playbackBar .progressBar").mouseup(function(e){
        // e: pass whatever calls it into the function: mouse in this case
        timeFromOffset(e, this); // this is ".playbackBar .progressBar"
        console.log('mouse up');
    });


    // VOLUME
    $(".volumeBar .progressBar").mousedown(function(){
        mouseDown = true;
        console.log('mouse down');
    });

    $(".volumeBar .progressBar").mousemove(function(e){
        // e: pass whatever calls it into the function: mouse in this case
        if (mouseDown==true){
            //set volume of song depending on position of mouse

            var percentage = e.offsetX / $(this).width();

            if(percentage>=0 && percentage <=1){
                audioElement.audio.volume = percentage;
            }

        }
    });
    $(".playbackBar .progressBar").mouseup(function(e){
        // e: pass whatever calls it into the function: mouse in this case
        var percentage = e.offsetX / $(this).width();

        if(percentage>=0 && percentage <=1){
            audioElement.audio.volume = percentage;
        }
        console.log('mouse up');
    });

    $(document).mouseup(function(){
        mouseDown = false;
        console.log('mouse up');
    });


});

function timeFromOffset(mouse,progressBar){
    var percentage = mouse.offsetX / $(progressBar).width() * 100; //horizontal
    var seconds = audioElement.audio.duration * (percentage / 100);
    audioElement.setTime(seconds);
}

// the above block will only be executed when everything is ready

////////////////////////////////////////////////////////////////////////////
// My Thought
// We use Ajax because we would like to access data based on users' action
// However, since php is loaded as soon as the page is loaded
// in the case, we have to use js to send request to separate php files
// in order to get the data
///////////////////////////////////////////////////////////////////////////

function prevSong(){
    if(audioElement.audio.currentTime>=3 || currentIndex==0){
        audioElement.setTime(0);
    }
    else{
        currentIndex = currentIndex - 1;
        setTrack(currentPlaylist[currentIndex],currentPlaylist,true);
    }
}

function nextSong(){

    if (repeat == true){
        audioElement.setTime(0);
        playSong();
        return;

    }

    if(currentIndex == currentPlaylist.length-1){ // js is zero-based
        currentIndex = 0;
    }
    else{
        currentIndex++;
    }

    var trackToPlay = shuffle ? shufflePlaylist[currentIndex] : currentPlaylist[currentIndex];
    setTrack(trackToPlay, currentPlaylist, true);
}

function setRepeat(){
    repeat = !repeat;
    var imageName = repeat ? "repeat-active.png" : "repeat.png";
    $(".controlButton.repeat img").attr("src","assets/images/icons/"+imageName);
}

function setMute(){
    audioElement.audio.muted = !audioElement.audio.muted;
    var imageName = audioElement.audio.muted ? "volume-mute.png" : "volume.png";
    $(".controlButton.volume img").attr("src","assets/images/icons/"+imageName);
}

/**
 * Shuffles array in place.
 * @param {Array} a items An array containing the items.
 */
function shuffleArray(a) {
    var j, x, i;
    for (i = a.length - 1; i > 0; i--) {
        j = Math.floor(Math.random() * (i + 1));
        x = a[i];
        a[i] = a[j];
        a[j] = x;
    }
    return a;
}

function setShuffle(){
    shuffle = !shuffle;
    var imageName = shuffle ? "shuffle-active.png" : "shuffle.png";
    $(".controlButton.shuffle img").attr("src","assets/images/icons/"+imageName);

    if (shuffle){
        //Randomize playlist
        shuffleArray(shufflePlaylist);
        currentIndex = shufflePlaylist.indexOf(audioElement.currentlyPlaying.id);
    }
    else{
        //shuffle deactivated
        //bacl to regular playlist
        currentIndex = currentPlaylist.indexOf(audioElement.currentlyPlaying.id);
    }


}

function setTrack(trackId, newPlaylist, play){
// ajax page url, how the data will be refer to in ajax page : data you want to send, what you want to with the response data

    if(newPlaylist != currentPlaylist){
        currentPlaylist = newPlaylist;
        shufflePlaylist = currentPlaylist.slice();
        shuffleArray(shufflePlaylist);
    }

    if (shuffle){
        currentIndex = shufflePlaylist.indexOf(trackId);
    }
    else{
        currentIndex = currentPlaylist.indexOf(trackId);
    }

    pauseSong();

    $.post("includes/handlers/ajax/getSongJson.php", {songId: trackId}, function(data){

        // have to parse the JSON data first
        var track = JSON.parse(data);
        $(".trackName span").text(track.title);
        // $(".artistName span").text(track.artist);
        // line above won't work because it returns id
        // we can't create a php Artist class and this->getName()
        // because we are in a js block
        // !! cannot call php in js
        $.post("includes/handlers/ajax/getArtistJson.php", {artistId: track.artist}, function(data){
            var artist = JSON.parse(data);
            //console.log(artist);
            $(".trackInfo .artistName span").text(artist.name);
            $(".trackInfo .artistName span").attr("onclick","openPage('artist.php?id=" + artist.id + "')");

        });

        $.post("includes/handlers/ajax/getAlbumJson.php", {albumId: track.album}, function(data){
            var album = JSON.parse(data);
            //console.log(album);
            $(".albumLink img").attr("src",album.artworkPath);
            // attr('attribute to update', the value)
            $(".albumLink img").attr("onclick","openPage('album.php?id=" + album.id + "')");
            $(".trackName span").attr("onclick","openPage('album.php?id=" + album.id + "')");

        });



        audioElement.setTrack(track);
        if (play){
            playSong();
        }
    });


}

function playSong(){
    if(audioElement.audio.currentTime==0){
        // console.log(audioElement.currentlyPlaying);
        $.post("includes/handlers/ajax/updatePlay.php", {songId: audioElement.currentlyPlaying.id});
    }

    $(".controlButton.play").hide();
    $(".controlButton.pause").show();
    // if there is space between then meaning play class under controlButt class
    // no space meaning the elements belonging to both of the classes at the same time
    // console.log(audioElement) // you can see there is a property called current time
    audioElement.play();
}

function pauseSong(){
    $(".controlButton.pause").hide();
    $(".controlButton.play").show();
    audioElement.pause();
}

</script>


<div id = 'nowPlayingBarContainer'>
    <div id = 'nowPlayingBar'>
        <div id = 'nowPlayingLeft'>
            <div class="content">
                <span class="albumLink">
                    <img src='' role="link" tabindex="0" class='albumArtwork'>
                </span>
                <span>
                    <div class="trackInfo">
                        <span class='trackName'>
                            <span role="link" tabindex="0"></span>
                        </span>

                        <span class='artistName'>
                            <span role="link" tabindex="0"></span>
                        </span>
                    </div>
                </span>
            </div>
        </div>

        <div id = 'nowPlayingCenter'>
            <div class='content playerControls'>

                <div class='buttons'>
                    <button class='controlButton shuffle' title='Shuffle Button' onclick = 'setShuffle()'>
                        <img src="assets/images/icons/shuffle.png" alt='Shuffle'>
                    </button>
                    <button class='controlButton previous' title='Previous Button' onclick=prevSong()>
                        <img src="assets/images/icons/previous.png" alt='Previous'>
                    </button>
                    <button class='controlButton play' title='Play Button' onclick="playSong()">
                        <img src="assets/images/icons/play.png" alt='Play'>
                    </button>
                    <button class='controlButton pause' title='Pause Button' onclick="pauseSong()" style='display:none'>
                        <img src="assets/images/icons/pause.png" alt='Pause'>
                    </button>
                    <button class='controlButton next' title='Next Button' onclick="nextSong()">
                        <img src="assets/images/icons/next.png" alt='Next'>
                    </button>
                    <button class='controlButton repeat' title='Repeat Button' onclick="setRepeat()">
                        <img src="assets/images/icons/repeat.png" alt='Repeat'>
                    </button>
                </div>

                <div class='playbackBar'>
                    <span class='progressTime current'>0.00</span>
                    <div class='progressBar'>
                        <div class='progressBarBg'>
                            <div class='progress'></div>
                        </div>
                    </div>
                    <span class='progressTime remain'>0.00</span>

                </div>
            </div>
        </div>

        <div id = 'nowPlayingRight'>
            <div class='volumeBar'>
                <button class='controlButton volume' title='Volume Button' onclick="setMute()">
                    <img src='assets/images/icons/volume.png' alt='Volume'>
                </button>
                <div class='progressBar'>
                    <div class='progressBarBg'>
                        <div class='progress'></div>
                    </div>
                </div>
            </div>

        </div>
    </div>
