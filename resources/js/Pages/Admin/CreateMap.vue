<template>
  <AdminLayout title="Buat Visualisasi Peta">
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
              <span class="ml-1 text-sm font-medium text-gray-500">Buat Baru</span>
            </div>
          </li>
        </ol>
      </nav>

      <!-- Form Card -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
          <h2 class="text-xl font-semibold text-gray-900">Informasi Visualisasi</h2>
          <p class="mt-1 text-sm text-gray-600">Lengkapi form berikut untuk membuat visualisasi peta baru</p>
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
              placeholder="Contoh: Sebaran Pendapatan per Daerah Yogyakarta"
              :class="{ 'border-red-300': errors.name }"
            />
            <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</p>
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
              placeholder="Jelaskan apa yang divisualisasikan dalam peta ini..."
            ></textarea>
            <p class="mt-1 text-xs text-gray-500">Opsional - Berikan konteks tentang data yang ditampilkan</p>
          </div>

          <!-- Pilih Wilayah -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Pilih Wilayah <span class="text-red-500">*</span>
            </label>
            <select 
              v-model="form.geojson_file"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
              :class="{ 'border-red-300': errors.geojson_file }"
            >
              <option value="">-- Pilih Wilayah --</option>
              <option v-for="(label, file) in geoJsonFiles" :key="file" :value="file">
                {{ label }}
              </option>
            </select>
            <p v-if="errors.geojson_file" class="mt-1 text-sm text-red-600">{{ errors.geojson_file }}</p>
          </div>

          <!-- Upload File Excel -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Upload File Excel <span class="text-red-500">*</span>
            </label>
            
            <div 
              @click="$refs.fileInput.click()"
              @dragover.prevent="dragOver = true"
              @dragleave.prevent="dragOver = false"
              @drop.prevent="handleDrop"
              class="border-2 border-dashed rounded-lg p-8 text-center cursor-pointer transition-all"
              :class="dragOver ? 'border-blue-500 bg-blue-50' : form.file ? 'border-green-300 bg-green-50' : errors.file ? 'border-red-300 bg-red-50' : 'border-gray-300 hover:border-blue-400 hover:bg-gray-50'"
            >
              <div v-if="!form.file">
                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                  <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <div class="mt-4">
                  <p class="text-sm text-gray-600">
                    <span class="font-semibold text-blue-600">Klik untuk memilih file</span> atau drag & drop
                  </p>
                  <p class="text-xs text-gray-500 mt-1">Format: .xlsx, .xls (Maksimal 5MB)</p>
                </div>
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
            <p v-if="errors.file" class="mt-1 text-sm text-red-600">{{ errors.file }}</p>
          </div>

          <!-- Format Info -->
          <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
              </div>
              <div class="ml-3 flex-1">
                <h3 class="text-sm font-medium text-blue-800">Format Excel yang Diperlukan</h3>
                <div class="mt-2 text-sm text-blue-700">
                  <p class="mb-2">File Excel harus memiliki struktur sebagai berikut:</p>
                  <div class="bg-white rounded border border-blue-200 p-3 font-mono text-xs">
                    <div class="grid grid-cols-5 gap-2 mb-1 font-bold">
                      <div>region_id</div>
                      <div>region_name</div>
                      <div>Variable 1</div>
                      <div>Variable 2</div>
                      <div>...</div>
                    </div>
                    <div class="grid grid-cols-5 gap-2 text-gray-600">
                      <div>1</div>
                      <div>Sleman</div>
                      <div>5000000</div>
                      <div>85</div>
                      <div>...</div>
                    </div>
                  </div>
                  <p class="mt-2 text-xs">
                    <strong>Kolom 1:</strong> ID Daerah (numerik)<br>
                    <strong>Kolom 2:</strong> Nama Daerah (teks)<br>
                    <strong>Kolom 3+:</strong> Variabel data (numerik, bisa lebih dari satu kolom)
                  </p>
                </div>
              </div>
            </div>
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
              {{ processing ? 'Memproses...' : 'Buat Visualisasi' }}
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
  geoJsonFiles: Object,
});

const fileInput = ref(null);
const errors = ref({});
const processing = ref(false);
const dragOver = ref(false);

const form = useForm({
  name: '',
  description: '',
  geojson_file: '',
  file: null,
});

const handleFileChange = (e) => {
  form.file = e.target.files[0];
};

const handleDrop = (e) => {
  dragOver.value = false;
  const files = e.dataTransfer.files;
  if (files.length > 0) {
    form.file = files[0];
  }
};

const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Bytes';
  const k = 1024;
  const sizes = ['Bytes', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
};

const submitForm = async () => {
  processing.value = true;
  errors.value = {};
  
  const formData = new FormData();
  formData.append('name', form.name);
  formData.append('description', form.description);
  formData.append('geojson_file', form.geojson_file);
  if (form.file) {
    formData.append('file', form.file);
  }

  form.post('/admin/map', {
    onError: (err) => {
      errors.value = err;
      processing.value = false;
    },
    onFinish: () => {
      processing.value = false;
    }
  });
};
</script>