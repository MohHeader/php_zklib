<?php
	include("config.php");
	
	function write_to_file($array) {
		foreach ($array as $data) {
			$data_string = serialize($data);
			file_put_contents("records.txt", $data_string.PHP_EOL, FILE_APPEND);
		}
	}

	function send_data_in_file(&$unsent_records) {
		$myfile = fopen("records.txt", "r");
		if($myfile && filesize("records.txt") > 0){
			while (($line = fgets($myfile)) !== false) {
				send_http_request(unserialize($line),$unsent_records);
			}
		}
		fclose("records.txt");
		unlink("records.txt");
	}

	function send_http_request($data,&$unsent_records){
		$options = array(
			'http' => array(
				'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
				'method'  => 'POST',
				'content' => http_build_query($data),
			),
		);
		$context  = stream_context_create($options);
		$result = file_get_contents(API_URL, false, $context);
		if ($result === FALSE) {
			array_push($unsent_records, $data);
		}
	}
?>