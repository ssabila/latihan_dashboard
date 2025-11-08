<?php

namespace App\Console\Commands;

use App\Models\Visualization;
use App\Models\MapData;
use App\Models\WordCloudData;
use Illuminate\Console\Command;

class CreateSampleDataCommand extends Command
{
    protected $signature = 'data:sample';
    protected $description = 'Create sample data for testing';

    public function handle(): void
    {
        $this->info('Creating sample data...');

        // Create sample map visualization
        $mapViz = Visualization::create([
            'name' => 'Sampel - Sebaran Pendapatan Yogyakarta',
            'type' => 'map',
            'description' => 'Data sampel sebaran pendapatan per daerah di Yogyakarta',
            'geojson_file' => 'yogyakarta',
        ]);

        $regions = [
            ['id' => '1', 'name' => 'Sleman'],
            ['id' => '2', 'name' => 'Bantul'],
            ['id' => '3', 'name' => 'Gunungkidul'],
            ['id' => '4', 'name' => 'Kulon Progo'],
            ['id' => '5', 'name' => 'Yogyakarta Kota'],
        ];

        $variables = ['Pendapatan', 'Pendidikan'];
        $values = [
            'Pendapatan' => [5000000, 4500000, 4000000, 4800000, 5500000],
            'Pendidikan' => [85, 80, 75, 82, 95],
        ];

        foreach ($regions as $index => $region) {
            foreach ($variables as $var) {
                MapData::create([
                    'visualization_id' => $mapViz->id,
                    'region_id' => $region['id'],
                    'region_name' => $region['name'],
                    'variable_name' => $var,
                    'variable_value' => $values[$var][$index],
                ]);
            }
        }

        $this->info("Map visualization created with ID: {$mapViz->id}");
        $this->line("Variables: " . implode(', ', $variables));

        // Create sample wordcloud
        $cloudViz = Visualization::create([
            'name' => 'Sampel - Topik Diskusi Utama',
            'type' => 'wordcloud',
            'description' => 'Topik yang paling sering dibicarakan dalam survei',
        ]);

        $words = [
            ['word' => 'Kesehatan', 'value' => 45],
            ['word' => 'Pendidikan', 'value' => 38],
            ['word' => 'Infrastruktur', 'value' => 62],
            ['word' => 'Lingkungan', 'value' => 28],
            ['word' => 'Pertanian', 'value' => 55],
        ];

        foreach ($words as $wordData) {
            WordCloudData::create([
                'visualization_id' => $cloudViz->id,
                ...$wordData,
            ]);
        }

        $this->info("WordCloud visualization created with ID: {$cloudViz->id}");

        $this->info('Sample data created successfully!');
        $this->line('');
        $this->line('Access the visualizations:');
        $this->line("  Map: http://localhost:8000/diseminasi/map/{$mapViz->id}");
        $this->line("  WordCloud: http://localhost:8000/diseminasi/wordcloud/{$cloudViz->id}");
    }
}
