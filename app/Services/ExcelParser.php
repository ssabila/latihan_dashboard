<?php

namespace App\Services;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;

class ExcelParser
{
    public function parseMapData($filePath): Collection
    {
        $rows = Excel::toArray(null, $filePath)[0] ?? [];
        
        $data = collect();
        $headers = [];

        foreach ($rows as $index => $row) {
            if ($index === 0) {
                // Header row: [region_id, region_name, variable1, variable2, ...]
                $headers = array_values(array_filter($row));
                continue;
            }

            if (empty($row[0]) || empty($row[1])) {
                continue;
            }

            $regionId = $row[0];
            $regionName = $row[1];

            // Parse setiap variable (mulai dari kolom ke-3)
            for ($i = 2; $i < count($headers); $i++) {
                $variableName = $headers[$i];
                $variableValue = (float) ($row[$i] ?? 0);

                $data->push([
                    'region_id' => $regionId,
                    'region_name' => $regionName,
                    'variable_name' => $variableName,
                    'variable_value' => $variableValue,
                ]);
            }
        }

        return $data;
    }
}