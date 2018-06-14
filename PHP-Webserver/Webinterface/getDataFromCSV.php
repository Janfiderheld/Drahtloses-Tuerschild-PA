<?php

	$fileName = 'Meldungsaustausch.csv';
	$file = fopen($fileName, 'r');	
	/* oldFileContent ist ein Array:
	 * Position 0 - Status des Professors
	 * Position 1 - erste Meldung, die angezeigt wird
	 * Position 2 - zweite Meldung, die angezeigt wird
	 */	
	$oldFileContent = fgetcsv($file, 1000, ",");
	fclose($file);
	
	echo json_encode( array( 'status' => $oldFileContent[0], 'newsNew' => $oldFileContent[1], 'newsOld' => $oldFileContent[2] ) );
	
	exit;
?>