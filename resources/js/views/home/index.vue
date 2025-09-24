<template>
  <div>
    <Nav />
    <div class="masthead">
      <div class="text-center text-white">
        <h1 class="py-5 home-title">
          Payment and Digital Channel Admin Dashboard
        </h1>
      </div>
    </div>
    <div class="container">
      <div class="text-center py-5 min-vh-50">
        <router-link to="/admin" class="btn btn-primary">
          Go to dashboard <i class="fas fa-chevron-right"></i
        ></router-link>
      </div>
    </div>
    <Footer />
  </div>
</template>

<script>
import Nav from "../../components/Nav.vue";
import Footer from "../../components/Footer.vue";

import QrisLineChart from '../../components/charts/QrisLineChart.vue';
import QrisHanaChart from '../../components/charts/QrisHanaChart.vue';
import BillerLineChart from '../../components/charts/BillerLineChart.vue';
import BillerHanaChart from '../../components/charts/BillerHanaChart.vue';
import DebitLineChart from '../../components/charts/DebitLineChart.vue';
import DebitHanaChart from '../../components/charts/DebitHanaChart.vue';

export default {
  name: "Home",
  components: {
    Nav,
    Footer,
    QrisLineChart,
    QrisHanaChart,
    BillerLineChart,
    BillerHanaChart,
    DebitLineChart,
    DebitHanaChart,
  },
};
</script>

<style>
.home-title {
  font-size: 4rem;
}
.masthead {
  background: linear-gradient(0deg, #4e73df 0%, #36b9cc 100%);
  padding-top: 5rem;
  padding-bottom: 5rem;
}
.min-vh-50 {
    min-height: 50vh !important;
}
</style>
