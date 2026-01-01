<?php

// Home Assistant Integration Routes

use App\Http\Controllers\HomeAssistantController;

Route::prefix('homeassistant')->middleware(['web'])->group(function () {
    // Dashboard
    Route::get('/', [HomeAssistantController::class, 'dashboard'])->name('homeassistant.dashboard');
    
    // API endpoints
    Route::prefix('api')->group(function () {
        Route::get('/entities', [HomeAssistantController::class, 'getEntities']);
        Route::get('/entities/{entity_id}', [HomeAssistantController::class, 'getEntity']);
        Route::get('/exposed', [HomeAssistantController::class, 'getExposedEntities']);
        Route::post('/entities/config', [HomeAssistantController::class, 'saveEntityConfiguration']);
        Route::delete('/entities/config/{entity_id}', [HomeAssistantController::class, 'deleteEntityConfiguration']);
        
        // Webhook configuration
        Route::post('/webhook/config', [HomeAssistantController::class, 'saveWebhookConfiguration']);
        Route::post('/webhook/test', [HomeAssistantController::class, 'testWebhook']);
    });
});
