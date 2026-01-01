# Home Assistant Add-on: TRMNL BYOS

![Supports aarch64 Architecture][aarch64-shield]
![Supports amd64 Architecture][amd64-shield]
![Supports armhf Architecture][armhf-shield]
![Supports armv7 Architecture][armv7-shield]
![Supports i386 Architecture][i386-shield]

Host your own TRMNL (The Really Minimal kNowledge Library) backend using the BYOS (Bring Your Own Server) variant with deep Home Assistant integration.

## About

This add-on provides a self-hosted TRMNL backend that integrates seamlessly with Home Assistant. TRMNL is an e-ink display platform that shows various types of information. With this add-on, you can:

- Host your own TRMNL backend using the official BYOS Laravel application
- Access the TRMNL dashboard to configure your devices
- Create a custom dashboard to select which Home Assistant entities to expose to your TRMNL displays
- Full control over your data with local hosting

## Installation

1. Add this repository to your Home Assistant add-on store
2. Install the "TRMNL BYOS" add-on
3. Configure the add-on (see Configuration section)
4. Start the add-on
5. Access the web interface through the Home Assistant UI or directly via port 8080

## Configuration

### Add-on configuration:

```yaml
app_key: "base64:your-generated-key-here"
app_url: "http://homeassistant.local:8080"
db_connection: sqlite
```

### Options

#### Option: `app_key`

The Laravel application key. If left empty, the add-on will generate one on the first run. **Important**: Save this key in your configuration to prevent session invalidation on restarts.

#### Option: `app_url`

The URL where your add-on will be accessible. Update this to match your Home Assistant URL.

Default: `http://homeassistant.local:8080`

#### Option: `db_connection`

Database connection type. SQLite is recommended for most users.

Options:
- `sqlite` (recommended)
- `mysql`
- `pgsql`

Default: `sqlite`

## Usage

1. **First Run**: 
   - Start the add-on and check the logs for the generated `APP_KEY`
   - Copy this key and add it to your add-on configuration
   - Restart the add-on

2. **Access the Dashboard**:
   - Click "Open Web UI" in the add-on page
   - Or navigate to `http://your-home-assistant:8080`

3. **TRMNL Dashboard**:
   - The main TRMNL dashboard allows you to manage your TRMNL devices
   - Configure displays, layouts, and plugins
   - All standard TRMNL BYOS features are available

4. **Home Assistant Entity Configuration**:
   - Access the custom dashboard to configure entity exposure
   - Select which sensors, switches, and other entities to make available
   - Configure how they appear on your TRMNL displays

## Features

- ✅ Full TRMNL BYOS Laravel application
- ✅ SQLite database (no external database required)
- ✅ Persistent data storage
- ✅ Home Assistant ingress support
- ✅ Automatic SSL via Home Assistant
- ✅ Multi-architecture support (amd64, armv7, aarch64, etc.)

## Support

For issues with:
- **This add-on**: Open an issue on this repository
- **TRMNL BYOS**: Visit the [official TRMNL BYOS repository](https://github.com/usetrmnl/byos_laravel)
- **Home Assistant**: Visit the [Home Assistant community](https://community.home-assistant.io/)

## Credits

This add-on uses:
- [TRMNL BYOS Laravel](https://github.com/usetrmnl/byos_laravel) - The official TRMNL backend
- [Home Assistant](https://www.home-assistant.io/) - Open source home automation

## License

MIT License - See LICENSE file for details

[aarch64-shield]: https://img.shields.io/badge/aarch64-yes-green.svg
[amd64-shield]: https://img.shields.io/badge/amd64-yes-green.svg
[armhf-shield]: https://img.shields.io/badge/armhf-yes-green.svg
[armv7-shield]: https://img.shields.io/badge/armv7-yes-green.svg
[i386-shield]: https://img.shields.io/badge/i386-yes-green.svg