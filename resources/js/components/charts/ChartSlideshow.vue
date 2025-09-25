<template>
    <div class="chart-container">
          <div v-show="isVisible('qris', 'line')">
            <qris-line-chart :data="chartData.qris || []"></qris-line-chart>
        </div>
        <div v-show="isVisible('qris', 'hana')">
            <qris-hana-chart :data="chartData.qris || []"></qris-hana-chart>
        </div>

        <div v-show="isVisible('biller', 'line')">
            <biller-line-chart :data="chartData.biller || []"></biller-line-chart>
        </div>
        <div v-show="isVisible('biller', 'hana')">
            <biller-hana-chart :data="chartData.biller || []"></biller-hana-chart>
        </div>

        <div v-show="isVisible('debit', 'line')">
            <debit-line-chart :data="chartData.debit || []"></debit-line-chart>
        </div>
        <div v-show="isVisible('debit', 'hana')">
            <debit-hana-chart :data="chartData.debit || []"></debit-hana-chart>
        </div>

        <div v-show="isVisible('bifast', 'line')">
            <bifast-line-chart :data="chartData.bifast || []"></bifast-line-chart>
        </div>
        <div v-show="isVisible('bifast', 'hana')">
            <bifast-hana-chart :data="chartData.bifast || []"></bifast-hana-chart>
        </div>

        <div v-show="isVisible('rtol', 'line')">
            <rtol-line-chart :data="chartData.rtol || []"></rtol-line-chart>
        </div>
        <div v-show="isVisible('rtol', 'hana')">
            <rtol-hana-chart :data="chartData.rtol || []"></rtol-hana-chart>
        </div>
    </div>
</template>

<script>
import QrisLineChart from './QrisLineChart.vue';
import QrisHanaChart from './QrisHanaChart.vue';
import BillerLineChart from './BillerLineChart.vue';
import BillerHanaChart from './BillerHanaChart.vue';
import DebitLineChart from './DebitLineChart.vue';
import DebitHanaChart from './DebitHanaChart.vue';
import BifastLineChart from './BifastLineChart.vue';
import BifastHanaChart from './BifastHanaChart.vue';
import RtolLineChart from './RtolLineChart.vue';
import RtolHanaChart from './RtolHanaChart.vue';

export default {
    components: {
        QrisLineChart,
        QrisHanaChart,
        BillerLineChart,
        BillerHanaChart,
        DebitLineChart,
        DebitHanaChart,
        BifastLineChart,
        BifastHanaChart,
        RtolLineChart,
        RtolHanaChart
    },
    data() {
        return {
            chartData: window.chartData || {},
            charts: [
                { group: 'qris', type: 'line' },
                { group: 'qris', type: 'hana' },
                { group: 'biller', type: 'line' },
                { group: 'biller', type: 'hana' },
                { group: 'debit', type: 'line' },
                { group: 'debit', type: 'hana' },
                { group: 'bifast', type: 'line' },
                { group: 'bifast', type: 'hana' },
                { group: 'rtol', type: 'line' },
                { group: 'rtol', type: 'hana' }
            ],
            currentIndex: 0,
            interval: null
        };
    },
    mounted() {
        this.startSlideshow();
    },
    beforeDestroy() {
        this.stopSlideshow();
    },
    methods: {
        startSlideshow() {
            this.interval = setInterval(() => {
                this.currentIndex = (this.currentIndex + 1) % this.charts.length;
            }, 5000); // 10 seconds
        },
        stopSlideshow() {
            clearInterval(this.interval);
        },
        isVisible(group, type) {
            const currentChart = this.charts[this.currentIndex];
            return currentChart.group === group && currentChart.type === type;
        }
    }
};
</script>

<style scoped>
.chart-container {
    width: 100%;
}
</style>
