# Development Notes

## Architecture

### Components

1. **Base Image**
   - Uses official TRMNL BYOS Docker image (ghcr.io/usetrmnl/byos_laravel:latest)
   - Includes PHP 8.4, nginx, and all TRMNL BYOS functionality
   - Built on serversideup-php Alpine base

2. **Home Assistant Add-on Layer**
   - Adds Home Assistant integration on top of TRMNL BYOS
   - Custom Laravel controller for entity management
   - REST API for entity exposure
   - Web UI for configuration

3. **Home Assistant Integration**
   - Custom Laravel controller for entity management
   - REST API for entity exposure
   - Web UI for configuration

### Data Flow

```
Home Assistant Entities
         ↓
   Supervisor API
         ↓
HomeAssistantController
         ↓
   SQLite Database
         ↓
   TRMNL Plugins
         ↓
  TRMNL Display
```

## Custom Files

### Laravel Extensions

Located in `rootfs/var/www/html-custom/`:

1. **Controllers**
   - `HomeAssistantController.php`: Main integration controller
   - Handles entity fetching, configuration, and exposure

2. **Views**
   - `dashboard.blade.php`: Entity configuration UI
   - Interactive dashboard for selecting entities

3. **Routes**
   - `homeassistant.php`: Route definitions
   - API and web routes for integration

4. **Migrations**
   - `create_homeassistant_entities_table.php`: Database schema
   - Stores entity configuration

### Integration Script

`setup-ha-integration.sh`:
- Copies custom files to Laravel installation
- Runs during container startup
- Idempotent (safe to run multiple times)

## API Endpoints

### Web Routes

- `GET /homeassistant` - Entity configuration dashboard

### API Routes

- `GET /homeassistant/api/entities` - List all HA entities
- `GET /homeassistant/api/entities/{id}` - Get specific entity
- `POST /homeassistant/api/entities/config` - Save entity config
- `DELETE /homeassistant/api/entities/config/{id}` - Remove entity config
- `GET /api/homeassistant/entities` - Get exposed entities (for TRMNL)

## Environment Variables

Set in `run.sh`:

- `APP_KEY`: Laravel application key
- `APP_URL`: Add-on URL
- `SUPERVISOR_TOKEN`: Auto-provided by Home Assistant
- `HOMEASSISTANT_URL`: Supervisor API endpoint
- `DB_CONNECTION`: Database type
- `DB_DATABASE`: Database path

## Database Schema

### homeassistant_entities

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| entity_id | string | Home Assistant entity ID |
| display_name | string | Custom display name |
| format | string | Display format string |
| enabled | boolean | Whether entity is exposed |
| created_at | timestamp | Creation time |
| updated_at | timestamp | Last update time |

## Configuration Options

From `config.yaml`:

- `app_key`: Laravel encryption key (required after first run)
- `app_url`: URL where add-on is accessible
- `db_connection`: Database type (sqlite/mysql/pgsql)

## Security Considerations

1. **Supervisor Token**: Automatically provided by Home Assistant
2. **CSRF Protection**: Laravel CSRF tokens on all forms
3. **API Authentication**: Can be added for TRMNL plugin access
4. **Database**: Local SQLite file, isolated in container

## Future Enhancements

### Planned Features

- [ ] Entity grouping and organization
- [ ] Custom formatting templates
- [ ] Real-time entity updates via WebSocket
- [ ] TRMNL plugin templates for common entities
- [ ] Bulk entity configuration
- [ ] Export/import entity configurations
- [ ] Advanced filtering and search
- [ ] Entity history and trends

### Technical Improvements

- [ ] Add automated tests
- [ ] Implement caching for entity data
- [ ] Add logging and monitoring
- [ ] Create migration script for updates
- [ ] Add backup/restore functionality

## Debugging

### View Logs

```bash
# In Home Assistant
Settings → Add-ons → TRMNL BYOS → Log

# Or via CLI
ha addons logs trmnl-byos
```

### Common Issues

1. **App key not found**: Generate key on first run
2. **Database errors**: Check permissions on /data/database
3. **HA API errors**: Verify SUPERVISOR_TOKEN is set
4. **Port conflicts**: Ensure 8080 is available

### Development Tips

- Use `bashio::log.info` for logging in shell scripts
- Laravel logs go to stderr (visible in add-on logs)
- Test with SQLite first before other databases
- Use the built-in Laravel debugging tools

## Resources

- [Home Assistant Add-on Documentation](https://developers.home-assistant.io/docs/add-ons)
- [TRMNL BYOS Repository](https://github.com/usetrmnl/byos_laravel)
- [Laravel Documentation](https://laravel.com/docs)
