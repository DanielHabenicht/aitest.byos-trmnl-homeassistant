# TRMNL Plugin Examples for Home Assistant

This directory contains examples and documentation for using the Home Assistant integration with TRMNL devices via webhooks.

## Webhook-Based Integration

The Home Assistant add-on uses **webhooks** to push entity data to TRMNL's native plugin system. This is more efficient than polling and integrates seamlessly with TRMNL's architecture.

### How It Works

1. **Configure Webhook**: In the Home Assistant add-on UI (`/homeassistant`), enter your TRMNL plugin webhook URL
2. **Select Entities**: Choose which Home Assistant entities to expose to TRMNL
3. **Receive Webhooks**: TRMNL receives webhook notifications with current entity states
4. **Display Data**: Use TRMNL's native Liquid templating to render the data

## Creating a TRMNL Plugin

### Step 1: Create Custom Plugin in TRMNL

1. Log into your TRMNL BYOS dashboard
2. Navigate to **Plugins** → **Custom Plugins**
3. Click **Create Plugin**
4. Copy the webhook URL provided (e.g., `https://your-trmnl/api/custom_plugins/[uuid]/webhook`)

### Step 2: Configure in Home Assistant Add-on

1. Open the Home Assistant add-on dashboard at `/homeassistant`
2. Paste your TRMNL webhook URL in the configuration section
3. Select which entities you want to expose
4. Click "Save & Send Test" to test the webhook

### Step 3: Design Your Plugin Markup

Use TRMNL's Liquid templating to display the data. See the `trmnl-plugin/` directory for complete examples.

## Webhook Data Format

The webhook sends data in this structure:

```json
{
  "merge_variables": {
    "entities": [
      {
        "entity_id": "sensor.temperature",
        "state": "22.5",
        "display_name": "Living Room Temp",
        "attributes": {
          "unit_of_measurement": "°C",
          "friendly_name": "Living Room Temperature"
        },
        "last_changed": "2026-01-01T12:00:00+00:00"
      }
    ],
    "updated_at": "2026-01-01T12:00:00+00:00"
  }
}
```

## Example Plugin Markup

### Basic Entity List

```liquid
<h1>🏠 Home Status</h1>

{% for entity in entities %}
  <div>
    <strong>{{ entity.display_name }}</strong>: 
    {{ entity.state }} {{ entity.attributes.unit_of_measurement }}
  </div>
{% endfor %}
```

### Temperature Dashboard

```liquid
<h1>🌡️ Temperature Monitor</h1>

{% for entity in entities %}
  {% if entity.entity_id contains "temperature" %}
    <div class="temp-card">
      <div class="location">{{ entity.display_name }}</div>
      <div class="value">
        {{ entity.state | round: 1 }}{{ entity.attributes.unit_of_measurement }}
      </div>
    </div>
  {% endif %}
{% endfor %}
```

### Smart Home Overview

```liquid
<div class="grid">
  <div class="section">
    <h2>💡 Lights</h2>
    {% for entity in entities %}
      {% if entity.entity_id contains "light" %}
        {{ entity.display_name }}: {{ entity.state | upcase }}
      {% endif %}
    {% endfor %}
  </div>
  
  <div class="section">
    <h2>🌡️ Climate</h2>
    {% for entity in entities %}
      {% if entity.entity_id contains "climate" or entity.entity_id contains "temperature" %}
        {{ entity.display_name }}: {{ entity.state }}°
      {% endif %}
    {% endfor %}
  </div>
</div>
```

## Advanced Features

### Filtering by Domain

```liquid
{% assign sensors = entities | where_exp: "item", "item.entity_id contains 'sensor'" %}
{% for sensor in sensors %}
  {{ sensor.display_name }}: {{ sensor.state }}
{% endfor %}
```

### Conditional Display

```liquid
{% for entity in entities %}
  {% if entity.entity_id == "binary_sensor.front_door" %}
    {% if entity.state == "on" %}
      ⚠️ Front door is OPEN
    {% else %}
      ✓ Front door is closed
    {% endif %}
  {% endif %}
{% endfor %}
```

### Icons by Device Class

```liquid
{% for entity in entities %}
  {% assign device_class = entity.attributes.device_class %}
  
  {% if device_class == "temperature" %}🌡️
  {% elsif device_class == "humidity" %}💧
  {% elsif device_class == "battery" %}🔋
  {% elsif device_class == "motion" %}🚶
  {% endif %}
  
  {{ entity.display_name }}: {{ entity.state }}
{% endfor %}
```

## Best Practices

1. **Limit Entities**: Only expose entities you want to display to reduce webhook payload size
2. **Use Display Names**: Configure friendly display names in the add-on UI
3. **Test Webhooks**: Use the "Test Webhook" button to verify your setup
4. **Handle Empty Data**: Always check if entities exist before displaying
5. **Optimize for E-ink**: Design for high contrast and minimal updates

## Complete Example

See the `../trmnl-plugin/` directory for a complete, production-ready TRMNL plugin example with:
- Full markup template
- Icon mapping
- Responsive grid layout
- Error handling

## Troubleshooting

### No Data Showing

1. Verify webhook URL is correct
2. Check that entities are selected in the add-on
3. Click "Test Webhook" to send manual update
4. Check TRMNL plugin logs

### Old Data Displaying

1. Webhooks are sent when you save configuration or test
2. For real-time updates, consider setting up automation in Home Assistant
3. Check webhook delivery in TRMNL plugin logs

## Support

- Add-on Issues: [GitHub Repository](https://github.com/DanielHabenicht/aitest.byos-trmnl-homeassistant)
- TRMNL Documentation: [TRMNL BYOS](https://github.com/usetrmnl/byos_laravel)
- Home Assistant: [Home Assistant Community](https://community.home-assistant.io/)

