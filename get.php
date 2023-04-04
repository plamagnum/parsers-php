<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

include "vendor/autoload.php";

$options = getopt('', ['file', 'mongo']);

process($options);


function process($options){

	if(isset($options['file'])){

		$url = 'https://tortuga.wtf/vod/';
		$num = 2000;

		check($url, $num);
		$text = 'new_films.txt';
		file_put_contents($text, $films[5], FILE_APPEND | LOCK_EX);
	
	}

	if(isset($options['mongo'])){

		$url = 'https://tortuga.wtf/vod/';
		$num = 2000;
		check($url, $num);
		db($url, $films[5]);	
	
	}

}


function check($url, $num){

	for($i = 3000; $i <= $num; $i++){
	
		$u = $url.$i;

		list($http_Code, $response) = is_working_url($u);

		if(strpos($response, 'File not found') !== false){

			echo "Сторінка $i не знайдена\n";
		
		} elseif (strpos($resonse, 'xhr.open("POST", "https://db.tortuga.wtf/engine/modules/playerjsstat/site/ajax.php");') !==false){
			
			$match = preg_match('/file:"\K[^"]+/', $response, $matches);
			$name = $matches[0];
			$films = explode('/', $name);


			echo "Сторінка $i: $url $films[5]\n";

		} else {
		
			echo "Сторінка $i не визначена\n";

		}

	}

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

