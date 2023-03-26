<?php

include "vendor/autoload.php";


function check($url, $num){
	
	for ($i = 6001; $i <= $num; $i++){

		$u = $url.$i;
		list($http_Code, $response) = is_working_url($u);
		if (strpos($response, 'File not found') !== false){
			echo "\033[31m Сторінка $i не знайдена \033[32m \n";
		} elseif (strpos($response, 'xhr.open("POST", "https://db.tortuga.wtf/engine/modules/playerjsstat/site/ajax.php");') !==false){
			$match = preg_match('/file:"\K[^"]+/', $response, $matches);
			$name = $matches[0];
			//print_r($name);
			//$films = preg_split('/file:.*\/(\w+\.\w+_\d+)_.*\//', $name);
			$films = explode('/', $name);


			//print_r($films);
						
			echo "\033[32m Сторінка $url$i знайдена\033[32m \033[34m $films[5] 033[34m \n";

			db($url, $films[5]);
		
		} else {
		
			echo "Сторінка $i: не визначена\n";
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



$url = "https://tortuga.wtf/vod/";
$num = 7000;

check($url, $num);
