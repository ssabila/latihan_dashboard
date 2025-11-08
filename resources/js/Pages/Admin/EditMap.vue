<template>
  <div class="min-h-screen bg-gradient-to-b from-slate-50 to-slate-100 p-8">
    <div class="max-w-2xl mx-auto">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-900 mb-2">Edit Visualisasi Peta</h1>
        <p class="text-slate-600">{{ visualization.name }}</p>
      </div>

      <!-- Form -->
      <form @submit.prevent="submitForm" class="bg-white rounded-lg shadow p-8">
        <!-- Nama -->
        <div class="mb-6">
          <label class="block text-sm font-medium text-slate-900 mb-2">Nama Visualisasi *</label>
          <input 
            v-model="form.name"
            type="text"
            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>

        <!-- Deskripsi -->
        <div class="mb-6">
          <label class="block text-sm font-medium text-slate-900 mb-2">Deskripsi</label>
          <textarea 
            v-model="form.description"
            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            rows="3"
          ></textarea>
        </div>

        <!-- Pilih GeoJSON -->
        <div class="mb-6">
          <label class="block text-sm font-medium text-slate-900 mb-2">Pilih Wilayah *</label>
          <select 
            v-model="form.geojson_file"
            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <option v-for="(label, file) in geoJsonFiles" :key="file" :value="file">
              {{ label }}
            </option>
          </select>
        </div>

        <!-- Upload File Excel -->
        <div class="mb-6">
          <label class="block text-sm font-medium text-slate-900 mb-2">Upload File Excel Baru (Opsional)</label>
          <div 
            class="border-2 border-dashed border-slate-300 rounded-lg p-8 text-center cursor-pointer hover:border-blue-500 transition"
            @click="$refs.fileInput.click()"
          >
            <p class="text-slate-600 mb-2">Klik untuk memilih file atau drag & drop</p>
            <p v-if="form.file" class="text-sm text-slate-700 mt-3">
              📄 {{ form.file.name }}
            </p>
          </div>
          <input 
            ref="fileInput"
            type="file"
            accept=".xlsx,.xls"
            @change="handleFileChange"
            class="hidden"
          />
        </div>

        <!-- Current Data -->
        <div class="mb-8 p-4 bg-slate-50 border border-slate-200 rounded-lg">
          <h3 class="font-semibold text-slate-900 mb-2">Data Saat Ini</h3>
          <p class="text-sm text-slate-600">Total data points: {{ visualization.map_data.length }}</p>
        </div>

        <!-- Buttons -->
        <div class="flex gap-4">
          <button 
            type="submit"
            :disabled="processing"
            class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:opacity-50"
          >
            {{ processing ? 'Memproses...' : 'Update Visualisasi' }}
          </button>
          <Link 
            href="/admin/map"
            class="flex-1 px-6 py-3 bg-slate-200 text-slate-900 rounded-lg hover:bg-slate-300 transition text-center"
          >
            Batal
          </Link>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  visualization: Object,
  geoJsonFiles: Object,
});

const fileInput = ref(null);
const processing = ref(false);

const form = useForm({
  name: props.visualization.name,
  description: props.visualization.description || '',
  geojson_file: props.visualization.geojson_file,
  file: null,
});

const handleFileChange = (e) => {
  form.file = e.target.files[0];
};

const submitForm = () => {
  processing.value = true;
  const formData = new FormData();
  formData.append('name', form.name);
  formData.append('description', form.description);
  formData.append('geojson_file', form.geojson_file);
  if (form.file) {
    formData.append('file', form.file);
  }
  formData.append('_method', 'PUT');

  form.post(`/admin/map/${props.visualization.id}`, {
    onError: () => {
      processing.value = false;
    },
  });
};
</script>