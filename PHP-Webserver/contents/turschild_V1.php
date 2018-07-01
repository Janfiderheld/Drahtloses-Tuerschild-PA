<?php
	/**
	*	Projektarbeit Sommersemester2018 - Drahtloses Türschild mit E-Paper-Display
	*	Author: Niclas Muss
	*	Version: 1.0
	*	Letzte Änderung: 28.06.2018
	*	
	*	Dieses Skript zeichnet, wenn es in der index.php aufgerufen wird, auf die Bilddatei $im
	*	Gezeichnet zeichnet ein Türschild, welches in Abhängigkeit des Parameters "Professor" ein Individuelles Türschild
	**/
    checkFreeType();
	
	//Variablendefinition
	
    $schriftGroesse = 42; //Schriftgröe der Überschrift. Anderer Text wird anhand dieser Größe skaliert
    $cursorY += $schriftGroesse*1.2; //Variable, die eine Höhe in Pixeln angibt
	
	If($hasRed){
		$bild = "contents/Bilder/".$professor."_bwr.jpg";
		if($_GET['debug'] == 'true'){
			$red = ImageColorAllocate($im, 0xFF, 0xFF, 0x00);
		}
	} else{
		$red = $black;
		$bild = "contents/Bilder/".$professor.".jpg";
	}
	
	//Einlesen der csv Datei mit den Infos zur Person
    if (($handle = fopen($professor.".csv", "r")) !== FALSE) {
		while (($reihe = fgetcsv($handle, 1000, ",")) !== FALSE) {
			if ($reihe[1] !== ''){
				$konstanten[] = $reihe[1]; //Das 2. Element jeder Reihe ist der variable Wert
			}
			else{
				die("Die csv-Datei \"professor\" wurde nicht richtig ausgefüllt");
			}
        }
        fclose($handle);
    }
    else {
        die("Problem beim lesen der csv-Datei \"professor\"");
    }
	
	//Einlesen der csv Datei mit dem Status und den Meldungen
    if (($handle = fopen($professor."_status.csv", "r")) !== FALSE) {
		$status = fgetcsv($handle, 1000, ",");
        fclose($handle);
		if ($status[0] == ''){
			die("Es ist kein Anweenheitsstauts bekannt");
		}
    }
    else {
        die("Problem beim lesen der csv-Datei \"professor_status\"");
    }
	
	
	/**
	*	Der obere Teil des Anzeigebildes.
	*	Zeigt die Raumnummer und den Raumnamen des Büros an.
	*	eingelesen werden diese datein aus der csv, die zur Einrichtung benutzt wird.
	*	Wird durch einen gelben oder schwarzen balken vom Rest der Anzeige getrennt.
	**/
    imagettftext($im, $schriftGroesse, 0, 10, $cursorY, $black, $DEFAULT_FONT['regular'], "Raum ".$konstanten[0]);
	$cursorY += $schriftGroesse;
	$schriftGroesse = $schriftGroesse/1.5;
	imagettftext($im, $schriftGroesse, 0, 10, $cursorY, $black, $DEFAULT_FONT['regular'], $konstanten[1]);
    $cursorY += 15;
	imagesetthickness($im, 10);
    imageline ($im , 0 , $cursorY , $displayWidth , $cursorY , $red	);
	
	
	/**
	*	Der mittlere Teil des Anzeigebildes.
	*	Zeigt einen kleinen Infomonitor an, auf dem beliebig viele Meldungen angezeigt werden können.
	*	Zeigt nur so viel Nachichten an, wie auf den Bildschirm passen, wobei die neuste Immer oben steht.
	**/
	$schriftGroesse = $schriftGroesse/2;
	$nachicht = array();
	$cursorY += $schriftGroesse*1.5;
	for ($nachichtCount=1; $nachichtCount<count($status); $nachichtCount++){ //Äußere For-Schleife: Wiederholung pro Nachicht
		$woerter = explode(" ",$status[$nachichtCount]);//Array der Wörter aus dem eingegebenen Text
		$woerterAnzahl = count($woerter);//Anzahl der Wörter
		$zeile = '';//Die mommentan beschriebene Zeile (benutzt zum Messes)
		$text = '';//Die mommentan druckbare Zeile
		$cursorY += $schriftGroesse*0.5;
		$nachichtHoehe = $cursorY;
		for($woerterCount=0; $woerterCount<$woerterAnzahl; $woerterCount++){ //Innere For Schleife: Wiederholung pro Wort
			$zeile .= $woerter[$woerterCount];
			$dimensionen = imagettfbbox($schriftGroesse, 0, $DEFAULT_FONT['bold'], $zeile);
			$zeileLaenge = $dimensionen[2] - $dimensionen[0]; //Länge einer Zeile
			
			if ($zeileLaenge > $displayWidth-60) { //Wenn die Breitenbeschränkung überschritten wird, soll eine neue Zeile gestartet werden
				$nachicht[]= $text; //Text dem Nachichtenarray hinzufügen
				$nachichtHoehe +=$schriftGroesse*1.5; //Höhe der Nachicht um die aktuelle Zeile erhöhen
				$zeile = $woerter[$woerterCount].' '; //Zeile und Text zurücksetzten
				$text = $woerter[$woerterCount].' ';
			}
			else {
				$zeile.=' ';
				$text.=$woerter[$woerterCount].' ';
			}
		}
		if ($nachichtHoehe > $displayHeight-105){ //Wenn die Höhenbeschrenkung überschritten wird, solllen keine Nachichten mehr ausgegeben werden
			break;
		}
		//Ausgabe einer Nachicht
		imagettftext($im, $schriftGroesse, 0, 20, $cursorY, $black, $DEFAULT_FONT['bold'], '>');
		$nachicht[]= $text; //letzte Zeile in das Nachichtenarray speichern
		for ($woerterCount=0; $woerterCount<count($nachicht); $woerterCount++){
			$dimensionen = imagettfbbox($schriftGroesse, 0, $DEFAULT_FONT['bold'], $nachicht[$woerterCount]);
			imagettftext($im, $schriftGroesse, 0, 40, $cursorY, $black, $DEFAULT_FONT['bold'], $nachicht[$woerterCount]);
			$cursorY += $schriftGroesse*1.5;
		}
		$nachicht = array(); //Nachichtenarray zurücksetzen
	}
	
	/**
	*	Der unter Teil des Anzeigebildes.
	*	Zeigt Infos über den Professor an, dem das Büro gehört.
	*	Zeigt den Namen, die E-Mail Adress und die Telefonnummer aus, die aus der professor.csv gelesen wurden
	*	Außeredem wird hier der Anwesenheitsstatus und ein kleines Bild angegeben angegeben.
	**/
	$cursorY = $displayHeight-102;
	imagesetthickness($im, 4);
	imageline ($im , 0 , $cursorY , $displayWidth , $cursorY , $black);
	
	//Infos zur Person ausgeben
	$schriftGroesse = 18;	//Schriftgröße des unteren Kastens skaliert nicht mit der oberen Schriftgröße
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
	
	//Bild anzeigen (Das Bild wird auf eine Größe höhe von 100 pixeln Runterskaliert. Das Seitenverhältniss kannch dabei ändern)
	$Datei = pathinfo($bild);
	$endung = $Datei['extension'];
	if($endung == "png" OR $endung == "jpg" OR $ndung == "jpeg"){ //Überprüfen ob der Pfad wirklich zu einem Bild führt
		if($Datei['extension'] == "png"){
				$imageSource = imagecreatefrompng($bild);
			}
			if($Datei['extension'] == "jpg" OR $Datei['extension'] == "jpeg" ){
				$imageSource = imagecreatefromjpeg($bild);
			}		
		list($originalwidth, $originalheight) = getimagesize($bild); //Höhe und Weite des Bildes einlesen
		$heigth = 100;
		$width = 75; ($heigth/$originalheight)*$width;
		imagecopyresampled($im, $imageSource, $displayWidth-$width, $displayHeight-$heigth, 0, 0, $width, $heigth, $originalwidth, $originalheight); //Bildgröße zu den geforderten neuen Werten verändern
	}
?>