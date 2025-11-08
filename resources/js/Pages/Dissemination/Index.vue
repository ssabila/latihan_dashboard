<template>
  <div class="min-h-screen bg-gradient-to-b from-slate-50 to-slate-100">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex justify-between items-center">
          <h1 class="text-2xl font-bold text-blue-600">Data Diseminasi Survei</h1>
          <div class="flex gap-4">
            <Link href="/" class="text-slate-600 hover:text-blue-600">Beranda</Link>
            <Link href="/admin/map" class="text-slate-600 hover:text-blue-600">Admin</Link>
          </div>
        </div>
      </div>
    </nav>

    <!-- Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <!-- Header -->
      <div class="mb-12">
        <h2 class="text-3xl font-bold text-slate-900 mb-2">Diseminasi Data Survei</h2>
        <p class="text-slate-600">Jelajahi visualisasi data dari berbagai survei yang telah dilakukan</p>
      </div>

      <!-- Map Visualizations -->
      <div v-if="mapVisualizations.length > 0" class="mb-12">
        <h3 class="text-2xl font-bold text-slate-900 mb-6">📊 Peta Interaktif</h3>
        <div class="grid md:grid-cols-2 gap-6">
          <Link 
            v-for="viz in mapVisualizations"
            :key="viz.id"
            :href="`/diseminasi/map/${viz.id}`"
            class="group bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden cursor-pointer"
          >
            <div class="h-40 bg-gradient-to-br from-blue-100 to-blue-50 flex items-center justify-center">
              <span class="text-4xl">📍</span>
            </div>
            <div class="p-6">
              <h4 class="text-lg font-bold text-slate-900 group-hover:text-blue-600 transition mb-2">
                {{ viz.name }}
              </h4>
              <p class="text-sm text-slate-600 mb-3">{{ viz.description || 'Tidak ada deskripsi' }}</p>
              <div class="text-xs text-slate-500">
                {{ viz.map_data.length }} data points
              </div>
            </div>
          </Link>
        </div>
      </div>

      <!-- WordCloud Visualizations -->
      <div v-if="wordcloudVisualizations.length > 0">
        <h3 class="text-2xl font-bold text-slate-900 mb-6">☁️ WordCloud</h3>
        <div class="grid md:grid-cols-2 gap-6">
          <Link 
            v-for="viz in wordcloudVisualizations"
            :key="viz.id"
            :href="`/diseminasi/wordcloud/${viz.id}`"
            class="group bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden cursor-pointer"
          >
            <div class="h-40 bg-gradient-to-br from-purple-100 to-purple-50 flex items-center justify-center">
              <span class="text-4xl">☁️</span>
            </div>
            <div class="p-6">
              <h4 class="text-lg font-bold text-slate-900 group-hover:text-blue-600 transition mb-2">
                {{ viz.name }}
              </h4>
              <p class="text-sm text-slate-600 mb-3">{{ viz.description || 'Tidak ada deskripsi' }}</p>
              <div class="text-xs text-slate-500">
                {{ viz.wordcloud_data.length }} kata
              </div>
            </div>
          </Link>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="mapVisualizations.length === 0 && wordcloudVisualizations.length === 0" class="text-center py-12">
        <p class="text-slate-600 mb-4">Belum ada visualisasi yang tersedia</p>
        <Link href="/admin/map" class="text-blue-600 hover:underline">Buat visualisasi di admin panel</Link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
  mapVisualizations: Array,
  wordcloudVisualizations: Array,
});
</script>