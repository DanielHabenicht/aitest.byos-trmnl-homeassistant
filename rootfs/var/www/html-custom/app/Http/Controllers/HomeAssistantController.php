<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class HomeAssistantController extends Controller
{
    private function getHomeAssistantUrl()
    {
        return env('HOMEASSISTANT_URL', 'http://supervisor/core');
    }

    private function getAuthHeaders()
    {
        $token = env('SUPERVISOR_TOKEN', '');
        
        if (empty($token)) {
            throw new \Exception('SUPERVISOR_TOKEN environment variable not found. Ensure this add-on is running within Home Assistant with proper supervisor access.');
        }
        
        return [
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ];
    }

    public function dashboard()
    {
        try {
            $response = Http::withHeaders($this->getAuthHeaders())
                ->get($this->getHomeAssistantUrl() . '/api/states');

            $entities = $response->successful() ? $response->json() : [];
            
            // Get saved entity configurations
            $savedEntities = DB::table('homeassistant_entities')
                ->pluck('entity_id')
                ->toArray();
            
            // Get webhook configuration
            $webhookConfig = DB::table('homeassistant_webhook_config')
                ->first();

            return view('homeassistant.dashboard', [
                'entities' => $entities,
                'savedEntities' => $savedEntities,
                'webhookConfig' => $webhookConfig,
            ]);
        } catch (\Exception $e) {
            return view('homeassistant.dashboard', [
                'entities' => [],
                'savedEntities' => [],
                'webhookConfig' => null,
                'error' => 'Failed to connect to Home Assistant: ' . $e->getMessage(),
            ]);
        }
    }

    public function getEntities(Request $request)
    {
        try {
            $response = Http::withHeaders($this->getAuthHeaders())
                ->get($this->getHomeAssistantUrl() . '/api/states');

            if ($response->successful()) {
                $entities = $response->json();
                
                // Filter by domain if provided
                if ($request->has('domain')) {
                    $domain = $request->input('domain');
                    $entities = array_filter($entities, function($entity) use ($domain) {
                        return str_starts_with($entity['entity_id'], $domain . '.');
                    });
                }

                return response()->json([
                    'success' => true,
                    'entities' => array_values($entities),
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch entities from Home Assistant',
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getEntity($entityId)
    {
        try {
            // Validate entity ID format (domain.entity_name)
            // Allow letters, numbers, underscores, and hyphens
            if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*\.[a-zA-Z0-9_-]+$/', $entityId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid entity ID format',
                ], 400);
            }
            
            $response = Http::withHeaders($this->getAuthHeaders())
                ->get($this->getHomeAssistantUrl() . '/api/states/' . urlencode($entityId));

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'entity' => $response->json(),
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Entity not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function saveEntityConfiguration(Request $request)
    {
        $validated = $request->validate([
            'entity_id' => 'required|string',
            'display_name' => 'nullable|string',
            'format' => 'nullable|string',
            'enabled' => 'boolean',
        ]);

        try {
            $existing = DB::table('homeassistant_entities')
                ->where('entity_id', $validated['entity_id'])
                ->first();

            if ($existing) {
                // Update existing record, preserving created_at
                DB::table('homeassistant_entities')
                    ->where('entity_id', $validated['entity_id'])
                    ->update([
                        'display_name' => $validated['display_name'] ?? null,
                        'format' => $validated['format'] ?? null,
                        'enabled' => $validated['enabled'] ?? true,
                        'updated_at' => now(),
                    ]);
            } else {
                // Insert new record
                DB::table('homeassistant_entities')->insert([
                    'entity_id' => $validated['entity_id'],
                    'display_name' => $validated['display_name'] ?? null,
                    'format' => $validated['format'] ?? null,
                    'enabled' => $validated['enabled'] ?? true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Entity configuration saved',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function deleteEntityConfiguration($entityId)
    {
        try {
            DB::table('homeassistant_entities')
                ->where('entity_id', $entityId)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Entity configuration deleted',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getExposedEntities()
    {
        try {
            $configs = DB::table('homeassistant_entities')
                ->where('enabled', true)
                ->get();

            if ($configs->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'entities' => [],
                ]);
            }

            // Fetch all states in a single API call
            $response = Http::withHeaders($this->getAuthHeaders())
                ->get($this->getHomeAssistantUrl() . '/api/states');

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch entity states from Home Assistant',
                ], 500);
            }

            $allStates = $response->json();
            $statesByEntityId = [];
            
            // Index states by entity_id for quick lookup
            foreach ($allStates as $state) {
                $statesByEntityId[$state['entity_id']] = $state;
            }

            // Map configured entities to their current states
            $entities = [];
            foreach ($configs as $config) {
                if (isset($statesByEntityId[$config->entity_id])) {
                    $entity = $statesByEntityId[$config->entity_id];
                    $entity['display_name'] = $config->display_name;
                    $entity['format'] = $config->format;
                    $entities[] = $entity;
                }
            }

            return response()->json([
                'success' => true,
                'entities' => $entities,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function saveWebhookConfiguration(Request $request)
    {
        $validated = $request->validate([
            'webhook_url' => 'required|url',
            'plugin_uuid' => 'nullable|string',
            'enabled' => 'boolean',
        ]);

        try {
            $existing = DB::table('homeassistant_webhook_config')->first();

            if ($existing) {
                DB::table('homeassistant_webhook_config')
                    ->where('id', $existing->id)
                    ->update([
                        'webhook_url' => $validated['webhook_url'],
                        'plugin_uuid' => $validated['plugin_uuid'] ?? null,
                        'enabled' => $validated['enabled'] ?? true,
                        'updated_at' => now(),
                    ]);
            } else {
                DB::table('homeassistant_webhook_config')->insert([
                    'webhook_url' => $validated['webhook_url'],
                    'plugin_uuid' => $validated['plugin_uuid'] ?? null,
                    'enabled' => $validated['enabled'] ?? true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Trigger initial webhook with current entity states
            $this->sendWebhook();

            return response()->json([
                'success' => true,
                'message' => 'Webhook configuration saved and initial data sent',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function testWebhook()
    {
        try {
            $result = $this->sendWebhook();
            
            return response()->json([
                'success' => $result,
                'message' => $result ? 'Webhook sent successfully' : 'Failed to send webhook',
            ], $result ? 200 : 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    private function sendWebhook()
    {
        try {
            // Get webhook configuration
            $webhookConfig = DB::table('homeassistant_webhook_config')
                ->where('enabled', true)
                ->first();

            if (!$webhookConfig || !$webhookConfig->webhook_url) {
                return false;
            }

            // Get enabled entities with their current states
            $configs = DB::table('homeassistant_entities')
                ->where('enabled', true)
                ->get();

            if ($configs->isEmpty()) {
                return false;
            }

            // Fetch all states from Home Assistant
            $response = Http::withHeaders($this->getAuthHeaders())
                ->get($this->getHomeAssistantUrl() . '/api/states');

            if (!$response->successful()) {
                return false;
            }

            $allStates = $response->json();
            $statesByEntityId = [];
            
            foreach ($allStates as $state) {
                $statesByEntityId[$state['entity_id']] = $state;
            }

            // Build webhook payload with formatted entities
            $entities = [];
            foreach ($configs as $config) {
                if (isset($statesByEntityId[$config->entity_id])) {
                    $entity = $statesByEntityId[$config->entity_id];
                    $entities[] = [
                        'entity_id' => $entity['entity_id'],
                        'state' => $entity['state'],
                        'display_name' => $config->display_name ?? $entity['attributes']['friendly_name'] ?? $entity['entity_id'],
                        'attributes' => $entity['attributes'],
                        'last_changed' => $entity['last_changed'] ?? null,
                        'last_updated' => $entity['last_updated'] ?? null,
                    ];
                }
            }

            // Send webhook to TRMNL
            $webhookResponse = Http::timeout(10)->post($webhookConfig->webhook_url, [
                'merge_variables' => [
                    'entities' => $entities,
                    'updated_at' => now()->toIso8601String(),
                ],
            ]);

            return $webhookResponse->successful();
        } catch (\Exception $e) {
            \Log::error('Failed to send webhook: ' . $e->getMessage());
            return false;
        }
    }
}
