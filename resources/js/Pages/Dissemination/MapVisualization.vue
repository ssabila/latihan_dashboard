<template>
  <div class="min-h-screen bg-gradient-to-b from-slate-50 to-slate-100">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex justify-between items-center">
          <h1 class="text-2xl font-bold text-blue-600">Data Diseminasi</h1>
          <Link href="/diseminasi" class="text-slate-600 hover:text-blue-600">← Kembali</Link>
        </div>
      </div>
    </nav>

    <!-- Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Header -->
      <div class="mb-8">
        <h2 class="text-3xl font-bold text-slate-900 mb-2">{{ visualization.name }}</h2>
        <p class="text-slate-600">{{ visualization.description }}</p>
      </div>

      <!-- Controls -->
      <div class="bg-white rounded-lg shadow p-6 mb-8">
        <label class="block text-sm font-medium text-slate-900 mb-3">Pilih Variabel:</label>
        <select 
          v-model="selectedVariable"
          @change="loadMapData"
          class="w-full md:w-64 px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          <option value="">-- Pilih Variabel --</option>
          <option v-for="variable in variables" :key="variable" :value="variable">
            {{ variable }}
          </option>
        </select>
      </div>

      <!-- Map Container -->
      <div class="bg-white rounded-lg shadow overflow-hidden">
        <div id="map" class="w-full" style="height: 500px;"></div>
      </div>

      <!-- Legend -->
      <div v-if="selectedVariable && mapData.length > 0" class="bg-white rounded-lg shadow p-6 mt-8">
        <h3 class="font-bold text-slate-900 mb-4">Legend - {{ selectedVariable }}</h3>
        <div class="flex gap-2 flex-wrap">
          <div v-for="(color, index) in colorLegend" :key="index" class="flex items-center gap-2">
            <div 
              class="w-6 h-6 rounded border border-slate-300"
              :style="{ backgroundColor: color }"
            ></div>
            <span class="text-sm text-slate-600">{{ colorLabels[index] }}</span>
          </div>
        </div>
        
        <!-- Interpretasi -->
        <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
          <h4 class="font-semibold text-blue-900 mb-2">📊 Interpretasi Data</h4>
          <p class="text-sm text-blue-800">{{ interpretation }}</p>
        </div>
      </div>

      <!-- Info Table -->
      <div v-if="selectedVariable && mapData.length > 0" class="bg-white rounded-lg shadow p-6 mt-8">
        <h3 class="font-bold text-slate-900 mb-4">Data Detail - {{ selectedVariable }}</h3>
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="bg-slate-100 border-b border-slate-200">
              <tr>
                <th class="px-4 py-2 text-left">Daerah</th>
                <th class="px-4 py-2 text-right">{{ selectedVariable }}</th>
                <th class="px-4 py-2 text-left">Kategori</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
              <tr v-for="data in sortedMapData" :key="data.region_id" class="hover:bg-slate-50">
                <td class="px-4 py-2">{{ data.region_name }}</td>
                <td class="px-4 py-2 text-right font-mono">{{ formatNumber(data.variable_value) }}</td>
                <td class="px-4 py-2">
                  <span 
                    class="px-2 py-1 text-xs rounded-full"
                    :class="getCategoryClass(data.variable_value)"
                  >
                    {{ getCategory(data.variable_value) }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import L from 'leaflet';

const props = defineProps({
  visualization: Object,
});

const selectedVariable = ref('');
const variables = ref([]);
const mapData = ref([]);
const map = ref(null);
const colorLegend = ref([]);
const colorLabels = ref([]);
const minValue = ref(0);
const maxValue = ref(0);

const sortedMapData = computed(() => {
  return [...mapData.value].sort((a, b) => b.variable_value - a.variable_value);
});

const interpretation = computed(() => {
  if (mapData.value.length === 0) return '';
  
  const highest = sortedMapData.value[0];
  const lowest = sortedMapData.value[sortedMapData.value.length - 1];
  const average = mapData.value.reduce((sum, d) => sum + d.variable_value, 0) / mapData.value.length;
  
  return `Berdasarkan data ${selectedVariable.value}, daerah ${highest.region_name} memiliki nilai tertinggi (${formatNumber(highest.variable_value)}), sedangkan ${lowest.region_name} memiliki nilai terendah (${formatNumber(lowest.variable_value)}). Rata-rata nilai untuk seluruh daerah adalah ${formatNumber(average)}.`;
});

onMounted(async () => {
  // Inisialisasi map
  map.value = L.map('map').setView([-7.67, 110.25], 9);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors',
    maxZoom: 19,
  }).addTo(map.value);

  // Load variables
  await loadVariables();
});

const loadVariables = async () => {
  try {
    const response = await fetch(`/api/visualizations/${props.visualization.id}/map-variables`);
    const data = await response.json();
    variables.value = data.variables;
    if (variables.value.length > 0) {
      selectedVariable.value = variables.value[0];
      await loadMapData();
    }
  } catch (error) {
    console.error('Error loading variables:', error);
  }
};

const loadMapData = async () => {
  if (!selectedVariable.value) return;

  try {
    const response = await fetch(
      `/api/visualizations/${props.visualization.id}/map-data?variable=${selectedVariable.value}`
    );
    const data = await response.json();
    mapData.value = data.data;

    // Load GeoJSON dan render map
    await renderMap();
  } catch (error) {
    console.error('Error loading map data:', error);
  }
};

const renderMap = async () => {
  try {
    const targetUrl = `/geojson/${props.visualization.geojson_file}.geojson`;
    console.log('MEMBACA GEOJSON DARI URL:', targetUrl);

    const geoJsonResponse = await fetch(
      `/geojson/${props.visualization.geojson_file}.geojson`
    );

    if (!geoJsonResponse.ok) {
      console.error('Failed to load GeoJSON:', geoJsonResponse.status);
      return;
    }

    const geoJsonData = await geoJsonResponse.json();
    console.log('GeoJSON loaded:', geoJsonData);
    console.log('Map data:', mapData.value);

    // Remove existing layers
    map.value.eachLayer((layer) => {
      if (layer instanceof L.GeoJSON) {
        map.value.removeLayer(layer);
      }
    });

    // Calculate min/max values
    const values = mapData.value.map(d => d.variable_value);
    minValue.value = Math.min(...values);
    maxValue.value = Math.max(...values);

    // Create data map
    const dataMap = {};
    mapData.value.forEach(item => {
      dataMap[item.region_id] = item;
    });

    // Color function
    const getColor = (value) => {
      if (value === undefined) return '#cccccc';
      const ratio = (value - minValue.value) / (maxValue.value - minValue.value);
      if (ratio < 0.2) return '#fee5d9';
      if (ratio < 0.4) return '#fcae91';
      if (ratio < 0.6) return '#fb6a4a';
      if (ratio < 0.8) return '#de2d26';
      return '#a50f15';
    };

    // Generate legend
    colorLegend.value = ['#fee5d9', '#fcae91', '#fb6a4a', '#de2d26', '#a50f15'];
    colorLabels.value = [
      `Sangat Rendah (${formatNumber(minValue.value)} - ${formatNumber(minValue.value + (maxValue.value - minValue.value) * 0.2)})`,
      `Rendah (${formatNumber(minValue.value + (maxValue.value - minValue.value) * 0.2)} - ${formatNumber(minValue.value + (maxValue.value - minValue.value) * 0.4)})`,
      `Sedang (${formatNumber(minValue.value + (maxValue.value - minValue.value) * 0.4)} - ${formatNumber(minValue.value + (maxValue.value - minValue.value) * 0.6)})`,
      `Tinggi (${formatNumber(minValue.value + (maxValue.value - minValue.value) * 0.6)} - ${formatNumber(minValue.value + (maxValue.value - minValue.value) * 0.8)})`,
      `Sangat Tinggi (${formatNumber(minValue.value + (maxValue.value - minValue.value) * 0.8)} - ${formatNumber(maxValue.value)})`,
    ];

    // Add GeoJSON layer
    L.geoJSON(geoJsonData, {
      style: (feature) => {
        const data = dataMap[feature.properties.id];
        return {
          fillColor: getColor(data?.variable_value),
          weight: 2,
          opacity: 1,
          color: '#333',
          fillOpacity: 0.7,
        };
      },
      onEachFeature: (feature, layer) => {
        const data = dataMap[feature.properties.id];
        let popupText = `<div class="p-2">
          <strong class="text-lg">${feature.properties.name}</strong><br/>`;
        
        if (data) {
          popupText += `
            <div class="mt-2">
              <span class="font-semibold">${selectedVariable.value}:</span> ${formatNumber(data.variable_value)}<br/>
              <span class="text-sm text-slate-600">Kategori: ${getCategory(data.variable_value)}</span>
            </div>`;
        } else {
          popupText += '<span class="text-red-600">Tidak ada data</span>';
        }
        popupText += '</div>';
        
        layer.bindPopup(popupText);
        
        // Hover effect
        layer.on({
          mouseover: (e) => {
            const layer = e.target;
            layer.setStyle({
              weight: 3,
              color: '#666',
              fillOpacity: 0.9
            });
          },
          mouseout: (e) => {
            const layer = e.target;
            layer.setStyle({
              weight: 2,
              color: '#333',
              fillOpacity: 0.7
            });
          }
        });
      },
    }).addTo(map.value);
  } catch (error) {
    console.error('Error rendering map:', error);
  }
};

const getCategory = (value) => {
  const ratio = (value - minValue.value) / (maxValue.value - minValue.value);
  if (ratio < 0.2) return 'Sangat Rendah';
  if (ratio < 0.4) return 'Rendah';
  if (ratio < 0.6) return 'Sedang';
  if (ratio < 0.8) return 'Tinggi';
  return 'Sangat Tinggi';
};

const getCategoryClass = (value) => {
  const ratio = (value - minValue.value) / (maxValue.value - minValue.value);
  if (ratio < 0.2) return 'bg-red-100 text-red-800';
  if (ratio < 0.4) return 'bg-orange-100 text-orange-800';
  if (ratio < 0.6) return 'bg-yellow-100 text-yellow-800';
  if (ratio < 0.8) return 'bg-green-100 text-green-800';
  return 'bg-blue-100 text-blue-800';
};

const formatNumber = (num) => {
  return new Intl.NumberFormat('id-ID', {
    minimumFractionDigits: 0,
    maximumFractionDigits: 2
  }).format(num);
};
</script>

<style scoped>
#map {
  position: relative;
  z-index: 0;
}
</style>