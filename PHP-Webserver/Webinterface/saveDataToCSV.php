<?php

	echo "Übertragene Daten werden gespeichert...\n";
	$fileName = 'Meldungsaustausch.csv';
	
	// die übertragenen Daten von JavaScript
	$state = $_POST['zustand'];
	$newsWithoutDate = $_POST['meldungWrite'];
	$newsToWrite = date('d.m.y - H:i')." ".$newsWithoutDate;
	$newsNew = $_POST['meldung1'];
	$newsNewWithoutDate = substr($newsNew, 17);
	$newsOld = $_POST['meldung2'];
	
	if(!empty($state)) {
		if(empty($newsWithoutDate)) {
			$dataToWrite = "\"".$state."\",\"".$newsNew."\",\"".$newsOld."\"";
			echo "Keine neue Meldung eingetragen\n";
		} else if(strcmp($newsWithoutDate, $newsNewWithoutDate) === 0) {
			$dataToWrite = "\"".$state."\",\"".$newsNew."\",\"".$newsOld."\"";
			echo "Die gleiche Meldung wie beim letzten Mal\n";
		} else if(strcmp($newsWithoutDate, $newsNewWithoutDate) !== 0){
			$dataToWrite = "\"".$state."\",\"".$newsToWrite."\",\"".$newsNew."\"";
			echo "Neue Meldung!\n";
		}
		
		$file = fopen($fileName, 'w'); 
		fwrite($file, $dataToWrite);		
		fclose($file);
		echo "Übertragene Daten wurden gespeichert!\n";
	} else {
		echo "Übertragene Daten wurden nicht gespeichert!\n";
	}

	exit;
?>