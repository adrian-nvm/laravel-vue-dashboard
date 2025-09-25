<template>
    <div class="rtol-chart-container">
        <!-- Chart Section -->
        <div class="chart-section">
            <div class="chart-header mb-3 d-flex justify-content-between align-items-center">
                <h4>RTOL Out - Transaction Analytics (MyLine)</h4>
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

// Register Chart.js components and the trendline plugin
Chart.register(...registerables, trendline);

export default {
    name: 'RtolLineChart',
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
                        data: sortedData.map(item => item.rtolTrxAmt || 0),
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
                        data: sortedData.map(item => item.rtolTrxAmt || 0),
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
                        data: sortedData.map(item => item.rtolTrxFreq || 0),
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
                        data: sortedData.map(item => item.rtolUniqueCifQty || 0),
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
                            text: 'RTOL Out - MyLine Transaction Amount Distribution',
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
                            text: 'RTOL Out - Transaction - Line Bank',
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
                options: options,
                plugins: type !== 'pie' ? [{
                    afterDatasetsDraw: (chart) => {
                        const ctx = chart.ctx;
                        chart.data.datasets.forEach((dataset, i) => {
                            const meta = chart.getDatasetMeta(i);
                            if (!meta.hidden) {
                                meta.data.forEach((element, index) => {
                                    const data = dataset.data[index];
                                    const fontSize = 15;
                                    const fontStyle = 'bold';
                                    ctx.font = `${fontStyle} ${fontSize}px Arial`;
                                    ctx.textAlign = 'center';
                                    ctx.textBaseline = 'bottom';
                                    let formattedValue;
                                    let xPos = element.x;
                                    let yPos;
                                    if (dataset.type === 'bar') {
                                        formattedValue = this.formatLargeNumber(data);
                                        yPos = element.y + element.height - 5;
                                        ctx.fillStyle = 'rgb(0, 0, 0)';
                                        ctx.fillText(formattedValue, xPos, yPos);
                                    } else if (dataset.type === 'line') {
                                        formattedValue = this.formatLargeNumber(data);
                                        yPos = element.y - 15;
                                        ctx.fillStyle = 'rgb(0, 0, 0)';
                                        ctx.textBaseline = 'bottom';
                                        ctx.fillText(formattedValue, xPos, yPos);
                                    }
                                });
                            }
                        });
                    }
                }] : []
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
.rtol-chart-container {
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
    .rtol-chart-container {
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
