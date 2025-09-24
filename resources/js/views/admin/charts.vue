<template>
  <div>
    <h1 class="h3 mb-2 text-gray-800">Charts</h1>
    <p class="mb-4">
      Select a chart from the dropdown below to view the corresponding data visualization.
    </p>

    <!-- Chart Selector Dropdown -->
    <div class="mb-4">
      <select class="form-control" v-model="selectedChart">
        <option value="QrisLineChart">QRIS Line Chart</option>
        <option value="QrisHanaChart">QRIS MyHana Chart</option>
        <option value="BillerLineChart">Biller Line Chart</option>
        <option value="BillerHanaChart">Biller MyHana Chart</option>
        <option value="DebitLineChart">Debit Line Chart</option>
        <option value="DebitHanaChart">Debit MyHana Chart</option>
      </select>
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
            <component :is="selectedChart" :data="chartData" v-if="chartData.length" />
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

export default {
  name: "Charts",
  components: {
    QrisLineChart,
    QrisHanaChart,
    BillerLineChart,
    BillerHanaChart,
    DebitLineChart,
    DebitHanaChart,
  },
  data() {
    return {
      selectedChart: 'QrisLineChart',
      chartData: [],
      chartTitles: {
        QrisLineChart: 'QRIS Line Chart',
        QrisHanaChart: 'QRIS MyHana Chart',
        BillerLineChart: 'Biller Line Chart',
        BillerHanaChart: 'Biller MyHana Chart',
        DebitLineChart: 'Debit Line Chart',
        DebitHanaChart: 'Debit MyHana Chart',
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
    this.fetchChartData();
  },
  methods: {
    async fetchChartData() {
      const endpoint = this.getEndpointForChart(this.selectedChart);
      if (endpoint) {
        try {
          const response = await axios.get(endpoint);
          this.chartData = response.data;
        } catch (error) {
          console.error('Error fetching chart data:', error);
        }
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
      };
      return endpoints[chartName];
    }
  }
};
</script>
