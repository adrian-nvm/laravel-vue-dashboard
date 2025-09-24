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
    </div>
</template>

<script>
export default {
    props: {
        data: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            chartData: this.data,
            charts: [
                { group: 'qris', type: 'line' },
                { group: 'qris', type: 'hana' },
                { group: 'biller', type: 'line' },
                { group: 'biller', type: 'hana' },
                { group: 'debit', type: 'line' },
                { group: 'debit', type: 'hana' }
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
            }, 10000); // 10 seconds
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
