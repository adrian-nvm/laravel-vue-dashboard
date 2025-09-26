<template>
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <div class="d-flex flex-row align-items-center justify-content-between">
        <div class="d-flex align-items-center">
          <h6 class="m-0 font-weight-bold text-primary">Bifast Hana Bank</h6>
          &nbsp;&nbsp;<img src="/images/hanaBankLogo.png" alt="Hana Bank Logo" style="height: 30px; margin-right: 10px;">
        </div>
        <button class="btn btn-success btn-sm" @click="exportHanaData">Export CSV</button>
      </div>
      <form @submit.prevent="applyHanaFilters" class="form-inline mt-3">
        <div class="form-group mr-3">
          <label for="hana_start_month" class="mr-2">Start Month</label>
          <input type="month" class="form-control form-control-sm" id="hana_start_month" v-model="hana_start_month">
        </div>
        <div class="form-group mr-3">
          <label for="hana_end_month" class="mr-2">End Month</label>
          <input type="month" class="form-control form-control-sm" id="hana_end_month" v-model="hana_end_month">
        </div>
        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
      </form>
    </div>
    <div class="card-body">
      <vue-good-table
        mode="remote"
        :is-loading="hanaIsLoading"
        :columns="hanaColumns"
        :rows="hanaData"
        :total-rows="hanaTotalRecords"
        :pagination-options="{ enabled: true }"
        @page-change="onHanaPageChange"
        @per-page-change="onHanaPerPageChange"
        @column-filter="onHanaFilterChange"
        @sort-change="onHanaSortChange"
      />
    </div>
  </div>
</template>

<script>
import 'vue-good-table-next/dist/vue-good-table-next.css'
import { VueGoodTable } from 'vue-good-table-next';
import axios from 'axios';
import { handleError } from '@/utils/notify';
import { useToast } from "vue-toastification";
import Papa from 'papaparse';

export default {
  name: 'BifastHanaDataTable',
  components: {
    VueGoodTable,
  },
  data() {
    return {
      hana_start_month: '',
      hana_end_month: '',
      toast: useToast(),
      hanaData: [],
      hanaTotalRecords: 0,
      hanaIsLoading: false,
      hanaServerParams: {
        page: 1,
        per_page: 10,
        start_dt: '',
        sort: [],
        start_month: '',
        end_month: '',
      },
      hanaColumns: [
        {
          label: 'Start Date',
          field: 'startDt',
          formatFn: this.formatMonthYear,
          filterOptions: {
            enabled: true,
            placeholder: 'Filter by date...',
          },
        },
        {
          label: 'Transaction Frequency',
          field: 'bifastMyhanaTrxFreq',
          tdClass: 'text-right',
          formatFn: this.formatCurrency,
        },
        {
          label: 'Transaction Amount',
          field: 'bifastMyhanaTrxAmt',
          tdClass: 'text-right',
          formatFn: this.formatCurrency,
        },
        {
          label: 'Unique CIF Quantity',
          field: 'bifastMyhanaUniqueCifQty',
          tdClass: 'text-right',
          formatFn: this.formatCurrency,
        },
      ],
    }
  },
  mounted() {
    this.fetchHanaData();
  },
  methods: {
    formatMonthYear(value) {
      if (!value) return '';
      const date = new Date(value);
      const month = String(date.getMonth() + 1).padStart(2, '0');
      const year = date.getFullYear();
      return `${month}/${year}`;
    },
    formatCurrency(value) {
      return new Intl.NumberFormat('id-ID').format(value);
    },
    applyHanaFilters() {
      this.hanaServerParams.start_month = this.hana_start_month;
      this.hanaServerParams.end_month = this.hana_end_month;
      this.fetchHanaData();
    },
    async fetchHanaData() {
      this.hanaIsLoading = true;
      try {
        const { page, per_page, start_dt, sort, start_month, end_month } = this.hanaServerParams;
        let url = `/bifast/bifast-hana-data?page=${page}&per_page=${per_page}&start_dt=${start_dt}&start_month=${start_month}&end_month=${end_month}`;
        if (sort.length > 0 && sort[0].field) {
          url += `&sort_field=${sort[0].field}&sort_type=${sort[0].type}`;
        }
        const response = await axios.get(url);
        this.hanaData = response.data.data;
        this.hanaTotalRecords = response.data.total;
      } catch (error) {
        handleError(error, this.toast);
      } finally {
        this.hanaIsLoading = false;
      }
    },
    onHanaPageChange(params) {
      this.hanaServerParams.page = params.currentPage;
      this.fetchHanaData();
    },
    onHanaPerPageChange(params) {
      this.hanaServerParams.per_page = params.currentPerPage;
      this.fetchHanaData();
    },
    onHanaFilterChange(params) {
      this.hanaServerParams.start_dt = params.columnFilters.startDt;
      this.fetchHanaData();
    },
    onHanaSortChange(params) {
      this.hanaServerParams.sort = params;
      this.fetchHanaData();
    },
    async exportHanaData() {
      try {
        const response = await axios.get('/bifast/bifast-hana-data?per_page=-1');
        const csv = Papa.unparse(response.data.data);
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.setAttribute('download', 'bifast-hana-data.csv');
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
      } catch (error) {
        handleError(error, this.toast);
      }
    },
  }
}
</script>
