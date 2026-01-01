# TRMNL BYOS Docs

## Home Assistant Entity Integration

This add-on includes a custom integration layer that allows you to expose Home Assistant entities to your TRMNL displays.

### Accessing Home Assistant Entities

The add-on has access to the Home Assistant Supervisor API, which allows it to:

1. Query available entities
2. Read entity states
3. Subscribe to state changes

### Configuration

The custom dashboard (available at `/homeassistant` path in the web UI) provides an interface to:

1. Browse all available Home Assistant entities
2. Select which entities to expose to TRMNL
3. Configure display formatting for each entity
4. Map entities to TRMNL plugins

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

To extend the Home Assistant integration:

1. Access the Laravel application in `/var/www/html`
2. Create custom controllers in `app/Http/Controllers/HomeAssistant`
3. Add routes in `routes/web.php`
4. Use the Home Assistant API client to fetch entity data

### API Endpoints

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
