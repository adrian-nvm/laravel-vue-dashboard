<template>
  <div>
    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Debit Data Input</h1>

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
    <ul class="nav nav-tabs" id="debitDataTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="line-tab" data-toggle="tab" data-target="#line" type="button" role="tab" aria-controls="line" aria-selected="true">Debit Line Bank</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="hana-tab" data-toggle="tab" data-target="#hana" type="button" role="tab" aria-controls="hana" aria-selected="false">Debit Hana Bank</button>
      </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="debitDataTabsContent">
      <!-- Debit Line Tab -->
      <div class="tab-pane fade show active" id="line" role="tabpanel" aria-labelledby="line-tab">
        <div class="row mt-4">
          <div class="col-lg-12">
            <div class="card shadow mb-4">
              <div class="card-header py-3">
                <div class="d-flex align-items-center">
                  <h6 class="m-0 font-weight-bold text-primary">Input Debit Line Data</h6>
                  &nbsp;&nbsp;<img src="/images/lineBankLogo.png" alt="Line Bank Logo" style="height: 30px;">
                </div>
              </div>
              <div class="card-body">
                <form @submit.prevent="submitLineForm">
                  <div class="form-group">
                    <label for="lineStartDt">Start Date</label>
                    <input type="month" class="form-control" id="lineStartDt" v-model="lineForm.startDt" :disabled="isSubmitting">
                  </div>
                  <div class="form-group">
                    <label for="debitTrxFreq">Debit Transaction Frequency</label>
                    <input type="number" class="form-control" id="debitTrxFreq" v-model.number="lineForm.debitTrxFreq" :disabled="isSubmitting">
                  </div>
                  <div class="form-group">
                    <label for="debitTrxAmt">Debit Transaction Amount</label>
                    <input type="number" class="form-control" id="debitTrxAmt" v-model.number="lineForm.debitTrxAmt" :disabled="isSubmitting">
                  </div>
                  <div class="form-group">
                    <label for="debitUniqueCifQty">Debit Unique CIF Quantity</label>
                    <input type="number" class="form-control" id="debitUniqueCifQty" v-model.number="lineForm.debitUniqueCifQty" :disabled="isSubmitting">
                  </div>
                  <button type="submit" class="btn btn-primary" :disabled="isSubmitting">Submit Line Bank</button>
                </form>
              </div>
            </div>
          </div>
          <div class="col-lg-12">
            <DebitLineDataTable ref="debitLineDataTable" />
          </div>
        </div>
      </div>

      <!-- Debit MyHana Tab -->
      <div class="tab-pane fade" id="hana" role="tabpanel" aria-labelledby="hana-tab">
        <div class="row mt-4">
          <div class="col-lg-12">
            <div class="card shadow mb-4">
              <div class="card-header py-3">
                <div class="d-flex align-items-center">
                  <h6 class="m-0 font-weight-bold text-primary">Input Debit Hana Bank</h6>
                  &nbsp;&nbsp;<img src="/images/hanaBankLogo.png" alt="Hana Bank Logo" style="height: 30px; margin-right: 10px;">
                </div>
              </div>
              <div class="card-body">
                <form @submit.prevent="submitHanaForm">
                  <div class="form-group">
                    <label for="hanaStartDt">Start Date</label>
                    <input type="month" class="form-control" id="hanaStartDt" v-model="hanaForm.startDt" :disabled="isSubmitting">
                  </div>
                  <div class="form-group">
                    <label for="debitMyhanaTrxFreq">Debit Transaction Frequency</label>
                    <input type="number" class="form-control" id="debitMyhanaTrxFreq" v-model.number="hanaForm.debitMyhanaTrxFreq" :disabled="isSubmitting">
                  </div>
                  <div class="form-group">
                    <label for="debitMyhanaTrxAmt">Debit Transaction Amount</label>
                    <input type="number" class="form-control" id="debitMyhanaTrxAmt" v-model.number="hanaForm.debitMyhanaTrxAmt" :disabled="isSubmitting">
                  </div>
                  <div class="form-group">
                    <label for="debitMyhanaUniqueCifQty">Debit Unique CIF Quantity</label>
                    <input type="number" class="form-control" id="debitMyhanaUniqueCifQty" v-model.number="hanaForm.debitMyhanaUniqueCifQty" :disabled="isSubmitting">
                  </div>
                  <button type="submit" class="btn btn-primary" :disabled="isSubmitting">Submit Hana Bank</button>
                </form>
              </div>
            </div>
          </div>
          <div class="col-lg-12">
            <DebitHanaDataTable ref="debitHanaDataTable" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import 'vue-good-table-next/dist/vue-good-table-next.css'
import { VueGoodTable } from 'vue-good-table-next';
import DebitLineDataTable from '@/components/table/DebitLineDataTable.vue';
import DebitHanaDataTable from '@/components/table/DebitHanaDataTable.vue';
import axios from 'axios';
import { handleError } from '@/utils/notify';
import { useToast } from "vue-toastification";
import Papa from 'papaparse';

export default {
  name: 'DebitDataForm',
  components: {
    VueGoodTable,
    DebitLineDataTable,
    DebitHanaDataTable,
  },
  data() {
    return {
      isSubmitting: false,
      toast: useToast(),
      lineForm: {
        startDt: new Date().toISOString().slice(0, 7),
        debitTrxFreq: 0,
        debitTrxAmt: 0,
        debitUniqueCifQty: 0,
      },
      hanaForm: {
        startDt: new Date().toISOString().slice(0, 7),
        debitMyhanaTrxFreq: 0,
        debitMyhanaTrxAmt: 0,
        debitMyhanaUniqueCifQty: 0,
      },
      csvFile: null,
      csvData: [],
      csvHeaders: [],
      expectedCsvHeaders: [
        'START_DT (DD/MM/YYYY)',
        'DEBIT_TRX_FREQ',
        'DEBIT_TRX_AMT',
        'DEBIT_UNIQUE_CIF_QTY',
        'DEBIT_MYHANA_TRX_FREQ',
        'DEBIT_MYHANA_TRX_AMT',
        'DEBIT_MYHANA_UNIQUE_CIF_QTY',
      ],
      isDragging: false,
    }
  },
  methods: {
    downloadTemplate() {
      const csv = Papa.unparse([this.expectedCsvHeaders]);
      const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
      const link = document.createElement('a');
      link.href = URL.createObjectURL(blob);
      link.setAttribute('download', 'debit-template.csv');
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    },
    async submitLineForm() {
      this.isSubmitting = true;
      try {
        const submissionData = {
          ...this.lineForm,
          startDt: `${this.lineForm.startDt}-01`
        };
        const response = await axios.post('debit/store-debit-line-data', submissionData);
        this.$refs.debitLineDataTable.fetchLineData();
        this.toast.success(response.data.message);
      } catch (error) {
        handleError(error, this.toast);
      } finally {
        this.isSubmitting = false;
      }
    },
    fetchLineData() {
      this.$refs.debitLineDataTable.fetchLineData();
    },
    async submitHanaForm() {
      this.isSubmitting = true;
      try {
        const submissionData = {
          ...this.hanaForm,
          startDt: `${this.hanaForm.startDt}-01`
        };
        const response = await axios.post('debit/store-debit-hana-data', submissionData);
        this.$refs.debitHanaDataTable.fetchHanaData();
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
        const response = await axios.post('/debit/upload', { data: this.csvData });
        this.fetchLineData();
        this.$refs.debitHanaDataTable.fetchHanaData();
        this.clearCsvFile();
        this.toast.success(response.data.message);
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
