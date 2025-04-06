<?php
$host = 'localhost';
$dbname = 'nij3en';
$username = 'nij3en';
$password = 'fAGU3p';

try {
    // Verbinding maken met de database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Associatieve array van RSS-feed URLs met hun naam
    $rssFeeds = [
        "Gem Hellendoorn" => "https://www.hellendoorn.net/create/gemhellendoorn.php?url=https://www.hellendoorn.nl/actueel/",
        "112 Twente" => "https://www.hellendoorn.net/create/112twente.php?url=https://112twente.nl/categorie/nijverdal/",
		"Visite Twente" => "https://www.hellendoorn.net/create/visitetwente.php?url=https://www.visittwente.nl/uitagenda/vandaag/",
		"Versling.Salland" => "https://cdn.mysitemapgenerator.com/shareapi/rss/1412857044",
		"Nu.NL" => "https://www.hellendoorn.net/create/rss_generator2.php?url=https://www.nu.nl/tag/hellendoorn",
        "KNMI Verwachtingen" => "https://cdn.knmi.nl/knmi/xml/rss/rss_KNMIverwachtingen.xml",
		"Hier in Hellendoorn" => "https://www.hierinhellendoorn.nl/feed/",
		"Hart van Hellendoorn" => "https://www.hellendoorn.net/create/createfeed.php?url=https://www.hartvannijverdal.com/",
        // Voeg andere feeds toe met een beschrijvende naam
    ];

    // Voeg deze functie toe om HTML-tags te strippen
    function strip_html_tags($text) {
        return strip_tags($text);
    }

    foreach ($rssFeeds as $sourceName => $rssUrl) {
        try {
            // Haal de RSS-feed op
            $rssContent = simplexml_load_file($rssUrl);

            // Controleer of de feed succesvol is geladen
            if ($rssContent === false) {
                throw new Exception("Kon de RSS-feed niet laden: " . htmlspecialchars($rssUrl));
            }

            // Haal alleen het eerste item uit de RSS-feed (de nieuwste post)
            $latestItem = $rssContent->channel->item[0];

            $title = (string) $latestItem->title;
            $description = strip_html_tags((string) $latestItem->description);
            $link = (string) $latestItem->link;
            $pubDate = date('Y-m-d H:i:s', strtotime((string) $latestItem->pubDate));

            // Controleer of deze post al in de database staat
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE link = :link");
            $stmt->execute(['link' => $link]);
            $exists = $stmt->fetchColumn();

            if (!$exists) {
                // Voeg de nieuwe post toe aan de database, inclusief de naam van de bron
                $stmt = $pdo->prepare("INSERT INTO posts (title, description, link, pubDate, sourceName) VALUES (:title, :description, :link, :pubDate, :sourceName)");
                $stmt->execute([
                    ':title' => $title,
                    ':description' => $description,
                    ':link' => $link,
                    ':pubDate' => $pubDate,
                    ':sourceName' => $sourceName
                ]);
                echo "Nieuwste post toegevoegd van " . htmlspecialchars($sourceName) . ": " . $title . "<br>";
            } else {
                echo "Post bestaat al van " . htmlspecialchars($sourceName) . ": " . $title . "<br>";
            }

        } catch (Exception $e) {
            echo "Fout bij " . htmlspecialchars($sourceName) . ": " . $e->getMessage() . "<br>";
        }
    }
} catch (PDOException $e) {
    echo "Databasefout: " . $e->getMessage();
}
?>
