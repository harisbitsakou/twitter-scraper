<?php

$row = 1;
if (($handle = fopen("./merged/data.csv", "r")) !== FALSE) {
  while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

    $username = $data[2];
    $profile  = $data[3];

    if(!file_exists("./profiles/".$username.".txt")){

      //Fetch Profile
      $data = file_get_contents($profile);

      //Get Info (First make sure that user has not deleted his/her account)
      if(
        ( strpos($data, "Tweets") !== false ) && 
        ( strpos($data, "Following") !== false ) && 
        ( strpos($data, "Followers") !== false )
      ) {


        preg_match_all( '/(<div class="statnum">)+([0-9])+(,)?\w+/', $data, $statnums, PREG_PATTERN_ORDER);

        $stats = $username."|".implode("|", array_map(function ($n){ return str_replace("<div class=\"statnum\">", "", $n); }, $statnums[0]) );

        preg_match_all( '/(<div class="location">)+.+?(?=div>)\w+/', $data, $location, PREG_PATTERN_ORDER);

        $stats .= "|".implode("|", array_map(function ($n){ return str_replace(array("<div class=\"location\">","</div"), "", $n); }, $location[0]) );

        //Save Info
        file_put_contents("./profiles/".$username.".txt", $stats);
        echo "Added (FULL) info for: ".$username."\n";

      }else{

        //Save Blank Info (Profile doesn't exist)
        file_put_contents("./profiles/".$username.".txt", "NA|NA|NA|NA");
        echo "Added (EMPTY) info for: ".$username."\n";

      }

    }else{
      echo "Info for: ".$username." already exists\n";
    }

  }
  fclose($handle);
}


function splitByElement($el,$data){

  $dom = new DOMDocument;
  @$dom->loadHTML($data);
  foreach($dom->getElementsByTagName($el) as $node)
  {
      $array[] = $dom->saveHTML($node);
  }

  return $array;

}

?>