<?php

// Array of valid API keys
$apiKeys = array(
    "your_api_key1",
    "your_api_key2",
    "your_api_key3",
    // Add more keys as needed
);

// Function to verify API key
function isValidApiKey($apiKey) {
    return in_array($apiKey, $GLOBALS['apiKeys']);
}

// Function to get webpage content using UTF-8 encoding
function getWebPageContent($url) {
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        throw new Exception("Invalid URL provided");
    }

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_ENCODING, '');
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true); // SSL Certificate verification
    $data = curl_exec($curl);
    if (curl_errno($curl)) {
        throw new Exception("Error retrieving URL content: " . curl_error($curl));
    }
    curl_close($curl);
    return $data;
}

// Function to extract meta data with UTF-8 support
function extractMetaData($html) {
    $doc = new DOMDocument();
    @$doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
    $nodes = $doc->getElementsByTagName('title');
    $title = $nodes->item(0)->nodeValue;

    $metaTags = $doc->getElementsByTagName('meta');
    $description = '';
    $image = '';

    foreach ($metaTags as $tag) {
        if ($tag->getAttribute('name') == 'description') {
            $description = $tag->getAttribute('content');
        }
        if ($tag->getAttribute('property') == 'og:image') {
            $image = $tag->getAttribute('content');
        }
    }

    return array($title, $description, $image);
}

// Main logic
try {
    header('Content-Type: application/json; charset=utf-8');
    if (!isset($_GET['api_key']) || !isValidApiKey($_GET['api_key'])) {
        http_response_code(401);
        throw new Exception('Invalid or missing API key');
    }

    if (!isset($_GET['url'])) {
        throw new Exception("No URL provided");
    }

    $url = $_GET['url'];
    $htmlContent = getWebPageContent($url);
    list($title, $description, $image) = extractMetaData($htmlContent);

    echo json_encode([
        'title' => $title,
        'url' => $url,
        'description' => $description,
        'image' => $image
    ], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

?>
