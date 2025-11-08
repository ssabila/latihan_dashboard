<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 to-slate-100">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex justify-between items-center">
          <h1 class="text-2xl font-bold text-blue-600">Data Diseminasi Survei</h1>
          <div class="flex gap-4">
            <Link href="/diseminasi" class="px-4 py-2 text-slate-700 hover:text-blue-600">
              Lihat Diseminasi
            </Link>
            <Link href="/admin/map" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
              Admin Panel
            </Link>
          </div>
        </div>
      </div>
    </nav>

    <!-- Hero Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
      <div class="text-center">
        <h2 class="text-4xl font-bold text-slate-900 mb-4">
          Platform Diseminasi Data Survei
        </h2>
        <p class="text-xl text-slate-600 mb-8">
          Visualisasi data survei dengan peta interaktif dan wordcloud yang menarik
        </p>
        <div class="flex gap-4 justify-center">
          <Link href="/diseminasi" class="px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold">
            Mulai Eksplorasi
          </Link>
          <Link href="/admin/map" class="px-8 py-3 bg-slate-200 text-slate-900 rounded-lg hover:bg-slate-300 font-semibold">
            Admin Panel
          </Link>
        </div>
      </div>
    </div>

    <!-- Features -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
      <div class="grid md:grid-cols-3 gap-8">
        <div class="bg-white p-6 rounded-lg shadow">
          <h3 class="text-lg font-bold text-slate-900 mb-2">📊 Peta Interaktif</h3>
          <p class="text-slate-600">Visualisasi data dengan peta choropleth yang dapat dipilih berdasarkan variabel</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
          <h3 class="text-lg font-bold text-slate-900 mb-2">☁️ WordCloud</h3>
          <p class="text-slate-600">Tampilkan distribusi kata dengan ukuran yang proporsional</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
          <h3 class="text-lg font-bold text-slate-900 mb-2">📁 Upload Data</h3>
          <p class="text-slate-600">Admin dapat dengan mudah upload data Excel dan generate visualisasi</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
</script>