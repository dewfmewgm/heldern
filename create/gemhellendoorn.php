<?php
// Configuratie
$websiteUrl = isset($_GET['url']) ? $_GET['url'] : die('Geef een URL op met ?url=');

// Controleer of de URL correct is
if (!filter_var($websiteUrl, FILTER_VALIDATE_URL)) {
    die("Ongeldige URL. Controleer of je een correcte website-URL invoert.");
}

// Haal de HTML-inhoud op van de opgegeven website
$htmlContent = @file_get_contents($websiteUrl);
if ($htmlContent === FALSE) {
    die("Kon de website niet ophalen. Controleer of de URL correct is.");
}

// Eenvoudige scraping op basis van flexibelere zoekcriteria
$items = [];

// Zoek naar secties die potentieel artikelen bevatten
$articleStart = '<article';
$articleEnd = '</article>';
$offset = 0;

// Als er geen <article>-tags zijn, gebruik een alternatieve structuur
if (strpos($htmlContent, $articleStart) === false) {
    $articleStart = '<div';
    $articleEnd = '</div>';
}

while (($startPos = strpos($htmlContent, $articleStart, $offset)) !== false) {
    $endPos = strpos($htmlContent, $articleEnd, $startPos);
    if ($endPos === false) break;

    $articleHtml = substr($htmlContent, $startPos, $endPos - $startPos + strlen($articleEnd));

    // Zoek naar titel
    $title = '';
    if (preg_match('/<h2.*?>(.*?)<\/h2>/s', $articleHtml, $titleMatch)) {
        $title = strip_tags($titleMatch[1]);
    } elseif (preg_match('/<h3.*?>(.*?)<\/h3>/s', $articleHtml, $titleMatch)) {
        $title = strip_tags($titleMatch[1]);
    }

    // Zoek naar link
    $link = '';
    if (preg_match('/<a.*?href=["\'](.*?)["\'].*?>/s', $articleHtml, $linkMatch)) {
        $link = $linkMatch[1];
        // Maak relatieve links absoluut
        if (!preg_match('#^https?://#', $link)) {
            $link = rtrim($websiteUrl, '/') . '/' . ltrim($link, '/');
        }
    }

    // Zoek naar beschrijving
    $description = '';
    if (preg_match('/<p.*?>(.*?)<\/p>/s', $articleHtml, $descMatch)) {
        $description = strip_tags($descMatch[1]);
    }

    // Voeg item toe als minimaal titel en link aanwezig zijn
    if ($title && $link) {
        $items[] = [
            'title' => $title,
            'link' => $link,
            'description' => $description,
            'date' => date(DATE_RSS),
        ];
    }

    $offset = $endPos + strlen($articleEnd);
}

// Genereer RSS-feed
header("Content-Type: application/rss+xml; charset=utf-8");
echo "<?xml version=\"1.0\"?>\n";
echo "<rss version=\"2.0\">\n";
echo "<channel>\n";
echo "<title>Automatisch gegenereerde RSS-feed</title>\n";
echo "<link>{$websiteUrl}</link>\n";
echo "<description>RSS-feed van de opgegeven website</description>\n";

foreach ($items as $item) {
    echo "<item>\n";
    echo "<title>" . htmlspecialchars($item['title']) . "</title>\n";
    echo "<link>" . htmlspecialchars($item['link']) . "</link>\n";
    echo "<description>" . htmlspecialchars($item['description']) . "</description>\n";
    echo "<pubDate>" . htmlspecialchars($item['date']) . "</pubDate>\n";
    echo "</item>\n";
}

echo "</channel>\n";
echo "</rss>\n";
?>
