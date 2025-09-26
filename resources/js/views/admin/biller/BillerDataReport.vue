<template>
  <div>
    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Biller Data Report</h1>

    <div class="row">
      <div class="col-lg-6">
        <BillerLineDataTable />
      </div>
      <div class="col-lg-6">
        <BillerHanaDataTable />
      </div>
    </div>
  </div>
</template>

<script>
import BillerLineDataTable from '@/components/table/BillerLineDataTable.vue';
import BillerHanaDataTable from '@/components/table/BillerHanaDataTable.vue';

export default {
  name: 'BillerDataReport',
  components: {
    BillerLineDataTable,
    BillerHanaDataTable,
  },
}
</script>
