<template>
  <div>
    <div class="row mb-3">
      <div class="col-md-3">
        <multiselect
          v-model="selectedCharts"
          :options="chartOptions"
          :multiple="true"
          group-values="options"
          group-label="group"
          :group-select="true"
          placeholder="Select charts"
          @select="updateChart"
          @remove="updateChart"
          :disabled="isLoading"
        ></multiselect>
      </div>
      <div class="col-md-2">
        <input type="month" class="form-control" v-model="startMonth" @change="updateChart" :disabled="isLoading">
      </div>
      <div class="col-md-2">
        <input type="month" class="form-control" v-model="endMonth" @change="updateChart" :disabled="isLoading">
      </div>
      <div class="col-md-2">
        <button class="btn btn-primary w-100" @click="updateChart" :disabled="isLoading">Filter</button>
      </div>
      <div class="col-md-2">
        <button class="btn btn-success w-100" @click="downloadChart">Download</button>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div style="height: 700px; position: relative;">
          <loading-indicator v-if="isLoading"></loading-indicator>
          <canvas id="combinedChart" :style="{ visibility: isLoading ? 'hidden' : 'visible' }"></canvas>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Chart from 'chart.js/auto';
import trendline from 'chartjs-plugin-trendline';
import ChartDataLabels from 'chartjs-plugin-datalabels';
import 'chartjs-adapter-date-fns';
import Multiselect from 'vue-multiselect';
import axios from 'axios';
import LoadingIndicator from '../LoadingIndicator.vue';

export default {
  components: {
    Multiselect,
    LoadingIndicator,
  },
  data() {
    return {
      isLoading: false,
      chart: null,
      selectedCharts: [],
      startMonth: '',
      endMonth: '',
      chartOptions: [
        {
          group: '----- LineBank - Volume -----',
          options: [
            'Qris_LineBank_Volume',
            'Debit_LineBank_Volume',
            'Biller_LineBank_Volume',
            'Bifast_LineBank_Volume',
            'Rtol_LineBank_Volume',
          ]
        },
        {
          group: 'LineBank - Frequency',
          options: [
            'Qris_LineBank_Frequency',
            'Debit_LineBank_Frequency',
            'Biller_LineBank_Frequency',
            'Bifast_LineBank_Frequency',
            'Rtol_LineBank_Frequency',
          ]
        },
        {
          group: '----- HanaBank - Volume -----',
          options: [
            'Qris_HanaBank_Volume',
            'Debit_HanaBank_Volume',
            'Biller_HanaBank_Volume',
            'Bifast_HanaBank_Volume',
            'Rtol_HanaBank_Volume',
          ]
        },
        {
          group: 'HanaBank - Frequency',
          options: [
            'Qris_HanaBank_Frequency',
            'Debit_HanaBank_Frequency',
            'Biller_HanaBank_Frequency',
            'Bifast_HanaBank_Frequency',
            'Rtol_HanaBank_Frequency',
          ]
        }
      ],
      chartData: {
        labels: [],
        datasets: [],
      },
    };
  },
  mounted() {
    Chart.register(trendline, ChartDataLabels);
    this.createChart();
  },
  methods: {
    createChart() {
      if (this.chart) {
        this.chart.destroy();
      }
      const hasFrequency = this.selectedCharts.some(chart => chart.toLowerCase().includes('_frequency'));
      const hasVolume = this.selectedCharts.some(chart => chart.toLowerCase().includes('_volume'));
      const ctx = document.getElementById('combinedChart').getContext('2d');
      this.chart = new Chart(ctx, {
        data: this.chartData,
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            datalabels: {
              formatter: (value) => {
                const yValue = value.y;
                if (yValue === null || yValue === undefined) return '';
                if (yValue >= 1000000000) {
                  return (yValue / 1000000000).toFixed(1) + 'B';
                }
                if (yValue >= 1000000) {
                  return (yValue / 1000000).toFixed(1) + 'M';
                }
                if (yValue >= 1000) {
                  return (yValue / 1000).toFixed(1) + 'K';
                }
                return yValue;
              }
            },
          },
          scales: {
            x: {
              type: 'time',
              time: {
                unit: 'month',
                displayFormats: {
                  month: 'MMM yyyy'
                }
              },
              ticks: {
                source: 'data'
              }
            },
            y: {
              type: 'linear',
              display: hasFrequency,
              position: 'left',
              title: {
                display: true,
                text: 'Frequency / Unique CIF'
              },
              ticks: {
                callback: function(value) {
                  if (value >= 1000) {
                    return value / 1000 + 'K';
                  }
                  return value;
                }
              }
            },
            y1: {
              type: 'linear',
              display: hasVolume,
              position: 'right',
              grid: {
                drawOnChartArea: false,
              },
              title: {
                display: true,
                text: 'Transaction Amount'
              },
              ticks: {
                callback: function(value) {
                  if (value >= 1000000000) {
                    return value / 1000000000 + 'B';
                  }
                  if (value >= 1000000) {
                    return value / 1000000 + 'M';
                  }
                  if (value >= 1000) {
                    return value / 1000 + 'K';
                  }
                  return value;
                }
              }
            },
          },
        },
      });
    },
    downloadChart() {
      if (this.chart) {
        const link = document.createElement('a');
        link.href = this.chart.toBase64Image();
        link.download = 'combined-chart.png';
        link.click();
      }
    },
    async updateChart() {
      this.isLoading = true;
      const charts = this.selectedCharts;

      if (charts.length === 0) {
        this.chartData.labels = [];
        this.chartData.datasets = [];
        this.createChart();
        this.isLoading = false;
        return;
      }

      try {
        const response = await axios.post('/combined-chart', {
          charts: charts,
          start_month: this.startMonth,
          end_month: this.endMonth,
        });
        const data = response.data;

        const datasets = data.datasets.map(dataset => {
          return {
            ...dataset,
            data: data.labels.map((label, index) => {
              return {
                x: new Date(label),
                y: dataset.data[index]
              };
            })
          };
        });

        this.chartData.labels = data.labels.map(label => new Date(label));
        this.chartData.datasets = datasets;
        this.createChart();
      } catch (error) {
        console.error('Error fetching combined chart data:', error);
      } finally {
        this.isLoading = false;
      }
    },
  },
};
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
