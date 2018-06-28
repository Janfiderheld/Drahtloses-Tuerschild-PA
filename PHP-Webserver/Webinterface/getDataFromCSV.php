<?php
	/**
	*	Projektarbeit Sommersemester 2018 - Drahtloses Türschild mit E-Paper-Display
	*	Author: Jan-Philipp Töberg
	*	Version: 1.3
	*	Letzte Änderung: 28.06.2018
	*	
	*	Dieses PHP-Skript regelt den Lesezugriff auf die unterschiedlichen CSV-Dateien.
	*	Dabei steuert es, welche Werte geladen werden und übertragt diese an den Client per XMLHttp-Request.
	**/

	// Einstellen der internen Zeichenkodierung auf UTF-8 (wie bei HTML)
	mb_internal_encoding("UTF-8");
	// Auslesen des aktuellen Profs aus der Client-Übertragung
	$currentProf = htmlentities($_POST['prof']);
	
	// wählt die Datei abhängig vom ausgewählten Professor aus
	switch($currentProf) {
		case 'korte':
			// $fileName = 'Korte_status.csv';			// Speicherort im gleichen Ort
			$fileName = '../Korte_status.csv';		// Speicherort einen Ordner darüber
			break;
		case 'hausdoerfer':
			// $fileName = 'Hausdoerfer_status.csv';		// Speicherort im gleichen Ort
			$fileName = '../Hausdoerfer_status.csv';		// Speicherort einen Ordner darüber
			break;
		default:
			// $fileName = 'status.csv';		// Speicherort im gleichen Ort
			$fileName = '../status.csv';		// Speicherort einen Ordner darüber
			break;
	}	

	// öffnet die CSV-Datei, um Daten in das fileContentOld-Array zu lesen
	$file = fopen($fileName, 'r');	
	
	/* fileContentOld ist ein Array:
	 * Position 0 - Status des Professors
	 * Position 1 - erste Meldung, die angezeigt wird
	 * Position 2 - zweite Meldung, die angezeigt wird
	 */	
	$fileContentOld = fgetcsv($file, 1000, ",");
	fclose($file);
	
	// Übergabe des Array-Inhalts, als JSON formatiert, an das Javascript-Skript 
	echo json_encode( array( 'status' => $fileContentOld[0], 'newsNew' => $fileContentOld[1], 'newsOld' => $fileContentOld[2] ) );
	
	// Beendet das PHP-Skript
	die;
?>