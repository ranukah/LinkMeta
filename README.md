# LinkMeta
LinkMeta is designed to be used as an API. To extract metadata from a URL using LinkMeta, you need to make a GET request to the API endpoint with the required parameters.

### API Endpoint

Access the metadata extraction API by sending a GET request to the following URL:

GET /LinkMeta.php?api_key=your_api_key&url=http://example.com

Replace `your_api_key` with your actual API key and `http://example.com` with the URL from which you want to extract metadata.

### API Parameters

- `api_key` (required): Your API key to authenticate requests.
- `url` (required): The URL of the webpage you want to extract metadata from.

### API Response

The API returns JSON formatted data, which includes:

```json
{
  "title": "Example Title",
  "url": "http://example.com",
  "description": "This is an example description.",
  "image": "http://example.com/image.jpg"
}
