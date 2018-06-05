<?php
    checkFreeType();
	
    //Konstanten
    const RAUM = 'Raum 365';
	const RAUMNAME = 'Labor für Informationstechnologie';
	const BILD = "contents/static_image/Huhn.jpg";
	
	//Variablen
    $fontSize = $scale;
    $cursorY += $fontSize*1.5;
	$status = file_get_contents('status.txt'); //status aus der status.txt Datei einlesen
	
	//Platzhalter, die auf dauer über externe Dateien eingelesen werden sollen
	$infomonitor = array("10.05.2018 - 15:12 Uhr: Ab nächstem Montag bin ich für 2 Wochen im Urlaub! Ich bin ab dem 28.05.2018 wieder anzutreffen. Bei dringenden Anliegen melden sie sich bitte im Büro des FB5.","21.05.2018 - 11:37 Uhr: Die Klausureinsicht im Fach \"Software-Design\" findet am 12.06. um 12:30 Uhr statt! Sie geht bis 13:30.","24.05.2018 - 08:50 Uhr: Die Vorlesung \"Programmiersprachen 2\" fällt diese Woche aus!");
	
	//Raumnummer und Name
    imagettftext($im, $fontSize, 0, 10, $cursorY, $black, $DEFAULT_FONT['regular'], RAUM);
	$fontSize = 0.7*$scale;
	imagettftext($im, $fontSize, 0, 210, $cursorY-1, $black, $DEFAULT_FONT['regular'], RAUMNAME);
    $cursorY += 10;
	imagesetthickness($im, 10);
    imageline ($im , 0 , $cursorY , $displayWidth , $cursorY , $yellow	);
	
	//Infomonitor
	$fontSize = 0.5*$scale;
	for ($j=0; $j<count($infomonitor); $j++){
		$words = explode(" ",$infomonitor[$j]);
		$wnum = count($words);
		$line = '';
		$text = '';
		$cursorY = $cursorY+$fontSize*2;
		imagettftext($im, $fontSize, 0, 20, $cursorY, $black, $DEFAULT_FONT['regular'], '>');
		for($i=0; $i<$wnum; $i++){
			$line .= $words[$i];
			$dimensions = imagettfbbox($fontSize, 0, $DEFAULT_FONT['regular'], $line);
			$lineWidth = $dimensions[2] - $dimensions[0];
			if ($lineWidth > $displayWidth-80) {
				imagettftext($im, $fontSize, 0, 40, $cursorY, $black, $DEFAULT_FONT['regular'], $text);
				$cursorY = $cursorY+$fontSize*1.5;
				$line = $words[$i].' ';
				$text = $words[$i].' ';
			}
			else {
				$line.=' ';
				$text.=$words[$i].' ';
			}
		}
		imagettftext($im, $fontSize, 0, 40, $cursorY, $black, $DEFAULT_FONT['regular'], $line);
	}
	
	//grauer Kasten am unteren Ende
	$cursorY = $displayHeight-100;
	imagesetthickness($im, 8);
	imageline ($im , 0 , $cursorY , $displayWidth , $cursorY , $black);
	imagefilledrectangle($im, 0 , $cursorY , $displayWidth , $displayHeight , $grey);

	//Einlesen der csv Datei mit den Infos zur Person
    if (($handle = fopen("Professor.csv", "r")) !== FALSE) {
		$csv_data = fgetcsv($handle, 1000, ",");
        fclose($handle);
    }
    else {
        exit("Problem beim lesen der csv Datei");
    }
	
	//Infos zur Person ausgeben
	$cursorY += 20;
	$fontSize = $fontSize*1.1;
	imagettftext($im, $fontSize, 0, 10, $cursorY, $black, $DEFAULT_FONT['regular'], $csv_data[0]);	//Name
	$cursorY += 30;
	imagettftext($im, $fontSize, 0, 10, $cursorY, $black, $DEFAULT_FONT['regular'], "E-Mail: ");
	imagettftext($im, $fontSize, 0, 100, $cursorY, $black, $DEFAULT_FONT['regular'], $csv_data[1]);	//E-Mail Adresse
	$cursorY += 30;
	imagettftext($im, $fontSize, 0, 10, $cursorY, $black, $DEFAULT_FONT['regular'], "Telefon: ");
	imagettftext($im, $fontSize, 0, 100, $cursorY, $black, $DEFAULT_FONT['regular'], $csv_data[2]);	//Telefonnummer
	
	//Anwesenheitsstatus ausgeben
	$fontSize = $fontSize*1.1;
	$cursorY = $displayHeight-60;
	imagettftext($im, $fontSize, 0, 400, $cursorY, $black, $DEFAULT_FONT['regular'], "Ich bin ...");
	$cursorY += 30;
	imagettftext($im, $fontSize, 0, 400, $cursorY, $black, $DEFAULT_FONT['regular'], "... ".$status);
	
	//Bild anzeigen (Das Bild wird auf eine Größe höhe von 100 pixeln Runterskaliert. Das Seitenverhältniss bleibt gleich)
    $imageSource = imagecreatefromjpeg(BILD);
    list($originalwidth, $originalheight) = getimagesize(BILD);
	$heigth = 100;
	$width = 77; ($heigth/$originalheight)*$width;
    imagecopyresampled($im, $imageSource, $displayWidth-$width, $displayHeight-$heigth, 0, 0, $width, $heigth, $originalwidth, $originalheight);
?>