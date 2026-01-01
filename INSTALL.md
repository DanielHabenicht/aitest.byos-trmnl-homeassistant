# Installation Guide for TRMNL BYOS Home Assistant Add-on

## Prerequisites

- Home Assistant OS or Supervised installation
- Sufficient storage space (minimum 2GB recommended)
- Network connectivity

## Installation Steps

### 1. Add the Repository

1. Navigate to your Home Assistant instance
2. Go to **Settings** → **Add-ons** → **Add-on Store**
3. Click the three-dot menu (⋮) in the top right corner
4. Select **Repositories**
5. Add this repository URL:
   ```
   https://github.com/DanielHabenicht/aitest.byos-trmnl-homeassistant
   ```
6. Click **Add**

### 2. Install the Add-on

1. Refresh the Add-on Store page
2. Find **TRMNL BYOS** in the list of available add-ons
3. Click on the add-on to open its details page
4. Click **Install**
5. Wait for the installation to complete (this may take several minutes)

### 3. Configure the Add-on

1. After installation, go to the **Configuration** tab
2. The first time you run the add-on, leave the `app_key` field empty
3. Configure the other options:

   ```yaml
   app_key: ""  # Leave empty on first run
   app_url: "http://homeassistant.local:8080"  # Update with your actual URL
   db_connection: sqlite  # Use SQLite for simplicity
   ```

4. Click **Save**

### 4. Start the Add-on

1. Go to the **Info** tab
2. Click **Start**
3. Wait for the add-on to start (check the **Log** tab for progress)
4. Look for the generated `APP_KEY` in the logs

### 5. Update Configuration with App Key

1. Copy the `APP_KEY` value from the logs (it will look like `base64:xxxxx...`)
2. Go back to the **Configuration** tab
3. Paste the key into the `app_key` field:
   ```yaml
   app_key: "base64:xxxxx..."  # Your actual key from the logs
   ```
4. Click **Save**
5. Go to the **Info** tab and click **Restart**

### 6. Access the Web Interface

Once the add-on is running:

1. Click **Open Web UI** on the Info tab
2. Or navigate to `http://your-home-assistant:8080`
3. Complete the TRMNL BYOS setup wizard

## Accessing the Home Assistant Entity Dashboard

To configure which Home Assistant entities are exposed to TRMNL:

1. Navigate to `http://your-home-assistant:8080/homeassistant`
2. Browse available entities
3. Click "Add to TRMNL" for entities you want to expose
4. Use the filter and search features to find specific entities

## Troubleshooting

### Add-on Won't Start

- Check the logs for error messages
- Ensure you have enough disk space
- Verify that port 8080 is not in use by another service

### Can't Access Web UI

- Verify the add-on is running (green status)
- Check your `app_url` configuration matches your network setup
- Try accessing via IP address instead of hostname

### Database Errors

- If using SQLite, ensure the `/data` directory has proper permissions
- Check logs for specific database error messages
- Consider recreating the database by stopping the add-on and removing `/data/database/trmnl.sqlite`

### Home Assistant Integration Not Working

- Verify the add-on has access to the Supervisor API
- Check that `SUPERVISOR_TOKEN` is available in the environment
- Review logs for Home Assistant API connection errors

## Updating the Add-on

1. Go to **Settings** → **Add-ons**
2. Find **TRMNL BYOS** in the list
3. If an update is available, click **Update**
4. Wait for the update to complete
5. The add-on will restart automatically

## Uninstalling

1. Stop the add-on
2. Click **Uninstall**
3. Confirm the uninstallation
4. Note: This will remove all TRMNL data stored in the add-on

## Data Backup

To backup your TRMNL configuration:

1. Stop the add-on
2. Use the Home Assistant backup feature to create a full backup
3. Or manually copy `/data/database/trmnl.sqlite` from the add-on data directory

## Support

For help with:
- **This add-on**: Open an issue on GitHub
- **TRMNL BYOS**: Visit https://github.com/usetrmnl/byos_laravel
- **Home Assistant**: Visit https://community.home-assistant.io/
