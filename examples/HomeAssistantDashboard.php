<?php

/**
 * Example TRMNL Plugin: Home Assistant Dashboard
 * 
 * This is an example plugin that demonstrates how to use the exposed
 * Home Assistant entities in a TRMNL plugin.
 * 
 * Installation:
 * 1. Copy this file to your TRMNL BYOS plugins directory
 * 2. Configure the plugin in the TRMNL dashboard
 * 3. Assign it to a display layout
 */

namespace App\Plugins;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HomeAssistantDashboard
{
    /**
     * Plugin metadata
     */
    public static function metadata()
    {
        return [
            'name' => 'Home Assistant Dashboard',
            'description' => 'Display Home Assistant entity states on your TRMNL',
            'author' => 'TRMNL BYOS Add-on',
            'version' => '1.0.0',
            'settings' => [
                [
                    'key' => 'layout',
                    'label' => 'Layout Style',
                    'type' => 'select',
                    'options' => [
                        'grid' => 'Grid',
                        'list' => 'List',
                        'compact' => 'Compact',
                    ],
                    'default' => 'grid',
                ],
                [
                    'key' => 'max_entities',
                    'label' => 'Maximum Entities to Display',
                    'type' => 'number',
                    'default' => 6,
                ],
            ],
        ];
    }

    /**
     * Render the plugin
     * 
     * @param array $settings Plugin settings from TRMNL dashboard
     * @return array Plugin data for rendering
     */
    public static function render($settings = [])
    {
        $layout = $settings['layout'] ?? 'grid';
        $maxEntities = $settings['max_entities'] ?? 6;

        // Fetch exposed Home Assistant entities
        $entities = self::getExposedEntities();

        // Limit number of entities
        $entities = array_slice($entities, 0, $maxEntities);

        // Format data for display
        $data = self::formatForDisplay($entities, $layout);

        return [
            'success' => true,
            'data' => $data,
            'layout' => $layout,
            'refresh_interval' => 300, // Refresh every 5 minutes
        ];
    }

    /**
     * Get exposed Home Assistant entities
     * 
     * @return array List of exposed entities
     */
    private static function getExposedEntities()
    {
        try {
            $response = Http::get(url('/api/homeassistant/entities'));

            if ($response->successful()) {
                $result = $response->json();
                return $result['entities'] ?? [];
            }

            return [];
        } catch (\Exception $e) {
            Log::error('Failed to fetch Home Assistant entities: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Format entities for display
     * 
     * @param array $entities List of entities
     * @param string $layout Layout style
     * @return array Formatted data
     */
    private static function formatForDisplay($entities, $layout)
    {
        $formatted = [];

        foreach ($entities as $entity) {
            $entityId = $entity['entity_id'];
            $domain = explode('.', $entityId)[0];
            $state = $entity['state'];
            $attributes = $entity['attributes'] ?? [];
            $displayName = $entity['display_name'] ?? $attributes['friendly_name'] ?? $entityId;
            $unit = $attributes['unit_of_measurement'] ?? '';

            // Format based on domain
            $icon = self::getIconForDomain($domain);
            $formattedState = self::formatState($state, $unit, $domain);

            $formatted[] = [
                'name' => $displayName,
                'value' => $formattedState,
                'icon' => $icon,
                'domain' => $domain,
                'raw_state' => $state,
            ];
        }

        return $formatted;
    }

    /**
     * Get icon for entity domain
     * 
     * @param string $domain Entity domain
     * @return string Icon character or emoji
     */
    private static function getIconForDomain($domain)
    {
        $icons = [
            'light' => '💡',
            'switch' => '🔌',
            'sensor' => '📊',
            'binary_sensor' => '🔘',
            'climate' => '🌡️',
            'weather' => '🌤️',
            'sun' => '☀️',
            'person' => '👤',
            'device_tracker' => '📍',
            'camera' => '📷',
            'media_player' => '🎵',
            'lock' => '🔒',
            'cover' => '🚪',
            'fan' => '💨',
        ];

        return $icons[$domain] ?? '•';
    }

    /**
     * Format state value
     * 
     * @param mixed $state State value
     * @param string $unit Unit of measurement
     * @param string $domain Entity domain
     * @return string Formatted state
     */
    private static function formatState($state, $unit, $domain)
    {
        // Handle binary states
        if (in_array($domain, ['switch', 'light', 'binary_sensor'])) {
            if ($state === 'on') return 'ON';
            if ($state === 'off') return 'OFF';
        }

        // Handle numeric states with units
        if (is_numeric($state) && $unit) {
            return number_format((float)$state, 1) . ' ' . $unit;
        }

        // Handle temperature
        if ($unit === '°C' || $unit === '°F') {
            return round((float)$state) . $unit;
        }

        // Default: return state as-is
        return ucfirst($state);
    }
}
