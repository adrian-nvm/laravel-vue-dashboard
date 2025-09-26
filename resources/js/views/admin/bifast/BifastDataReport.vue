<template>
  <div>
    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Bifast Data Report</h1>

    <div class="row">
      <div class="col-lg-6">
        <BifastLineDataTable />
      </div>
      <div class="col-lg-6">
        <BifastHanaDataTable />
      </div>
    </div>
  </div>
</template>

<script>
import BifastLineDataTable from '@/components/table/BifastLineDataTable.vue';
import BifastHanaDataTable from '@/components/table/BifastHanaDataTable.vue';

export default {
  name: 'BifastDataReport',
  components: {
    BifastLineDataTable,
    BifastHanaDataTable,
  },
}
</script>
