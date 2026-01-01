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
            throw new \Exception('SUPERVISOR_TOKEN not available. Add-on may not have proper Home Assistant permissions.');
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

            return view('homeassistant.dashboard', [
                'entities' => $entities,
                'savedEntities' => $savedEntities,
            ]);
        } catch (\Exception $e) {
            return view('homeassistant.dashboard', [
                'entities' => [],
                'savedEntities' => [],
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
                        return strpos($entity['entity_id'], $domain . '.') === 0;
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
            if (!preg_match('/^[a-z_]+\.[a-z0-9_]+$/', $entityId)) {
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
            DB::table('homeassistant_entities')->updateOrInsert(
                ['entity_id' => $validated['entity_id']],
                [
                    'display_name' => $validated['display_name'] ?? null,
                    'format' => $validated['format'] ?? null,
                    'enabled' => $validated['enabled'] ?? true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );

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
}
