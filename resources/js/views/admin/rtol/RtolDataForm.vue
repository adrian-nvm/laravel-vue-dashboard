<template>
  <div>
    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">RTOL Out - Data Input</h1>

    <div class="row">
      <div class="col-lg-12">
        <div class="card shadow mb-4">
          <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Import CSV Line Bank & Hana Bank</h6>
            <button class="btn btn-info btn-sm" @click="downloadTemplate">Download Template</button>
          </div>
          <div class="card-body">
            <div
              class="upload-zone"
              @dragover.prevent="onDragOver"
              @dragleave.prevent="onDragLeave"
              @drop.prevent="onDrop"
              :class="{ 'drag-over': isDragging }"
              @click="triggerFileInput"
            >
              <input
                type="file"
                ref="fileInput"
                class="d-none"
                accept=".csv"
                @change="handleFileUpload"
              />
              <div v-if="!csvFile">
                <i class="fas fa-cloud-upload-alt fa-3x mb-3"></i>
                <p>Drag & drop a CSV file here, or click to select a file.</p>
              </div>
              <div v-else>
                <i class="fas fa-file-csv fa-3x mb-3"></i>
                <p>File selected: {{ csvFile.name }}</p>
                <button class="btn btn-danger btn-sm" @click.stop="clearCsvFile">Remove</button>
              </div>
            </div>

            <div v-if="csvData.length" class="mt-4">
              <h6>CSV Data Preview</h6>
              <div class="table-responsive">
                <table class="table table-bordered table-sm">
                  <thead>
                    <tr>
                      <th v-for="header in csvHeaders" :key="header">{{ header }}</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(row, index) in csvData.slice(0, 5)" :key="index">
                      <td v-for="header in csvHeaders" :key="header">{{ row[header] }}</td>
                    </tr>
                  </tbody>
                </table>
                <p v-if="csvData.length > 5"><i>...and {{ csvData.length - 5 }} more rows.</i></p>
              </div>
              <button @click="uploadCsv" class="btn btn-primary mt-3">Confirm and Upload</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Tab Navigation -->
    <ul class="nav nav-tabs" id="rtolDataTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="line-tab" data-toggle="tab" data-target="#line" type="button" role="tab" aria-controls="line" aria-selected="true">RTOL Out - Line</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="hana-tab" data-toggle="tab" data-target="#hana" type="button" role="tab" aria-controls="hana" aria-selected="false">RTOL Out - MyHana</button>
      </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="rtolDataTabsContent">
      <!-- RTOL Out - Line Tab -->
      <div class="tab-pane fade show active" id="line" role="tabpanel" aria-labelledby="line-tab">
        <div class="row mt-4">
          <div class="col-lg-12">
            <div class="card shadow mb-4">
              <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Input RTOL Out - Line Data</h6>
              </div>
              <div class="card-body">
                <form @submit.prevent="submitLineForm">
                  <div class="form-group">
                    <label for="lineStartDt">Start Date</label>
                    <input type="month" class="form-control" id="lineStartDt" v-model="lineForm.startDt" :disabled="isSubmitting">
                  </div>
                  <div class="form-group">
                    <label for="rtolTrxFreq">RTOL Out - Transaction Frequency</label>
                    <input type="number" class="form-control" id="rtolTrxFreq" v-model.number="lineForm.rtolTrxFreq" :disabled="isSubmitting">
                  </div>
                  <div class="form-group">
                    <label for="rtolTrxAmt">RTOL Out - Transaction Amount</label>
                    <input type="number" class="form-control" id="rtolTrxAmt" v-model.number="lineForm.rtolTrxAmt" :disabled="isSubmitting">
                  </div>
                  <div class="form-group">
                    <label for="rtolUniqueCifQty">RTOL Out - Unique CIF Quantity</label>
                    <input type="number" class="form-control" id="rtolUniqueCifQty" v-model.number="lineForm.rtolUniqueCifQty" :disabled="isSubmitting">
                  </div>
                  <button type="submit" class="btn btn-primary" :disabled="isSubmitting">Submit Line Data</button>
                </form>
              </div>
            </div>
          </div>
          <div class="col-lg-12">
            <RtolLineDataTable ref="rtolLineDataTable" />
          </div>
        </div>
      </div>

      <!-- RTOL Out - MyHana Tab -->
      <div class="tab-pane fade" id="hana" role="tabpanel" aria-labelledby="hana-tab">
        <div class="row mt-4">
          <div class="col-lg-12">
            <div class="card shadow mb-4">
              <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Input RTOL Out - MyHana Data</h6>
              </div>
              <div class="card-body">
                <form @submit.prevent="submitHanaForm">
                  <div class="form-group">
                    <label for="hanaStartDt">Start Date</label>
                    <input type="month" class="form-control" id="hanaStartDt" v-model="hanaForm.startDt" :disabled="isSubmitting">
                  </div>
                  <div class="form-group">
                    <label for="rtolMyhanaTrxFreq">RTOL Out - MyHana Transaction Frequency</label>
                    <input type="number" class="form-control" id="rtolMyhanaTrxFreq" v-model.number="hanaForm.rtolMyhanaTrxFreq" :disabled="isSubmitting">
                  </div>
                  <div class="form-group">
                    <label for="rtolMyhanaTrxAmt">RTOL Out - MyHana Transaction Amount</label>
                    <input type="number" class="form-control" id="rtolMyhanaTrxAmt" v-model.number="hanaForm.rtolMyhanaTrxAmt" :disabled="isSubmitting">
                  </div>
                  <div class="form-group">
                    <label for="rtolMyhanaUniqueCifQty">RTOL Out - MyHana Unique CIF Quantity</label>
                    <input type="number" class="form-control" id="rtolMyhanaUniqueCifQty" v-model.number="hanaForm.rtolMyhanaUniqueCifQty" :disabled="isSubmitting">
                  </div>
                  <button type="submit" class="btn btn-primary" :disabled="isSubmitting">Submit MyHana Data</button>
                </form>
              </div>
            </div>
          </div>
          <div class="col-lg-12">
            <div class="card shadow mb-4">
              <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">RTOL Out - MyHana Data</h6>
                <button class="btn btn-success btn-sm" @click="exportHanaData">Export CSV</button>
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
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import 'vue-good-table-next/dist/vue-good-table-next.css'
import { VueGoodTable } from 'vue-good-table-next';
import RtolLineDataTable from '@/components/table/RtolLineDataTable.vue';
import axios from 'axios';
import { handleError } from '@/utils/notify';
import { useToast } from "vue-toastification";
import Papa from 'papaparse';

export default {
  name: 'RtolDataForm',
  components: {
    VueGoodTable,
    RtolLineDataTable,
  },
  data() {
    return {
      isSubmitting: false,
      toast: useToast(),
      lineForm: {
        startDt: new Date().toISOString().slice(0, 7),
        rtolTrxFreq: 0,
        rtolTrxAmt: 0,
        rtolUniqueCifQty: 0,
      },
      hanaForm: {
        startDt: new Date().toISOString().slice(0, 7),
        rtolMyhanaTrxFreq: 0,
        rtolMyhanaTrxAmt: 0,
        rtolMyhanaUniqueCifQty: 0,
      },
      csvFile: null,
      csvData: [],
      csvHeaders: [],
      expectedCsvHeaders: [
        'START_DT (DD/MM/YYYY)',
        'TRF_OUT_TRX_FREQ',
        'TRF_OUT_TRX_AMT',
        'TRF_OUT_UNIQUE_CIF_QTY',
        'TRF_OUT_MYHANA_TRX_FREQ',
        'TRF_OUT_MYHANA_TRX_AMT',
        'TRF_OUT_MYHANA_UNIQUE_CIF_QTY',
      ],
      isDragging: false,
      hanaData: [],
      hanaTotalRecords: 0,
      hanaIsLoading: false,
      hanaServerParams: {
        page: 1,
        per_page: 10,
        start_dt: '',
        sort: [],
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
          field: 'rtolMyhanaTrxFreq',
          tdClass: 'text-right',
          formatFn: this.formatCurrency,
        },
        {
          label: 'Transaction Amount',
          field: 'rtolMyhanaTrxAmt',
          tdClass: 'text-right',
          formatFn: this.formatCurrency,
        },
        {
          label: 'Unique CIF Quantity',
          field: 'rtolMyhanaUniqueCifQty',
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
    downloadTemplate() {
      const csv = Papa.unparse([this.expectedCsvHeaders]);
      const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
      const link = document.createElement('a');
      link.href = URL.createObjectURL(blob);
      link.setAttribute('download', 'rtol-template.csv');
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    },
    async fetchHanaData() {
      this.hanaIsLoading = true;
      try {
        const { page, per_page, start_dt, sort } = this.hanaServerParams;
        let url = `/rtol/rtol-hana-data?page=${page}&per_page=${per_page}&start_dt=${start_dt}`;
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
    async submitLineForm() {
      this.isSubmitting = true;
      try {
        const submissionData = {
          ...this.lineForm,
          startDt: `${this.lineForm.startDt}-01`
        };
        const response = await axios.post('rtol/store-rtol-line-data', submissionData);
        this.$refs.rtolLineDataTable.fetchLineData();
        this.toast.success(response.data.message);
      } catch (error) {
        handleError(error, this.toast);
      } finally {
        this.isSubmitting = false;
      }
    },
    fetchLineData() {
      this.$refs.rtolLineDataTable.fetchLineData();
    },
    async submitHanaForm() {
      this.isSubmitting = true;
      try {
        const submissionData = {
          ...this.hanaForm,
          startDt: `${this.hanaForm.startDt}-01`
        };
        const response = await axios.post('rtol/store-rtol-hana-data', submissionData);
        this.fetchHanaData();
        this.toast.success(response.data.message);
      } catch (error) {
        handleError(error, this.toast);
      } finally {
        this.isSubmitting = false;
      }
    },
    triggerFileInput() {
      this.$refs.fileInput.click();
    },
    onDragOver(event) {
      this.isDragging = true;
    },
    onDragLeave(event) {
      this.isDragging = false;
    },
    onDrop(event) {
      this.isDragging = false;
      const files = event.dataTransfer.files;
      if (files.length > 0) {
        this.csvFile = files[0];
        this.parseCsv();
      }
    },
    handleFileUpload(event) {
      const files = event.target.files;
      if (files.length > 0) {
        this.csvFile = files[0];
        this.parseCsv();
      }
    },
    parseCsv() {
      if (!this.csvFile) return;

      Papa.parse(this.csvFile, {
        header: true,
        skipEmptyLines: true,
        complete: (results) => {
          this.csvData = results.data.map(row => {
            const newRow = {};
            for (const key in row) {
              if (key.trim() === 'START_DT (DD/MM/YYYY)') {
                newRow['START_DT'] = row[key];
              } else {
                newRow[key] = row[key];
              }
            }
            return newRow;
          });
          this.csvHeaders = results.meta.fields;
          this.toast.success('CSV file parsed successfully. Please review the data before uploading.');
        },
        error: (error) => {
          this.toast.error('Error parsing CSV file.');
          handleError(error, this.toast);
        }
      });
    },
    clearCsvFile() {
      this.csvFile = null;
      this.csvData = [];
      this.csvHeaders = [];
      this.$refs.fileInput.value = '';
    },
    async uploadCsv() {
      if (this.csvData.length === 0) {
        this.toast.error('No data to upload. Please select and parse a CSV file.');
        return;
      }

      if (JSON.stringify(this.csvHeaders) !== JSON.stringify(this.expectedCsvHeaders)) {
          this.toast.error('CSV headers do not match the template.');
          return;
      }

      try {
        const response = await axios.post('/rtol/upload', { data: this.csvData });
        this.fetchLineData();
        this.fetchHanaData();
        this.clearCsvFile();
        this.toast.success(response.data.message);
      } catch (error) {
        handleError(error, this.toast);
      }
    },
    async exportHanaData() {
      try {
        const response = await axios.get('/rtol/rtol-hana-data?per_page=-1');
        const csv = Papa.unparse(response.data.data);
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.setAttribute('download', 'rtol-hana-data.csv');
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

<style scoped>
.upload-zone {
  border: 2px dashed #ccc;
  border-radius: 8px;
  padding: 40px;
  text-align: center;
  cursor: pointer;
  transition: background-color 0.3s, border-color 0.3s;
}
.upload-zone:hover {
  background-color: #f8f9fa;
  border-color: #007bff;
}
.upload-zone.drag-over {
  background-color: #e9ecef;
  border-color: #0056b3;
}
.upload-zone p {
  margin: 0;
  color: #6c757d;
}
</style>
