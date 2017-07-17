# Search query

POST to /api/maps/search

```json
{
  "keys": "krolf", 
  "fields": ["title"], 
  "limit": 5, 
  "page": 0, 
  "index": "activities",
  "facets": {
    "zipcode": "",
    "categories": [ "IT", "Mad" ]
  }
}
```