<template>
  <div class="min-h-screen bg-gradient-to-b from-slate-50 to-slate-100 p-8">
    <div class="max-w-2xl mx-auto">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-900 mb-2">Buat WordCloud</h1>
        <p class="text-slate-600">Tambahkan kata dan nilai untuk membuat wordcloud interaktif</p>
      </div>

      <!-- Form -->
      <form @submit.prevent="submitForm" class="bg-white rounded-lg shadow p-8">
        <!-- Nama -->
        <div class="mb-6">
          <label class="block text-sm font-medium text-slate-900 mb-2">Nama WordCloud *</label>
          <input 
            v-model="form.name"
            type="text"
            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder="Misalnya: Topik Diskusi Utama"
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
            placeholder="Jelaskan apa yang wordcloud ini tunjukkan"
          ></textarea>
        </div>

        <!-- Words Input -->
        <div class="mb-6">
          <label class="block text-sm font-medium text-slate-900 mb-4">Daftar Kata *</label>
          
          <!-- Existing Words -->
          <div v-if="form.words.length > 0" class="space-y-3 mb-6">
            <div v-for="(item, index) in form.words" :key="index" class="flex gap-3">
              <input 
                v-model="form.words[index].word"
                type="text"
                placeholder="Kata"
                class="flex-1 px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
              <input 
                v-model.number="form.words[index].value"
                type="number"
                placeholder="Nilai"
                min="1"
                class="w-24 px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
              <button 
                type="button"
                @click="removeWord(index)"
                class="px-4 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition"
              >
                Hapus
              </button>
            </div>
          </div>

          <!-- Add New Word -->
          <button 
            type="button"
            @click="addWord"
            class="w-full px-4 py-2 border-2 border-dashed border-slate-300 rounded-lg text-slate-600 hover:border-blue-500 hover:text-blue-600 transition"
          >
            + Tambah Kata
          </button>

          <p v-if="errors['words.required']" class="mt-2 text-sm text-red-600">{{ errors['words.required'] }}</p>
        </div>

        <!-- Buttons -->
        <div class="flex gap-4">
          <button 
            type="submit"
            :disabled="processing || form.words.length === 0"
            class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:opacity-50"
          >
            {{ processing ? 'Memproses...' : 'Buat WordCloud' }}
          </button>
          <Link 
            href="/admin/wordcloud"
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

const errors = ref({});
const processing = ref(false);

const form = useForm({
  name: '',
  description: '',
  words: [{ word: '', value: 1 }],
});

const addWord = () => {
  form.words.push({ word: '', value: 1 });
};

const removeWord = (index) => {
  form.words.splice(index, 1);
};

const submitForm = () => {
  processing.value = true;
  form.post('/admin/wordcloud', {
    onError: (err) => {
      errors.value = err;
      processing.value = false;
    },
  });
};
</script>