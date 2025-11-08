<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\VisualizationApiController;

Route::prefix('visualizations')->group(function () {
    Route::get('{visualization}/map-data', [VisualizationApiController::class, 'getMapData']);
    Route::get('{visualization}/map-variables', [VisualizationApiController::class, 'getMapVariables']);
    Route::get('{visualization}/wordcloud-data', [VisualizationApiController::class, 'getWordCloudData']);
    Route::get('geojson/{file}', [VisualizationApiController::class, 'getGeoJson']);
});