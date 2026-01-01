# Home Assistant TRMNL Plugin

This is a native TRMNL plugin that displays Home Assistant entity data on your TRMNL device.

## Installation

1. In your TRMNL BYOS dashboard, go to **Plugins** → **Create Plugin**
2. Choose **Custom Plugin** type
3. Copy the webhook URL provided
4. In the Home Assistant add-on dashboard (`/homeassistant`):
   - Paste the webhook URL into the "TRMNL Webhook URL" field
   - Select which entities you want to expose
   - Click "Save & Send Test"
5. Configure the plugin markup (see `plugin-markup.html`)
6. Assign the plugin to your TRMNL device display

## How It Works

1. You configure which Home Assistant entities to expose in the add-on's web UI
2. The add-on sends webhook notifications to TRMNL with the current state of those entities
3. The TRMNL plugin receives the webhook data and displays it using the markup template
4. Webhooks are sent when:
   - You save the webhook configuration
   - You click "Test Webhook"
   - (Future) When entity states change in Home Assistant

## Plugin Configuration

### Webhook Data Structure

The webhook sends data in this format:

```json
{
  "merge_variables": {
    "entities": [
      {
        "entity_id": "sensor.temperature",
        "state": "22.5",
        "display_name": "Living Room Temperature",
        "attributes": {
          "unit_of_measurement": "°C",
          "friendly_name": "Living Room Temperature",
          "device_class": "temperature"
        },
        "last_changed": "2026-01-01T12:00:00+00:00",
        "last_updated": "2026-01-01T12:00:00+00:00"
      }
    ],
    "updated_at": "2026-01-01T12:00:00+00:00"
  }
}
```

### Using in TRMNL Markup

You can access the entities in your TRMNL markup template using Liquid templating:

```liquid
{% for entity in entities %}
  {{ entity.display_name }}: {{ entity.state }} {{ entity.attributes.unit_of_measurement }}
{% endfor %}
```

See `plugin-markup.html` for a complete example.

## Advanced Features

### Filtering by Domain

You can filter entities by domain in your markup:

```liquid
{% assign sensors = entities | where: "entity_id", "sensor." %}
```

### Formatting Values

Use Liquid filters to format values:

```liquid
{{ entity.state | round: 1 }}
```

### Icons

Map entity domains to icons:

```liquid
{% if entity.entity_id contains "sensor" %}📊{% endif %}
{% if entity.entity_id contains "light" %}💡{% endif %}
{% if entity.entity_id contains "switch" %}🔌{% endif %}
```

## Troubleshooting

### Webhook not receiving data

1. Check that the webhook URL is correct
2. Ensure entities are selected in the add-on UI
3. Click "Test Webhook" to send a manual update
4. Check TRMNL plugin logs for errors

### Entities not showing

1. Verify entities are enabled in Home Assistant
2. Check that entities are selected in the add-on configuration
3. Test the webhook to ensure data is being sent

## Example Use Cases

- **Weather Dashboard**: Display outdoor temperature, humidity, and weather conditions
- **Home Status**: Show if doors are locked, lights are on, and who's home
- **Energy Monitor**: Track solar production and home energy consumption
- **Climate Control**: Display thermostat settings and indoor temperatures
- **Security**: Show camera status and motion sensor states
