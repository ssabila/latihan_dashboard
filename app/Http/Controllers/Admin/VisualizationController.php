<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMapDataRequest;
use App\Http\Requests\StoreWordCloudRequest;
use App\Models\Visualization;
use App\Models\MapData;
use App\Models\WordCloudData;
use App\Services\ExcelParser;
use Inertia\Inertia;

class VisualizationController extends Controller
{
    protected ExcelParser $excelParser;

    public function __construct(ExcelParser $excelParser)
    {
        $this->excelParser = $excelParser;
    }

    // ============ MAP VISUALIZATION ============

    public function indexMap()
    {
        $visualizations = Visualization::where('type', 'map')->with('mapData')->get();
        return Inertia::render('Admin/MapIndex', [
            'visualizations' => $visualizations,
        ]);
    }

    public function createMap()
    {
        $geoJsonFiles = $this->getAvailableGeoJsonFiles();
        return Inertia::render('Admin/CreateMap', [
            'geoJsonFiles' => $geoJsonFiles,
        ]);
    }

    public function storeMap(StoreMapDataRequest $request)
    {
        try {
            $file = $request->file('file');
            $filePath = $file->store('uploads/excel', 'public');

            // Parse Excel
            $excelData = $this->excelParser->parseMapData($file);

            // Create Visualization
            $visualization = Visualization::create([
                'name' => $request->name,
                'description' => $request->description,
                'type' => 'map',
                'geojson_file' => $request->geojson_file,
            ]);

            // Insert Map Data
            foreach ($excelData as $item) {
                MapData::create([
                    'visualization_id' => $visualization->id,
                    ...$item,
                ]);
            }

            return redirect()->route('admin.map.index')
                ->with('success', 'Visualisasi peta berhasil dibuat!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function editMap(Visualization $visualization)
    {
        if ($visualization->type !== 'map') {
            abort(404);
        }

        $geoJsonFiles = $this->getAvailableGeoJsonFiles();
        return Inertia::render('Admin/EditMap', [
            'visualization' => $visualization->load('mapData'),
            'geoJsonFiles' => $geoJsonFiles,
        ]);
    }

    public function updateMap(StoreMapDataRequest $request, Visualization $visualization)
    {
        try {
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $filePath = $file->store('uploads/excel', 'public');
                $excelData = $this->excelParser->parseMapData($file);

                // Delete old data
                $visualization->mapData()->delete();

                // Insert new data
                foreach ($excelData as $item) {
                    MapData::create([
                        'visualization_id' => $visualization->id,
                        ...$item,
                    ]);
                }
            }

            $visualization->update([
                'name' => $request->name,
                'description' => $request->description,
                'geojson_file' => $request->geojson_file,
            ]);

            return redirect()->route('admin.map.index')
                ->with('success', 'Visualisasi peta berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function deleteMap(Visualization $visualization)
    {
        if ($visualization->type !== 'map') {
            abort(404);
        }

        $visualization->delete();
        return redirect()->route('admin.map.index')
            ->with('success', 'Visualisasi peta berhasil dihapus!');
    }

    // ============ WORDCLOUD VISUALIZATION ============

    public function indexWordCloud()
    {
        $visualizations = Visualization::where('type', 'wordcloud')->with('wordCloudData')->get();
        return Inertia::render('Admin/WordCloudIndex', [
            'visualizations' => $visualizations,
        ]);
    }

    public function createWordCloud()
    {
        return Inertia::render('Admin/CreateWordCloud');
    }

    public function storeWordCloud(StoreWordCloudRequest $request)
    {
        try {
            $visualization = Visualization::create([
                'name' => $request->name,
                'description' => $request->description,
                'type' => 'wordcloud',
            ]);

            foreach ($request->words as $wordData) {
                WordCloudData::create([
                    'visualization_id' => $visualization->id,
                    'word' => $wordData['word'],
                    'value' => $wordData['value'],
                ]);
            }

            return redirect()->route('admin.wordcloud.index')
                ->with('success', 'Wordcloud berhasil dibuat!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function editWordCloud(Visualization $visualization)
    {
        if ($visualization->type !== 'wordcloud') {
            abort(404);
        }

        return Inertia::render('Admin/EditWordCloud', [
            'visualization' => $visualization->load('wordCloudData'),
        ]);
    }

    public function updateWordCloud(StoreWordCloudRequest $request, Visualization $visualization)
    {
        try {
            $visualization->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            $visualization->wordCloudData()->delete();

            foreach ($request->words as $wordData) {
                WordCloudData::create([
                    'visualization_id' => $visualization->id,
                    'word' => $wordData['word'],
                    'value' => $wordData['value'],
                ]);
            }

            return redirect()->route('admin.wordcloud.index')
                ->with('success', 'Wordcloud berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function deleteWordCloud(Visualization $visualization)
    {
        if ($visualization->type !== 'wordcloud') {
            abort(404);
        }

        $visualization->delete();
        return redirect()->route('admin.wordcloud.index')
            ->with('success', 'Wordcloud berhasil dihapus!');
    }

    // ============ HELPER METHODS ============

    private function getAvailableGeoJsonFiles(): array
    {
        return [
            'yogyakarta' => 'Yogyakarta (Jogja)',
            'custom' => 'Custom GeoJSON',
        ];
    }
}