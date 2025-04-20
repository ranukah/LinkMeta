<?php
declare(strict_types=1);

// Array of valid API keys
$apiKeys = [
    'your_api_key1',
    'your_api_key2',
    'your_api_key3',
    // Add more keys as needed
];

/**
 * Check if API key is valid.
 */
function isValidApiKey(string $key): bool
{
    return in_array($key, $GLOBALS['apiKeys'], true);
}

/**
 * Fetch webpage content with cURL and UTF-8 support.
 * Throws exception on error.
 */
function getWebPageContent(string $url): string
{
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        throw new InvalidArgumentException('Invalid URL provided');
    }

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HEADER         => false,
        CURLOPT_ENCODING       => '',
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_USERAGENT      => 'MetadataFetcher/1.0',
    ]);

    $data = curl_exec($curl);
    if (curl_errno($curl)) {
        throw new RuntimeException('Error retrieving URL content: ' . curl_error($curl));
    }
    curl_close($curl);

    return $data;
}

/**
 * Extract title, description, and og:image from HTML.
 */
function extractMetaData(string $html): array
{
    libxml_use_internal_errors(true);
    $doc = new DOMDocument();
    $doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
    libxml_clear_errors();

    $title = '';
    $description = '';
    $image = '';

    $titles = $doc->getElementsByTagName('title');
    if ($titles->length > 0) {
        $title = trim($titles->item(0)->textContent);
    }

    foreach ($doc->getElementsByTagName('meta') as $meta) {
        $nameAttr = strtolower($meta->getAttribute('name'));
        $propAttr = strtolower($meta->getAttribute('property'));

        if ($nameAttr === 'description') {
            $description = $meta->getAttribute('content');
        }
        if ($propAttr === 'og:image') {
            $image = $meta->getAttribute('content');
        }
    }

    return [$title, $description, $image];
}

/**
 * Send JSON response with proper headers and status code.
 */
function sendJsonResponse(array $payload, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
}

// Main logic
try {
    $apiKey = $_GET['api_key'] ?? '';
    if (!isValidApiKey($apiKey)) {
        sendJsonResponse(['error' => 'Invalid or missing API key'], 401);
        exit;
    }

    $url = $_GET['url'] ?? '';
    if (empty($url)) {
        throw new InvalidArgumentException('No URL provided');
    }

    $htmlContent = getWebPageContent($url);
    [$title, $description, $image] = extractMetaData($htmlContent);

    sendJsonResponse([
        'title'       => $title,
        'url'         => $url,
        'description' => $description,
        'image'       => $image,
    ]);
} catch (InvalidArgumentException $e) {
    sendJsonResponse(['error' => $e->getMessage()], 400);
} catch (RuntimeException $e) {
    sendJsonResponse(['error' => $e->getMessage()], 502);
} catch (JsonException $e) {
    sendJsonResponse(['error' => 'JSON encoding error'], 500);
} catch (Exception $e) {
    sendJsonResponse(['error' => $e->getMessage()], 500);
}