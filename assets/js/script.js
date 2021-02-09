var currentPlaylist = [];
var shufflePlaylist = [];
var tempPlaylist = [];
var audioElement;
var mouseDown = false;
var currentIndex = 0; // the song being played in the list
var repeat = false;
var shuffle = false;
var userLoggedIn;
var timer;

// hide the menu when click outside the box and button and scroll
$(document).click(function(click){
    var target = $(click.target);
    if(!target.hasClass("item") && !target.hasClass("optionButton")){
        hideOptionsMenu();
    }
});

$(window).scroll(function(){
    hideOptionsMenu();
});

// when select(the dropdown menu) is changed
$(document).on("change","select.playlist", function(){

    var select = $(this);
    var playlistId = select.val(); // this-> the option
    var songId = select.prev(".songId").val(); // the song id stored in hidden input

    $.post("includes/handlers/ajax/addToPlaylist.php",{playlistId: playlistId, songId:songId}).done(function(error){
        if(error != ""){
            alert(error);
            return;
        }
        hideOptionsMenu();
        select.val("");

    });
});

function updateEmail(emailClass){
    var emailValue = $("." + emailClass).val();
    $.post("includes/handlers/ajax/updateEmail.php",{email:emailValue, username:userLoggedIn})
    .done(function(response){
        $("."+emailClass).nextAll(".message").text(response);
        // nextUntil find the 1st element
        // nextAll: siblings
    });
}


function updatePassword(oldPasswordClass, newPasswordClass1, newPasswordClass2){
	var oldPassword = $("." + oldPasswordClass).val();
	var newPassword1 = $("." + newPasswordClass1).val();
	var newPassword2 = $("." + newPasswordClass2).val();
    $.post("includes/handlers/ajax/updatePassword.php",
    {oldPassword:oldPassword, newPassword1:newPassword1, newPassword2:newPassword2, username:userLoggedIn})
    .done(function(response){
        $("."+oldPasswordClass).nextAll(".message").text(response);
        // nextUntil find the 1st element
        // nextAll: siblings
    });
}

function logout(){
    $.post("includes/handlers/ajax/logout.php",function(){
        location.reload();
    })
}

function openPage(url){

    if(timer!=null){
        clearTimeout(timer); // when you open a new page(move away fron search page ) deactivate the timer
    }

    if(url.indexOf("?")==-1){
        url = url + "?";
    }
    var encodedUrl = encodeURI(url + "&userLoggedIn=" + userLoggedIn);
    console.log(encodedUrl);
    $("#mainContent").load(encodedUrl);
    $("body").scrollTop(0); // always scroll from top when changing link
    history.pushState(null,null,url); //
}

function removeFromPlaylist(button, playlistId){

    var songId = $(button).prevAll(".songId").val();

    $.post("includes/handlers/ajax/removeFromPlaylist.php",{playlistId:playlistId, songId: songId}).done(function(error){
        // do sth when ajax returns
        if(error != ""){
            alert(error);
            return;
        }
        openPage("playlist.php?id="+playlistId);
    });
}

function createPlaylist(){
    var popup = prompt("Please enter the name of your playlist");

    if(popup != null){
        $.post("includes/handlers/ajax/createPlaylist.php",{name:popup, username: userLoggedIn}).done(function(error){
            // do sth when ajax returns
            if(error != ""){
                alert(error);
                return;
            }
            openPage("yourMusic.php");
        });
    }
}

function deletePlaylist(playlistId){
    var prompt = confirm("Are you sure you want to delete this playlist?");
    if(prompt){
        $.post("includes/handlers/ajax/deletePlaylist.php",{playlistId:playlistId}).done(function(error){
            // do sth when ajax returns
            if(error != ""){
                alert(error);
                return;
            }
            openPage("yourMusic.php");
        });
    }
}

function showOptionsMenu(button){
    // every time you press ... to show option menu
    // the option menu takes songid from the .trackOption and push it to .optionMenu

    var songId = $(button).prev(".songId").val();
    // prev - only the inmmediate ancester;
    // prevALL - all ancester
    var menu = $(".optionsMenu");
    var menuWidth = menu.width();
    menu.find(".songId").val(songId);

    var scrollTop =$(window).scrollTop(); // Distance from top of window to top of document
    var elementOffset = $(button).offset().top; // Distance from top of the document

    var top = elementOffset - scrollTop; // distance from button to top of the window
    var left = $(button).position().left;

    menu.css({"top" : top + "px", "left": left - menuWidth + "px", "display":"inline"});
}

function hideOptionsMenu( ){
    var menu = $(".optionsMenu");
    if(menu.css("display")!="none"){
        menu.css("display","none");
    }
}

function formatTime(seconds){
    var time = Math.round(seconds);
    var minutes = Math.floor(time/60);
    var seconds = time - (minutes*60);

    var extraZero;

    if (seconds < 10){
        extraZero="0";
    }
    else{
        extraZero = '';
    }

    return minutes + ":" + extraZero + seconds;
    // in js use + to append string

}

function updateTimeProgressBar(audio){
    $(".progressTime.current").text(formatTime(audio.currentTime));
    $(".progressTime.remain").text(formatTime(audio.duration-audio.currentTime));

    // progressBar
    var progress = audio.currentTime / audio.duration * 100;
    $(".playbackBar .progress").css("width", progress + "%");
}

function updateVolumeProgressBar(audio){
    // progressBar
    var volume = audio.volume * 100;
    $(".volumeBar .progress").css("width", volume + "%");
}

function playFirstSong(){
    setTrack(tempPlaylist[0], tempPlaylist,true);
}

function Audio(){

    this.currentlyPlaying; // keep track of the song on play
    this.audio = document.createElement('audio'); //property of class; like public xxx; in php

    this.audio.addEventListener('canplay',function(){
        // this refers to the object the event was called on
        var duration = formatTime(this.duration);
        $(".progressTime.remain").text(duration);
        // this refers to 'this.audio'
    });

    this.audio.addEventListener("ended",function(){
        nextSong();
    });

    this.audio.addEventListener('timeupdate',function(){
        if(this.duration){
            updateTimeProgressBar(this); //'this' is the audio object in line 43; this.audio in line 52

        }
    });

    this.audio.addEventListener('volumechange',function(){
        updateVolumeProgressBar(this);
    });

    this.setTrack = function(track){ // track: a JSON object
        this.currentlyPlaying = track;
        this.audio.src = track.path; // add property to a html element
    }

    this.play = function(){
        this.audio.play();
    }

    this.pause = function(){
        this.audio.pause();
    }

    this.setTime = function(seconds){
        this.audio.currentTime = seconds;
    }
}
