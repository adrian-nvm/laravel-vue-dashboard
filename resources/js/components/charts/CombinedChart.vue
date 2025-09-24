<template>
  <div>
    <div class="row">
      <div class="col-md-12">
        <multiselect
          v-model="selectedCharts"
          :options="chartOptions"
          :multiple="true"
          placeholder="Select charts"
          @select="updateChart"
          @remove="updateChart"
        ></multiselect>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div style="height: 700px">
          <canvas id="combinedChart"></canvas>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Chart from 'chart.js/auto';
import Multiselect from 'vue-multiselect';
import axios from 'axios';

export default {
  components: {
    Multiselect,
  },
  data() {
    return {
      chart: null,
      selectedCharts: [],
      chartOptions: [
        'qrisLine_volume',
        'debitLine_volume',
        'billerLine_volume',
        'qrisLine_frequency',
        'debitLine_frequency',
        'billerLine_frequency',
      ],
      chartData: {
        labels: [],
        datasets: [],
      },
    };
  },
  mounted() {
    this.createChart();
  },
  methods: {
    createChart() {
      if (this.chart) {
        this.chart.destroy();
      }
      const ctx = document.getElementById('combinedChart').getContext('2d');
      this.chart = new Chart(ctx, {
        type: 'line',
        data: this.chartData,
        options: {
          responsive: true,
          maintainAspectRatio: false,
        },
      });
    },
    async updateChart() {
      const charts = this.selectedCharts;

      if (charts.length === 0) {
        this.chartData.labels = [];
        this.chartData.datasets = [];
        this.createChart();
        return;
      }

      try {
        const response = await axios.post('/combined-chart', {
          charts: charts,
        });
        const data = response.data;

        this.chartData.labels = data.labels;
        this.chartData.datasets = data.datasets;
        this.createChart();
      } catch (error) {
        console.error('Error fetching combined chart data:', error);
      }
    },
  },
};
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
