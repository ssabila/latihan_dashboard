<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\VisualizationController;
use App\Models\Visualization;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
});

// Admin Routes (tanpa autentikasi untuk sementara)
Route::prefix('admin')->name('admin.')->group(function () {
    // Map Routes
    Route::prefix('map')->name('map.')->group(function () {
        Route::get('/', [VisualizationController::class, 'indexMap'])->name('index');
        Route::get('/create', [VisualizationController::class, 'createMap'])->name('create');
        Route::post('/', [VisualizationController::class, 'storeMap'])->name('store');
        Route::get('/{visualization}/edit', [VisualizationController::class, 'editMap'])->name('edit');
        Route::put('/{visualization}', [VisualizationController::class, 'updateMap'])->name('update');
        Route::post('/{visualization}', [VisualizationController::class, 'updateMap'])->name('update.post');
        Route::delete('/{visualization}', [VisualizationController::class, 'deleteMap'])->name('delete');
    });

    // WordCloud Routes
    Route::prefix('wordcloud')->name('wordcloud.')->group(function () {
        Route::get('/', [VisualizationController::class, 'indexWordCloud'])->name('index');
        Route::get('/create', [VisualizationController::class, 'createWordCloud'])->name('create');
        Route::post('/', [VisualizationController::class, 'storeWordCloud'])->name('store');
        Route::get('/{visualization}/edit', [VisualizationController::class, 'editWordCloud'])->name('edit');
        Route::put('/{visualization}', [VisualizationController::class, 'updateWordCloud'])->name('update');
        Route::delete('/{visualization}', [VisualizationController::class, 'deleteWordCloud'])->name('delete');
    });
});

// Dissemination Routes
Route::prefix('diseminasi')->name('diseminasi.')->group(function () {
    Route::get('/', function () {
        return Inertia::render('Dissemination/Index', [
            'mapVisualizations' => Visualization::where('type', 'map')
                ->with('mapData')
                ->get(),
            'wordcloudVisualizations' => Visualization::where('type', 'wordcloud')
                ->with('wordCloudData')
                ->get(),
        ]);
    })->name('index');

    Route::get('/map/{visualization}', function (Visualization $visualization) {
        if ($visualization->type !== 'map') abort(404);
        
        return Inertia::render('Dissemination/MapVisualization', [
            'visualization' => $visualization->load('mapData')
        ]);
    })->name('map');

    Route::get('/wordcloud/{visualization}', function (Visualization $visualization) {
        if ($visualization->type !== 'wordcloud') abort(404);
        
        return Inertia::render('Dissemination/WordCloudVisualization', [
            'visualization' => $visualization->load('wordCloudData')
        ]);
    })->name('wordcloud');
});