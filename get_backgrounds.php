<?php
// Verbind met de database of geef een array van afbeeldings-URL's terug.
$backgroundImages = [
    'images/background1.jpg',
    'images/background2.jpg',
    'images/background3.jpg',
	'images/background4.jpg',
	'images/background5.jpg',
    // Voeg hier meer afbeeldingen toe
];

header('Content-Type: application/json');
echo json_encode($backgroundImages);
?>
