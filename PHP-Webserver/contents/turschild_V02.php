<?php
    checkFreeType();
    //Konstanten
    const RAUM = 'Raum 365';
	const RAUMNAME = 'Labor für Informationstechnologie';
	const BILD = "contents/static_image/Huhn.jpg";
	
	//Variablen
    $fontSize = $scale*1.5;
    $cursorY += $fontSize*1.2;
	
	//Einlesen der csv Datei mit den Infos zur Person
    if (($handle = fopen("Professor.csv", "r")) !== FALSE) {
		$professor = fgetcsv($handle, 1000, ",");
        fclose($handle);
    }
    else {
        exit("Problem beim lesen der csv Datei \"Professor\"");
    }
	
	//Einlesen der csv Datei mit dem Status und den Meldungen
    if (($handle = fopen("status.csv", "r")) !== FALSE) {
		$status = fgetcsv($handle, 1000, ",");
        fclose($handle);
    }
    else {
        exit("Problem beim lesen der csv Datei \"Status\"");
    }
	
	//Raumnummer und Raumname
    imagettftext($im, $fontSize, 0, 10, $cursorY, $black, $DEFAULT_FONT['regular'], RAUM);
	$cursorY += $fontSize;
	$fontSize = $scale;
	imagettftext($im, $fontSize, 0, 10, $cursorY, $black, $DEFAULT_FONT['regular'], RAUMNAME);
    $cursorY += 15;
	imagesetthickness($im, 10);
    imageline ($im , 0 , $cursorY , $displayWidth , $cursorY , $yellow	);
	
	//Infomonitor
	$fontSize = 0.5*$scale;
	$nachicht;
	$abbruch=0;
	for ($j=1; $j<count($status); $j++){
		$words = explode(" ",$status[$j]);
		$wnum = count($words);
		$line = '';
		$text = '';
		$cursorY += $fontSize*2;
		$textHeigth = $cursorY;
		for($i=0; $i<$wnum; $i++){
			$line .= $words[$i];
			$dimensions = imagettfbbox($fontSize, 0, $DEFAULT_FONT['bold'], $line);
			$lineWidth = $dimensions[2] - $dimensions[0];
			if ($lineWidth > $displayWidth-40) {	//Breitenbeschränkung des Bildschirms
				if ($textHeigth+$fontSize*1.5 > $displayHeight-105){ //Wenn die Höhenbeschrenkung überschritten wird, wird die letzte Nachicht nicht ausgegeben
					$abbruch=1;
					break;
				}
				$nachicht[]= $text;
				$textHeigth +=$fontSize*1.5;
				$line = $words[$i].' ';
				$text = $words[$i].' ';
			}
			else {
				$line.=' ';
				$text.=$words[$i].' ';
			}
		}
		if ($abbruch == 1){
			break;
		}
		//ausgabe einer Nachicht
		imagettftext($im, $fontSize, 0, 20, $cursorY, $black, $DEFAULT_FONT['bold'], '>');
		$nachicht[]= $text; //letzte Zeile in das Nachichtenarray speichern
		for ($i=0; $i<count($nachicht); $i++){
			imagettftext($im, $fontSize, 0, 40, $cursorY, $black, $DEFAULT_FONT['bold'], $nachicht[$i]);
			$cursorY += $fontSize*1.5;
		}
		$cursorY -= $fontSize*1.5;
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
	imagettftext($im, $fontSize, 0, 10, $cursorY, $black, $DEFAULT_FONT['bold'], $professor[0]);	//Name
	$cursorY += 30;
	imagettftext($im, $fontSize, 0, 10, $cursorY, $black, $DEFAULT_FONT['bold'], "E-Mail: ");
	imagettftext($im, $fontSize, 0, 100, $cursorY, $black, $DEFAULT_FONT['bold'], $professor[1]);//E-Mail Adresse
	$cursorY += 30;
	imagettftext($im, $fontSize, 0, 10, $cursorY, $black, $DEFAULT_FONT['bold'], "Telefon: ");
	imagettftext($im, $fontSize, 0, 100, $cursorY, $black, $DEFAULT_FONT['bold'], $professor[2]);//Telefonnummer
	
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