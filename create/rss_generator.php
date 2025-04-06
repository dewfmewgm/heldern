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

// Debug: Toon de opgehaalde HTML-inhoud
header("Content-Type: text/plain; charset=utf-8");
echo "DEBUG: Opgehaalde HTML-inhoud:\n\n";
echo $htmlContent;

// Eenvoudige scraping op basis van jouw originele aanpak
$items = [];

// Zoek naar artikel-secties
$articleStart = '<article';
$articleEnd = '</article>';
$offset = 0;

while (($startPos = strpos($htmlContent, $articleStart, $offset)) !== false) {
    $endPos = strpos($htmlContent, $articleEnd, $startPos);
    if ($endPos === false) break;

    $articleHtml = substr($htmlContent, $startPos, $endPos - $startPos + strlen($articleEnd));
    
    // Debug: Toon de HTML van elk artikel
    echo "\n\nDEBUG: Gevonden artikel HTML:\n";
    echo $articleHtml;

    // Zoek naar titel
    $title = '';
    if (preg_match('/<h2.*?>(.*?)<\/h2>/s', $articleHtml, $titleMatch)) {
        $title = strip_tags($titleMatch[1]);
    }
    // Debug: Toon gevonden titel
    echo "\nDEBUG: Gevonden titel: " . $title . "\n";

    // Zoek naar link
    $link = '';
    if (preg_match('/<a.*?href=["\'](.*?)["\'].*?>/s', $articleHtml, $linkMatch)) {
        $link = $linkMatch[1];
        // Maak relatieve links absoluut
        if (!preg_match('#^https?://#', $link)) {
            $link = rtrim($websiteUrl, '/') . '/' . ltrim($link, '/');
        }
    }
    // Debug: Toon gevonden link
    echo "DEBUG: Gevonden link: " . $link . "\n";

    // Zoek naar beschrijving
    $description = '';
    if (preg_match('/<p.*?>(.*?)<\/p>/s', $articleHtml, $descMatch)) {
        $description = strip_tags($descMatch[1]);
    }
    // Debug: Toon gevonden beschrijving
    echo "DEBUG: Gevonden beschrijving: " . $description . "\n";

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

// Debug: Toon aantal gevonden artikelen
header("Content-Type: text/plain; charset=utf-8");
echo "\n\nDEBUG: Aantal gevonden artikelen: " . count($items) . "\n\n";
print_r($items);
exit;

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
