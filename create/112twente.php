<?php
// Configuratie
$defaultUrl = 'https://www.hellendoorn.nl/actueel/'; // Standaard URL als ?url= ontbreekt
$websiteUrl = isset($_GET['url']) ? $_GET['url'] : $defaultUrl; 
$rssFeedTitle = "Website RSS Feed";
$rssFeedDescription = "Een automatisch gegenereerde RSS-feed van een opgegeven website.";
$rssFeedLink = $websiteUrl;

// Controleer of de URL correct is
if (!filter_var($websiteUrl, FILTER_VALIDATE_URL)) {
    die("Ongeldige URL. Controleer of je een correcte website-URL invoert.");
}

// Functie om de website te scrapen
function scrapeWebsite($url) {
    $context = stream_context_create(array('http' => array('header' => 'User-Agent: PHP')));
    $htmlContent = @file_get_contents($url, false, $context);

    if ($htmlContent === FALSE) {
        die("Kon de website niet ophalen. Controleer of de URL correct is.");
    }

    $dom = new DOMDocument();
    @$dom->loadHTML($htmlContent); // Suppress warnings bij HTML-fouten
    $xpath = new DOMXPath($dom);

    // Zoek artikelen met titels, links en datums
    $articles = [];
    foreach ($xpath->query('//a[contains(@href, "/")]') as $element) {
        $title = trim($element->textContent);
        $link = $element->getAttribute('href');

        // Zorg ervoor dat de links absolute zijn
        if (!preg_match('#^https?://#', $link)) {
            $link = rtrim($url, '/') . '/' . ltrim($link, '/');
        }

        // Zoek een datum bij het element (pas aan aan jouw HTML-structuur)
        $dateElement = $xpath->query('.//time', $element);
        $date = null;
        if ($dateElement->length > 0) {
            $date = $dateElement->item(0)->getAttribute('datetime');
        }

        if (!empty($title) && !empty($link)) {
            $articles[] = [
                'title' => $title,
                'link' => $link,
                'date' => $date ? $date : date(DATE_RSS) // Gebruik huidige tijd als er geen datum is
            ];
        }
    }

    return $articles;
}

// Functie om RSS-feed te genereren
function generateRSS($articles, $title, $description, $link) {
    $rss = new SimpleXMLElement('<rss/>');
    $rss->addAttribute('version', '2.0');

    $channel = $rss->addChild('channel');
    $channel->addChild('title', htmlspecialchars($title));
    $channel->addChild('description', htmlspecialchars($description));
    $channel->addChild('link', htmlspecialchars($link));

    foreach ($articles as $article) {
        $item = $channel->addChild('item');
        $item->addChild('title', htmlspecialchars($article['title']));
        $item->addChild('link', htmlspecialchars($article['link']));
        if (!empty($article['date'])) {
            $item->addChild('pubDate', htmlspecialchars($article['date']));
        }
    }

    return $rss->asXML();
}

// Haal artikelen op
$articles = scrapeWebsite($websiteUrl);

// Genereer en toon RSS
header("Content-Type: application/rss+xml; charset=utf-8");
echo generateRSS($articles, $rssFeedTitle, $rssFeedDescription, $rssFeedLink);
?>
