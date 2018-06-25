<?php
	echo "Übertragene Daten werden gespeichert...\n";
	// Name (& Speicherort) der CSV-Datei, über die die Daten ausgetauscht werden
	$fileName = 'status.csv';		// Speicherort im gleichen Ort
	$fileName = './status.csv';		// Speicherort einen Ordner darüber
	// Standard-Text aus dem TextArea der HTML-Datei
	$defaultText = 'Geben Sie hier die neue Meldung ein!';
	// Einstellen der internen Zeichenkodierung auf UTF-8 (wie bei HTML)
	mb_internal_encoding("UTF-8");
	
	// die übertragenen Daten von JavaScript
	$state = htmlentities($_POST['zustand']);
	$newsWithoutDate = htmlentities($_POST['meldungWrite']);
	$newsToWrite = date('d.m.y - H:i')." ".$newsWithoutDate;
	$newsNew = htmlentities($_POST['meldung1']);
	// die erste alte Meldung, ohne die Datumsangabe vorweg
	// Da die Datumsangabe genau 16 Zeichen lang ist, wird bei Zeichen 17 getrennt 
	$newsNewWithoutDate = substr($newsNew, 17);
	$newsOld = htmlentities($_POST['meldung2']);
	
	// Wenn der Zustand nicht übertragen wurde, hat die gesamte Übertragung nicht geklappt
	if(!empty($state)) {
		// Wenn eine leere neue Meldung ODER die Standard-Meldung übertragen wurde, sollen die Meldungen nicht geändert werden
		if(empty($newsWithoutDate) || (strcmp($newsWithoutDate, $defaultText) === 0)) {
			$dataToWrite = "\"".$state."\",\"".$newsNew."\",\"".$newsOld."\"";
			echo "Keine neue Meldung eingetragen\n";
		//	Wenn die neue Meldung gleich der ersten alten Meldung ist, dann sollen diese ebenfalls nicht geändert werden
		} else if(strcmp($newsWithoutDate, $newsNewWithoutDate) === 0) {
			$dataToWrite = "\"".$state."\",\"".$newsNew."\",\"".$newsOld."\"";
			echo "Die gleiche Meldung wie beim letzten Mal\n";
		// Die neue Meldung ist ungleich der alten Meldung, also wird die neue Meldung hinzugefügt
		} else if(strcmp($newsWithoutDate, $newsNewWithoutDate) !== 0){
			$dataToWrite = "\"".$state."\",\"".$newsToWrite."\",\"".$newsNew."\"";
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