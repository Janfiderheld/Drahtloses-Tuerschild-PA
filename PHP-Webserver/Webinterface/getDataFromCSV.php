<?php
	// Name (& Speicherort) der CSV-Datei, über die die Daten ausgetauscht werden
	$fileName = 'status.csv';
	// liest die Daten aus der CSV-Datei in das fileContentOld-Array
	$file = fopen($fileName, 'r');	
	
	/* fileContentOld ist ein Array:
	 * Position 0 - Status des Professors
	 * Position 1 - erste Meldung, die angezeigt wird
	 * Position 2 - zweite Meldung, die angezeigt wird
	 */	
	$fileContentOld = fgetcsv($file, 1000, ",");
	fclose($file);
	
	// Übergabe des Array-Inhalts als JSON formatiert an das Javascript-Skript 
	echo json_encode( array( 'status' => $fileContentOld[0], 'newsNew' => $fileContentOld[1], 'newsOld' => $fileContentOld[2] ) );
	
	// Beendet das PHP-Skript
	die;
?>