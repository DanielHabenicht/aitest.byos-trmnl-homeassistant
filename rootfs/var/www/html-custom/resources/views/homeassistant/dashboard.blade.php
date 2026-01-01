<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Assistant Entity Configuration - TRMNL BYOS</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #f5f5f5;
            color: #333;
            line-height: 1.6;
        }

        .header {
            background: #03a9f4;
            color: white;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .header h1 {
            font-size: 1.5rem;
            font-weight: 500;
        }

        .header p {
            margin-top: 0.5rem;
            opacity: 0.9;
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 4px;
            background: #f44336;
            color: white;
        }

        .alert.info {
            background: #2196f3;
        }

        .controls {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .controls label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .controls select,
        .controls input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }

        .controls button {
            background: #03a9f4;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            margin-top: 1rem;
        }

        .controls button:hover {
            background: #0288d1;
        }

        .entity-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1rem;
        }

        .entity-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .entity-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        .entity-card.saved {
            border-left: 4px solid #4caf50;
        }

        .entity-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 1rem;
        }

        .entity-id {
            font-weight: 600;
            color: #03a9f4;
            word-break: break-all;
        }

        .entity-badge {
            background: #4caf50;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            white-space: nowrap;
        }

        .entity-state {
            font-size: 1.5rem;
            font-weight: 500;
            margin: 0.5rem 0;
        }

        .entity-attributes {
            font-size: 0.875rem;
            color: #666;
        }

        .entity-actions {
            margin-top: 1rem;
            display: flex;
            gap: 0.5rem;
        }

        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.875rem;
            flex: 1;
        }

        .btn-primary {
            background: #03a9f4;
            color: white;
        }

        .btn-primary:hover {
            background: #0288d1;
        }

        .btn-danger {
            background: #f44336;
            color: white;
        }

        .btn-danger:hover {
            background: #d32f2f;
        }

        .loading {
            text-align: center;
            padding: 2rem;
            color: #666;
        }

        .no-entities {
            text-align: center;
            padding: 3rem;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🏠 Home Assistant Entity Configuration</h1>
        <p>Select which entities to expose to your TRMNL displays</p>
    </div>

    <div class="container">
        @if(isset($error))
        <div class="alert">
            {{ $error }}
        </div>
        @endif

        <div class="controls">
            <h2 style="margin-bottom: 1rem; font-size: 1.25rem;">TRMNL Webhook Configuration</h2>
            <p style="margin-bottom: 1rem; color: #666; font-size: 0.9rem;">
                Configure the TRMNL webhook URL to push Home Assistant entity data to your TRMNL plugin. 
                Get your webhook URL from your TRMNL plugin settings.
            </p>
            
            <label for="webhook-url">TRMNL Webhook URL *</label>
            <input type="url" id="webhook-url" placeholder="https://usetrmnl.com/api/custom_plugins/..." 
                   value="{{ $webhookConfig->webhook_url ?? '' }}">
            
            <label for="plugin-uuid">Plugin UUID (optional)</label>
            <input type="text" id="plugin-uuid" placeholder="550e8400-e29b-41d4-a716-446655440000" 
                   value="{{ $webhookConfig->plugin_uuid ?? '' }}">
            
            <div style="display: flex; gap: 0.5rem; margin-top: 1rem;">
                <button onclick="saveWebhookConfig()" style="flex: 1;">Save & Send Test</button>
                <button onclick="testWebhook()" style="flex: 1; background: #4caf50;">Test Webhook</button>
            </div>
            
            <div id="webhook-status" style="margin-top: 1rem; padding: 0.75rem; border-radius: 4px; display: none;"></div>
        </div>

        <div class="controls" style="margin-top: 2rem;">
            <h2 style="margin-bottom: 1rem; font-size: 1.25rem;">Entity Selection</h2>
            <p style="margin-bottom: 1rem; color: #666; font-size: 0.9rem;">
                Select which Home Assistant entities to expose to TRMNL via webhook notifications.
            </p>
            
            <label for="domain-filter">Filter by Domain</label>
            <select id="domain-filter">
                <option value="">All Domains</option>
                <option value="sensor">Sensors</option>
                <option value="switch">Switches</option>
                <option value="light">Lights</option>
                <option value="climate">Climate</option>
                <option value="binary_sensor">Binary Sensors</option>
                <option value="weather">Weather</option>
                <option value="sun">Sun</option>
                <option value="person">Person</option>
                <option value="device_tracker">Device Trackers</option>
            </select>

            <label for="search">Search Entities</label>
            <input type="text" id="search" placeholder="Type to search...">
        </div>

        <div id="entity-list" class="entity-grid">
            <div class="loading">Loading entities...</div>
        </div>
    </div>

    <script>
        let allEntities = @json($entities ?? []);
        let savedEntities = @json($savedEntities ?? []);

        function renderEntities(entities) {
            const container = document.getElementById('entity-list');
            
            if (entities.length === 0) {
                container.innerHTML = '<div class="no-entities">No entities found</div>';
                return;
            }

            container.innerHTML = entities.map(entity => {
                const isSaved = savedEntities.includes(entity.entity_id);
                const domain = entity.entity_id.split('.')[0];
                const state = entity.state;
                const unit = entity.attributes?.unit_of_measurement || '';
                
                return `
                    <div class="entity-card ${isSaved ? 'saved' : ''}">
                        <div class="entity-header">
                            <div class="entity-id">${entity.entity_id}</div>
                            ${isSaved ? '<div class="entity-badge">Exposed</div>' : ''}
                        </div>
                        <div class="entity-state">${state} ${unit}</div>
                        <div class="entity-attributes">
                            ${entity.attributes?.friendly_name || domain}
                        </div>
                        <div class="entity-actions">
                            ${isSaved 
                                ? `<button class="btn btn-danger" onclick="removeEntity('${entity.entity_id}')">Remove</button>`
                                : `<button class="btn btn-primary" onclick="addEntity('${entity.entity_id}')">Add to TRMNL</button>`
                            }
                        </div>
                    </div>
                `;
            }).join('');
        }

        function filterEntities() {
            const domain = document.getElementById('domain-filter').value;
            const search = document.getElementById('search').value.toLowerCase();

            let filtered = allEntities;

            if (domain) {
                filtered = filtered.filter(e => e.entity_id.startsWith(domain + '.'));
            }

            if (search) {
                filtered = filtered.filter(e => 
                    e.entity_id.toLowerCase().includes(search) ||
                    (e.attributes?.friendly_name || '').toLowerCase().includes(search)
                );
            }

            renderEntities(filtered);
        }

        async function addEntity(entityId) {
            try {
                const response = await fetch('/homeassistant/api/entities/config', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        entity_id: entityId,
                        enabled: true
                    })
                });

                const data = await response.json();
                if (data.success) {
                    savedEntities.push(entityId);
                    filterEntities();
                } else {
                    alert('Failed to add entity: ' + data.message);
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }

        async function removeEntity(entityId) {
            try {
                const response = await fetch('/homeassistant/api/entities/config/' + encodeURIComponent(entityId), {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                const data = await response.json();
                if (data.success) {
                    savedEntities = savedEntities.filter(id => id !== entityId);
                    filterEntities();
                } else {
                    alert('Failed to remove entity: ' + data.message);
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }

        async function saveWebhookConfig() {
            const webhookUrl = document.getElementById('webhook-url').value;
            const pluginUuid = document.getElementById('plugin-uuid').value;
            const statusDiv = document.getElementById('webhook-status');

            if (!webhookUrl) {
                statusDiv.style.display = 'block';
                statusDiv.style.background = '#f44336';
                statusDiv.style.color = 'white';
                statusDiv.textContent = 'Please enter a webhook URL';
                return;
            }

            try {
                const response = await fetch('/homeassistant/api/webhook/config', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        webhook_url: webhookUrl,
                        plugin_uuid: pluginUuid,
                        enabled: true
                    })
                });

                const data = await response.json();
                statusDiv.style.display = 'block';
                if (data.success) {
                    statusDiv.style.background = '#4caf50';
                    statusDiv.style.color = 'white';
                    statusDiv.textContent = '✓ ' + data.message;
                } else {
                    statusDiv.style.background = '#f44336';
                    statusDiv.style.color = 'white';
                    statusDiv.textContent = '✗ ' + data.message;
                }
            } catch (error) {
                statusDiv.style.display = 'block';
                statusDiv.style.background = '#f44336';
                statusDiv.style.color = 'white';
                statusDiv.textContent = '✗ Error: ' + error.message;
            }
        }

        async function testWebhook() {
            const statusDiv = document.getElementById('webhook-status');
            
            try {
                statusDiv.style.display = 'block';
                statusDiv.style.background = '#2196f3';
                statusDiv.style.color = 'white';
                statusDiv.textContent = 'Sending test webhook...';

                const response = await fetch('/homeassistant/api/webhook/test', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                const data = await response.json();
                if (data.success) {
                    statusDiv.style.background = '#4caf50';
                    statusDiv.textContent = '✓ ' + data.message;
                } else {
                    statusDiv.style.background = '#f44336';
                    statusDiv.textContent = '✗ ' + data.message;
                }
            } catch (error) {
                statusDiv.style.background = '#f44336';
                statusDiv.textContent = '✗ Error: ' + error.message;
            }
        }

        document.getElementById('domain-filter').addEventListener('change', filterEntities);
        document.getElementById('search').addEventListener('input', filterEntities);

        // Initial render
        renderEntities(allEntities);
    </script>
</body>
</html>
