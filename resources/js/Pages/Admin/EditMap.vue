<template>
  <AdminLayout title="Edit Visualisasi Peta">
    <div class="max-w-4xl mx-auto">
      <!-- Breadcrumb -->
      <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
          <li class="inline-flex items-center">
            <Link href="/admin/map" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
              <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
              </svg>
              Visualisasi Peta
            </Link>
          </li>
          <li>
            <div class="flex items-center">
              <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
              </svg>
              <span class="ml-1 text-sm font-medium text-gray-500">Edit</span>
            </div>
          </li>
        </ol>
      </nav>

      <!-- Form Card -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
          <h2 class="text-xl font-semibold text-gray-900">Edit Visualisasi</h2>
          <p class="mt-1 text-sm text-gray-600">{{ visualization.name }}</p>
        </div>

        <form @submit.prevent="submitForm" class="p-6 space-y-6">
          <!-- Nama -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Nama Visualisasi <span class="text-red-500">*</span>
            </label>
            <input 
              v-model="form.name"
              type="text"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
            />
          </div>

          <!-- Deskripsi -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Deskripsi
            </label>
            <textarea 
              v-model="form.description"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
              rows="3"
            ></textarea>
          </div>

          <!-- Pilih Wilayah -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Pilih Wilayah <span class="text-red-500">*</span>
            </label>
            <select 
              v-model="form.geojson_file"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
            >
              <option v-for="(label, file) in geoJsonFiles" :key="file" :value="file">
                {{ label }}
              </option>
            </select>
          </div>

          <!-- Current Data Info -->
          <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
            <h3 class="text-sm font-medium text-gray-900 mb-2">Data Saat Ini</h3>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <p class="text-xs text-gray-500">Total Data Points</p>
                <p class="text-lg font-semibold text-gray-900">{{ visualization.map_data.length }}</p>
              </div>
              <div>
                <p class="text-xs text-gray-500">Wilayah</p>
                <p class="text-lg font-semibold text-gray-900">{{ visualization.geojson_file }}</p>
              </div>
            </div>
          </div>

          <!-- Upload File Excel (Optional) -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Upload File Excel Baru (Opsional)
            </label>
            
            <div 
              @click="$refs.fileInput.click()"
              class="border-2 border-dashed rounded-lg p-8 text-center cursor-pointer transition-all"
              :class="form.file ? 'border-green-300 bg-green-50' : 'border-gray-300 hover:border-blue-400 hover:bg-gray-50'"
            >
              <div v-if="!form.file">
                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                  <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <p class="mt-2 text-sm text-gray-600">Klik untuk memilih file baru</p>
                <p class="text-xs text-gray-500 mt-1">Biarkan kosong jika tidak ingin mengubah data</p>
              </div>
              <div v-else class="flex items-center justify-center">
                <svg class="h-10 w-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <div class="ml-3 text-left">
                  <p class="text-sm font-medium text-gray-900">{{ form.file.name }}</p>
                  <p class="text-xs text-gray-500">{{ formatFileSize(form.file.size) }}</p>
                </div>
                <button 
                  @click.stop="form.file = null"
                  type="button"
                  class="ml-4 text-red-500 hover:text-red-700"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
            </div>
            <input 
              ref="fileInput"
              type="file"
              accept=".xlsx,.xls"
              @change="handleFileChange"
              class="hidden"
            />
          </div>

          <!-- Action Buttons -->
          <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
            <Link 
              href="/admin/map"
              class="px-6 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors"
            >
              Batal
            </Link>
            <button 
              type="submit"
              :disabled="processing"
              class="px-6 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center"
            >
              <svg v-if="processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import AdminLayout from './AdminLayout.vue';

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

const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Bytes';
  const k = 1024;
  const sizes = ['Bytes', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
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
    onFinish: () => {
      processing.value = false;
    }
  });
};
</script>