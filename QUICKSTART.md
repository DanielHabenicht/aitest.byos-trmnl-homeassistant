# Quick Start Guide

## Installation in 5 Minutes

### Step 1: Add Repository
1. In Home Assistant, go to **Settings** → **Add-ons** → **Add-on Store**
2. Click the ⋮ menu → **Repositories**
3. Add: `https://github.com/DanielHabenicht/aitest.byos-trmnl-homeassistant`

### Step 2: Install Add-on
1. Find "TRMNL BYOS" in the add-on store
2. Click **Install**
3. Wait for installation to complete

### Step 3: Start
1. Click **Start**
2. Check the **Log** tab
3. Copy the `APP_KEY` from the logs (looks like `base64:xxxxx...`)

### Step 4: Configure
1. Go to **Configuration** tab
2. Set `app_key` to the copied value
3. Click **Save**
4. Go to **Info** tab and click **Restart**

### Step 5: Use
1. Click **Open Web UI**
2. Complete TRMNL setup wizard
3. Visit `/homeassistant` path to configure entity exposure

## Quick Commands

### Access Web UI
```
http://your-home-assistant:8080
```

### Configure Entities
```
http://your-home-assistant:8080/homeassistant
```

### View Logs
Settings → Add-ons → TRMNL BYOS → Log

## Configuration Template

```yaml
app_key: "base64:your-generated-key-from-first-run"
app_url: "http://homeassistant.local:8080"
db_connection: sqlite
```

## Troubleshooting

| Problem | Solution |
|---------|----------|
| Won't start | Check logs for errors, ensure port 8080 is free |
| Can't access UI | Verify add-on is running (green status) |
| No entities | Visit `/homeassistant` to add entities |
| Session errors | Make sure `app_key` is set in configuration |

## Next Steps

1. ✅ Configure TRMNL devices in the main dashboard
2. ✅ Select Home Assistant entities to expose
3. ✅ Create or install TRMNL plugins
4. ✅ Assign plugins to your displays

## Resources

- 📖 Full Documentation: [README.md](README.md)
- 🔧 Installation Guide: [INSTALL.md](INSTALL.md)
- 💡 Plugin Examples: [examples/README.md](examples/README.md)
- 🐛 Report Issues: [GitHub Issues](https://github.com/DanielHabenicht/aitest.byos-trmnl-homeassistant/issues)
