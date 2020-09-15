<template>
  <div>
    <v-app-bar app color="white" elevation="1">
      <h2>Operations</h2>
    </v-app-bar>

    <h2 class="mb-5">Top</h2>
    <v-data-table
      :headers="table_top_operations.headers"
      :items="topOperations"
      hide-default-footer
    ></v-data-table>

    <h2 class="mt-8 mb-5">Slow</h2>
    <v-data-table
      :headers="table_slowlest_operations.headers"
      :items="slowlestOperations"
      hide-default-footer
    ></v-data-table>

    <!-- <div v-for="operation in operations" :key="operation.id" class="my-5">
      <v-card>
        <v-card-title class="py-2 bordered">{{ operation.name }}</v-card-title>
        <v-card-text>
          <apexchart
            type="area"
            height="150"
            :options="getOptions(operation)"
            :series="getSeries(operation)"
          />
        </v-card-text>
      </v-card>
    </div>-->
  </div>
</template>

<script>
export default {
  props: ["schema", "operations", "topOperations", "slowlestOperations"],
  data() {
    return {
      table_top_operations: {
        headers: [
          { text: "Operation", value: "name", sortable: false },
          {
            text: "Requests",
            value: "total_requests",
            sortable: false,
            align: "end",
          },
        ],
      },
      table_slowlest_operations: {
        headers: [
          { text: "Operation", value: "name", sortable: false },
          {
            text: "Average (ms)",
            value: "average_duration",
            sortable: false,
            align: "end",
          },
          {
            text: "Latest (ms)",
            value: "latest_duration",
            sortable: false,
            align: "end",
          },
        ],
      },
      series: [
        {
          name: "Requests",
          data: [],
        },
      ],
      options: {
        chart: {
          type: "line",
          width: "100%",
          toolbar: {
            show: false,
          },
        },
        dataLabels: {
          enabled: false,
        },
        stroke: {
          curve: "straight",
          width: 1,
        },
        xaxis: {
          categories: [],
        },
        yaxis: {
          opposite: true,
        },
        grid: {
          yaxis: {
            lines: {
              show: false,
            },
          },
        },
      },
    };
  },
  methods: {
    getSeries(operation) {
      console.log(operation.metrics.totals);
      return [
        {
          name: "Requests",
          data: operation.metrics.totals,
        },
      ];
    },
    getOptions(operation) {
      return {
        ...this.options,
        ...{
          xaxis: { categories: operation.metrics.dates },
        },
      };
      // Object.assign(this.options.xaxis.categories, operation.metrics.dates);
      console.log(operation.metrics.dates);

      return this.options;
    },
  },
};
</script>
<style scoped>
.bordered {
  border-bottom: 1px solid #efefef;
}
</style>