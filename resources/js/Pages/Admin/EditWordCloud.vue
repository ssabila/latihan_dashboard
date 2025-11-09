<template>
  <AdminLayout title="Edit WordCloud">
    <div class="max-w-4xl mx-auto">
      <!-- Breadcrumb -->
      <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
          <li class="inline-flex items-center">
            <Link href="/admin/wordcloud" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-purple-600">
              <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
              </svg>
              WordCloud
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
          <h2 class="text-xl font-semibold text-gray-900">Edit WordCloud</h2>
          <p class="mt-1 text-sm text-gray-600">{{ visualization.name }}</p>
        </div>

        <form @submit.prevent="submitForm" class="p-6 space-y-6">
          <!-- Nama -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Nama WordCloud <span class="text-red-500">*</span>
            </label>
            <input 
              v-model="form.name"
              type="text"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition"
            />
          </div>

          <!-- Deskripsi -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Deskripsi
            </label>
            <textarea 
              v-model="form.description"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition"
              rows="3"
            ></textarea>
          </div>

          <!-- Words List -->
          <div>
            <div class="flex items-center justify-between mb-4">
              <label class="block text-sm font-medium text-gray-700">
                Daftar Kata <span class="text-red-500">*</span>
              </label>
              <span class="text-xs text-gray-500">{{ form.words.length }} kata</span>
            </div>

            <!-- Word Items -->
            <div v-if="form.words.length > 0" class="space-y-3 mb-4">
              <div 
                v-for="(item, index) in form.words" 
                :key="index"
                class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-purple-300 transition-colors"
              >
                <div class="flex-shrink-0 w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                  <span class="text-sm font-medium text-purple-700">{{ index + 1 }}</span>
                </div>
                
                <input 
                  v-model="form.words[index].word"
                  type="text"
                  placeholder="Kata"
                  class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                />
                
                <div class="flex items-center space-x-2">
                  <input 
                    v-model.number="form.words[index].value"
                    type="number"
                    placeholder="Nilai"
                    min="1"
                    class="w-24 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                  />
                  <button 
                    type="button"
                    @click="removeWord(index)"
                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                    title="Hapus"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
                </div>
              </div>
            </div>

            <!-- Add Word Button -->
            <button 
              type="button"
              @click="addWord"
              class="w-full px-4 py-3 border-2 border-dashed border-gray-300 rounded-lg text-gray-600 hover:border-purple-500 hover:text-purple-600 hover:bg-purple-50 transition-all flex items-center justify-center"
            >
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              Tambah Kata
            </button>
          </div>

          <!-- Preview -->
          <div v-if="form.words.length > 0" class="bg-purple-50 border border-purple-200 rounded-lg p-4">
            <h3 class="text-sm font-medium text-purple-900 mb-3">Preview Kata</h3>
            <div class="flex flex-wrap gap-2">
              <span 
                v-for="(word, idx) in form.words.filter(w => w.word)" 
                :key="idx"
                class="px-3 py-1 bg-white border border-purple-200 rounded-full text-sm"
                :style="{ fontSize: `${Math.min(12 + word.value / 5, 20)}px` }"
              >
                {{ word.word }} <span class="text-purple-600 font-medium">({{ word.value }})</span>
              </span>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
            <Link 
              href="/admin/wordcloud"
              class="px-6 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors"
            >
              Batal
            </Link>
            <button 
              type="submit"
              :disabled="processing"
              class="px-6 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center"
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
});

const processing = ref(false);

const form = useForm({
  name: props.visualization.name,
  description: props.visualization.description || '',
  words: props.visualization.word_cloud_data.map(w => ({ word: w.word, value: w.value })),
});

const addWord = () => {
  form.words.push({ word: '', value: 1 });
};

const removeWord = (index) => {
  if (form.words.length > 1) {
    form.words.splice(index, 1);
  }
};

const submitForm = () => {
  processing.value = true;
  
  form.put(`/admin/wordcloud/${props.visualization.id}`, {
    onFinish: () => {
      processing.value = false;
    }
  });
};
</script>