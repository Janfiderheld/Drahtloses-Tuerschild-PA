<?php
	//Zeigt ein Bild auf der vollen Größe des E-Papers an
	
	$pfad = "contents/static_image/Huhn_bw.jpg";	//Dateipfad
	$Datei = pathinfo($pfad);
	$extension = $Datei['extension'];
	//Überprüfen ob der Pfad wirklich zu einem Bild führt
	if($extension == "png" OR $extension == "jpg" OR $extension == "jpeg"){
        
		//Bildressource erstellen in abhängigkeit der Dateiendung
		if($Datei['extension'] == "png"){
            $imageSource = imagecreatefrompng($pfad);
        }
        if($Datei['extension'] == "jpg" OR $Datei['extension'] == "jpeg" ){
            $imageSource = imagecreatefromjpeg($pfad);
        }		
	
		//Höhe und Weite des Bildes einlesen
		list($originalWidth, $originalHeight) = getimagesize($pfad);
	
		if($originalWidth < $originalHeight){
			//Bild um 90 Grad drehen
			$imageSource = imagerotate ($imageSource , 90 , 0 );
			$width = $originalHeight;
			$height = $originalWidth;
		}
		else{
			$width = $originalWidth;
			$height = $originalHeight;
		}
		//Bild ausgeben
		imagecopyresampled($im, $imageSource, 0, 0, 0, 0, $displayWidth, $displayHeight, $width, $height);
    }
?>