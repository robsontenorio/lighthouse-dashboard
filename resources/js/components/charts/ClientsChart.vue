<template>
  <div>
    <apexchart type="bar" :height="height" :options="chart.options" :series="chart.series" />
  </div>
</template>

<script>
export default {
  props: ["series"],
  data() {
    return {
      chart: {
        series: [
          {
            name: "Requests",
            data: this.series || [],
          },
        ],
        options: {
          chart: {
            toolbar: {
              show: false,
            },
          },
          plotOptions: {
            bar: {
              horizontal: true,
            },
          },
          dataLabels: {
            enabled: false,
          },
        },
      },
    };
  },
  computed: {
    height() {
      return this.chart.series[0].data.length * 50;
    },
  },
};
</script>
