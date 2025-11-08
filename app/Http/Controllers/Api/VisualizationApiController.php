<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Visualization;
use App\Models\MapData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VisualizationApiController extends Controller
{
    public function getMapData(Visualization $visualization, Request $request)
    {
        $variable = $request->query('variable');

        if (!$variable) {
            return response()->json(['error' => 'Variable parameter required'], 400);
        }

        $mapData = MapData::where('visualization_id', $visualization->id)
            ->where('variable_name', $variable)
            ->get();

        return response()->json([
            'data' => $mapData,
            'variable' => $variable,
        ]);
    }

    public function getMapVariables(Visualization $visualization)
    {
        $variables = MapData::where('visualization_id', $visualization->id)
            ->distinct('variable_name')
            ->pluck('variable_name');

        return response()->json(['variables' => $variables]);
    }

    public function getWordCloudData(Visualization $visualization)
    {
        return response()->json(['data' => $visualization->wordCloudData]);
    }

    public function getGeoJson($file)
    {
        $path = "geojson/{$file}.geojson";

        if (!Storage::disk('public')->exists($path)) {
            return response()->json(['error' => 'GeoJSON not found'], 404);
        }

        $content = Storage::disk('public')->get($path);
        return response()->json(json_decode($content, true));
    }
}