<template>
  <div class="min-h-screen bg-gradient-to-b from-slate-50 to-slate-100 p-8">
    <div class="max-w-7xl mx-auto">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-4xl font-bold text-slate-900 mb-2">Manajemen Visualisasi Peta</h1>
        <p class="text-slate-600">Kelola semua visualisasi peta survei Anda</p>
      </div>

      <!-- Navigation -->
      <div class="mb-6 flex gap-4">
        <Link href="/admin/map" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
          Peta
        </Link>
        <Link href="/admin/wordcloud" class="px-4 py-2 bg-slate-200 text-slate-900 rounded hover:bg-slate-300">
          WordCloud
        </Link>
        <Link href="/diseminasi" class="px-4 py-2 bg-slate-200 text-slate-900 rounded hover:bg-slate-300">
          Lihat Diseminasi
        </Link>
      </div>

      <!-- Alert -->
      <div v-if="$page.props.flash?.success" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
        <p class="text-green-800">{{ $page.props.flash.success }}</p>
      </div>
      <div v-if="$page.props.flash?.error" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
        <p class="text-red-800">{{ $page.props.flash.error }}</p>
      </div>

      <!-- Action Button -->
      <div class="mb-8">
        <Link 
          href="/admin/map/create"
          class="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
        >
          + Buat Visualisasi Peta Baru
        </Link>
      </div>

      <!-- Table -->
      <div class="bg-white rounded-lg shadow overflow-hidden">
        <div v-if="visualizations.length === 0" class="p-8 text-center text-slate-500">
          <p>Belum ada visualisasi peta.</p>
          <Link href="/admin/map/create" class="text-blue-600 hover:underline">Buat yang baru</Link>
        </div>
        <table v-else class="w-full">
          <thead class="bg-slate-100 border-b border-slate-200">
            <tr>
              <th class="px-6 py-3 text-left text-sm font-semibold text-slate-900">Nama</th>
              <th class="px-6 py-3 text-left text-sm font-semibold text-slate-900">Deskripsi</th>
              <th class="px-6 py-3 text-left text-sm font-semibold text-slate-900">Data Points</th>
              <th class="px-6 py-3 text-left text-sm font-semibold text-slate-900">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-200">
            <tr v-for="viz in visualizations" :key="viz.id" class="hover:bg-slate-50">
              <td class="px-6 py-4 text-sm font-medium text-slate-900">{{ viz.name }}</td>
              <td class="px-6 py-4 text-sm text-slate-600">{{ viz.description || '-' }}</td>
              <td class="px-6 py-4 text-sm text-slate-600">{{ viz.map_data.length }}</td>
              <td class="px-6 py-4 text-sm space-x-2">
                <Link 
                  :href="`/admin/map/${viz.id}/edit`"
                  class="text-blue-600 hover:text-blue-800"
                >
                  Edit
                </Link>
                <button 
                  @click="deleteViz(viz.id)"
                  class="text-red-600 hover:text-red-800"
                >
                  Hapus
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';

defineProps({
  visualizations: Array,
});

const deleteViz = (id) => {
  if (confirm('Apakah Anda yakin ingin menghapus?')) {
    router.delete(`/admin/map/${id}`);
  }
};
</script>