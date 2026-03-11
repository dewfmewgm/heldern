<?php
/**
 * Xelion Communications Audio Proxy
 *
 * Haalt een geluidsopname op van de Xelion API voor een specifieke communicatie.
 *
 * Gebruik: xelion_audio.php?oid={communicatie-oid}
 *
 * Xelion API documentatie:
 * https://portal.xelion.com/nl/apidocs/user-guide/resources/communications_audio_get.html
 */

// Configuratie — stel deze omgevingsvariabelen in op de server
define('XELION_HOST',      getenv('XELION_HOST')      ?: '');  // bijv. bedrijf.xelion.com
define('XELION_TENANT',    getenv('XELION_TENANT')    ?: 'master');
define('XELION_USERNAME',  getenv('XELION_USERNAME')  ?: '');
define('XELION_PASSWORD',  getenv('XELION_PASSWORD')  ?: '');
define('XELION_USERSPACE', getenv('XELION_USERSPACE') ?: 'heldern-audio-proxy');

// Controleer vereiste configuratie zo vroeg mogelijk
if (empty(XELION_HOST) || empty(XELION_USERNAME) || empty(XELION_PASSWORD)) {
    http_response_code(503);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Xelion API-configuratie ontbreekt. Stel de omgevingsvariabelen in.']);
    exit;
}

/**
 * Logt in op de Xelion API en geeft het authenticatietoken terug.
 *
 * Eindpunt: POST https://{host}/api/v1/{tenant}/me/login
 *
 * @param string $host     Xelion hostname (bijv. bedrijf.xelion.com)
 * @param string $tenant   Tenant-naam (standaard: master)
 * @param string $username Gebruikersnaam
 * @param string $password Wachtwoord
 * @param string $userSpace Unieke gebruikersruimte voor deze sessie
 * @return string|false Authenticatietoken of false bij mislukking
 */
function xelionLogin($host, $tenant, $username, $password, $userSpace) {
    $url = "https://{$host}/api/v1/{$tenant}/me/login";

    $payload = json_encode([
        'userName'  => $username,
        'password'  => $password,
        'userSpace' => $userSpace,
    ]);

    $context = stream_context_create([
        'http' => [
            'method'  => 'POST',
            'header'  => "Content-Type: application/json\r\nAccept: application/json\r\n",
            'content' => $payload,
            'ignore_errors' => true,
        ],
        'ssl' => [
            'verify_peer'      => true,
            'verify_peer_name' => true,
        ],
    ]);

    $response = @file_get_contents($url, false, $context);
    if ($response === false) {
        error_log('Xelion login mislukt: ' . (error_get_last()['message'] ?? 'onbekende fout'));
        return false;
    }

    $data = json_decode($response, true);
    if (empty($data['authentication'])) {
        return false;
    }

    return $data['authentication'];
}

/**
 * Haalt de audiogegevens op van een communicatie via de Xelion API.
 *
 * Eindpunt: GET https://{host}/api/v1/{tenant}/communications/{oid}/audio
 *
 * @param string $host   Xelion hostname
 * @param string $tenant Tenant-naam
 * @param string $token  Authenticatietoken
 * @param string $oid    Object-ID van de communicatie
 * @return array|false Array met 'data' (binaire audio), 'contentType' en 'headers', of false
 */
function xelionGetCommunicationAudio($host, $tenant, $token, $oid) {
    $url = "https://{$host}/api/v1/{$tenant}/communications/{$oid}/audio";

    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => "Authorization: xelion {$token}\r\nAccept: audio/wav, audio/mpeg, audio/ogg, application/octet-stream\r\n",
            'ignore_errors' => true,
        ],
        'ssl' => [
            'verify_peer'      => true,
            'verify_peer_name' => true,
        ],
    ]);

    $audioData = @file_get_contents($url, false, $context);
    if ($audioData === false) {
        error_log('Xelion audio ophalen mislukt voor OID ' . $oid . ': ' . (error_get_last()['message'] ?? 'onbekende fout'));
        return false;
    }

    // Lees de HTTP-statuscode en Content-Type uit de responsheaders
    $responseHeaders = $http_response_header ?? [];
    $statusCode      = 0;
    $contentType     = 'application/octet-stream';

    foreach ($responseHeaders as $header) {
        if (preg_match('#^HTTP/\S+\s+(\d+)#i', $header, $m)) {
            $statusCode = (int) $m[1];
        }
        if (preg_match('#^Content-Type:\s*([^;\r\n]+)#i', $header, $m)) {
            $contentType = trim($m[1]);
        }
    }

    if ($statusCode === 403 || $statusCode === 401) {
        return ['error' => 'forbidden', 'statusCode' => $statusCode];
    }

    if ($statusCode !== 200) {
        return false;
    }

    return [
        'data'        => $audioData,
        'contentType' => $contentType,
        'headers'     => $responseHeaders,
    ];
}

// Valideer de opgegeven communicatie-OID
$oid = isset($_GET['oid']) ? trim($_GET['oid']) : '';
if ($oid === '' || !preg_match('/^[a-zA-Z0-9_\-]+$/', $oid)) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Ongeldig of ontbrekend OID-parameter.']);
    exit;
}

// Inloggen bij de Xelion API
$token = xelionLogin(XELION_HOST, XELION_TENANT, XELION_USERNAME, XELION_PASSWORD, XELION_USERSPACE);
if ($token === false) {
    http_response_code(502);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Kon niet inloggen bij Xelion API.']);
    exit;
}

// Audio ophalen
$result = xelionGetCommunicationAudio(XELION_HOST, XELION_TENANT, $token, $oid);
if ($result === false) {
    http_response_code(404);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Audio niet gevonden voor communicatie ' . htmlspecialchars($oid) . '.']);
    exit;
}
if (isset($result['error']) && $result['error'] === 'forbidden') {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Toegang geweigerd voor communicatie ' . htmlspecialchars($oid) . '.']);
    exit;
}

// Bepaal de bestandsextensie op basis van het ontvangen Content-Type
$extensionMap = [
    'audio/wav'                => 'wav',
    'audio/x-wav'              => 'wav',
    'audio/mpeg'               => 'mp3',
    'audio/mp3'                => 'mp3',
    'audio/ogg'                => 'ogg',
    'audio/opus'               => 'opus',
    'application/octet-stream' => 'bin',
];
$extension = $extensionMap[$result['contentType']] ?? 'bin';
$safeOid   = preg_replace('/[^a-zA-Z0-9_\-]/', '', $oid);

// Stuur de audio door naar de client
header('Content-Type: ' . $result['contentType']);
header('Content-Disposition: inline; filename="communicatie_' . $safeOid . '.' . $extension . '"');
header('Content-Length: ' . strlen($result['data']));
header('Cache-Control: no-store, no-cache, must-revalidate');
header('X-Content-Type-Options: nosniff');

echo $result['data'];
exit;
