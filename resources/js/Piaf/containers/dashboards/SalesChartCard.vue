<template>
  <b-card :title="$t('dashboards.sales')">
    <div class="dashboard-line-chart">
      <div v-if="lineChartData">
         <line-chart ref="chart" :data="lineChartData" shadow />
      </div>
    </div>
  </b-card>
</template>
<script>
import LineChart from "@/Piaf/components/Charts/Line";
import { lineChartData } from "../../data/charts";
import axios from "axios";

export default {
  components: {
    "line-chart": LineChart
  },
  data() {
    return {
      lineChartData: null
    };
  },

  async mounted() {
    const res = await axios.get(`/api/SalesWidget`);

    lineChartData.labels = res.data.labels;
    lineChartData.datasets[0].data = res.data.data;

    this.lineChartData = lineChartData;
  }
};
</script>
