# Changelog

All notable changes to this project will be documented in this file.

## [1.0.0] - 2026-01-01

### Added
- Initial release of TRMNL BYOS Home Assistant add-on
- Integration with official TRMNL BYOS Laravel Docker image
- Home Assistant entity configuration dashboard
- SQLite database support for easy setup
- Home Assistant ingress support
- Web UI accessible through Home Assistant
- Automatic database migrations
- Laravel application key generation
- Persistent data storage in /data directory

### Technical
- Built on top of official ghcr.io/usetrmnl/byos_laravel Docker image
- Custom Home Assistant integration layer
- Entity exposure management via web dashboard
- API endpoints for TRMNL plugins to access Home Assistant data
