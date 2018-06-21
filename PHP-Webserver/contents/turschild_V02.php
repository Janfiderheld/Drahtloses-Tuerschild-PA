<?php
    checkFreeType();
	
    //Konstanten
	const BILD = "contents/static_image/Huhn.jpg";
	
	//Variablen
    $fontSize = $scale*1.5;
    $cursorY += $fontSize*1.2;
	
	//Einlesen der csv Datei mit den Infos zur Person
    if (($handle = fopen("einrichtung.csv", "r")) !== FALSE) {
		while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
			if ($row[1] !== ''){
				$professor[] = $row[1]; //Das 2. Element jeder Reihe ist der Variable Wert
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
    imagettftext($im, $fontSize, 0, 10, $cursorY, $black, $DEFAULT_FONT['regular'], "Raum ".$professor[0]);
	$cursorY += $fontSize;
	$fontSize = $scale;
	imagettftext($im, $fontSize, 0, 10, $cursorY, $black, $DEFAULT_FONT['regular'], $professor[1]);
    $cursorY += 15;
	imagesetthickness($im, 10);
    imageline ($im , 0 , $cursorY , $displayWidth , $cursorY , $yellow	);
	
	//Infomonitor
	$fontSize = 0.5*$scale;
	$nachicht;
	$abbruch=0;
	$cursorY += $fontSize;
	for ($j=1; $j<count($status); $j++){ //Äußere For-Schleife: Wiederholung pro Nachicht
		$words = explode(" ",$status[$j]);//Array der Wörter aus dem eingegebenen Text
		$wnum = count($words);//Anzahl der Wörter
		$line = '';//Die mommentan beschriebene Zeile (benutzt zum Messes)
		$text = '';//Die mommentan druckbare Zeile
		$cursorY += $fontSize;
		$nachichtHeigth = $cursorY;
		for($i=0; $i<$wnum; $i++){ //Innere For Schleife: Wiederholung pro Wort
			$line .= $words[$i];
			$dimensions = imagettfbbox($fontSize, 0, $DEFAULT_FONT['bold'], $line);
			$lineWidth = $dimensions[2] - $dimensions[0]; //Länge einer Zeile
			$lineHeigth = $dimensions[1] - $dimensions[7]; //Höhe einer Zeile
			if ($lineWidth > $displayWidth-60) { //Wenn die Breitenbeschränkung überschritten wird, soll eine neue Zeile gestartet werden
				$nachicht[]= $text; //Text dem Nachichtenarray hinzufügen
				$nachichtHeigth +=$lineHeigth+$fontSize*0.5; //Höhe der Nachicht um die aktuelle Zeile erhöhen
				$line = $words[$i].' '; //Zeile und Text zurücksetzten
				$text = $words[$i].' ';
			}
			else {
				$line.=' ';
				$text.=$words[$i].' ';
			}
		}
		if ($nachichtHeigth+$lineHeigth > $displayHeight-105){ //Wenn die Höhenbeschrenkung überschritten wird, solllen keine Nachichten mehr ausgegeben werden
			break;
		}
		//Ausgabe einer Nachicht
		imagettftext($im, $fontSize, 0, 20, $cursorY, $black, $DEFAULT_FONT['bold'], '>');
		$nachicht[]= $text; //letzte Zeile in das Nachichtenarray speichern
		for ($i=0; $i<count($nachicht); $i++){
			$dimensions = imagettfbbox($fontSize, 0, $DEFAULT_FONT['bold'], $nachicht[$i]);
			$lineHeigth = $dimensions[1] - $dimensions[7]; //Höhe einer Zeile
			imagettftext($im, $fontSize, 0, 40, $cursorY, $black, $DEFAULT_FONT['bold'], $nachicht[$i]);
			$cursorY += $lineHeigth+$fontSize*0.5;
		}
		$nachicht = array(); //Nachichtenarray zurücksetzen
	}
	
	//Kasten am unteren Ende
	$cursorY = $displayHeight-102;
	imagesetthickness($im, 4);
	imageline ($im , 0 , $cursorY , $displayWidth , $cursorY , $black);
	
	//Infos zur Person ausgeben
	$fontSize = 0.5*$scale;
	$cursorY += 20;
	$fontSize = $fontSize*1.1;
	imagettftext($im, $fontSize, 0, 10, $cursorY, $black, $DEFAULT_FONT['bold'], $professor[2]);//Name
	$cursorY += 30;
	imagettftext($im, $fontSize*1.4, 0, 10, $cursorY, $black, $DEFAULT_FONT['bold'], "@");
	imagettftext($im, $fontSize, 0, 50, $cursorY, $black, $DEFAULT_FONT['bold'], $professor[3]);//E-Mail Adresse
	$cursorY += 30;
	imagettftext($im, $fontSize, 0, 10, $cursorY, $black, $DEFAULT_FONT['emoji'],"&#9742;");
	imagettftext($im, $fontSize, 0, 50, $cursorY, $black, $DEFAULT_FONT['bold'], $professor[4]);//Telefonnummer
	
	//Anwesenheitsstatus ausgeben
	$fontSize = $fontSize*1.1;
	$cursorY = $displayHeight-60;
	imagettftext($im, $fontSize, 0, 400, $cursorY, $black, $DEFAULT_FONT['bold'], "Ich bin ...");
	$cursorY += 30;
	imagettftext($im, $fontSize, 0, 400, $cursorY, $black, $DEFAULT_FONT['bold'], "... ".$status[0]);
	
	//Bild anzeigen (Das Bild wird auf eine Größe höhe von 100 pixeln Runterskaliert. Das Seitenverhältniss bleibt gleich)
    $imageSource = imagecreatefromjpeg(BILD);
    list($originalwidth, $originalheight) = getimagesize(BILD);
	$heigth = 100;
	$width = 77; ($heigth/$originalheight)*$width;
    imagecopyresampled($im, $imageSource, $displayWidth-$width, $displayHeight-$heigth, 0, 0, $width, $heigth, $originalwidth, $originalheight);
?>