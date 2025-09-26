<template>
  <div>
    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Debit Data Report</h1>

    <div class="row">
      <div class="col-lg-6">
        <DebitLineDataTable />
      </div>
      <div class="col-lg-6">
        <DebitHanaDataTable />
      </div>
    </div>
  </div>
</template>

<script>
import DebitLineDataTable from '@/components/table/DebitLineDataTable.vue';
import DebitHanaDataTable from '@/components/table/DebitHanaDataTable.vue';

export default {
  name: 'DebitDataReport',
  components: {
    DebitLineDataTable,
    DebitHanaDataTable,
  },
}
</script>
