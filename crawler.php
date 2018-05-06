<?php

//Add %23 if you are looking for a #
$search_param = "%23Paris";

$url = "https://mobile.twitter.com/search?q=".$search_param."&s=typd&x=18&y=8";
$loop = 1;
do{

	//GET the page
	$data = file_get_contents($url);

	//Find the dates of all the tweets contained in the file
	preg_match_all( '/(\?p=p">)+([a-zA-Z0-9\s])+([a-zA-Z0-9\s])\w+/', $data, $dates, PREG_PATTERN_ORDER);

	$dates[0] = implode(" | ", array_map(function ($n){ return str_replace("?p=p\">", "", $n); }, $dates[0]) );

	//Echo the dates (just to check crawler progress)
	echo $dates[0] . "\n";

	//Find the link to the next page
	preg_match_all( '/(href=\"\/search)+(\s*(.*?)\s*")/', $data, $matches, PREG_PATTERN_ORDER);
	
	$url = "https://mobile.twitter.com" . str_replace(array("href=","\""), "", $matches[0][count($matches[0])-1]);

	//Write raw data file
	file_put_contents("./tweets/data-".($loop++).".txt", $data);

	//Wait before hitting next page
	//sleep(1);

}while(true)

?>