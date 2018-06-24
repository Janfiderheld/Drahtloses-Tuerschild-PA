<?php
    checkFreeType();
	
    //Konstanten
	const BILD = "contents/static_image/Huhn.jpg";
	
	//Variablen
    $schriftGroesse = 42;
    $cursorY += $schriftGroesse*1.2;
	
	//Einlesen der csv Datei mit den Infos zur Person
    if (($handle = fopen("einrichtung.csv", "r")) !== FALSE) {
		while (($reihe = fgetcsv($handle, 1000, ",")) !== FALSE) {
			if ($reihe[1] !== ''){
				$konstanten[] = $reihe[1]; //Das 2. Element jeder Reihe ist der Variable Wert
			}
			else{
				die("Die csv-Datei \"Einrichtung\" wurde nicht richtig ausgefüllt");
			}
        }
        fclose($handle);
    }
    else {
        die("Problem beim lesen der csv-Datei \"Einrichtung\"");
    }
	
	//Einlesen der csv Datei mit dem Status und den Meldungen
    if (($handle = fopen("status.csv", "r")) !== FALSE) {
		$status = fgetcsv($handle, 1000, ",");
        fclose($handle);
		if ($status[0] == ''){
			die("Es ist kein Anweenheitsstauts bekannt");
		}
    }
    else {
        die("Problem beim lesen der csv-Datei \"Status\"");
    }
	
	//Raumnummer und Raumname
    imagettftext($im, $schriftGroesse, 0, 10, $cursorY, $black, $DEFAULT_FONT['regular'], "Raum ".$konstanten[0]);
	$cursorY += $schriftGroesse;
	$schriftGroesse = 28;
	imagettftext($im, $schriftGroesse, 0, 10, $cursorY, $black, $DEFAULT_FONT['regular'], $konstanten[1]);
    $cursorY += 15;
	imagesetthickness($im, 10);
    imageline ($im , 0 , $cursorY , $displayWidth , $cursorY , $yellow	);
	
	//Infomonitor
	$schriftGroesse = 14;
	$nachicht = array();
	$cursorY += $schriftGroesse;
	for ($nachichtCount=1; $nachichtCount<=count($status); $nachichtCount++){ //Äußere For-Schleife: Wiederholung pro Nachicht
		$woerter = explode(" ",$status[$nachichtCount]);//Array der Wörter aus dem eingegebenen Text
		$woerterAnzahl = count($woerter);//Anzahl der Wörter
		$zeile = '';//Die mommentan beschriebene Zeile (benutzt zum Messes)
		$text = '';//Die mommentan druckbare Zeile
		$cursorY += $schriftGroesse;
		$nachichtHoehe = $cursorY;
		for($woerterCount=0; $woerterCount<$woerterAnzahl; $woerterCount++){ //Innere For Schleife: Wiederholung pro Wort
			$zeile .= $woerter[$woerterCount];
			$dimensionen = imagettfbbox($schriftGroesse, 0, $DEFAULT_FONT['bold'], $zeile);
			$zeileLaenge = $dimensionen[2] - $dimensionen[0]; //Länge einer Zeile
			$zeileHoehe = $dimensionen[1] - $dimensionen[7]; //Höhe einer Zeile
			if ($zeileLaenge > $displayWidth-60) { //Wenn die Breitenbeschränkung überschritten wird, soll eine neue Zeile gestartet werden
				$nachicht[]= $text; //Text dem Nachichtenarray hinzufügen
				$nachichtHoehe +=$zeileHoehe+$schriftGroesse*0.5; //Höhe der Nachicht um die aktuelle Zeile erhöhen
				$zeile = $woerter[$woerterCount].' '; //Zeile und Text zurücksetzten
				$text = $woerter[$woerterCount].' ';
			}
			else {
				$zeile.=' ';
				$text.=$woerter[$woerterCount].' ';
			}
		}
		if ($nachichtHoehe+$zeileHoehe > $displayHeight-105){ //Wenn die Höhenbeschrenkung überschritten wird, solllen keine Nachichten mehr ausgegeben werden
			break;
		}
		//Ausgabe einer Nachicht
		imagettftext($im, $schriftGroesse, 0, 20, $cursorY, $black, $DEFAULT_FONT['bold'], '>');
		$nachicht[]= $text; //letzte Zeile in das Nachichtenarray speichern
		for ($woerterCount=0; $woerterCount<count($nachicht); $woerterCount++){
			$dimensionen = imagettfbbox($schriftGroesse, 0, $DEFAULT_FONT['bold'], $nachicht[$woerterCount]);
			$zeileHoehe = $dimensionen[1] - $dimensionen[7]; //Höhe einer Zeile
			imagettftext($im, $schriftGroesse, 0, 40, $cursorY, $black, $DEFAULT_FONT['bold'], $nachicht[$woerterCount]);
			$cursorY += $zeileHoehe+$schriftGroesse*0.5;
		}
		$nachicht = array(); //Nachichtenarray zurücksetzen
	}
	
	//Kasten am unteren Ende
	$cursorY = $displayHeight-102;
	imagesetthickness($im, 4);
	imageline ($im , 0 , $cursorY , $displayWidth , $cursorY , $black);
	
	//Infos zur Person ausgeben
	$schriftGroesse = 18;
	$cursorY += 25;
	imagettftext($im, $schriftGroesse, 0, 10, $cursorY, $black, $DEFAULT_FONT['bold'], $konstanten[2]);//Name
	$cursorY += 30;
	$schriftGroesse = 15;
	imagettftext($im, $schriftGroesse*1.4, 0, 10, $cursorY, $black, $DEFAULT_FONT['bold'], "@");
	imagettftext($im, $schriftGroesse, 0, 50, $cursorY, $black, $DEFAULT_FONT['bold'], $konstanten[3]);//E-Mail Adresse
	$cursorY += 30;
	imagettftext($im, $schriftGroesse, 0, 10, $cursorY, $black, $DEFAULT_FONT['emoji'],"&#9742;");
	imagettftext($im, $schriftGroesse, 0, 50, $cursorY, $black, $DEFAULT_FONT['bold'], $konstanten[4]);//Telefonnummer
	
	//Anwesenheitsstatus ausgeben
	$schriftGroesse = $schriftGroesse*1.1;
	$cursorY = $displayHeight-60;
	imagettftext($im, $schriftGroesse, 0, 400, $cursorY, $black, $DEFAULT_FONT['bold'], "Ich bin ...");
	$cursorY += 30;
	imagettftext($im, $schriftGroesse, 0, 400, $cursorY, $black, $DEFAULT_FONT['bold'], "... ".$status[0]);
	
	//Bild anzeigen (Das Bild wird auf eine Größe höhe von 100 pixeln Runterskaliert. Das Seitenverhältniss bleibt gleich)
    $imageSource = imagecreatefromjpeg(BILD);
    list($originalwidth, $originalheight) = getimagesize(BILD);
	$heigth = 100;
	$width = 77; ($heigth/$originalheight)*$width;
    imagecopyresampled($im, $imageSource, $displayWidth-$width, $displayHeight-$heigth, 0, 0, $width, $heigth, $originalwidth, $originalheight);
?>