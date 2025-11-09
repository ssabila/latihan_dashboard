<script setup>
import { computed, provide } from 'vue';
import { Link } from '@inertiajs/vue3';
import { use } from 'echarts/core';
import { CanvasRenderer } from 'echarts/renderers';
import 'echarts-wordcloud';
import VChart, { THEME_KEY } from 'vue-echarts';

// 1. Daftarkan komponen ECharts yang kita butuhkan
use([CanvasRenderer]);

// 2. Sediakan tema (opsional, tapi bagus)
provide(THEME_KEY, 'light');

const props = defineProps({
  visualization: Object,
});

// 3. Siapkan data untuk ECharts
// Format ECharts: [ { name: 'kata', value: 10 }, ... ]
const chartData = computed(() => {
  if (!props.visualization?.word_cloud_data) {
    return [];
  }
  
  // Gunakan filter yang sudah kita buat untuk membersihkan data 'rusak'
  return props.visualization.word_cloud_data
    .filter(item => {
      const hasWord = item.word && item.word.trim() !== '';
      const hasValue = typeof item.value === 'number' && item.value > 0;
      return hasWord && hasValue;
    })
    .map(item => ({
      name: item.word,
      value: item.value,
    }));
});

// 4. Buat Opsi Konfigurasi untuk ECharts
const chartOption = computed(() => {
  return {
    tooltip: {
      trigger: 'item',
      formatter: '{b}: {c}', // Tampilkan "Kata: Nilai" saat di-hover
    },
    series: [
      {
        type: 'wordCloud',
        shape: 'circle', // Bentuk bisa 'circle', 'cardioid', 'diamond', 'triangle'
        left: 'center',
        top: 'center',
        width: '100%',
        height: '100%',
        sizeRange: [12, 60], // Ukuran font min dan max
        rotationRange: [-45, 45], // Biarkan kata sedikit berotasi
        textStyle: {
          fontFamily: 'sans-serif',
          fontWeight: 'bold',
          color: function () {
            // Fungsi untuk warna acak
            return (
              'rgb(' +
              [
                Math.round(Math.random() * 160),
                Math.round(Math.random() * 160),
                Math.round(Math.random() * 160),
              ].join(',') +
              ')'
            );
          },
        },
        emphasis: {
          focus: 'self',
          textStyle: {
            textShadowBlur: 10,
            textShadowColor: '#333',
          },
        },
        // Data kita
        data: chartData.value,
      },
    ],
  };
});
</script>

<template>
  <div class="min-h-screen bg-slate-100 p-8">
    <div class="max-w-4xl mx-auto">
      
      <h1 class="text-3xl font-bold text-slate-900 mb-2">{{ visualization.name }}</h1>
      <p class="text-slate-600 mb-8">{{ visualization.description }}</p>

      <div class="w-full h-[500px] bg-white rounded-lg shadow-lg p-4">
        <v-chart
          v-if="chartData.length > 0"
          :option="chartOption"
          autoresize
          style="width: 100%; height: 100%;"
        />
        <div v-else class="flex items-center justify-center h-full text-slate-500">
          Data kata untuk visualisasi ini tidak ditemukan atau kosong.
        </div>
      </div>
      
      <Link href="/diseminasi" class="mt-8 inline-block text-blue-600 hover:underline">
        &larr; Kembali ke Indeks
      </Link>
    </div>
  </div>
</template>