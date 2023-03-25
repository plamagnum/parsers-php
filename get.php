<?php
function check($url, $num){
	
	for ($i = 1111; $i <= $num; $i++){

		$u = $url.$i;
		list($http_Code, $response) = is_working_url($u);
		if (strpos($response, 'File not found') !== false){
			echo "Сторінка $i не знайдена\n";
		} elseif (strpos($response, 'xhr.open("POST", "https://db.tortuga.wtf/engine/modules/playerjsstat/site/ajax.php");') !==false){
			$match = preg_match('/file:"\K[^"]+/', $response, $matches);
			$name = $matches[0];
			//print_r($name);
			//$films = preg_split('/file:.*\/(\w+\.\w+_\d+)_.*\//', $name);
			$films = explode('/', $name);


			//print_r($films);
						
			echo "Сторінка $url$i знайдена $films[5]\n";
		
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

$url = "https://tortuga.wtf/vod/";
$num = 6000;

check($url, $num);
