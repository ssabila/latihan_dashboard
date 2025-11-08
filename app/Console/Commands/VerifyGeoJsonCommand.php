<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class VerifyGeoJsonCommand extends Command
{
    protected $signature = 'geojson:verify';
    protected $description = 'Verify GeoJSON files are properly formatted';

    public function handle(): void
    {
        $this->info('Verifying GeoJSON files...');

        $geoJsonPath = 'public/geojson';
        if (!Storage::exists($geoJsonPath)) {
            $this->warn('GeoJSON directory not found at ' . $geoJsonPath);
            return;
        }

        $files = Storage::files($geoJsonPath);

        if (empty($files)) {
            $this->warn('No GeoJSON files found');
            return;
        }

        foreach ($files as $file) {
            if (str_ends_with($file, '.geojson')) {
                $this->verifyFile($file);
            }
        }

        $this->info('GeoJSON verification complete!');
    }

    private function verifyFile(string $filePath): void
    {
        try {
            $content = Storage::get($filePath);
            $data = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->error("Invalid JSON in {$filePath}: " . json_last_error_msg());
                return;
            }

            if ($data['type'] !== 'FeatureCollection') {
                $this->error("Invalid GeoJSON type in {$filePath}");
                return;
            }

            $featureCount = count($data['features'] ?? []);
            $this->line("<info>✓</info> {$filePath} - {$featureCount} features");

            foreach ($data['features'] as $feature) {
                if (!isset($feature['properties']['id']) || !isset($feature['properties']['name'])) {
                    $this->warn("  Warning: Feature missing 'id' or 'name' property");
                }
            }
        } catch (\Exception $e) {
            $this->error("Error verifying {$filePath}: " . $e->getMessage());
        }
    }
}
