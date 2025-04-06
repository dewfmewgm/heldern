<?php
$host = 'localhost';
$dbname = 'ni3n';
$username = 'nij3den';
$password = 'fAGU3p';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Databasefout: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Het snelste nieuws uit de gemeente Hellendoorn</title>
</head>
<body>
    <div id="timelineContainer"></div>	
    <div id="seoPopup" class="popup">
        <div class="popup-content">
            <span class="close" onclick="closePopup()">&times;</span>
            <p>HELLENDOORN.NET -> De snelste nieuwssite voor de gemeente Hellendoorn !</p>
        </div>
    </div>
    <script src="backgrounds.js" defer></script>
    <script src="timeline.js" defer></script>
    <script src="popup.js" defer></script>
    <script>
        const posts = <?php
            $stmt = $pdo->query("SELECT * FROM posts WHERE description IS NOT NULL AND TRIM(description) != '' ORDER BY pubDate DESC LIMIT 8");
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        ?>;        
        document.addEventListener("DOMContentLoaded", function() {
            setRandomBackgroundForMobile();
            changeBackgroundForDesktop();
            displayPosts();
            showPopup();
        });
    </script>
</body>
</html>