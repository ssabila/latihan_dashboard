<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50">
    <!-- Navigation -->
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-50 backdrop-blur-sm bg-white/90">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
              </svg>
            </div>
            <div>
              <h1 class="text-xl font-bold text-gray-900">Data Diseminasi</h1>
              <p class="text-xs text-gray-500">Platform Visualisasi Data Survei</p>
            </div>
          </div>
          
          <div class="flex items-center space-x-4">
            <Link 
              href="/" 
              class="text-gray-600 hover:text-gray-900 text-sm font-medium transition-colors"
            >
              Beranda
            </Link>
            <Link 
              href="/admin/map" 
              class="px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 transition-colors"
            >
              Admin Panel
            </Link>
          </div>
        </div>
      </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative overflow-hidden">
      <div class="absolute inset-0 bg-gradient-to-br from-blue-100/50 to-purple-100/50"></div>
      <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
        <h2 class="text-4xl font-bold text-gray-900 mb-4">
          Jelajahi Visualisasi Data
        </h2>
        <p class="text-xl text-gray-600 max-w-2xl mx-auto">
          Temukan insight dari data survei melalui peta interaktif dan wordcloud yang informatif
        </p>
        
        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-3xl mx-auto mt-12">
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="text-3xl font-bold text-blue-600 mb-2">{{ mapVisualizations.length }}</div>
            <div class="text-sm text-gray-600">Peta Interaktif</div>
          </div>
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="text-3xl font-bold text-purple-600 mb-2">{{ wordcloudVisualizations.length }}</div>
            <div class="text-sm text-gray-600">WordCloud</div>
          </div>
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="text-3xl font-bold text-green-600 mb-2">{{ totalVisualizations }}</div>
            <div class="text-sm text-gray-600">Total Visualisasi</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <!-- Map Visualizations -->
      <div v-if="mapVisualizations.length > 0" class="mb-16">
        <div class="flex items-center justify-between mb-8">
          <div>
            <h3 class="text-2xl font-bold text-gray-900 flex items-center">
              <span class="text-3xl mr-3">🗺️</span>
              Peta Interaktif
            </h3>
            <p class="text-gray-600 mt-1">Eksplorasi data geografis dengan visualisasi peta</p>
          </div>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
          <Link 
            v-for="viz in mapVisualizations"
            :key="viz.id"
            :href="`/diseminasi/map/${viz.id}`"
            class="group bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition-all duration-300 hover:-translate-y-1"
          >
            <div class="h-48 bg-gradient-to-br from-blue-400 to-blue-600 relative overflow-hidden">
              <div class="absolute inset-0 bg-black/10 group-hover:bg-black/0 transition-colors"></div>
              <div class="absolute inset-0 flex items-center justify-center">
                <svg class="w-20 h-20 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                </svg>
              </div>
            </div>
            <div class="p-6">
              <h4 class="text-lg font-bold text-gray-900 group-hover:text-blue-600 transition-colors mb-2">
                {{ viz.name }}
              </h4>
              <p class="text-sm text-gray-600 mb-4 line-clamp-2">
                {{ viz.description || 'Tidak ada deskripsi' }}
              </p>
              <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-gray-500">
                  {{ viz.map_data.length }} data points
                </span>
                <span class="text-blue-600 group-hover:translate-x-1 transition-transform">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                </span>
              </div>
            </div>
          </Link>
        </div>
      </div>

      <!-- WordCloud Visualizations -->
      <div v-if="wordcloudVisualizations.length > 0">
        <div class="flex items-center justify-between mb-8">
          <div>
            <h3 class="text-2xl font-bold text-gray-900 flex items-center">
              <span class="text-3xl mr-3">☁️</span>
              WordCloud
            </h3>
            <p class="text-gray-600 mt-1">Lihat distribusi kata dari hasil survei</p>
          </div>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
          <Link 
            v-for="viz in wordcloudVisualizations"
            :key="viz.id"
            :href="`/diseminasi/wordcloud/${viz.id}`"
            class="group bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition-all duration-300 hover:-translate-y-1"
          >
            <div class="h-48 bg-gradient-to-br from-purple-400 to-purple-600 relative overflow-hidden">
              <div class="absolute inset-0 bg-black/10 group-hover:bg-black/0 transition-colors"></div>
              <div class="absolute inset-0 flex items-center justify-center">
                <svg class="w-20 h-20 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                </svg>
              </div>
            </div>
            <div class="p-6">
              <h4 class="text-lg font-bold text-gray-900 group-hover:text-purple-600 transition-colors mb-2">
                {{ viz.name }}
              </h4>
              <p class="text-sm text-gray-600 mb-4 line-clamp-2">
                {{ viz.description || 'Tidak ada deskripsi' }}
              </p>
              <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-gray-500">
                  {{ viz.word_cloud_data.length }} kata
                </span>
                <span class="text-purple-600 group-hover:translate-x-1 transition-transform">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                </span>
              </div>
            </div>
          </Link>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="mapVisualizations.length === 0 && wordcloudVisualizations.length === 0" class="text-center py-16">
        <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <h3 class="text-xl font-medium text-gray-900 mb-2">Belum Ada Visualisasi</h3>
        <p class="text-gray-600 mb-6">Saat ini belum ada data visualisasi yang tersedia</p>
        <Link href="/admin/map" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
          Buat Visualisasi
          <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </Link>
      </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white mt-20">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="text-center">
          <p class="text-gray-400 text-sm">© 2025 Data Diseminasi. Platform Visualisasi Data Survei.</p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  mapVisualizations: Array,
  wordcloudVisualizations: Array,
});

const totalVisualizations = computed(() => {
  return props.mapVisualizations.length + props.wordcloudVisualizations.length;
});
</script>