<?php

	echo "ich bin gestartet";
	
	$file = fopen('HatGeklappt.txt', 'w'); 
	fwrite($file, 'Test');
	fclose($file);
	
	if(!empty($_POST['dataToSend'])){
		$data = $_POST['dataToSend'];
		$fname = 'HatGeklappt.txt';

		$file = fopen('HatGeklappt.txt', 'w'); 
		fwrite($file, 'Test');
		fclose($file);
	}

?>