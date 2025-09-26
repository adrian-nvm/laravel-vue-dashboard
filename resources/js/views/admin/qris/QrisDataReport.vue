<template>
  <div>
    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">QRIS Data Report</h1>

    <div class="row">
      <div class="col-lg-6">
        <QrisLineDataTable />
      </div>
      <div class="col-lg-6">
        <QrisHanaDataTable />
      </div>
    </div>
  </div>
</template>

<script>
import QrisLineDataTable from '@/components/table/QrisLineDataTable.vue';
import QrisHanaDataTable from '@/components/table/QrisHanaDataTable.vue';

export default {
  name: 'QrisDataReport',
  components: {
    QrisLineDataTable,
    QrisHanaDataTable,
  },
}
</script>
