<template>
  <div class="min-h-screen bg-gradient-to-b from-slate-50 to-slate-100">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex justify-between items-center">
          <h1 class="text-2xl font-bold text-blue-600">Data Diseminasi</h1>
          <Link href="/diseminasi" class="text-slate-600 hover:text-blue-600">← Kembali</Link>
        </div>
      </div>
    </nav>

    <!-- Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Header -->
      <div class="mb-8">
        <h2 class="text-3xl font-bold text-slate-900 mb-2">{{ visualization.name }}</h2>
        <p class="text-slate-600">{{ visualization.description }}</p>
      </div>

      <!-- WordCloud Container -->
      <div class="bg-white rounded-lg shadow p-6 mb-8">
        <div class="w-full h-[500px] relative">
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
      </div>

      <!-- Word Frequency Table -->
      <div v-if="chartData.length > 0" class="bg-white rounded-lg shadow p-6">
        <h3 class="font-bold text-slate-900 mb-4">📊 Frekuensi Kata</h3>
        
        <!-- Interpretasi -->
        <div class="mb-6 p-4 bg-purple-50 border border-purple-200 rounded-lg">
          <h4 class="font-semibold text-purple-900 mb-2">Interpretasi</h4>
          <p class="text-sm text-purple-800">{{ interpretation }}</p>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="bg-slate-100 border-b border-slate-200">
              <tr>
                <th class="px-4 py-2 text-left">Peringkat</th>
                <th class="px-4 py-2 text-left">Kata</th>
                <th class="px-4 py-2 text-right">Frekuensi</th>
                <th class="px-4 py-2 text-right">Persentase</th>
                <th class="px-4 py-2 text-center">Visual</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
              <tr v-for="(item, index) in sortedData" :key="index" class="hover:bg-slate-50">
                <td class="px-4 py-2">{{ index + 1 }}</td>
                <td class="px-4 py-2 font-semibold">{{ item.name }}</td>
                <td class="px-4 py-2 text-right font-mono">{{ item.value }}</td>
                <td class="px-4 py-2 text-right font-mono">{{ getPercentage(item.value) }}%</td>
                <td class="px-4 py-2">
                  <div class="w-full bg-slate-200 rounded-full h-2">
                    <div 
                      class="h-2 rounded-full transition-all"
                      :class="getBarColor(index)"
                      :style="{ width: getPercentage(item.value) + '%' }"
                    ></div>
                  </div>
                </td>
              </tr>
            </tbody>
            <tfoot class="bg-slate-50 border-t-2 border-slate-300">
              <tr>
                <td colspan="2" class="px-4 py-2 font-bold">Total</td>
                <td class="px-4 py-2 text-right font-bold">{{ totalFrequency }}</td>
                <td class="px-4 py-2 text-right font-bold">100%</td>
                <td></td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>

import { computed, provide } from 'vue';
import { Link } from '@inertiajs/vue3';
import { use } from 'echarts/core';
import { CanvasRenderer } from 'echarts/renderers';
import { TooltipComponent } from 'echarts/components'; // <-- 1. TAMBAHKAN BARIS INI
import 'echarts-wordcloud';
import VChart, { THEME_KEY } from 'vue-echarts';

use([CanvasRenderer, TooltipComponent]); // <-- 2. TAMBAHKAN TooltipComponent DI SINI
provide(THEME_KEY, 'light');

const props = defineProps({
  visualization: Object,
});

const chartData = computed(() => {
  if (!props.visualization?.word_cloud_data) {
    return [];
  }
  
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

const sortedData = computed(() => {
  return [...chartData.value].sort((a, b) => b.value - a.value);
});

const totalFrequency = computed(() => {
  return chartData.value.reduce((sum, item) => sum + item.value, 0);
});

const interpretation = computed(() => {
  if (chartData.value.length === 0) return '';
  
  const top3 = sortedData.value.slice(0, 3);
  const topWords = top3.map(item => `"${item.name}" (${item.value}x)`).join(', ');
  
  return `Dari total ${totalFrequency.value} kemunculan kata, tiga kata yang paling sering muncul adalah ${topWords}. Kata "${sortedData.value[0].name}" merupakan yang paling dominan dengan ${getPercentage(sortedData.value[0].value)}% dari total frekuensi.`;
});

const chartOption = computed(() => {
  return {
    tooltip: {
      trigger: 'item',
      formatter: (params) => {
        const percentage = getPercentage(params.value);
        return `<div class="p-2">
          <strong>${params.name}</strong><br/>
          Frekuensi: ${params.value}<br/>
          Persentase: ${percentage}%
        </div>`;
      },
      backgroundColor: 'rgba(255, 255, 255, 0.95)',
      borderColor: '#ccc',
      borderWidth: 1,
      textStyle: {
        color: '#333'
      }
    },
    series: [
      {
        type: 'wordCloud',
        shape: 'circle',
        left: 'center',
        top: 'center',
        width: '100%',
        height: '100%',
        sizeRange: [14, 60],
        rotationRange: [-45, 45],
        rotationStep: 45,
        gridSize: 8,
        drawOutOfBound: false,
        layoutAnimation: true,
        textStyle: {
          fontFamily: 'sans-serif',
          fontWeight: 'bold',
          color: function () {
            const colors = [
              '#5470c6', '#91cc75', '#fac858', '#ee6666', '#73c0de',
              '#3ba272', '#fc8452', '#9a60b4', '#ea7ccc'
            ];
            return colors[Math.floor(Math.random() * colors.length)];
          },
        },
        emphasis: {
          focus: 'self',
          textStyle: {
            textShadowBlur: 10,
            textShadowColor: '#333',
          },
        },
        data: chartData.value,
      },
    ],
  };
});

const getPercentage = (value) => {
  if (totalFrequency.value === 0) return 0;
  return ((value / totalFrequency.value) * 100).toFixed(1);
};

const getBarColor = (index) => {
  const colors = [
    'bg-blue-600',
    'bg-green-600',
    'bg-yellow-600',
    'bg-red-600',
    'bg-purple-600',
    'bg-pink-600',
    'bg-indigo-600',
  ];
  return colors[index % colors.length];
};
</script>