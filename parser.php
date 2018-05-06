<?php
ini_set('memory_limit', '512M');

$tweets = array();
$i      = 0;

for ($f=1; $f <= 14586; $f++) { 
  $file = "./tweets/data-".$f.".txt";
  parseFile($file);
}


$fp = fopen('./merged/data.csv', 'w');
foreach ($tweets as $tweet) {
    fputcsv($fp, $tweet);
}
fclose($fp);


function parseFile($file){
  global $tweets,$i;

  $data   = file_get_contents($file);
  $tables = splitByElement('table',$data);

  foreach ($tables as $key => $table) {
    if( strpos($table, "tweet  ")!==false ){
      
      //We are inside a tweet

      $tweets[$i] = array();

      $tweets[$i]["url"]      = "https://twitter.com".explode("\"", $table)[3];
      $tweets[$i]["tweet_id"] = explode("?", explode("/", $tweets[$i]["url"])[5])[0];
      $tweets[$i]["username"] = explode("/", $tweets[$i]["url"])[3];
      $tweets[$i]["profile"]  = "https://mobile.twitter.com/".explode("/", $tweets[$i]["url"])[3];

      $tds = splitByElement('td',$table);
      foreach ($tds as $key => $td) {

        if( 
            ( strpos($td, "user-info")!==false ) &&
            ( strpos($td, $tweets[$i]["username"])!==false )
        ){
          //We are inside user's info section
          $tweets[$i]["fullname"] = str_replace(array(" class=\"fullname\">","</"),"",explode("strong", $td)[1]);
        }


        if( 
            ( strpos($td, "timestamp")!==false ) &&
            ( strpos($td, "tweet_".$tweets[$i]["tweet_id"])!==false )
        ){
          //We are inside timestamp section
          if(count(explode("p\">", $td))>2){
            $tweets[$i]["timestamp"] = trim(str_replace(array("</a>","</td>"),"",explode("p\">", $td)[2]));
          }else{
            continue 2;
          }
        }


        if( 
            ( strpos($td, "tweet-text")!==false ) &&
            ( strpos($td, "data-id=\"".$tweets[$i]["tweet_id"])!==false )
        ){
          //We are inside tweet's text section
          $tweets[$i]["data"] = str_replace("\n","",strip_tags(preg_replace('#(pic|twitter)+([.a-zA-Z0-9\s])+([.a-zA-Z0-9\s])+([/a-zA-Z0-9\s])\w+#i','',(trim(str_replace(array("</div>","\n","</td>"),"",explode("\"tweet-text\" data-id=\"".$tweets[$i]["tweet_id"]."\">", $td)[1]))."</div>"))));
        }


      }

      $i++;
    }
  }

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