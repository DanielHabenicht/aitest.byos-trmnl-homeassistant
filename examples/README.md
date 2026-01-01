# Example TRMNL Plugins for Home Assistant

This directory contains example TRMNL plugins that demonstrate how to use the Home Assistant entity integration.

## Available Examples

### HomeAssistantDashboard.php

A comprehensive example plugin that:
- Fetches exposed Home Assistant entities
- Formats them for display on TRMNL
- Supports multiple layout styles
- Includes proper error handling
- Demonstrates icon mapping and state formatting

## Using the Examples

### Installation

1. Access your TRMNL BYOS installation
2. Navigate to the plugins directory (typically `/var/www/html/app/Plugins/`)
3. Copy the example plugin file:
   ```bash
   cp /path/to/examples/HomeAssistantDashboard.php /var/www/html/app/Plugins/
   ```
4. Register the plugin in your TRMNL dashboard
5. Configure and assign to a display layout

### Customization

You can customize these plugins by:

1. **Modifying the layout**: Change how entities are displayed
2. **Adding filters**: Filter entities by domain or attributes
3. **Custom formatting**: Change how states are formatted
4. **Icons**: Use different icons or emojis
5. **Refresh interval**: Adjust how often data is updated

## Creating Your Own Plugin

Use these examples as a template for creating custom plugins:

```php
namespace App\Plugins;

class MyCustomPlugin
{
    public static function metadata()
    {
        return [
            'name' => 'My Plugin',
            'description' => 'Custom plugin description',
            'author' => 'Your Name',
            'version' => '1.0.0',
        ];
    }

    public static function render($settings = [])
    {
        // Fetch Home Assistant data
        $entities = self::getHomeAssistantData();
        
        // Process and format data
        $data = self::processData($entities);
        
        return [
            'success' => true,
            'data' => $data,
        ];
    }

    private static function getHomeAssistantData()
    {
        $response = Http::get(url('/api/homeassistant/entities'));
        return $response->json()['entities'] ?? [];
    }

    private static function processData($entities)
    {
        // Your custom processing logic
        return $entities;
    }
}
```

## API Reference

### Available Endpoints

#### Get All Exposed Entities
```
GET /api/homeassistant/entities
```

Returns all entities that have been marked as "exposed" in the Home Assistant dashboard.

**Response:**
```json
{
  "success": true,
  "entities": [
    {
      "entity_id": "sensor.temperature",
      "state": "22.5",
      "attributes": {
        "friendly_name": "Living Room Temperature",
        "unit_of_measurement": "°C"
      },
      "display_name": "Living Room Temp",
      "format": null
    }
  ]
}
```

#### Get Specific Entity
```
GET /api/homeassistant/entities/{entity_id}
```

Returns data for a specific entity.

**Response:**
```json
{
  "success": true,
  "entity": {
    "entity_id": "sensor.temperature",
    "state": "22.5",
    "attributes": {
      "friendly_name": "Living Room Temperature",
      "unit_of_measurement": "°C"
    }
  }
}
```

## Best Practices

1. **Error Handling**: Always handle API failures gracefully
2. **Caching**: Consider caching entity data to reduce API calls
3. **Refresh Intervals**: Set appropriate refresh intervals (5-15 minutes recommended)
4. **Display Limits**: Limit the number of entities shown based on screen size
5. **State Formatting**: Format states appropriately for e-ink displays
6. **Icons**: Use simple, clear icons that work well on e-ink

## Common Use Cases

### Temperature Dashboard
Display temperature sensors from different rooms

### Energy Monitor
Show energy consumption and solar production

### Security Status
Display lock states, door sensors, and camera status

### Weather Station
Combine weather entities with indoor conditions

### Presence Detection
Show who's home using person/device tracker entities

## Support

For questions or issues:
- Check the main README.md
- Review DOCS.md for integration details
- Open an issue on GitHub
