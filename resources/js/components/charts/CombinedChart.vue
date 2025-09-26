<template>
  <div>
    <div class="row mb-3">
      <div class="col-md-6">
        <div class="d-flex align-items-center mb-2">
          <h5>LineBank</h5>
           &nbsp; &nbsp;<img src="/images/lineBankLogo.png" alt="Line Bank Logo" style="height: 30px; margin-right: 10px;">
        </div>
        <div v-for="group in lineBankChartOptions" :key="group.group" class="mb-2">
          <h6>{{ group.group.replace(/-----|LineBank/g, '').trim() }}</h6>
          <div class="d-flex flex-wrap">
            <div class="form-check form-check-inline mr-3" v-for="option in group.options" :key="option">
              <input class="form-check-input" type="checkbox" :id="option" :value="option" v-model="selectedCharts" @change="debouncedUpdateChart" :disabled="isLoading">
              <label class="form-check-label" :for="option">{{ option.replace(/_/g, ' ').replace('LineBank', '') }}</label>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="d-flex align-items-center mb-2">
          <h5>HanaBank</h5>
           &nbsp; &nbsp;<img src="/images/hanaBankLogo.png" alt="Hana Bank Logo" style="height: 30px; margin-right: 10px;">
        </div>
        <div v-for="group in hanaBankChartOptions" :key="group.group" class="mb-2">
          <h6>{{ group.group.replace(/-----|HanaBank/g, '').trim() }}</h6>
          <div class="d-flex flex-wrap">
            <div class="form-check form-check-inline mr-3" v-for="option in group.options" :key="option">
              <input class="form-check-input" type="checkbox" :id="option" :value="option" v-model="selectedCharts" @change="debouncedUpdateChart" :disabled="isLoading">
              <label class="form-check-label" :for="option">{{ option.replace(/_/g, ' ').replace('HanaBank', '') }}</label>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row mb-3">
      <div class="col-md-2">
        <input type="month" class="form-control" v-model="startMonth" @change="debouncedUpdateChart" :disabled="isLoading">
      </div>
      <div class="col-md-2">
        <input type="month" class="form-control" v-model="endMonth" @change="debouncedUpdateChart" :disabled="isLoading">
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
import axios from 'axios';
import LoadingIndicator from '../LoadingIndicator.vue';

export default {
  components: {
    LoadingIndicator,
  },
  data() {
    return {
      isLoading: false,
      chart: null,
      selectedCharts: [],
      startMonth: '',
      endMonth: '',
      debounceTimeout: null,
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
        },
        {
          group: 'LineBank - Unique CIF',
          options: [
            'Qris_Unique_CIF',
            'Debit_Unique_CIF',
            'Biller_Unique_CIF',
            'Bifast_Unique_CIF',
            'Rtol_Unique_CIF',
          ]
        },
        {
          group: 'HanaBank - Unique CIF',
          options: [
            'Qris_HanaBank_Unique_CIF',
            'Debit_HanaBank_Unique_CIF',
            'Biller_HanaBank_Unique_CIF',
            'Bifast_HanaBank_Unique_CIF',
            'Rtol_HanaBank_Unique_CIF',
          ]
        }
      ],
      chartData: {
        labels: [],
        datasets: [],
      },
    };
  },
  computed: {
    lineBankChartOptions() {
      return this.chartOptions.filter(group => group.group.includes('LineBank'));
    },
    hanaBankChartOptions() {
      return this.chartOptions.filter(group => group.group.includes('HanaBank'));
    },
    uniqueCifChartOptions() {
      return this.chartOptions.filter(group => group.group.includes('Unique CIF'));
    }
  },
  mounted() {
    Chart.register(trendline, ChartDataLabels);
    this.createChart();
  },
  methods: {
    debouncedUpdateChart() {
      clearTimeout(this.debounceTimeout);
      this.debounceTimeout = setTimeout(() => {
        this.updateChart();
      }, 500);
    },
    createChart() {
      if (this.chart) {
        this.chart.destroy();
      }
      const hasFrequency = this.selectedCharts.some(chart => chart.toLowerCase().includes('_frequency') || chart.toLowerCase().includes('_cif'));
      const hasVolume = this.selectedCharts.some(chart => chart.toLowerCase().includes('_volume'));
      const barCharts = this.selectedCharts.filter(chart => chart.toLowerCase().includes('volume'));
      const numBarCharts = barCharts.length;
      const barThickness = numBarCharts > 0 ? Math.max(10, 60 / numBarCharts) : 10;

      this.chartData.datasets.forEach(dataset => {
        if (dataset.type === 'bar') {
          dataset.barThickness = barThickness;
        }
      });

      const ctx = document.getElementById('combinedChart').getContext('2d');
      this.chart = new Chart(ctx, {
        data: this.chartData,
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            datalabels: {
              display: true,
              formatter: (value, context) => {
                const yValue = context.dataset.data[context.dataIndex].y;
                if (yValue === null || yValue === undefined) return '';
                return this.formatLargeNumber(yValue);
              },
              align: (context) => {
                return context.dataset.type === 'bar' ? 'center' : 'top';
              },
              anchor: (context) => {
                return context.dataset.type === 'bar' ? 'center' : 'end';
              },
              backgroundColor: (context) => {
                return context.dataset.backgroundColor;
              },
              borderRadius: 4,
              color: 'black',
              font: {
                weight: 'bold'
              },
              padding: {
                top: 4,
                bottom: 4,
                left: 8,
                right: 8
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
                callback: (value) => this.formatLargeNumber(value)
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
                callback: (value) => this.formatLargeNumber(value)
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
    formatLargeNumber(num) {
      const absNum = Math.abs(num);
      if (absNum >= 1e9) {
        return (num / 1e9).toFixed(1) + 'B';
      } else if (absNum >= 1e6) {
        return (num / 1e6).toFixed(1) + 'M';
      } else if (absNum >= 1e3) {
        return (num / 1e3).toFixed(1) + 'K';
      }
      return num.toString();
    },
  },
};
</script>
