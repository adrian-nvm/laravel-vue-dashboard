<template>
  <div>
    <h1 class="h3 mb-2 text-gray-800">Charts</h1>
    <p class="mb-4">
      Select a chart from the dropdown below to view the corresponding data visualization.
    </p>

    <!-- Chart Selector Dropdown -->
    <div class="mb-4">
      <select class="form-control" v-model="selectedChart" :disabled="loading">
        <option value="QrisLineChart">QRIS Line Chart</option>
        <option value="QrisHanaChart">QRIS MyHana Chart</option>
        <option value="BillerLineChart">Biller Line Chart</option>
        <option value="BillerHanaChart">Biller MyHana Chart</option>
        <option value="DebitLineChart">Debit Line Chart</option>
        <option value="DebitHanaChart">Debit MyHana Chart</option>
        <option value="BifastLineChart">Bifast Line Chart</option>
        <option value="BifastHanaChart">Bifast MyHana Chart</option>
        <option value="RtolLineChart">RTOL Out - Line Chart</option>
        <option value="RtolHanaChart">RTOL Out - MyHana Chart</option>
      </select>
    </div>

    <!-- Month Range Filter -->
    <div class="row mb-4">
      <div class="col-md-5">
        <input type="month" class="form-control" v-model="startMonth" :disabled="loading">
      </div>
      <div class="col-md-5">
        <input type="month" class="form-control" v-model="endMonth" :disabled="loading">
      </div>
      <div class="col-md-2">
        <button class="btn btn-primary btn-block" @click="fetchChartData" :disabled="loading">Filter</button>
      </div>
    </div>

    <!-- Content Row -->
    <div class="row">
      <div class="col-xl-12 col-lg-12">
        <!-- Dynamic Chart Component -->
        <div class="card shadow mb-4">
          <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ selectedChartTitle }}</h6>
          </div>
          <div class="card-body">
            <div v-if="loading" class="text-center">
              <div class="spinner-border" role="status">
                <span class="sr-only">Loading...</span>
              </div>
            </div>
            <component :is="selectedChart" :key="selectedChart" :data="chartData" v-if="!loading && chartData.length" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import QrisLineChart from '../../components/charts/QrisLineChart.vue';
import QrisHanaChart from '../../components/charts/QrisHanaChart.vue';
import BillerLineChart from '../../components/charts/BillerLineChart.vue';
import BillerHanaChart from '../../components/charts/BillerHanaChart.vue';
import DebitLineChart from '../../components/charts/DebitLineChart.vue';
import DebitHanaChart from '../../components/charts/DebitHanaChart.vue';
import BifastLineChart from '../../components/charts/BifastLineChart.vue';
import BifastHanaChart from '../../components/charts/BifastHanaChart.vue';
import RtolLineChart from '../../components/charts/RtolLineChart.vue';
import RtolHanaChart from '../../components/charts/RtolHanaChart.vue';

export default {
  name: "Charts",
  components: {
    QrisLineChart,
    QrisHanaChart,
    BillerLineChart,
    BillerHanaChart,
    DebitLineChart,
    DebitHanaChart,
    BifastLineChart,
    BifastHanaChart,
    RtolLineChart,
    RtolHanaChart,
  },
  data() {
    return {
      selectedChart: 'QrisLineChart',
      chartData: [],
      loading: false,
      startMonth: '',
      endMonth: '',
      chartTitles: {
        QrisLineChart: 'QRIS Line Chart',
        QrisHanaChart: 'QRIS MyHana Chart',
        BillerLineChart: 'Biller Line Chart',
        BillerHanaChart: 'Biller MyHana Chart',
        DebitLineChart: 'Debit Line Chart',
        DebitHanaChart: 'Debit MyHana Chart',
        BifastLineChart: 'Bifast Line Chart',
        BifastHanaChart: 'Bifast MyHana Chart',
        RtolLineChart: 'RTOL Out - Line Chart',
        RtolHanaChart: 'RTOL Out - MyHana Chart',
      }
    }
  },
  computed: {
    selectedChartTitle() {
      return this.chartTitles[this.selectedChart];
    }
  },
  watch: {
    selectedChart: 'fetchChartData'
  },
  mounted() {
    this.setDefaultMonths();
    this.fetchChartData();
  },
  methods: {
    setDefaultMonths() {
      const endDate = new Date();
      const endYear = endDate.getFullYear();
      const endMonth = (endDate.getMonth() + 1).toString().padStart(2, '0');
      this.endMonth = `${endYear}-${endMonth}`;

      const startDate = new Date();
      startDate.setMonth(startDate.getMonth() - 11);
      const startYear = startDate.getFullYear();
      const startMonth = (startDate.getMonth() + 1).toString().padStart(2, '0');
      this.startMonth = `${startYear}-${startMonth}`;
    },
    async fetchChartData() {
      this.loading = true;
      this.chartData = []; // Clear data to unmount the old chart
      const endpoint = this.getEndpointForChart(this.selectedChart);
      if (endpoint) {
        try {
          // Wait for the DOM to update before fetching new data
          await this.$nextTick();
          const response = await axios.get(endpoint, {
            params: {
              start_month: this.startMonth,
              end_month: this.endMonth,
            }
          });
          this.chartData = response.data;
        } catch (error) {
          console.error('Error fetching chart data:', error);
        } finally {
          this.loading = false;
        }
      } else {
        this.loading = false;
      }
    },
    getEndpointForChart(chartName) {
      const endpoints = {
        QrisLineChart: '/chart/qris-line',
        QrisHanaChart: '/chart/qris-hana',
        BillerLineChart: '/chart/biller-line',
        BillerHanaChart: '/chart/biller-hana',
        DebitLineChart: '/chart/debit-line',
        DebitHanaChart: '/chart/debit-hana',
        BifastLineChart: '/chart/bifast-line',
        BifastHanaChart: '/chart/bifast-hana',
        RtolLineChart: '/chart/rtol-line',
        RtolHanaChart: '/chart/rtol-hana',
      };
      return endpoints[chartName];
    }
  }
};
</script>
