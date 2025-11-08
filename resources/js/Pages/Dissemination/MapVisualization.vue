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
        <h3 class="font-bold text-slate-900 mb-4">Legend</h3>
        <div class="flex gap-2 flex-wrap">
          <div v-for="(color, index) in colorLegend" :key="index" class="flex items-center gap-2">
            <div 
              class="w-6 h-6 rounded"
              :style="{ backgroundColor: color }"
            ></div>
            <span class="text-sm text-slate-600">{{ colorLabels[index] }}</span>
          </div>
        </div>
      </div>

      <!-- Info Table -->
      <div v-if="selectedVariable && mapData.length > 0" class="bg-white rounded-lg shadow p-6 mt-8">
        <h3 class="font-bold text-slate-900 mb-4">Data Detail</h3>
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="bg-slate-100 border-b border-slate-200">
              <tr>
                <th class="px-4 py-2 text-left">Daerah</th>
                <th class="px-4 py-2 text-right">{{ selectedVariable }}</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
              <tr v-for="data in mapData" :key="data.region_id" class="hover:bg-slate-50">
                <td class="px-4 py-2">{{ data.region_name }}</td>
                <td class="px-4 py-2 text-right font-mono">{{ data.variable_value.toFixed(2) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
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
    const geoJsonResponse = await fetch(
      `/api/visualizations/geojson/${props.visualization.geojson_file}`
    );
    const geoJsonData = await geoJsonResponse.json();

    // Remove existing layers
    map.value.eachLayer((layer) => {
      if (layer instanceof L.GeoJSON) {
        map.value.removeLayer(layer);
      }
    });

    // Calculate min/max values
    const values = mapData.value.map(d => d.variable_value);
    const minValue = Math.min(...values);
    const maxValue = Math.max(...values);

    // Create data map
    const dataMap = {};
    mapData.value.forEach(item => {
      dataMap[item.region_id] = item;
    });

    // Color function
    const getColor = (value) => {
      if (value === undefined) return '#cccccc';
      const ratio = (value - minValue) / (maxValue - minValue);
      if (ratio < 0.2) return '#ffffcc';
      if (ratio < 0.4) return '#c2e699';
      if (ratio < 0.6) return '#78c679';
      if (ratio < 0.8) return '#31a354';
      return '#006837';
    };

    // Generate legend
    colorLegend.value = ['#ffffcc', '#c2e699', '#78c679', '#31a354', '#006837'];
    colorLabels.value = [
      `${minValue.toFixed(0)} - ${(minValue + (maxValue - minValue) * 0.2).toFixed(0)}`,
      `${(minValue + (maxValue - minValue) * 0.2).toFixed(0)} - ${(minValue + (maxValue - minValue) * 0.4).toFixed(0)}`,
      `${(minValue + (maxValue - minValue) * 0.4).toFixed(0)} - ${(minValue + (maxValue - minValue) * 0.6).toFixed(0)}`,
      `${(minValue + (maxValue - minValue) * 0.6).toFixed(0)} - ${(minValue + (maxValue - minValue) * 0.8).toFixed(0)}`,
      `${(minValue + (maxValue - minValue) * 0.8).toFixed(0)} - ${maxValue.toFixed(0)}`,
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
        let popupText = `<strong>${feature.properties.name}</strong><br/>`;
        if (data) {
          popupText += `${selectedVariable.value}: ${data.variable_value.toFixed(2)}`;
        } else {
          popupText += 'Tidak ada data';
        }
        layer.bindPopup(popupText);
      },
    }).addTo(map.value);
  } catch (error) {
    console.error('Error rendering map:', error);
  }
};

watch(selectedVariable, () => {
  if (selectedVariable.value) {
    loadMapData();
  }
});
</script>

<style scoped>
#map {
  position: relative;
}
</style>