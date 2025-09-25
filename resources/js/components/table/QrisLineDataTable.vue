<template>
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <div class="d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">QRIS Line Data</h6>
        <button class="btn btn-success btn-sm" @click="exportLineData">Export CSV</button>
      </div>
      <form @submit.prevent="applyLineFilters" class="form-inline mt-3">
        <div class="form-group mr-3">
          <label for="line_start_month" class="mr-2">Start Month</label>
          <input type="month" class="form-control form-control-sm" id="line_start_month" v-model="line_start_month">
        </div>
        <div class="form-group mr-3">
          <label for="line_end_month" class="mr-2">End Month</label>
          <input type="month" class="form-control form-control-sm" id="line_end_month" v-model="line_end_month">
        </div>
        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
      </form>
    </div>
    <div class="card-body" style="position: relative;">
      <loading-indicator v-if="lineIsLoading"></loading-indicator>
      <vue-good-table
        mode="remote"
        :columns="lineColumns"
        :rows="lineData"
        :total-rows="lineTotalRecords"
        :pagination-options="{ enabled: true }"
        @page-change="onLinePageChange"
        @per-page-change="onLinePerPageChange"
        @column-filter="onLineFilterChange"
        @sort-change="onLineSortChange"
      />
    </div>
  </div>
</template>

<script>
import 'vue-good-table-next/dist/vue-good-table-next.css'
import { VueGoodTable } from 'vue-good-table-next';
import axios from 'axios';
import LoadingIndicator from '../LoadingIndicator.vue';
import { handleError } from '@/utils/notify';
import { useToast } from "vue-toastification";
import Papa from 'papaparse';

export default {
  name: 'QrisLineDataTable',
  components: {
    VueGoodTable,
    LoadingIndicator,
  },
  data() {
    return {
      line_start_month: '',
      line_end_month: '',
      toast: useToast(),
      lineData: [],
      lineTotalRecords: 0,
      lineIsLoading: false,
      lineServerParams: {
        page: 1,
        per_page: 10,
        start_dt: '',
        sort: [],
        start_month: '',
        end_month: '',
      },
      lineColumns: [
        {
          label: 'Start Date (MM/YYYY)',
          field: 'startDt',
          formatFn: this.formatMonthYear,
          filterOptions: {
            enabled: true,
            placeholder: 'Filter by date...',
          },
        },
        {
          label: 'Transaction Frequency',
          field: 'qrisTrxFreq',
          tdClass: 'text-right',
          formatFn: this.formatCurrency,
        },
        {
          label: 'Transaction Amount',
          field: 'qrisTrxAmt',
          tdClass: 'text-right',
          formatFn: this.formatCurrency,
        },
        {
          label: 'Unique CIF Quantity',
          field: 'qrisUniqueCifQty',
          tdClass: 'text-right',
          formatFn: this.formatCurrency,
        },
      ],
    }
  },
  mounted() {
    this.fetchLineData();
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
    applyLineFilters() {
      this.lineServerParams.start_month = this.line_start_month;
      this.lineServerParams.end_month = this.line_end_month;
      this.fetchLineData();
    },
    async fetchLineData() {
      this.lineIsLoading = true;
      try {
        const { page, per_page, start_dt, sort, start_month, end_month } = this.lineServerParams;
        let url = `/qris/line-data?page=${page}&per_page=${per_page}&start_dt=${start_dt}&start_month=${start_month}&end_month=${end_month}`;
        if (sort.length > 0 && sort[0].field) {
          url += `&sort_field=${sort[0].field}&sort_type=${sort[0].type}`;
        }
        const response = await axios.get(url);
        this.lineData = response.data.data;
        this.lineTotalRecords = response.data.total;
      } catch (error) {
        handleError(error, this.toast);
      } finally {
        this.lineIsLoading = false;
      }
    },
    onLinePageChange(params) {
      this.lineServerParams.page = params.currentPage;
      this.fetchLineData();
    },
    onLinePerPageChange(params) {
      this.lineServerParams.per_page = params.currentPerPage;
      this.fetchLineData();
    },
    onLineFilterChange(params) {
      this.lineServerParams.start_dt = params.columnFilters.startDt;
      this.fetchLineData();
    },
    onLineSortChange(params) {
      this.lineServerParams.sort = params;
      this.fetchLineData();
    },
    async exportLineData() {
      try {
        const response = await axios.get('/qris/line-data?per_page=-1');
        const csv = Papa.unparse(response.data.data);
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.setAttribute('download', 'qris-line-data.csv');
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
