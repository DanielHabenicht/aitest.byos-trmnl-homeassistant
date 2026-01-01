# TRMNL BYOS Docs

## Home Assistant Entity Integration

This add-on includes a webhook-based integration that allows you to push Home Assistant entity data to your TRMNL displays using TRMNL's native plugin system.

### How It Works

1. Create a custom plugin in your TRMNL BYOS dashboard
2. Copy the webhook URL from the plugin settings
3. Configure the webhook URL in the Home Assistant add-on dashboard (`/homeassistant`)
4. Select which entities you want to expose
5. The add-on sends webhook notifications to TRMNL with current entity states
6. Your TRMNL plugin receives and displays the data using Liquid templating

### Configuration

The custom dashboard (available at `/homeassistant` path in the web UI) provides an interface to:

1. Configure TRMNL webhook URL
2. Browse all available Home Assistant entities
3. Select which entities to expose to TRMNL
4. Test webhook delivery
5. View webhook status and errors

### Webhook Data Format

The add-on sends data to TRMNL in this format:

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
          "friendly_name": "Living Room Temperature"
        },
        "last_changed": "2026-01-01T12:00:00+00:00",
        "last_updated": "2026-01-01T12:00:00+00:00"
      }
    ],
    "updated_at": "2026-01-01T12:00:00+00:00"
  }
}
```

### API Access

The add-on uses the `SUPERVISOR_TOKEN` environment variable to authenticate with Home Assistant. This token is automatically provided by the Home Assistant supervisor.

### Example Use Cases

- **Temperature Sensors**: Display indoor/outdoor temperatures
- **Weather**: Show current weather conditions
- **Calendar**: Display upcoming events
- **Device Status**: Monitor smart home device states
- **Energy**: Track energy consumption
- **Custom Sensors**: Display any sensor data from Home Assistant

### Development

To create a TRMNL plugin that works with this add-on:

1. In TRMNL BYOS, create a new Custom Plugin
2. Copy the webhook URL
3. Use Liquid templating to display entity data
4. Access entities via `entities` array in your markup
5. See `trmnl-plugin/` directory for complete examples

### Plugin Markup Example

```liquid
<h1>🏠 Home Assistant</h1>

{% for entity in entities %}
  <div>
    <strong>{{ entity.display_name }}</strong>: 
    {{ entity.state }} {{ entity.attributes.unit_of_measurement }}
  </div>
{% endfor %}
```

### API Endpoints

While the primary integration uses webhooks, the add-on also provides these internal API endpoints:

- `POST /homeassistant/api/webhook/config` - Configure webhook settings
- `POST /homeassistant/api/webhook/test` - Test webhook delivery
- `GET /homeassistant/api/entities` - List all Home Assistant entities
- `POST /homeassistant/api/entities/config` - Configure entity exposure

The Home Assistant Supervisor API is accessible at:
- Base URL: `http://supervisor/core/api`
- Authentication: Bearer token from `SUPERVISOR_TOKEN` environment variable

Example API calls:
```bash
# Get all states
curl -H "Authorization: Bearer $SUPERVISOR_TOKEN" \
     http://supervisor/core/api/states

# Get specific entity
curl -H "Authorization: Bearer $SUPERVISOR_TOKEN" \
     http://supervisor/core/api/states/sensor.temperature
```

