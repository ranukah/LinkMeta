# LinkMeta

LinkMeta is a simple PHP API to extract webpage metadata: title, description, and image URL from a given URL.

## Requirements

- PHP 7.4 or higher
- cURL extension enabled

## Installation

1. Upload `metadata_api.php` to your web server.
2. Open the file and add your API keys in the `$apiKeys` array.

## Usage

Make a GET request to the API endpoint with your API key and the target URL:

```
GET /metadata_api.php?api_key=YOUR_API_KEY&url=https://example.com
```

### Parameters

- `api_key` (string, required): Your API key for authentication.
- `url` (string, required): The full URL of the webpage to fetch metadata from.

### Success Response

- **Status Code:** 200 OK
- **Content-Type:** `application/json`

```json
{
  "title": "Example Page Title",
  "url": "https://example.com",
  "description": "This is the page description.",
  "image": "https://example.com/image.jpg"
}
```

### Error Responses

| Status Code | Reason                           | Example Response                              |
|-------------|----------------------------------|-----------------------------------------------|
| 400         | Bad Request (missing or invalid URL) | `{ "error": "No URL provided" }`         |
| 401         | Unauthorized (invalid API key)   | `{ "error": "Invalid or missing API key" }` |
| 502         | Bad Gateway (failed to fetch URL) | `{ "error": "Error retrieving URL content: ...." }` |
| 500         | Internal Server Error (others)   | `{ "error": "JSON encoding error" }`       |

Responses use `charset=utf-8` and return JSON with `JSON_UNESCAPED_UNICODE`.

### Configuration Options

- **Timeout:** 10 seconds for the HTTP request.
- **User-Agent:** `MetadataFetcher/1.0`.
- **SSL Verification:** Enabled by default (CURLOPT_SSL_VERIFYPEER = true).

## License

This project is open-source and released under the MIT License.