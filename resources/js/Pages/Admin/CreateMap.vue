<template>
  <div class="min-h-screen bg-gradient-to-b from-slate-50 to-slate-100 p-8">
    <div class="max-w-2xl mx-auto">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-900 mb-2">Buat Visualisasi Peta</h1>
        <p class="text-slate-600">Upload data Excel untuk membuat visualisasi peta interaktif</p>
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
            placeholder="Misalnya: Sebaran Pendapatan per Daerah"
          />
          <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</p>
        </div>

        <!-- Deskripsi -->
        <div class="mb-6">
          <label class="block text-sm font-medium text-slate-900 mb-2">Deskripsi</label>
          <textarea 
            v-model="form.description"
            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            rows="3"
            placeholder="Jelaskan apa yang visualisasi ini tunjukkan"
          ></textarea>
        </div>

        <!-- Pilih GeoJSON -->
        <div class="mb-6">
          <label class="block text-sm font-medium text-slate-900 mb-2">Pilih Wilayah *</label>
          <select 
            v-model="form.geojson_file"
            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <option value="">-- Pilih Wilayah --</option>
            <option v-for="(label, file) in geoJsonFiles" :key="file" :value="file">
              {{ label }}
            </option>
          </select>
          <p v-if="errors.geojson_file" class="mt-1 text-sm text-red-600">{{ errors.geojson_file }}</p>
        </div>

        <!-- Upload File Excel -->
        <div class="mb-6">
          <label class="block text-sm font-medium text-slate-900 mb-2">Upload File Excel *</label>
          <div 
            class="border-2 border-dashed border-slate-300 rounded-lg p-8 text-center cursor-pointer hover:border-blue-500 transition"
            @click="$refs.fileInput.click()"
          >
            <p class="text-slate-600 mb-2">Klik untuk memilih file atau drag & drop</p>
            <p class="text-xs text-slate-500">Format: .xlsx, .xls (Max 5MB)</p>
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
          <p v-if="errors.file" class="mt-1 text-sm text-red-600">{{ errors.file }}</p>
        </div>

        <!-- Format Template -->
        <div class="mb-8 p-4 bg-blue-50 border border-blue-200 rounded-lg">
          <h3 class="font-semibold text-blue-900 mb-3">Format Excel yang Diperlukan:</h3>
          <table class="text-sm text-blue-900 w-full">
            <tbody> <tr>
                <td class="font-mono font-bold">Kolom 1:</td>
                <td class="pl-4">ID Daerah (misalnya: 1, 2, 3)</td>
              </tr>
              <tr>
                <td class="font-mono font-bold">Kolom 2:</td>
                <td class="pl-4">Nama Daerah (misalnya: Sleman, Bantul)</td>
              </tr>
              <tr>
                <td class="font-mono font-bold">Kolom 3+:</td>
                <td class="pl-4">Variabel survei (bisa lebih dari satu)</td>
              </tr>
            </tbody> 
          </table>
          <p class="text-xs text-blue-800 mt-3">Contoh: ID=1, Nama=Sleman, Pendapatan=500000, Pendidikan=85</p>
        </div>

        <!-- Buttons -->
        <div class="flex gap-4">
          <button 
            type="submit"
            :disabled="processing"
            class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:opacity-50"
          >
            {{ processing ? 'Memproses...' : 'Buat Visualisasi' }}
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
  geoJsonFiles: Object,
});

const fileInput = ref(null);
const errors = ref({});
const processing = ref(false);

const form = useForm({
  name: '',
  description: '',
  geojson_file: '',
  file: null,
});

const handleFileChange = (e) => {
  form.file = e.target.files[0];
};

const submitForm = async () => {
  processing.value = true;
  const formData = new FormData();
  formData.append('name', form.name);
  formData.append('description', form.description);
  formData.append('geojson_file', form.geojson_file);
  formData.append('file', form.file);

  form.post('/admin/map', {
    onError: (err) => {
      errors.value = err;
      processing.value = false;
    },
  });
};
</script>