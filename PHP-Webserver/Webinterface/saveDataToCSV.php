<?php
	/**
	*	Projektarbeit Sommersemester 2018 - Drahtloses Türschild mit E-Paper-Display
	*	Author: Jan-Philipp Töberg
	*	Version: 1.5
	*	Letzte Änderung: 28.06.2018
	*	
	*	Dieses PHP-Skript regelt den Schreibzugriff auf die unterschiedlichen CSV-Dateien.
	*	Dabei bekommt es Daten per XMLHttp-Request vom Client gesendet, welche es überprüft und anschließend
	*	in der passenden Datei speichert.
	**/

	// Einstellen der internen Zeichenkodierung auf UTF-8 (wie bei HTML)
	mb_internal_encoding("UTF-8");
		
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
	
	// Standard-Text in dem Eingabebereich
	$defaultText = 'Geben Sie hier die neue Meldung ein';
	
	// die übertragenen Daten von JavaScript
	$state = htmlentities($_POST['zustand']);
	$newsWithoutDate = htmlentities($_POST['meldungWrite']);
	$newsToWrite = date('d.m.y - H:i')." ".$newsWithoutDate;
	$newsNew = htmlentities($_POST['meldung1']);
	// die erste alte Meldung, ohne die Datumsangabe vorweg
	// Da die Datumsangabe genau 16 Zeichen lang ist, wird bei Zeichen 17 getrennt 
	$newsNewWithoutDate = substr($newsNew, 17);
	$newsOld = htmlentities($_POST['meldung2']);
	$currentProf = htmlentities($_POST['prof']);
	
	echo "Übertragene Daten werden gespeichert...\n";	
	
	// Wenn der Zustand nicht übertragen wurde, hat die gesamte Übertragung nicht geklappt
	if(!empty($state)) {
		// Wenn eine leere neue Meldung ODER die Standard-Meldung übertragen wurde, sollen die Meldungen nicht geändert werden
		if(empty($newsWithoutDate) || (strcmp($newsWithoutDate, $defaultText) === 0)) {
			$dataToWrite = $state.", ".$newsNew.", ".$newsOld;
			echo "Keine neue Meldung eingetragen\n";
		//	Wenn die neue Meldung gleich der ersten alten Meldung ist, dann sollen diese ebenfalls nicht geändert werden
		} else if(strcmp($newsWithoutDate, $newsNewWithoutDate) === 0) {
			$dataToWrite = $state.", ".$newsNew.", ".$newsOld;
			echo "Die gleiche Meldung wie beim letzten Mal\n";
		// Die neue Meldung ist ungleich der alten Meldung, also wird die neue Meldung hinzugefügt
		} else if(strcmp($newsWithoutDate, $newsNewWithoutDate) !== 0){
			$dataToWrite = $state.", ".$newsToWrite.", ".$newsNew;
			echo "Neue Meldung!\n";
		}
		
		// Die CSV-Datei wird geöffnet und beschrieben
		$file = fopen($fileName, 'w'); 
		fwrite($file, $dataToWrite);		
		fclose($file);
		echo "Übertragene Daten wurden gespeichert!\n";
	} else {
		echo "Übertragene Daten wurden nicht gespeichert!\nIrgendetwas ist schief gegangen...";
	}

	// Beendet das PHP-Skript
	die;
?>