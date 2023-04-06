<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

include "vendor/autoload.php";

$options = getopt('', ['file', 'mongo']);

process($options);


function process($options){

	if(isset($options['file'])){
		global $ret;
		$url = 'https://tortuga.wtf/vod/';
		$num = 4000;
		var_dump(check($url, $num));
		
		$a = check($url, $num);
		/*
		var_dump($a);
		$text = 'new_films.txt';
		file_put_contents($text, $a, FILE_APPEND | LOCK_EX);
		//var_dump(check($url, $num));
		 */
		foreach($a as $b){
			echo $b;
		} 
	
	}

	if(isset($options['mongo'])){

		$url = 'https://tortuga.wtf/vod/';
		$num = 4000;
		check($url, $num);
		db($url, $films[5]);	
	
	}

}


function check($url, $num){

	global $ret;
	$ret = '';

	for($i = 3000; $i <= $num; $i++){
	
		$u = $url.$i;

		list($http_Code, $response) = is_working_url($u);

		if(strpos($response, 'File not found') !== false){

			echo "Сторінка $i не знайдена\n";
		
		} elseif (strpos($response, 'xhr.open("POST", "https://db.tortuga.wtf/engine/modules/playerjsstat/site/ajax.php");') !==false){
			
			$match = preg_match('/file:"\K[^"]+/', $response, $matches);
			$name = $matches[0];
			$films = explode('/', $name);
			$ret .= $films[5];

			echo "Сторінка $i: $url $ret\n";
			//return $ret;

		} else {
		
			echo "Сторінка $i не визначена\n";

		}

	}
	return $ret;

}


function is_working_url($u){



	$handle = curl_init();
	curl_setopt($handle, CURLOPT_URL, $u);
	curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($handle, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($handle, CURLOPT_MAXREDIRS, 10);
	//curl_setopt($handle, );

	$response = curl_exec($handle);
	$http_Code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
	curl_close($handle);

	return array($http_Code, $response);
}


function db($links, $title){

	$coll = (new MongoDB\Client)->films->links;

	$insert = $coll->insertOne([
	
		'link' => $links,
		'title' => $title
	
	]);

	printf(" \033[33m Insert %d documents \033[33m\n", $insert->getInsertedCount());

}

