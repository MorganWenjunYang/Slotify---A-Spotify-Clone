<?php
  class Playlist{

    private $con;
    private $id;
    private $name;
    private $owner;

    public function __construct($con, $data) { // like _init_ in python
      // pass in $con from config.php

      if(!is_array($data)){
          //if data is a id (string)
          $query = mysqli_query($con, "SELECT *FROM playlists where id='$data'");
          $data = mysqli_fetch_array($query);

      }



      $this->con = $con; // pass $con from config.php to current con
      $this->id = $data['id'];
      $this->name = $data['name'];
      $this->owner = $data['owner'];

    }

    public function getId(){
        return $this->id;
    }
    public function getName(){
        return $this->name;
    }
    public function getOwner(){
        return $this->owner;
    }
    public function getNumberOfSongs(){
        $query = mysqli_query($this->con, "SELECT songId FROM playlistSongs WHERE playlistID='$this->id'");
        return mysqli_num_rows($query);

    }

    public function getSongIds(){
        $query = mysqli_query($this->con, "SELECT * FROM playlistSongs WHERE playlistId='$this->id' ORDER by playlistOrder ASC");
        $array = array();
        while ($row = mysqli_fetch_array($query)){
            array_push($array, $row['songId']);
        }
        return $array;
    }

    public static function getPlaylistDropdown($con, $username){
        $dropdown  = '<select class="item playlist">
        		          <option value="">Add to playlist</option>
        	          ';
        $query =  mysqli_query($con, "SELECT id, name FROM playlists WHERE owner='$username'");
        while($row =  mysqli_fetch_array($query)){
            $id = $row['id'];
            $name = $row['name'];
            $dropdown = $dropdown . "<option value='$id'>$name</option>";
        }

        return $dropdown . "</select>";

    }

  }

?>
