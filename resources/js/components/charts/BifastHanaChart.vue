<template>
    <div class="bifast-chart-container">
        <!-- Chart Section -->
        <div class="chart-section">
    <div class="chart-header mb-3 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
          <h4>Bifast Transaction Analytics (MyHana)</h4>
          &nbsp;&nbsp;<img src="/images/hanaBankLogo.png" alt="Hana Bank Logo" style="height: 30px; margin-right: 10px;">
        </div>
        <div class="col-md-3">
                    <select class="form-control" v-model="selectedChartType">
                        <option value="line">Line/Bar Chart</option>
                        <option value="pie">Pie Chart</option>
                        <option value="area">Area Chart</option>
                    </select>
                </div>
            </div>
            <div class="chart-wrapper">
                <canvas ref="chartCanvas"></canvas>
            </div>

            <!-- Error State -->
            <div v-if="error" class="alert alert-danger mt-3">
                <strong>Error:</strong> {{ error }}
            </div>
        </div>
    </div>
</template>

<script>
import { Chart, registerables } from 'chart.js';
import trendline from 'chartjs-plugin-trendline';
import ChartDataLabels from 'chartjs-plugin-datalabels';

// Register Chart.js components and the trendline plugin
Chart.register(...registerables, trendline, ChartDataLabels);

export default {
    name: 'BifastHanaChart',
    props: {
        data: {
            type: Array,
            required: true
        }
    },
    data() {
        return {
            chart: null,
            error: null,
            selectedChartType: 'line',
        };
    },
    mounted() {
        this.initializeChart();
    },
    watch: {
        data: {
            handler() {
                this.$nextTick(() => {
                    this.initializeChart();
                });
            },
            deep: true
        },
        selectedChartType: {
            handler() {
                this.$nextTick(() => {
                    this.initializeChart();
                });
            }
        }
    },
    beforeUnmount() {
        if (this.chart) {
            this.chart.destroy();
        }
    },
    methods: {
        initializeChart() {
            if (Array.isArray(this.data) && this.data.length > 0) {
                this.error = null;
                const chartData = this.processData(this.data);
                this.createChart(chartData);
            } else {
                this.error = 'No data available for the chart.';
                if (this.chart) {
                    this.chart.destroy();
                    this.chart = null;
                }
            }
        },

        processData(data) {
            const sortedData = [...data].sort((a, b) => new Date(a.period || a.startDt) - new Date(b.period || b.startDt));
            const labels = sortedData.map(item => {
                const date = new Date(item.period || item.startDt);
                return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
            });

            if (this.selectedChartType === 'pie') {
                return {
                    labels,
                    datasets: [{
                        label: 'Transaction Amount',
                        data: sortedData.map(item => item.bifastMyhanaTrxAmt || 0),
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.8)',
                            'rgba(54, 162, 235, 0.8)',
                            'rgba(255, 206, 86, 0.8)',
                            'rgba(75, 192, 192, 0.8)',
                            'rgba(153, 102, 255, 0.8)',
                            'rgba(255, 159, 64, 0.8)'
                        ],
                        borderColor: 'white',
                        borderWidth: 2,
                    }],
                };
            }

            const isAreaChart = this.selectedChartType === 'area';

            return {
                labels,
                datasets: [
                    {
                        label: 'Transaction Amount',
                        data: sortedData.map(item => item.bifastMyhanaTrxAmt || 0),
                        backgroundColor: isAreaChart ? 'rgba(99, 102, 241, 0.2)' : 'rgba(99, 102, 241, 0.8)',
                        borderColor: 'red',
                        borderWidth: isAreaChart ? 2 : 1,
                        type: isAreaChart ? 'line' : 'bar',
                        fill: isAreaChart,
                        yAxisID: 'yAmt',
                        order: 2,
                        trendlineLinear: {
                            style: "rgba(255, 0, 0, 1)",
                            lineStyle: "dotted",
                            width: 3,
                            projection: false,
                        }
                    },
                    {
                        label: 'Transaction Frequency',
                        data: sortedData.map(item => item.bifastMyhanaTrxFreq || 0),
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        borderColor: 'rgba(34, 197, 94, 1)',
                        borderWidth: 3,
                        fill: isAreaChart,
                        type: 'line',
                        yAxisID: 'yFreq',
                        order: 1,
                        pointBackgroundColor: 'rgba(34, 197, 94, 1)',
                        pointBorderColor: 'rgba(34, 197, 94, 1)',
                        pointRadius: 5
                    },
                    {
                        label: 'Unique CIF',
                        data: sortedData.map(item => item.bifastMyhanaUniqueCifQty || 0),
                        backgroundColor: 'rgba(251, 191, 36, 0.1)',
                        borderColor: 'rgba(251, 191, 36, 1)',
                        borderWidth: 3,
                        fill: isAreaChart,
                        type: 'line',
                        yAxisID: 'yFreq',
                        order: 1,
                        pointBackgroundColor: 'rgba(251, 191, 36, 1)',
                        pointBorderColor: 'rgba(251, 191, 36, 1)',
                        pointRadius: 5
                    }
                ],
            };
        },

        createChart(chartData) {
            if (this.chart) {
                this.chart.destroy();
            }

            const canvas = this.$refs.chartCanvas;
            if (!canvas) {
                return;
            }
            const ctx = canvas.getContext('2d');
            let options;
            let type;

            if (this.selectedChartType === 'pie') {
                type = 'pie';
                options = {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Bifast MyHana Transaction Amount Distribution',
                            font: { size: 25, weight: 'bold' }
                        },
                        legend: { display: true, position: 'top' },
                        tooltip: {
                            callbacks: {
                                label: (context) => {
                                    const label = context.label || '';
                                    const value = context.parsed || 0;
                                    return `${label}: ${this.formatLargeNumber(value)}`;
                                }
                            }
                        },
                        datalabels: {
                            backgroundColor: (context) => {
                                return context.dataset.backgroundColor[context.dataIndex];
                            },
                            borderColor: 'white',
                            borderRadius: 4,
                            borderWidth: 2,
                            color: 'black',
                            font: {
                                weight: 'bold'
                            },
                            padding: 4,
                            formatter: (value, context) => {
                                const total = context.chart.getDatasetMeta(0).total;
                                const percentage = total > 0 ? (value / total * 100).toFixed(1) + '%' : '0%';
                                return percentage;
                            },
                        }
                    }
                };
            } else {
                type = this.selectedChartType === 'area' ? 'line' : 'bar';
                options = {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    layout: { padding: { bottom: 20 } },
                    plugins: {
                        title: {
                            display: true,
                            text: 'Bifast Transaction - Hana Bank',
                            font: { size: 25, weight: 'bold' }
                        },
                        legend: {
                            display: true,
                            position: 'top',
                            labels: { usePointStyle: true, padding: 20 }
                        },
                        tooltip: {
                            callbacks: {
                                label: (context) => {
                                    const datasetLabel = context.dataset.label || '';
                                    const value = context.parsed.y || 0;
                                    return `${datasetLabel}: ${this.formatLargeNumber(value)}`;
                                }
                            }
                        },
                        datalabels: {
                            display: true,
                            formatter: (value, context) => {
                                if (value === null || value === undefined) return '';
                                return this.formatLargeNumber(value);
                            },
                            align: (context) => {
                                return context.dataset.type === 'bar' ? 'center' : 'top';
                            },
                            anchor: (context) => {
                                return context.dataset.type === 'bar' ? 'center' : 'end';
                            },
                            backgroundColor: (context) => {
                                return context.dataset.borderColor;
                            },
                            borderRadius: 4,
                            color: 'white',
                            font: {
                                weight: 'bold'
                            },
                            padding: {
                                top: 4,
                                bottom: 4,
                                left: 8,
                                right: 8
                            }
                        }
                    },
                    scales: {
                        x: {
                            display: true,
                            title: { display: true, text: 'Period', font: { weight: 'bold', size: 19 } },
                            ticks: { font: { size: 14 } }
                        },
                        yAmt: {
                            type: 'linear',
                            position: 'right',
                            title: { display: true, text: 'Transaction Amount', font: { weight: 'bold', size: 19 } },
                            ticks: {
                                font: { size: 14 },
                                callback: (value) => this.formatLargeNumber(value)
                            }
                        },
                        yFreq: {
                            type: 'linear',
                            position: 'left',
                            title: { display: true, text: 'Frequency / Unique CIF', font: { weight: 'bold', size: 19 } },
                            grid: { drawOnChartArea: false },
                            ticks: {
                                font: { size: 14 },
                                callback: (value) => this.formatLargeNumber(value)
                            }
                        }
                    }
                };
            }

            this.chart = new Chart(ctx, {
                type: type,
                data: chartData,
                options: options
            });
        },
        formatLargeNumber(num) {
            const absNum = Math.abs(num);
            if (absNum >= 1e9) {
                return (num / 1e9).toFixed(1) + 'B';
            } else if (absNum >= 1e6) {
                return (num / 1e6).toFixed(1) + 'M';
            } else if (absNum >= 1e3) {
                return (num / 1e3).toFixed(1) + 'K';
            }
            return num.toString();
        }
    },
};
</script>

<style scoped>
.bifast-chart-container {
    padding: 20px;
    background: #f8f9fa;
    min-height: 100vh;
}

.chart-section {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.chart-wrapper {
    position: relative;
    height: 850px;
    width: 100%;
}

.chart-header h4 {
    color: #374151;
    margin: 0;
    font-weight: 600;
}

.form-label {
    font-weight: 500;
    color: #374151;
    margin-bottom: 5px;
}

.form-control {
    border: 1px solid #d1d5db;
    border-radius: 6px;
    padding: 8px 12px;
    font-size: 14px;
}

.form-control:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    outline: none;
}

.form-check-input:checked {
    background-color: #6366f1;
    border-color: #6366f1;
}

.spinner-border {
    color: #6366f1;
}

.alert-danger {
    border: none;
    border-radius: 6px;
    background-color: #fef2f2;
    color: #dc2626;
    border-left: 4px solid #dc2626;
}

.text-muted {
    color: #6b7280 !important;
    font-size: 12px;
}

@media (max-width: 768px) {
    .bifast-chart-container {
        padding: 10px;
    }

    .filter-section,
    .chart-section {
        padding: 15px;
    }

    .chart-wrapper {
        height: 300px;
    }
}
</style>
