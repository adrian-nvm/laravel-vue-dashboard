<template>
  <div>
    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">RTOL Out - Data Report</h1>

    <div class="row">
      <div class="col-lg-6">
        <RtolLineDataTable />
      </div>
      <div class="col-lg-6">
        <RtolHanaDataTable />
      </div>
    </div>
  </div>
</template>

<script>
import RtolLineDataTable from '@/components/table/RtolLineDataTable.vue';
import RtolHanaDataTable from '@/components/table/RtolHanaDataTable.vue';

export default {
  name: 'RtolDataReport',
  components: {
    RtolLineDataTable,
    RtolHanaDataTable,
  },
}
</script>
