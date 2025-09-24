<template>
  <div>
    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Combined Chart</h1>

    <div class="row">
      <div class="col-lg-12">
        <div class="card shadow mb-4">
          <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Dynamic Combined Chart</h6>
          </div>
          <div class="card-body">
            <combined-chart></combined-chart>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import CombinedChart from '../../components/charts/CombinedChart.vue';

export default {
  name: 'CombinedChartPage',
  components: {
    CombinedChart,
  },
};
</script>
