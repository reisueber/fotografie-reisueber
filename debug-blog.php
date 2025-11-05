<?php

// Direkter Check was auf /blog passiert
$url = 'https://fotografie-reisueber.de/blog';

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HEADER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status: $httpCode\n\n";

// Trenne Header und Body
list($headers, $body) = explode("\r\n\r\n", $response, 2);

echo "=== HEADERS ===\n";
echo $headers . "\n\n";

echo "=== BODY LENGTH ===\n";
echo "Body Länge: " . strlen($body) . " Zeichen\n\n";

echo "=== SUCHE NACH SPEZIFISCHEN ELEMENTEN ===\n";
echo "Enthält 'mt-8': " . (strpos($body, 'mt-8') !== false ? 'JA' : 'NEIN') . "\n";
echo "Enthält 'Blog': " . (strpos($body, 'Blog') !== false ? 'JA' : 'NEIN') . "\n";
echo "Enthält 'Noch keine Blog-Posts': " . (strpos($body, 'Noch keine Blog-Posts') !== false ? 'JA' : 'NEIN') . "\n";
echo "Enthält '{% block content %}': " . (strpos($body, '{% block content %}') !== false ? 'JA (RAW TWIG!)' : 'NEIN') . "\n\n";

echo "=== ERSTE 2000 ZEICHEN DES BODY ===\n";
echo substr($body, 0, 2000) . "\n";

